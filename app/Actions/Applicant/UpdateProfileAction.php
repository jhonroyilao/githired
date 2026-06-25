<?php

namespace App\Actions\Applicant;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateProfileAction
{
    /**
     * @param  array{name: string, headline: string, bio: string, location: string, phone: string, website?: string|null, linkedin?: string|null, github?: string|null, desired_job_type: string, work_preference: string, experience_level: string, skills: array<int, string>}  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        DB::transaction(function () use ($user, $attributes): void {
            $user->update([
                'name' => $attributes['name'],
            ]);

            $user->profile()->firstOrCreate([])->update([
                'headline' => $attributes['headline'],
                'bio' => $attributes['bio'],
                'location' => $attributes['location'],
                'phone' => $attributes['phone'],
                'website' => $attributes['website'] ?? null,
                'linkedin' => $attributes['linkedin'] ?? null,
                'github' => $attributes['github'] ?? null,
                'desired_job_type' => $attributes['desired_job_type'],
                'work_preference' => $attributes['work_preference'],
                'experience_level' => $attributes['experience_level'],
                'skills' => $attributes['skills'],
            ]);
        });
    }
}
