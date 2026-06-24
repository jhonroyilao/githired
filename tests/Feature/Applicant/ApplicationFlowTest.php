<?php

namespace Tests\Feature\Applicant;

use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Jobs\ExtractResumeText;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_can_apply_to_visible_job_with_current_resume(): void
    {
        $applicant = $this->applicant();
        $job = $this->job();
        $resume = $this->resumeFor($applicant, [
            'extraction_status' => 'ready',
            'extracted_text' => 'Laravel and PostgreSQL experience.',
        ]);

        $response = $this->actingAs($applicant)->post(route('applicant.job-listings.apply.store', $job), [
            'cover_letter' => 'I can help ship this role.',
        ]);

        $response->assertRedirect(route('applicant.dashboard', absolute: false));
        $this->assertDatabaseHas('applications', [
            'user_id' => $applicant->id,
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
            'resume_path' => $resume->file_path,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('ai_job_matches', [
            'user_id' => $applicant->id,
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
            'generation_status' => 'pending',
        ]);
    }

    public function test_uploaded_resume_creates_resume_document_and_attaches_it_to_application(): void
    {
        Storage::fake('local');
        Queue::fake();

        $applicant = $this->applicant();
        $job = $this->job();
        $file = UploadedFile::fake()->create('application-resume.pdf', 128, 'application/pdf');

        $response = $this->actingAs($applicant)->post(route('applicant.job-listings.apply.store', $job), [
            'resume' => $file,
        ]);

        $response->assertRedirect(route('applicant.dashboard', absolute: false));

        $resume = ResumeDocument::query()->where('user_id', $applicant->id)->first();

        $this->assertNotNull($resume);
        $this->assertDatabaseHas('applications', [
            'user_id' => $applicant->id,
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
            'resume_path' => $resume->file_path,
            'status' => 'pending',
        ]);
        Storage::disk('local')->assertExists($resume->file_path);

        Queue::assertPushed(ExtractResumeText::class, function ($job) use ($resume) {
            return $job->resume->is($resume);
        });
        $this->assertDatabaseMissing('ai_job_matches', [
            'user_id' => $applicant->id,
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
        ]);
    }

    public function test_duplicate_applications_are_blocked(): void
    {
        $applicant = $this->applicant();
        $job = $this->job();

        Application::query()->create([
            'user_id' => $applicant->id,
            'job_listing_id' => $job->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($applicant)->post(route('applicant.job-listings.apply.store', $job));

        $response->assertRedirect(route('applicant.dashboard', absolute: false));
        $response->assertSessionHas('info', 'You have already applied to this job.');
        $this->assertSame(1, Application::query()->where('user_id', $applicant->id)->where('job_listing_id', $job->id)->count());
    }

    public function test_applicant_cannot_apply_to_hidden_job(): void
    {
        $applicant = $this->applicant();
        $hiddenJobs = [
            $this->job(['approved_at' => null]),
            $this->job(['status' => JobStatus::Draft->value]),
            $this->job(['status' => JobStatus::Pending->value]),
            $this->job(['status' => JobStatus::Rejected->value]),
            $this->job(['status' => JobStatus::Closed->value, 'closed_at' => now()]),
            $this->job(['expires_at' => now()->subDay()]),
        ];
        $deleted = $this->job();
        $deleted->delete();
        $hiddenJobs[] = $deleted;

        foreach ($hiddenJobs as $hiddenJob) {
            $this->actingAs($applicant)
                ->post(route('applicant.job-listings.apply.store', $hiddenJob))
                ->assertNotFound();

            $this->assertDatabaseMissing('applications', [
                'user_id' => $applicant->id,
                'job_listing_id' => $hiddenJob->id,
            ]);
        }
    }

    public function test_guest_and_non_applicant_users_cannot_access_apply_routes(): void
    {
        $job = $this->job();

        $this->get(route('applicant.job-listings.apply', $job))->assertRedirect(route('login'));
        $this->post(route('applicant.job-listings.apply.store', $job))->assertRedirect(route('login'));

        $employer = User::factory()->create(['role' => UserRole::Employer->value]);

        $this->actingAs($employer)->get(route('applicant.job-listings.apply', $job))->assertRedirect();
        $this->actingAs($employer)->post(route('applicant.job-listings.apply.store', $job))->assertRedirect();
    }

    private function applicant(): User
    {
        return User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);
    }

    private function resumeFor(User $user, array $overrides = []): ResumeDocument
    {
        return $user->resumeDocuments()->create(array_merge([
            'file_path' => "resumes/{$user->id}/resume.pdf",
            'original_name' => 'resume.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 204800,
            'content_hash' => str_repeat('b', 64),
            'extraction_status' => 'pending',
            'is_current' => true,
        ], $overrides));
    }

    private function job(array $overrides = []): JobListing
    {
        $employer = User::factory()->create(['role' => UserRole::Employer->value]);
        $company = Company::query()->create([
            'user_id' => $employer->id,
            'name' => 'Hiring Co',
            'slug' => 'hiring-co-'.Str::random(8),
        ]);
        $category = JobCategory::query()->firstOrCreate(
            ['slug' => 'engineering'],
            ['name' => 'Engineering'],
        );

        return JobListing::query()->create(array_merge([
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'category_id' => $category->id,
            'title' => 'Laravel Engineer',
            'slug' => 'laravel-engineer-'.Str::random(8),
            'location' => 'Manila',
            'location_type' => 'remote',
            'type' => JobType::FullTime->value,
            'experience_level' => ExperienceLevel::Entry->value,
            'description' => 'Build product features.',
            'requirements' => 'Experience with Laravel.',
            'skills_required' => ['Laravel'],
            'status' => JobStatus::Active->value,
            'approved_at' => now()->subDay(),
            'approved_by' => $employer->id,
            'published_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ], $overrides));
    }
}
