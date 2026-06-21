<?php

namespace App\Actions\Onboarding;

use App\Models\User;

final class UpdateApplicantSummaryAction
{
    /**
     * @param  array{headline: string, bio: string, skills: array<int, string>}  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        $user->profile()->firstOrCreate([])->update([
            'headline' => $attributes['headline'],
            'bio' => $attributes['bio'],
            'skills' => $attributes['skills'],
        ]);
    }
}
