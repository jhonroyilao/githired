<?php

namespace App\Actions\Applicant;

use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class StoreResumeAction
{
    public function handle(User $user, UploadedFile $resume): ResumeDocument
    {
        //Compute sha256 hash immediately from the uploaded file
        $newHash = hash_file('sha256', $resume->getRealPath());

        //Fetch the user's currently active resume
        $currentResume = $user->resumeDocuments()->where('is_current', true)->first();

        //Compare hashes. If identical, return the existing record and stop.
        if ($currentResume && $currentResume->content_hash === $newHash) {
            return $currentResume;
        }

        //If hash is different (or no current resume exists). Proceed with storing.
        $diskName = config('filesystems.resume_disk', 'local');
        $disk = Storage::disk($diskName);
        $path = $resume->store("resumes/{$user->id}", $diskName);

        try {
            return DB::transaction(function () use ($user, $resume, $path, $newHash): ResumeDocument {
                $lockedUser = User::query()
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $lockedUser->resumeDocuments()
                    ->where('is_current', true)
                    ->update(['is_current' => false]);

                $resumeDocument = $lockedUser->resumeDocuments()->create([
                    'file_path' => $path,
                    'original_name' => $resume->getClientOriginalName(),
                    'mime_type' => $resume->getMimeType() ?: 'application/pdf',
                    'file_size' => $resume->getSize(),
                    'content_hash' => $newHash, // Store the new hash
                    'extraction_status' => 'pending',
                    'is_current' => true,
                ]);

                $lockedUser->profile()->firstOrCreate([])->update([
                    'resume_path' => $path,
                ]);

                return $resumeDocument;
            });
        } catch (\Throwable $exception) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }

            throw $exception;
        }
    }
}