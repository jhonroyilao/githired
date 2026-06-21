<?php

namespace App\Actions\Onboarding;

use App\Models\Profile;
use App\Models\User;

final class DetermineApplicantOnboardingStepAction
{
    public function handle(User $user): ?string
    {
        $profile = $user->profile;

        if (! $profile instanceof Profile || $this->missingAny($user->name, $profile->location, $profile->phone)) {
            return 'applicant.onboarding.profile';
        }

        if ($this->missingAny($profile->headline, $profile->bio) || empty($profile->skills)) {
            return 'applicant.onboarding.summary';
        }

        if ($this->missingAny($profile->desired_job_type, $profile->work_preference, $profile->experience_level)) {
            return 'applicant.onboarding.preferences';
        }

        if ($this->missingAll($profile->github, $profile->linkedin, $profile->website)) {
            return 'applicant.onboarding.links';
        }

        return null;
    }

    private function missingAny(?string ...$values): bool
    {
        foreach ($values as $value) {
            if ($value === null || trim($value) === '') {
                return true;
            }
        }

        return false;
    }

    private function missingAll(?string ...$values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && trim($value) !== '') {
                return false;
            }
        }

        return true;
    }
}
