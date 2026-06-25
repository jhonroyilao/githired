<?php

namespace App\Actions\Applicant;

use App\Models\User;
use App\Support\StorageUrl;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class UpdateProfileAction
{
    /**
     * @param  array{name: string, headline: string, bio: string, location: string, phone: string, website?: string|null, linkedin?: string|null, github?: string|null, desired_job_type: string, work_preference: string, experience_level: string, skills: array<int, string>, avatar?: UploadedFile|null, remove_avatar?: bool}  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        DB::transaction(function () use ($user, $attributes): void {
            $imageDisk = config('filesystems.image_disk', 'public');

            $user->update([
                'name' => $attributes['name'],
            ]);

            $profile = $user->profile()->firstOrCreate([]);

            $profile->fill([
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

            if (($attributes['remove_avatar'] ?? false) && $profile->avatar_path) {
                Storage::disk($imageDisk)->delete(StorageUrl::imageObjectPath($profile->avatar_path));
                $profile->avatar_path = null;
            }

            if (($attributes['avatar'] ?? null) instanceof UploadedFile) {
                if ($profile->avatar_path) {
                    Storage::disk($imageDisk)->delete(StorageUrl::imageObjectPath($profile->avatar_path));
                }

                $profile->avatar_path = $attributes['avatar']->store('avatars', $imageDisk);
            }

            $profile->save();
        });
    }
}
