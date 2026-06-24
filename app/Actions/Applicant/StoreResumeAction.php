<?php

namespace App\Actions\Applicant;

use App\Jobs\ExtractResumeText;
use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class StoreResumeAction
{
    public function handle(User $user, UploadedFile $resume): ResumeDocument
    {
        $newHash = hash_file('sha256', $resume->getRealPath());
        $diskName = config('filesystems.resume_disk', 'local');
        $disk = Storage::disk($diskName);
        $path = $resume->store("resumes/{$user->id}", $diskName);

        try {
            $resumeDocument = DB::transaction(function () use ($user, $resume, $path, $newHash): ResumeDocument {
                $lockedUser = User::query()
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $currentResume = $lockedUser->resumeDocuments()
                    ->current()
                    ->first();

                if ($currentResume?->content_hash === $newHash) {
                    $lockedUser->profile()->firstOrCreate([])->update([
                        'resume_path' => $currentResume->file_path,
                    ]);

                    return $currentResume;
                }

                $lockedUser->resumeDocuments()
                    ->where('is_current', true)
                    ->update(['is_current' => false]);

                $resumeDocument = $lockedUser->resumeDocuments()->create([
                    'file_path' => $path,
                    'original_name' => $resume->getClientOriginalName(),
                    'mime_type' => $resume->getMimeType() ?: 'application/pdf',
                    'file_size' => $resume->getSize(),
                    'content_hash' => $newHash,
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

        if (! $resumeDocument->wasRecentlyCreated) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }

            return $resumeDocument;
        }

        ExtractResumeText::dispatch($resumeDocument);

        return $resumeDocument;
    }
}
