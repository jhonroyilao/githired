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

class AdminJobManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_deleted_jobs_and_restore_them(): void
    {
        $admin = $this->admin();
        [$employer, $company] = $this->employerWithCompany();
        $job = $this->job($employer, $company, [
            'title' => 'Deleted Laravel Role',
            'status' => JobStatus::Active->value,
            'deleted_by' => $admin->id,
            'delete_reason' => 'Duplicate listing.',
        ]);
        $job->delete();

        $this->actingAs($admin)
            ->get(route('admin.jobs.all', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee('Deleted Laravel Role')
            ->assertSee('Restore');

        $this->actingAs($admin)
            ->post(route('admin.jobs.restore', $job->id))
            ->assertRedirect();

        $job->refresh();

        $this->assertFalse($job->trashed());
        $this->assertNull($job->deleted_by);
        $this->assertNull($job->delete_reason);
    }

    public function test_admin_soft_delete_requires_reason(): void
    {
        $admin = $this->admin();
        [$employer, $company] = $this->employerWithCompany();
        $job = $this->job($employer, $company);

        $this->actingAs($admin)
            ->delete(route('admin.jobs.destroy', $job), ['delete_reason' => ''])
            ->assertSessionHasErrors('delete_reason');

        $this->assertFalse($job->fresh()->trashed());
    }

    public function test_admin_can_soft_delete_pending_jobs_from_all_listings(): void
    {
        $admin = $this->admin();
        [$employer, $company] = $this->employerWithCompany();
        $job = $this->job($employer, $company, [
            'title' => 'Pending Role',
            'status' => JobStatus::Pending->value,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.jobs.all', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('Pending Role')
            ->assertSee('Soft Delete');

        $this->actingAs($admin)
            ->delete(route('admin.jobs.destroy', $job), ['delete_reason' => 'Spam listing.'])
            ->assertRedirect();

        $job->refresh();

        $this->assertTrue($job->trashed());
        $this->assertSame($admin->id, $job->deleted_by);
        $this->assertSame('Spam listing.', $job->delete_reason);
    }

    public function test_hide_only_updates_active_jobs(): void
    {
        $admin = $this->admin();
        [$employer, $company] = $this->employerWithCompany();
        $active = $this->job($employer, $company, [
            'status' => JobStatus::Active->value,
            'approved_at' => now()->subDay(),
            'approved_by' => $admin->id,
            'published_at' => now()->subDay(),
        ]);
        $pending = $this->job($employer, $company, [
            'status' => JobStatus::Pending->value,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.jobs.hide', $active))
            ->assertRedirect();

        $active->refresh();

        $this->assertSame(JobStatus::Closed->value, $active->status);
        $this->assertNotNull($active->closed_at);

        $this->actingAs($admin)
            ->post(route('admin.jobs.hide', $pending))
            ->assertStatus(409);

        $this->assertSame(JobStatus::Pending->value, $pending->fresh()->status);
    }

    public function test_reapprove_only_updates_rejected_jobs(): void
    {
        $admin = $this->admin();
        [$employer, $company] = $this->employerWithCompany();
        $rejected = $this->job($employer, $company, [
            'status' => JobStatus::Rejected->value,
            'rejected_at' => now()->subDay(),
            'rejected_by' => $admin->id,
            'rejection_reason' => 'Incomplete details.',
        ]);
        $pending = $this->job($employer, $company, [
            'status' => JobStatus::Pending->value,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.jobs.reapprove', $rejected))
            ->assertRedirect();

        $rejected->refresh();

        $this->assertSame(JobStatus::Active->value, $rejected->status);
        $this->assertNull($rejected->rejection_reason);
        $this->assertNotNull($rejected->approved_at);

        $this->actingAs($admin)
            ->post(route('admin.jobs.reapprove', $pending))
            ->assertStatus(409);

        $this->assertSame(JobStatus::Pending->value, $pending->fresh()->status);
    }

    public function test_reactivate_only_updates_hidden_jobs(): void
    {
        $admin = $this->admin();
        [$employer, $company] = $this->employerWithCompany();
        $hidden = $this->job($employer, $company, [
            'status' => JobStatus::Closed->value,
            'closed_at' => now()->subDay(),
        ]);
        $pending = $this->job($employer, $company, [
            'status' => JobStatus::Pending->value,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.jobs.reactivate', $hidden))
            ->assertRedirect();

        $hidden->refresh();

        $this->assertSame(JobStatus::Active->value, $hidden->status);
        $this->assertNull($hidden->closed_at);
        $this->assertNotNull($hidden->approved_at);

        $this->actingAs($admin)
            ->post(route('admin.jobs.reactivate', $pending))
            ->assertStatus(409);

        $this->assertSame(JobStatus::Pending->value, $pending->fresh()->status);
    }

    public function test_status_filter_survives_search_and_sort(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.jobs.all', [
                'status' => 'deleted',
                'search' => 'Laravel',
                'sort' => 'oldest',
            ]))
            ->assertOk()
            ->assertSee('name="status" value="deleted"', false);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin->value,
        ]);
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
