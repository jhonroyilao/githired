<?php

namespace Tests\Feature\Applicant;

use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResumeUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        
        Storage::fake('local'); //Mock storage for no saving of real files during tests
    }

    public function test_applicant_can_upload_a_pdf_resume()
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf');

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $response->assertRedirect(route('applicant.resume'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('resume_documents', [ //Check if it actually saved in the db
            'user_id' => $user->id,
            'original_name' => 'resume.pdf',
            'is_current' => true,
        ]);

        $resume = ResumeDocument::sole();
        Storage::disk('local')->assertExists($resume->file_path);
    }

    public function test_guests_cannot_upload_a_resume()
    {
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $response->assertRedirect(route('login')); //Get kicked back to login screen
        $this->assertDatabaseCount('resume_documents', 0);
    }

    public function test_non_pdf_uploads_are_rejected()
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('resume.docx', 200, 'application/msword'); //Try uploading a word doc

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $response->assertSessionHasErrors('resume');
        $this->assertDatabaseCount('resume_documents', 0);
    }

    public function test_oversized_uploads_are_rejected()
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('resume.pdf', 6000, 'application/pdf'); //Upload file over 5MB limit

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $response->assertSessionHasErrors('resume');
        $this->assertDatabaseCount('resume_documents', 0);
    }

    public function test_missing_resume_file_is_rejected()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('applicant.resume.store'), []); //Send nothing

        $response->assertSessionHasErrors('resume');
    }

    public function test_uploading_a_new_resume_supersedes_the_previous_current_resume()
    {
        $user = User::factory()->create();
        $oldResume = ResumeDocument::factory()->for($user)->create(['is_current' => true]);

        $file = UploadedFile::fake()->create('new-resume.pdf', 150, 'application/pdf');

        $this->actingAs($user)->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $this->assertDatabaseHas('resume_documents', [ //Demote old one
            'id' => $oldResume->id,
            'is_current' => false,
        ]);

        $this->assertDatabaseHas('resume_documents', [ //Promote new upload
            'user_id' => $user->id,
            'original_name' => 'new-resume.pdf',
            'is_current' => true,
        ]);

        $this->assertSame(
            1,
            ResumeDocument::where('user_id', $user->id)->where('is_current', true)->count()
        );
    }

    public function test_uploading_a_resume_does_not_affect_other_users_current_resume()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $userBsResume = ResumeDocument::factory()->for($userB)->create(['is_current' => true]);

        $file = UploadedFile::fake()->create('resume.pdf', 150, 'application/pdf');

        $this->actingAs($userA)->post(route('applicant.resume.store'), [
            'resume' => $file,
        ]);

        $this->assertDatabaseHas('resume_documents', [ //User B's file should be untouched
            'id' => $userBsResume->id,
            'is_current' => true,
        ]);
    }

    public function test_applicant_can_view_their_own_resume()
    {
        $user = User::factory()->create();
        $resume = ResumeDocument::factory()->for($user)->create();
        Storage::disk('local')->put($resume->file_path, 'fake-pdf-contents');

        $response = $this->actingAs($user)->get(route('applicant.resume.show', $resume));

        $response->assertOk();
    }

    public function test_applicant_cannot_view_another_users_resume()
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $resume = ResumeDocument::factory()->for($owner)->create();
        Storage::disk('local')->put($resume->file_path, 'fake-pdf-contents');

        $response = $this->actingAs($intruder)->get(route('applicant.resume.show', $resume));

        $response->assertForbidden(); //Block intruder from viewing
    }

    public function test_applicant_cannot_delete_another_users_resume()
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $resume = ResumeDocument::factory()->for($owner)->create();

        $response = $this->actingAs($intruder)->delete(route('applicant.resume.destroy', $resume));

        $response->assertForbidden(); //Block intruder from deleting
        $this->assertDatabaseHas('resume_documents', ['id' => $resume->id]); // Make sure file is still there
    }

    public function test_applicant_can_delete_their_own_resume()
    {
        $user = User::factory()->create();
        $resume = ResumeDocument::factory()->for($user)->create();
        Storage::disk('local')->put($resume->file_path, 'fake-pdf-contents');

        $response = $this->actingAs($user)->delete(route('applicant.resume.destroy', $resume));

        $response->assertRedirect(route('applicant.resume'));
        $this->assertDatabaseMissing('resume_documents', ['id' => $resume->id]);
        Storage::disk('local')->assertMissing($resume->file_path);
    }

    public function test_resume_index_shows_current_resume_and_history()
    {
        $user = User::factory()->create();
        
        $current = ResumeDocument::factory()->for($user)->create(['is_current' => true]);
        $old = ResumeDocument::factory()->for($user)->notCurrent()->create();

        $response = $this->actingAs($user)->get(route('applicant.resume'));

        $response->assertOk();
        $response->assertViewHas('currentResume', fn ($resume) => $resume->id === $current->id);
        $response->assertViewHas('resumeHistory', fn ($history) => $history->contains('id', $old->id));
    }
}