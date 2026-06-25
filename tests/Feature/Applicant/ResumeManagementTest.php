<?php

namespace Tests\Feature\Applicant;

use App\Enums\UserRole;
use App\Jobs\ExtractResumeText;
use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ResumeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Queue::fake();
    }

    public function test_applicant_can_upload_current_pdf_resume_and_profile_path_is_updated(): void
    {
        $user = $this->applicant();
        $file = UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf');

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $response->assertRedirect(route('applicant.resume'));
        $response->assertSessionHas('status');

        $resume = ResumeDocument::sole();

        $this->assertSame($user->id, $resume->user_id);
        $this->assertSame('resume.pdf', $resume->original_name);
        $this->assertSame('application/pdf', $resume->mime_type);
        $this->assertTrue($resume->is_current);
        $this->assertSame('pending', $resume->extraction_status);
        $this->assertNotNull($resume->content_hash);
        Storage::disk('local')->assertExists($resume->file_path);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'resume_path' => $resume->file_path,
        ]);

        Queue::assertPushed(ExtractResumeText::class, function ($job) use ($user) {
            return $job->resume->user_id === $user->id;
        });
    }

    public function test_same_current_resume_upload_reuses_existing_document_without_extracting_again(): void
    {
        $user = $this->applicant();
        $content = $this->pdfContent('same resume');
        $current = $this->resumeFor($user, [
            'content_hash' => hash('sha256', $content),
            'extraction_status' => 'ready',
            'extracted_text' => 'Existing extracted text.',
        ]);
        $user->profile()->firstOrCreate([])->update(['resume_path' => $current->file_path]);

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $this->pdfUpload('same-resume.pdf', $content),
        ]);

        $response->assertRedirect(route('applicant.resume'));
        $response->assertSessionHas('status', 'Duplicate resume detected. Using your existing file.');

        $this->assertSame(1, $user->resumeDocuments()->count());
        $this->assertTrue($current->fresh()->is_current);
        $this->assertSame($current->file_path, $user->profile()->first()->resume_path);
        Queue::assertNotPushed(ExtractResumeText::class);
    }

    public function test_failed_current_resume_with_same_hash_does_not_auto_retry_extraction(): void
    {
        $user = $this->applicant();
        $content = $this->pdfContent('failed resume');
        $current = $this->resumeFor($user, [
            'content_hash' => hash('sha256', $content),
            'extraction_status' => 'failed',
            'extraction_error' => 'No extractable text found.',
        ]);

        $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $this->pdfUpload('failed-resume.pdf', $content),
        ]);

        $this->assertSame(1, $user->resumeDocuments()->count());
        $this->assertSame('failed', $current->fresh()->extraction_status);
        Queue::assertNotPushed(ExtractResumeText::class);
    }

    public function test_existing_profile_resume_paths_can_be_backfilled_into_resume_documents(): void
    {
        $user = $this->applicant();
        $path = "resumes/{$user->id}/legacy-resume.pdf";
        $content = $this->pdfContent('legacy resume');
        $user->profile()->firstOrCreate([])->update(['resume_path' => $path]);
        Storage::disk('local')->put($path, $content);

        Artisan::call('resumes:backfill-documents');

        $resume = $user->resumeDocuments()->first();

        $this->assertNotNull($resume);
        $this->assertSame($path, $resume->file_path);
        $this->assertSame(hash('sha256', $content), $resume->content_hash);
        $this->assertSame('pending', $resume->extraction_status);
        Queue::assertPushed(ExtractResumeText::class, function ($job) use ($resume) {
            return $job->resume->is($resume);
        });
    }

    public function test_resume_backfill_skips_users_that_already_have_current_resume_documents(): void
    {
        $user = $this->applicant();
        $existing = $this->resumeFor($user);
        $path = "resumes/{$user->id}/legacy-resume.pdf";
        $user->profile()->firstOrCreate([])->update(['resume_path' => $path]);
        Storage::disk('local')->put($path, $this->pdfContent('legacy resume'));

        Artisan::call('resumes:backfill-documents');

        $this->assertSame(1, $user->resumeDocuments()->count());
        $this->assertTrue($existing->fresh()->is_current);
        Queue::assertNotPushed(ExtractResumeText::class);
    }

    public function test_non_pdf_uploads_are_rejected(): void
    {
        $user = $this->applicant();
        $file = UploadedFile::fake()->create('resume.docx', 200, 'application/msword');

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $response->assertSessionHasErrors('resume');
        $this->assertDatabaseCount('resume_documents', 0);
    }

    public function test_oversized_uploads_are_rejected(): void
    {
        $user = $this->applicant();
        $file = UploadedFile::fake()->create('resume.pdf', 6000, 'application/pdf');

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $response->assertSessionHasErrors('resume');
        $this->assertDatabaseCount('resume_documents', 0);
    }

    public function test_new_upload_supersedes_all_existing_current_resumes(): void
    {
        $user = $this->applicant();
        $oldCurrent = $this->resumeFor($user, ['file_path' => 'resumes/old-current.pdf', 'is_current' => true]);
        $duplicateCurrent = $this->resumeFor($user, ['file_path' => 'resumes/duplicate-current.pdf', 'is_current' => true]);
        $otherUser = $this->applicant();
        $otherCurrent = $this->resumeFor($otherUser, ['file_path' => 'resumes/other-current.pdf', 'is_current' => true]);

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => UploadedFile::fake()->create('new-resume.pdf', 150, 'application/pdf'),
        ]);

        $response->assertRedirect(route('applicant.resume'));

        $this->assertFalse($oldCurrent->fresh()->is_current);
        $this->assertFalse($duplicateCurrent->fresh()->is_current);
        $this->assertTrue($otherCurrent->fresh()->is_current);
        $this->assertSame(1, $user->resumeDocuments()->where('is_current', true)->count());

        $newResume = $user->resumeDocuments()->where('original_name', 'new-resume.pdf')->sole();
        $this->assertTrue($newResume->is_current);
        $this->assertSame($newResume->file_path, $user->profile()->first()->resume_path);

        Queue::assertPushed(ExtractResumeText::class);
    }

    public function test_applicant_can_download_their_own_resume(): void
    {
        $user = $this->applicant();
        $resume = $this->resumeFor($user);
        Storage::disk('local')->put($resume->file_path, 'pdf-contents');

        $response = $this->actingAs($user)->get(route('applicant.resume.show', $resume));

        $response->assertOk();
    }

    public function test_missing_resume_file_returns_not_found(): void
    {
        $user = $this->applicant();
        $resume = $this->resumeFor($user);

        $response = $this->actingAs($user)->get(route('applicant.resume.show', $resume));

        $response->assertNotFound();
    }

    public function test_applicant_cannot_view_or_manage_another_users_resume(): void
    {
        $owner = $this->applicant();
        $intruder = $this->applicant();
        $resume = $this->resumeFor($owner, ['is_current' => false]);
        Storage::disk('local')->put($resume->file_path, 'pdf-contents');

        $this->actingAs($intruder)->get(route('applicant.resume.show', $resume))->assertForbidden();
        $this->actingAs($intruder)->patch(route('applicant.resume.set-current', $resume))->assertForbidden();
        $this->actingAs($intruder)->delete(route('applicant.resume.destroy', $resume))->assertForbidden();

        $this->assertDatabaseHas('resume_documents', ['id' => $resume->id]);
    }

    public function test_guest_is_redirected_from_resume_routes(): void
    {
        $owner = $this->applicant();
        $resume = $this->resumeFor($owner);

        $this->get(route('applicant.resume'))->assertRedirect(route('login'));
        $this->post(route('applicant.resume.store'))->assertRedirect(route('login'));
        $this->get(route('applicant.resume.show', $resume))->assertRedirect(route('login'));
        $this->patch(route('applicant.resume.set-current', $resume))->assertRedirect(route('login'));
        $this->delete(route('applicant.resume.destroy', $resume))->assertRedirect(route('login'));
    }

    public function test_non_applicant_users_are_redirected_from_resume_routes(): void
    {
        $owner = $this->applicant();
        $resume = $this->resumeFor($owner);

        foreach ([UserRole::Employer->value, UserRole::Admin->value] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get(route('applicant.resume'))->assertRedirect();
            $this->actingAs($user)->post(route('applicant.resume.store'))->assertRedirect();
            $this->actingAs($user)->get(route('applicant.resume.show', $resume))->assertRedirect();
            $this->actingAs($user)->patch(route('applicant.resume.set-current', $resume))->assertRedirect();
            $this->actingAs($user)->delete(route('applicant.resume.destroy', $resume))->assertRedirect();
        }
    }

    public function test_applicant_can_set_old_resume_as_current_and_profile_path_is_updated(): void
    {
        $user = $this->applicant();
        $current = $this->resumeFor($user, ['file_path' => 'resumes/current.pdf', 'is_current' => true]);
        $old = $this->resumeFor($user, ['file_path' => 'resumes/old.pdf', 'is_current' => false]);
        $user->profile()->firstOrCreate([])->update(['resume_path' => $current->file_path]);

        $response = $this->actingAs($user)->patch(route('applicant.resume.set-current', $old));

        $response->assertRedirect(route('applicant.resume'));
        $this->assertFalse($current->fresh()->is_current);
        $this->assertTrue($old->fresh()->is_current);
        $this->assertSame($old->file_path, $user->profile()->first()->resume_path);
    }

    public function test_applicant_can_delete_resume_even_when_file_is_missing(): void
    {
        $user = $this->applicant();
        $resume = $this->resumeFor($user, ['file_path' => 'resumes/missing.pdf']);
        $user->profile()->firstOrCreate([])->update(['resume_path' => $resume->file_path]);

        $response = $this->actingAs($user)->delete(route('applicant.resume.destroy', $resume));

        $response->assertRedirect(route('applicant.resume'));
        $this->assertDatabaseMissing('resume_documents', ['id' => $resume->id]);
        $this->assertNull($user->profile()->first()->resume_path);
    }

    public function test_applicant_can_delete_current_resume_and_return_to_resume_dashboard(): void
    {
        $user = $this->applicant();
        $resume = $this->resumeFor($user, ['original_name' => 'current.pdf']);
        $user->profile()->firstOrCreate([])->update(['resume_path' => $resume->file_path]);
        Storage::disk('local')->put($resume->file_path, 'pdf-contents');

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->delete(route('applicant.resume.destroy', $resume));

        $response->assertOk();
        $response->assertSee('No current resume uploaded.');
        $response->assertDontSee('current.pdf');
        $this->assertDatabaseMissing('resume_documents', ['id' => $resume->id]);
        $this->assertNull($user->profile()->first()->resume_path);
        Storage::disk('local')->assertMissing($resume->file_path);
    }

    public function test_stale_resume_delete_request_returns_to_resume_dashboard(): void
    {
        $user = $this->applicant();
        $resume = $this->resumeFor($user);
        $url = route('applicant.resume.destroy', $resume);
        $resume->delete();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->delete($url);

        $response->assertOk();
        $response->assertSee('Resume already removed.');
    }

    public function test_resume_index_shows_current_resume_and_history(): void
    {
        $user = $this->applicant();
        $current = $this->resumeFor($user, [
            'original_name' => 'current.pdf',
            'file_path' => 'resumes/current.pdf',
            'is_current' => true,
        ]);
        $old = $this->resumeFor($user, [
            'original_name' => 'old.pdf',
            'file_path' => 'resumes/old.pdf',
            'is_current' => false,
        ]);

        $response = $this->actingAs($user)->get(route('applicant.resume'));

        $response->assertOk();
        $response->assertViewHas('currentResume', fn (ResumeDocument $resume): bool => $resume->id === $current->id);
        $response->assertViewHas('resumeHistory', fn ($history): bool => $history->contains('id', $old->id));
        $response->assertSee('current.pdf');
        $response->assertSee('old.pdf');
        $response->assertSee('Remove selected file');
    }

    private function applicant(): User
    {
        return User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function resumeFor(User $user, array $attributes = []): ResumeDocument
    {
        return $user->resumeDocuments()->create(array_merge([
            'file_path' => 'resumes/'.$user->id.'/resume.pdf',
            'original_name' => 'resume.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 204800,
            'content_hash' => str_repeat('a', 64),
            'extraction_status' => 'pending',
            'is_current' => true,
        ], $attributes));
    }

    private function pdfUpload(string $name, string $content): UploadedFile
    {
        return UploadedFile::fake()
            ->createWithContent($name, $content)
            ->mimeType('application/pdf');
    }

    private function pdfContent(string $marker): string
    {
        return "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n% {$marker}\n%%EOF\n";
    }
}
