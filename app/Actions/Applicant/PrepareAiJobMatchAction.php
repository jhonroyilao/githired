<?php

namespace App\Actions\Applicant;

use App\Models\AiJobMatch;
use App\Models\JobListing;
use App\Models\ResumeDocument;
use App\Models\User;
use App\Services\BuildAiJobMatchInput;

final class PrepareAiJobMatchAction
{
    public function __construct(
        private readonly BuildAiJobMatchInput $buildInput,
    ) {}

    public function handle(User $user, JobListing $jobListing, ?ResumeDocument $resumeDocument): AiJobMatch
    {
        $matchInput = $this->buildInput->handle($user, $jobListing, $resumeDocument);

        return AiJobMatch::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'job_listing_id' => $jobListing->id,
                'profile_hash' => $matchInput['profile_hash'],
                'resume_hash' => $matchInput['resume_hash'],
                'job_hash' => $matchInput['job_hash'],
            ],
            [
                'resume_document_id' => $resumeDocument?->id,
                'match_score' => null,
                'score_breakdown' => [],
                'matching_skills' => [],
                'missing_skills' => [],
                'explanation' => null,
                'suggested_action' => null,
                'provider' => null,
                'model' => null,
                'generation_status' => 'pending',
                'error_message' => null,
                'generated_at' => null,
            ],
        );
    }
}
