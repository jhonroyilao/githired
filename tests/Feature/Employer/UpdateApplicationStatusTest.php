<?php

namespace Tests\Feature\Employer;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationStatusLog;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateApplicationStatusTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────────────

    /**
     * Create an employer user with one active job listing.
     *
     * @return array{0: User, 1: JobListing}
     */
    private function makeEmployerWithJob(): array
    {
        $employer = User::factory()->create(['role' => UserRole::Employer->value]);

        $company = Company::create([
            'user_id' => $employer->id,
            'name' => 'Test Corp',
            'slug' => 'test-corp-'.uniqid(),
        ]);

        $category = JobCategory::create([
            'name' => 'Engineering',
            'slug' => 'engineering-'.uniqid(),
        ]);

        $job = JobListing::create([
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'category_id' => $category->id,
            'title' => 'Software Engineer',
            'slug' => 'software-engineer-'.uniqid(),
            'location' => 'Manila',
            'location_type' => 'onsite',
            'type' => 'full-time',
            'experience_level' => 'mid',
            'description' => 'Description',
            'requirements' => 'Requirements',
            'status' => 'active',
            'published_at' => now(),
        ]);

        return [$employer, $job];
    }

    /**
     * Create an applicant user with one application on the given job.
     */
    private function makeApplication(JobListing $job, string $status = 'pending'): Application
    {
        $applicant = User::factory()->create(['role' => UserRole::Applicant->value]);

        return Application::create([
            'user_id' => $applicant->id,
            'job_listing_id' => $job->id,
            'status' => $status,
        ]);
    }

    private function updateUrl(JobListing $job, Application $application): string
    {
        return route('employer.jobs.applicants.status.update', [$job, $application]);
    }

    // ── Tests ────────────────────────────────────────────────────────

    public function test_employer_can_update_application_status(): void
    {
        [$employer, $job] = $this->makeEmployerWithJob();
        $application = $this->makeApplication($job, 'pending');

        $this->actingAs($employer)
            ->patch($this->updateUrl($job, $application), ['status' => 'interview'])
            ->assertRedirect(route('employer.jobs.applicants.show', [$job, $application]));

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'interview',
        ]);
    }

    public function test_status_change_creates_a_log_entry_with_correct_data(): void
    {
        [$employer, $job] = $this->makeEmployerWithJob();
        $application = $this->makeApplication($job, 'pending');

        $this->actingAs($employer)
            ->patch($this->updateUrl($job, $application), [
                'status' => 'interview',
                'note' => 'Technical interview on Friday.',
            ]);

        $this->assertDatabaseHas('application_status_logs', [
            'application_id' => $application->id,
            'old_status' => 'pending',
            'new_status' => 'interview',
            'changed_by' => $employer->id,
            'changed_by_name' => $employer->name,
            'changed_by_email' => $employer->email,
            'note' => 'Technical interview on Friday.',
        ]);

        $log = ApplicationStatusLog::where('application_id', $application->id)->first();

        $this->assertNotNull($log?->created_at);
    }

    public function test_status_updated_at_is_stamped_on_change(): void
    {
        [$employer, $job] = $this->makeEmployerWithJob();
        $application = $this->makeApplication($job, 'pending');

        $this->assertNull($application->status_updated_at);

        $this->actingAs($employer)
            ->patch($this->updateUrl($job, $application), ['status' => 'hired']);

        $this->assertNotNull($application->fresh()->status_updated_at);
    }

    public function test_invalid_status_is_rejected(): void
    {
        [$employer, $job] = $this->makeEmployerWithJob();
        $application = $this->makeApplication($job, 'pending');

        $this->actingAs($employer)
            ->patch($this->updateUrl($job, $application), ['status' => 'shortlisted'])
            ->assertSessionHasErrors('status');

        // Status must remain unchanged in the database.
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'pending',
        ]);
    }

    public function test_status_field_is_required(): void
    {
        [$employer, $job] = $this->makeEmployerWithJob();
        $application = $this->makeApplication($job);

        $this->actingAs($employer)
            ->patch($this->updateUrl($job, $application), [])
            ->assertSessionHasErrors('status');
    }

    public function test_no_log_entry_is_created_when_status_is_unchanged(): void
    {
        [$employer, $job] = $this->makeEmployerWithJob();
        $application = $this->makeApplication($job, 'pending');

        $this->actingAs($employer)
            ->patch($this->updateUrl($job, $application), ['status' => 'pending'])
            ->assertRedirect();

        $this->assertDatabaseCount('application_status_logs', 0);
    }

    public function test_employer_cannot_update_another_employers_application(): void
    {
        [$employer1, $job1] = $this->makeEmployerWithJob();
        [$employer2, $job2] = $this->makeEmployerWithJob();

        $application = $this->makeApplication($job2, 'pending');

        // employer1 tries to update an application on employer2's job.
        $this->actingAs($employer1)
            ->patch($this->updateUrl($job2, $application), ['status' => 'interview'])
            ->assertForbidden();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('application_status_logs', 0);
    }

    public function test_application_must_belong_to_the_job(): void
    {
        [$employer, $job1] = $this->makeEmployerWithJob();

        // A second job for the same employer.
        $job2 = JobListing::create([
            'user_id' => $employer->id,
            'company_id' => Company::where('user_id', $employer->id)->first()->id,
            'category_id' => JobCategory::first()->id,
            'title' => 'Designer',
            'slug' => 'designer-'.uniqid(),
            'location' => 'Cebu',
            'location_type' => 'remote',
            'type' => 'part-time',
            'experience_level' => 'entry',
            'description' => 'Desc',
            'requirements' => 'Req',
            'status' => 'active',
            'published_at' => now(),
        ]);

        // Application belongs to job1, but we submit via job2's URL.
        $application = $this->makeApplication($job1);

        $this->actingAs($employer)
            ->patch($this->updateUrl($job2, $application), ['status' => 'interview'])
            ->assertNotFound();
    }

    public function test_unauthenticated_request_is_redirected_to_login(): void
    {
        [$employer, $job] = $this->makeEmployerWithJob();
        $application = $this->makeApplication($job);

        $this->patch($this->updateUrl($job, $application), ['status' => 'interview'])
            ->assertRedirect(route('login'));
    }

    public function test_applicant_cannot_update_application_status(): void
    {
        [$employer, $job] = $this->makeEmployerWithJob();
        $application = $this->makeApplication($job);

        $applicant = User::find($application->user_id);

        $this->actingAs($applicant)
            ->patch($this->updateUrl($job, $application), ['status' => 'interview'])
            ->assertRedirect();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('application_status_logs', 0);
    }
}
