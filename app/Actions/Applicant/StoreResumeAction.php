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
        $disk = Storage::disk('local');
        $path = $resume->store("resumes/{$user->id}", 'local');

        try {
            return DB::transaction(function () use ($user, $resume, $path): ResumeDocument {
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
                    'content_hash' => hash_file('sha256', $resume->getRealPath()),
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
