<?php

namespace Tests\Feature;

use App\Actions\Applicant\PrepareAiJobMatchAction;
use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Jobs\ExtractResumeText;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\ResumeDocument;
use App\Models\User;
use App\Services\BuildAiJobMatchInput;
use App\Services\ResumeTextExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ResumeTextExtractionTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

    private function makeResume(array $overrides = []): ResumeDocument
    {
        $user = User::factory()->create(['role' => 'applicant']);

        return ResumeDocument::create(array_merge([
            'user_id' => $user->id,
            'file_path' => 'resumes/test-resume.pdf',
            'original_name' => 'test-resume.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'extraction_status' => 'pending',
            'is_current' => true,
        ], $overrides));
    }

    private function mockExtractorWith(string $text, string $hash): ResumeTextExtractor
    {
        $extractor = Mockery::mock(ResumeTextExtractor::class);
        $extractor->expects('extract')->andReturn([
            'text' => $text,
            'hash' => $hash,
        ]);

        return $extractor;
    }

    private function mockExtractorFailing(string $message): ResumeTextExtractor
    {
        $extractor = Mockery::mock(ResumeTextExtractor::class);
        $extractor->expects('extract')
            ->andThrow(new \DomainException($message));

        return $extractor;
    }

    private function runExtraction(ResumeDocument $resume, ResumeTextExtractor $extractor): void
    {
        (new ExtractResumeText($resume))->handle($extractor, app(PrepareAiJobMatchAction::class));
    }

    // ── Success path ──────────────────────────────────────────────────────

    public function test_successful_extraction_marks_resume_ready(): void
    {
        $resume = $this->makeResume();
        $extractor = $this->mockExtractorWith('Software engineer with 5 years PHP experience.', hash('sha256', 'bytes'));

        $this->runExtraction($resume, $extractor);

        $resume->refresh();
        $this->assertEquals('ready', $resume->extraction_status);
    }

    public function test_successful_extraction_stores_extracted_text(): void
    {
        $resume = $this->makeResume();
        $extractor = $this->mockExtractorWith('Proficient in Laravel and Vue.js.', hash('sha256', 'bytes'));

        $this->runExtraction($resume, $extractor);

        $this->assertEquals('Proficient in Laravel and Vue.js.', $resume->fresh()->extracted_text);
    }

    public function test_successful_extraction_stores_content_hash(): void
    {
        $resume = $this->makeResume();
        $expectedHash = hash('sha256', 'raw-file-bytes');
        $extractor = $this->mockExtractorWith('Some text.', $expectedHash);

        $this->runExtraction($resume, $extractor);

        $this->assertEquals($expectedHash, $resume->fresh()->content_hash);
    }

    public function test_successful_extraction_clears_any_previous_error(): void
    {
        $resume = $this->makeResume([
            'extraction_status' => 'failed',
            'extraction_error' => 'Old error message.',
        ]);
        $extractor = $this->mockExtractorWith('Good text this time.', hash('sha256', 'bytes'));

        $this->runExtraction($resume, $extractor);

        $resume->refresh();
        $this->assertEquals('ready', $resume->extraction_status);
        $this->assertNull($resume->extraction_error);
    }

    // ── Failure path ──────────────────────────────────────────────────────

    public function test_failed_extraction_marks_resume_failed(): void
    {
        $resume = $this->makeResume();
        $extractor = $this->mockExtractorFailing('PDF parser error: corrupt file');

        $this->runExtraction($resume, $extractor);

        $this->assertEquals('failed', $resume->fresh()->extraction_status);
    }

    public function test_failed_extraction_stores_error_message(): void
    {
        $resume = $this->makeResume();
        $extractor = $this->mockExtractorFailing('No extractable text found.');

        $this->runExtraction($resume, $extractor);

        $resume->refresh();
        $this->assertNotNull($resume->extraction_error);
        $this->assertStringContainsString('No extractable text found.', $resume->extraction_error);
    }

    public function test_failed_extraction_does_not_overwrite_previously_extracted_text(): void
    {
        // If we ever re-run extraction on a resume that already had text, a
        // failure on re-run should not wipe the previously stored text.
        $resume = $this->makeResume([
            'extracted_text' => 'Previously extracted content.',
            'extraction_status' => 'ready',
        ]);
        $extractor = $this->mockExtractorFailing('File not found on disk.');

        $this->runExtraction($resume, $extractor);

        $resume->refresh();
        $this->assertEquals('failed', $resume->extraction_status);
        $this->assertEquals('Previously extracted content.', $resume->extracted_text);
    }

    // ── Retry / transient failure behavior ───────────────────────────────

    public function test_unexpected_exception_propagates_to_allow_queue_retry(): void
    {
        // Non-RuntimeException (e.g. DB blip) should propagate so the queue can retry.
        // The document should NOT be marked failed prematurely.
        $resume = $this->makeResume();
        $extractor = Mockery::mock(ResumeTextExtractor::class);
        $extractor->expects('extract')
            ->andThrow(new \Exception('Unexpected: connection timeout'));

        try {
            $this->runExtraction($resume, $extractor);
            $this->fail('Expected exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertStringContainsString('connection timeout', $e->getMessage());
        }

        // Status must remain 'pending' so a retry can succeed.
        $this->assertEquals('pending', $resume->fresh()->extraction_status);
    }

    // ── Profile-only matching fallback ────────────────────────────────────

    public function test_resume_with_failed_extraction_remains_usable(): void
    {
        // Acceptance criterion: the system can continue with profile-only matching when extraction fails.
        // A 'failed' ResumeDocument is still a valid record that can be attached to applications — nothing blocks on extraction_status.

        $resume = $this->makeResume([
            'extraction_status' => 'failed',
            'extracted_text' => null,
            'content_hash' => null,
        ]);

        $this->assertNotNull($resume->id);
        $this->assertEquals('failed', $resume->extraction_status);
        $this->assertNull($resume->extracted_text);

        // The record is intact; AI matching can skip to profile-only when text is null.
        $this->assertDatabaseHas('resume_documents', [
            'id' => $resume->id,
            'extraction_status' => 'failed',
        ]);
    }

    public function test_successful_extraction_prepares_application_match_with_ready_resume_text(): void
    {
        $user = $this->applicant();
        $job = $this->job();
        $resume = $this->makeResume([
            'user_id' => $user->id,
            'extraction_status' => 'pending',
        ]);
        $user->applications()->create([
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
            'resume_path' => $resume->file_path,
            'status' => 'pending',
        ]);
        $extractor = $this->mockExtractorWith('Laravel queue worker and PostgreSQL experience.', hash('sha256', 'ready-bytes'));

        $this->runExtraction($resume, $extractor);

        $resumeHash = app(BuildAiJobMatchInput::class)
            ->handle($user->fresh(['profile']), $job, $resume->fresh())['resume_hash'];

        $this->assertDatabaseHas('ai_job_matches', [
            'user_id' => $user->id,
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
            'resume_hash' => $resumeHash,
            'generation_status' => 'pending',
        ]);
    }

    public function test_failed_extraction_prepares_application_match_with_profile_only_fallback(): void
    {
        $user = $this->applicant();
        $job = $this->job();
        $resume = $this->makeResume([
            'user_id' => $user->id,
            'extraction_status' => 'pending',
        ]);
        $user->applications()->create([
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
            'resume_path' => $resume->file_path,
            'status' => 'pending',
        ]);
        $extractor = $this->mockExtractorFailing('No extractable text found.');

        $this->runExtraction($resume, $extractor);

        $resumeHash = app(BuildAiJobMatchInput::class)
            ->handle($user->fresh(['profile']), $job, $resume->fresh())['resume_hash'];

        $this->assertDatabaseHas('ai_job_matches', [
            'user_id' => $user->id,
            'job_listing_id' => $job->id,
            'resume_document_id' => $resume->id,
            'resume_hash' => $resumeHash,
            'generation_status' => 'pending',
        ]);
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
