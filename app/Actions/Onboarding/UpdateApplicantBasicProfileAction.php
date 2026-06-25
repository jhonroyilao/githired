<?php

namespace App\Actions\Onboarding;

use App\Models\User;
use App\Support\StorageUrl;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class UpdateApplicantBasicProfileAction
{
    /**
     * @param  array{name: string, location: string, phone: string, avatar?: UploadedFile|null, remove_avatar?: bool}  $attributes
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
                'location' => $attributes['location'],
                'phone' => $attributes['phone'],
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
