<?php

namespace Tests\Feature;

use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmployerJobListingCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_create_job_listing_that_defaults_to_pending(): void
    {
        [$employer, $company] = $this->employerWithCompany();
        $category = $this->category();

        $response = $this->actingAs($employer)->post(route('employer.jobs.store'), $this->validPayload([
            'category_id' => $category->id,
            'title' => 'Senior Laravel Engineer',
            'skills_required' => 'Laravel, PostgreSQL,, Testing, ',
        ]));

        $response->assertRedirect(route('employer.dashboard', absolute: false));
        $response->assertSessionHas('success', 'Job listing submitted for review.');

        $this->assertDatabaseHas('job_listings', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'category_id' => $category->id,
            'title' => 'Senior Laravel Engineer',
            'status' => JobStatus::Pending->value,
            'published_at' => null,
        ]);

        $job = JobListing::query()->where('title', 'Senior Laravel Engineer')->firstOrFail();

        $this->assertNotNull($job->submitted_at);
        $this->assertSame(['Laravel', 'PostgreSQL', 'Testing'], $job->skills_required);
        $this->assertFalse($job->isPubliclyVisible());

        $this->get(route('jobs.index'))
            ->assertOk()
            ->assertDontSee('Senior Laravel Engineer');

        $this->get(route('jobs.show', $job))
            ->assertNotFound();
    }

    public function test_employer_without_company_is_redirected_from_create_and_index(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $this->actingAs($employer)
            ->get(route('employer.jobs.create'))
            ->assertRedirect(route('employer.onboarding.company', absolute: false));

        $this->actingAs($employer)
            ->get(route('employer.jobs.index'))
            ->assertRedirect(route('employer.onboarding.company', absolute: false));
    }

    public function test_invalid_salary_currency_is_rejected_before_database_write(): void
    {
        [$employer] = $this->employerWithCompany();
        $payload = $this->validPayload([
            'salary_currency' => 'USDT',
        ]);

        $this->actingAs($employer)
            ->from(route('employer.jobs.create'))
            ->post(route('employer.jobs.store'), $payload)
            ->assertSessionHasErrors('salary_currency');

        $this->actingAs($employer)
            ->from(route('employer.jobs.create'))
            ->post(route('employer.jobs.store'), $this->validPayload([
                'salary_currency' => '@@@',
            ]))
            ->assertSessionHasErrors('salary_currency');
    }

    public function test_whitespace_only_required_fields_are_rejected(): void
    {
        [$employer] = $this->employerWithCompany();

        $this->actingAs($employer)
            ->from(route('employer.jobs.create'))
            ->post(route('employer.jobs.store'), $this->validPayload([
                'title' => '   ',
                'description' => " \n ",
            ]))
            ->assertSessionHasErrors(['title', 'description']);
    }

    public function test_employer_can_list_and_view_only_their_own_job_listings(): void
    {
        [$employer, $company] = $this->employerWithCompany();
        [$otherEmployer, $otherCompany] = $this->employerWithCompany('Other Company');

        $ownJob = $this->job($employer, $company, ['title' => 'Owned Role']);
        $otherJob = $this->job($otherEmployer, $otherCompany, ['title' => 'Other Role']);

        $this->actingAs($employer)
            ->get(route('employer.jobs.index'))
            ->assertOk()
            ->assertSee('Owned Role')
            ->assertDontSee('Other Role');

        $this->actingAs($employer)
            ->get(route('employer.jobs.show', $ownJob))
            ->assertOk()
            ->assertSee('Owned Role');

        $this->actingAs($employer)
            ->get(route('employer.jobs.show', $otherJob))
            ->assertForbidden();
    }

    public function test_employer_can_edit_owned_job_and_resubmit_it_for_moderation(): void
    {
        [$employer, $company] = $this->employerWithCompany();
        $job = $this->job($employer, $company, [
            'title' => 'Published Role',
            'status' => JobStatus::Active->value,
            'approved_at' => now()->subDay(),
            'approved_by' => $employer->id,
            'published_at' => now()->subDay(),
            'salary_currency' => 'USD',
        ]);

        $this->actingAs($employer)
            ->get(route('employer.jobs.edit', $job))
            ->assertOk()
            ->assertSee('value="USD"', false);

        $response = $this->actingAs($employer)->put(
            route('employer.jobs.update', $job),
            $this->validPayload([
                'category_id' => $job->category_id,
                'title' => 'Updated Role',
                'location' => 'Cebu',
                'skills_required' => 'Laravel, Queues',
                'salary_currency' => 'USD',
            ]),
        );

        $response->assertRedirect(route('employer.dashboard', absolute: false));
        $response->assertSessionHas('success', 'Job listing updated and submitted for review.');

        $job->refresh();

        $this->assertSame('Updated Role', $job->title);
        $this->assertSame('Cebu', $job->location);
        $this->assertSame(['Laravel', 'Queues'], $job->skills_required);
        $this->assertSame('USD', $job->salary_currency);
        $this->assertSame(JobStatus::Pending->value, $job->status);
        $this->assertNull($job->approved_at);
        $this->assertNull($job->approved_by);
        $this->assertNull($job->published_at);
        $this->assertFalse($job->isPubliclyVisible());
    }

    public function test_editing_job_without_salary_keeps_null_values(): void
    {
        [$employer, $company] = $this->employerWithCompany();
        $job = $this->job($employer, $company, [
            'title' => 'Negotiable Role',
            'salary_min' => null,
            'salary_max' => null,
        ]);

        $this->actingAs($employer)
            ->put(route('employer.jobs.update', $job), $this->validPayload([
                'category_id' => $job->category_id,
                'title' => 'Negotiable Role Updated',
                'salary_min' => null,
                'salary_max' => null,
            ]))
            ->assertRedirect(route('employer.dashboard', absolute: false));

        $job->refresh();

        $this->assertNull($job->salary_min);
        $this->assertNull($job->salary_max);
    }

    public function test_editing_job_accepts_decimal_salary_values_from_form(): void
    {
        [$employer, $company] = $this->employerWithCompany();
        $job = $this->job($employer, $company);

        $this->actingAs($employer)
            ->put(route('employer.jobs.update', $job), $this->validPayload([
                'category_id' => $job->category_id,
                'salary_min' => '50000.00',
                'salary_max' => '90000.00',
            ]))
            ->assertRedirect(route('employer.dashboard', absolute: false));

        $job->refresh();

        $this->assertSame('50000.00', $job->salary_min);
        $this->assertSame('90000.00', $job->salary_max);
    }

    public function test_zero_salary_values_are_not_displayed_as_missing(): void
    {
        [$employer, $company] = $this->employerWithCompany();

        $job = $this->job($employer, $company, [
            'salary_min' => 0,
            'salary_max' => 1000,
        ]);

        $this->assertSame('PHP 0 - 1,000', $job->salaryRange());
    }

    public function test_employer_cannot_edit_another_companys_job_listing(): void
    {
        [$employer] = $this->employerWithCompany();
        [$otherEmployer, $otherCompany] = $this->employerWithCompany('Other Company');
        $otherJob = $this->job($otherEmployer, $otherCompany, ['title' => 'Other Role']);

        $this->actingAs($employer)
            ->get(route('employer.jobs.edit', $otherJob))
            ->assertForbidden();

        $this->actingAs($employer)
            ->put(route('employer.jobs.update', $otherJob), $this->validPayload([
                'category_id' => $otherJob->category_id,
                'title' => 'Hijacked Role',
            ]))
            ->assertForbidden();

        $this->assertSame('Other Role', $otherJob->fresh()->title);
    }

    public function test_employer_cannot_validate_update_for_another_companys_job_listing(): void
    {
        [$employer] = $this->employerWithCompany();
        [$otherEmployer, $otherCompany] = $this->employerWithCompany('Other Company');
        $otherJob = $this->job($otherEmployer, $otherCompany, ['title' => 'Other Role']);

        $this->actingAs($employer)
            ->put(route('employer.jobs.update', $otherJob), [
                'title' => '',
            ])
            ->assertForbidden();

        $this->assertSame('Other Role', $otherJob->fresh()->title);
    }

    public function test_closed_job_listings_cannot_be_edited(): void
    {
        [$employer, $company] = $this->employerWithCompany();
        $job = $this->job($employer, $company, [
            'status' => JobStatus::Closed->value,
            'closed_at' => now(),
        ]);

        $this->actingAs($employer)
            ->get(route('employer.jobs.edit', $job))
            ->assertForbidden();

        $this->actingAs($employer)
            ->put(route('employer.jobs.update', $job), $this->validPayload([
                'category_id' => $job->category_id,
                'title' => 'Updated Closed Role',
            ]))
            ->assertForbidden();
    }

    public function test_employer_can_download_application_resume_after_applicant_removes_it_from_profile(): void
    {
        Storage::fake('local');

        [$employer, $company] = $this->employerWithCompany();
        $applicant = User::factory()->create(['role' => UserRole::Applicant->value]);
        $job = $this->job($employer, $company);
        $resume = ResumeDocument::query()->create([
            'user_id' => $applicant->id,
            'file_path' => "resumes/{$applicant->id}/submitted.pdf",
            'original_name' => 'submitted.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 256,
            'content_hash' => hash('sha256', 'submitted resume'),
            'extraction_status' => 'ready',
            'is_current' => true,
        ]);
        Storage::disk('local')->put($resume->file_path, 'submitted resume');
        $applicant->profile()->firstOrCreate([])->update(['resume_path' => $resume->file_path]);

        Application::query()->create([
            'user_id' => $applicant->id,
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
            'resume_path' => $resume->file_path,
            'status' => 'pending',
        ]);

        $this->actingAs($applicant)
            ->delete(route('applicant.resume.destroy', $resume))
            ->assertRedirect();

        $this->assertDatabaseMissing('resume_documents', ['id' => $resume->id]);
        Storage::disk('local')->assertExists($resume->file_path);

        $this->actingAs($employer)
            ->get(route('employer.jobs.applicants.resume', [$job, Application::sole()]))
            ->assertOk();
    }

    /**
     * @return array{0: User, 1: Company}
     */
    private function employerWithCompany(string $companyName = 'Acme Careers'): array
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer->value,
        ]);

        $company = Company::query()->create([
            'user_id' => $user->id,
            'name' => $companyName,
            'slug' => Str::slug($companyName).'-'.Str::random(6),
            'industry' => 'Software',
            'size' => '11-50 employees',
            'location' => 'Manila, Philippines',
            'description' => 'We build hiring tools.',
        ]);

        return [$user, $company];
    }

    private function category(): JobCategory
    {
        return JobCategory::query()->firstOrCreate(
            ['slug' => 'software-development'],
            ['name' => 'Software Development'],
        );
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'category_id' => $this->category()->id,
            'title' => 'Laravel Engineer',
            'location' => 'Manila',
            'location_type' => 'remote',
            'type' => JobType::FullTime->value,
            'experience_level' => ExperienceLevel::Mid->value,
            'description' => 'Build durable Laravel services.',
            'requirements' => 'Ship tested production code.',
            'skills_required' => 'Laravel, PHP',
            'salary_min' => 50000,
            'salary_max' => 90000,
            'salary_currency' => 'PHP',
            'expires_at' => now()->addMonth()->toDateString(),
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function job(User $employer, Company $company, array $overrides = []): JobListing
    {
        return JobListing::query()->create(array_merge([
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'category_id' => $this->category()->id,
            'title' => 'Laravel Engineer',
            'slug' => 'laravel-engineer-'.Str::random(8),
            'location' => 'Manila',
            'location_type' => 'remote',
            'type' => JobType::FullTime->value,
            'experience_level' => ExperienceLevel::Mid->value,
            'description' => 'Build product features.',
            'requirements' => 'Experience with Laravel.',
            'skills_required' => ['Laravel'],
            'salary_min' => 50000,
            'salary_max' => 90000,
            'salary_currency' => 'PHP',
            'status' => JobStatus::Pending->value,
            'submitted_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ], $overrides));
    }
}
