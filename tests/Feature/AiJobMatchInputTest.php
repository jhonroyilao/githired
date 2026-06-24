<?php

namespace Tests\Feature;

use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\ResumeDocument;
use App\Models\User;
use App\Services\BuildAiJobMatchInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class AiJobMatchInputTest extends TestCase
{
    use RefreshDatabase;

    public function test_ready_resume_text_contributes_to_matching_input(): void
    {
        $user = $this->applicant();
        $resume = $this->resumeFor($user, [
            'extraction_status' => 'ready',
            'extracted_text' => 'Laravel queue worker and PostgreSQL experience.',
        ]);

        $input = app(BuildAiJobMatchInput::class)->handle($user, $this->job(), $resume);

        $this->assertSame(
            'Laravel queue worker and PostgreSQL experience.',
            $input['input']['resume']['text'],
        );
        $this->assertSame('ready', $input['input']['resume']['extraction_status']);
        $this->assertNotEmpty($input['resume_hash']);
    }

    public function test_failed_resume_uses_profile_only_matching_input(): void
    {
        $user = $this->applicant();
        $resume = $this->resumeFor($user, [
            'extraction_status' => 'failed',
            'extracted_text' => null,
            'extraction_error' => 'Parser crashed.',
        ]);

        $input = app(BuildAiJobMatchInput::class)->handle($user, $this->job(), $resume);

        $this->assertNull($input['input']['resume']['text']);
        $this->assertSame(['Laravel', 'PostgreSQL'], $input['input']['profile']['skills']);
        $this->assertSame('failed', $input['input']['resume']['extraction_status']);
        $this->assertNotEmpty($input['resume_hash']);
    }

    private function applicant(): User
    {
        $user = User::factory()->create([
            'name' => 'Ada Applicant',
            'role' => UserRole::Applicant->value,
        ]);

        $user->profile()->create([
            'headline' => 'Backend Developer',
            'bio' => 'Builds Laravel products.',
            'skills' => ['Laravel', 'PostgreSQL'],
            'desired_job_type' => JobType::FullTime->value,
            'work_preference' => 'remote',
            'experience_level' => ExperienceLevel::Entry->value,
        ]);

        return $user;
    }

    private function resumeFor(User $user, array $overrides = []): ResumeDocument
    {
        return $user->resumeDocuments()->create(array_merge([
            'file_path' => "resumes/{$user->id}/resume.pdf",
            'original_name' => 'resume.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 204800,
            'content_hash' => str_repeat('a', 64),
            'extraction_status' => 'pending',
            'is_current' => true,
        ], $overrides));
    }

    private function job(): JobListing
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

        return JobListing::query()->create([
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
        ]);
    }
}
