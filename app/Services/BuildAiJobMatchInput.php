<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\ResumeDocument;
use App\Models\User;

final class BuildAiJobMatchInput
{
    /**
     * @return array{
     *     input: array<string, mixed>,
     *     profile_hash: string,
     *     resume_hash: string,
     *     job_hash: string
     * }
     */
    public function handle(User $user, JobListing $jobListing, ?ResumeDocument $resumeDocument): array
    {
        $profile = $user->profile;

        $profilePayload = [
            'name' => $user->name,
            'headline' => $profile?->headline,
            'bio' => $profile?->bio,
            'skills' => $profile?->skills ?? [],
            'desired_job_type' => $profile?->desired_job_type,
            'work_preference' => $profile?->work_preference,
            'experience_level' => $profile?->experience_level,
        ];

        $resumePayload = [
            'id' => $resumeDocument?->id,
            'content_hash' => $resumeDocument?->content_hash,
            'extraction_status' => $resumeDocument?->extraction_status,
            'text' => $this->usableResumeText($resumeDocument),
        ];

        $jobPayload = [
            'id' => $jobListing->id,
            'title' => $jobListing->title,
            'description' => $jobListing->description,
            'requirements' => $jobListing->requirements,
            'skills_required' => $jobListing->skills_required ?? [],
            'type' => $jobListing->type,
            'experience_level' => $jobListing->experience_level,
            'location_type' => $jobListing->location_type,
        ];

        return [
            'input' => [
                'profile' => $profilePayload,
                'resume' => $resumePayload,
                'job' => $jobPayload,
            ],
            'profile_hash' => $this->hash($profilePayload),
            'resume_hash' => $this->hash($resumePayload),
            'job_hash' => $this->hash($jobPayload),
        ];
    }

    private function usableResumeText(?ResumeDocument $resumeDocument): ?string
    {
        if ($resumeDocument?->extraction_status !== 'ready') {
            return null;
        }

        $text = trim((string) $resumeDocument->extracted_text);

        return $text === '' ? null : $text;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function hash(array $payload): string
    {
        return hash('sha256', (string) json_encode($payload, JSON_THROW_ON_ERROR));
    }
}
