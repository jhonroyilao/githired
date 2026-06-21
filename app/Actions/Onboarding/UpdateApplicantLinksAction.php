<?php

namespace App\Actions\Onboarding;

use App\Models\User;

final class UpdateApplicantLinksAction
{
    /**
     * @param  array{github?: string|null, linkedin?: string|null, website?: string|null}  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        $user->profile()->firstOrCreate([])->update([
            'github' => $attributes['github'] ?? null,
            'linkedin' => $attributes['linkedin'] ?? null,
            'website' => $attributes['website'] ?? null,
        ]);
    }
}
