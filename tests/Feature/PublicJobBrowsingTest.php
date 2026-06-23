<?php

namespace Tests\Feature;

use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicJobBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_browse_only_shows_active_approved_visible_jobs(): void
    {
        $visible = $this->job(['title' => 'Visible Laravel Role']);
        $this->job(['title' => 'Unapproved Active Role', 'approved_at' => null]);
        $this->job(['title' => 'Draft Role', 'status' => JobStatus::Draft->value]);
        $this->job(['title' => 'Pending Role', 'status' => JobStatus::Pending->value]);
        $this->job(['title' => 'Rejected Role', 'status' => JobStatus::Rejected->value]);
        $this->job(['title' => 'Closed Role', 'status' => JobStatus::Closed->value, 'closed_at' => now()]);
        $this->job(['title' => 'Expired Role', 'expires_at' => now()->subDay()]);
        $deleted = $this->job(['title' => 'Soft Deleted Role']);
        $deleted->delete();

        $response = $this->get(route('jobs.index'));

        $response->assertOk();
        $response->assertSee($visible->title);
        $response->assertDontSee('Unapproved Active Role');
        $response->assertDontSee('Draft Role');
        $response->assertDontSee('Pending Role');
        $response->assertDontSee('Rejected Role');
        $response->assertDontSee('Closed Role');
        $response->assertDontSee('Expired Role');
        $response->assertDontSee('Soft Deleted Role');
    }

    public function test_public_browse_filters_by_keyword_location_category_type_and_experience(): void
    {
        $category = JobCategory::query()->create(['name' => 'Engineering', 'slug' => 'engineering']);
        $otherCategory = JobCategory::query()->create(['name' => 'Design', 'slug' => 'design']);

        $match = $this->job([
            'title' => 'Senior Laravel Engineer',
            'location' => 'Manila',
            'category_id' => $category->id,
            'type' => JobType::FullTime->value,
            'experience_level' => ExperienceLevel::Senior->value,
        ]);

        $this->job([
            'title' => 'Senior Laravel Engineer Remote',
            'location' => 'Cebu',
            'category_id' => $category->id,
            'type' => JobType::FullTime->value,
            'experience_level' => ExperienceLevel::Senior->value,
        ]);

        $this->job([
            'title' => 'Senior Laravel Designer',
            'location' => 'Manila',
            'category_id' => $otherCategory->id,
            'type' => JobType::Contract->value,
            'experience_level' => ExperienceLevel::Mid->value,
        ]);

        $response = $this->get(route('jobs.index', [
            'search' => 'Laravel',
            'location' => 'Manila',
            'category' => $category->slug,
            'job_type' => [JobType::FullTime->value],
            'experience_level' => [ExperienceLevel::Senior->value],
        ]));

        $response->assertOk();
        $response->assertSee($match->title);
        $response->assertDontSee('Senior Laravel Engineer Remote');
        $response->assertDontSee('Senior Laravel Designer');
    }

    public function test_public_job_detail_shows_required_fields_to_guest_users(): void
    {
        $job = $this->job([
            'title' => 'Backend Engineer',
            'location' => 'Manila',
            'type' => JobType::Contract->value,
            'experience_level' => ExperienceLevel::Mid->value,
            'description' => 'Build durable Laravel services.',
            'requirements' => 'Ship tested production code.',
            'skills_required' => ['Laravel', 'Postgres'],
        ]);

        $response = $this->get(route('jobs.show', $job));

        $response->assertOk();
        $response->assertSee('Backend Engineer');
        $response->assertSee($job->company->name);
        $response->assertSee('Manila');
        $response->assertSee('Contract');
        $response->assertSee('Mid');
        $response->assertSee('Build durable Laravel services.');
        $response->assertSee('Ship tested production code.');
        $response->assertSee('Laravel');
    }

    public function test_applicant_job_detail_shows_apply_cta(): void
    {
        $job = $this->job(['title' => 'Frontend Engineer']);

        $response = $this->actingAs($this->applicant())->get(route('jobs.show', $job));

        $response->assertOk();
        $response->assertSee(route('applicant.job-listings.apply', $job), false);
    }

    public function test_hidden_job_detail_urls_return_not_found(): void
    {
        $hiddenJobs = [
            $this->job(['title' => 'Unapproved Hidden Job', 'approved_at' => null]),
            $this->job(['title' => 'Pending Hidden Job', 'status' => JobStatus::Pending->value]),
            $this->job(['title' => 'Rejected Hidden Job', 'status' => JobStatus::Rejected->value]),
            $this->job(['title' => 'Closed Hidden Job', 'status' => JobStatus::Closed->value, 'closed_at' => now()]),
            $this->job(['title' => 'Expired Hidden Job', 'expires_at' => now()->subDay()]),
        ];
        $deleted = $this->job(['title' => 'Deleted Hidden Job']);
        $deleted->delete();
        $hiddenJobs[] = $deleted;

        foreach ($hiddenJobs as $hiddenJob) {
            $this->get(route('jobs.show', $hiddenJob))->assertNotFound();
        }
    }

    private function applicant(): User
    {
        return User::factory()->create([
            'role' => UserRole::Applicant->value,
        ]);
    }

    private function job(array $overrides = []): JobListing
    {
        $employer = User::factory()->create(['role' => UserRole::Employer->value]);
        $company = Company::query()->create([
            'user_id' => $employer->id,
            'name' => $overrides['company_name'] ?? 'Acme Labs',
            'slug' => Str::slug(($overrides['company_name'] ?? 'Acme Labs').'-'.Str::random(6)),
        ]);
        $category = JobCategory::query()->firstOrCreate(
            ['slug' => 'software-development'],
            ['name' => 'Software Development'],
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
            'salary_min' => 50000,
            'salary_max' => 90000,
            'status' => JobStatus::Active->value,
            'approved_at' => now()->subDay(),
            'approved_by' => $employer->id,
            'published_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ], $overrides));
    }
}
