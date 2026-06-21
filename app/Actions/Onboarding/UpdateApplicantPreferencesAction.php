<?php

namespace App\Actions\Onboarding;

use App\Models\User;

final class UpdateApplicantPreferencesAction
{
    /**
     * @param  array{desired_job_type: string, work_preference: string, experience_level: string}  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        $user->profile()->firstOrCreate([])->update([
            'desired_job_type' => $attributes['desired_job_type'],
            'work_preference' => $attributes['work_preference'],
            'experience_level' => $attributes['experience_level'],
        ]);
    }
}
