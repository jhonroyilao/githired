<?php

namespace App\Actions\Applicant;

use App\Models\ResumeDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class DeleteResumeAction
{
    public function handle(ResumeDocument $resumeDocument): void
    {
        $path = $resumeDocument->file_path;
        $user = $resumeDocument->user;

        DB::transaction(function () use ($resumeDocument, $path, $user): void {
            $user->profile()
                ->where('resume_path', $path)
                ->update(['resume_path' => null]);

            $resumeDocument->delete();
        });

        $disk = Storage::disk(config('filesystems.resume_disk', 'local'));

        if ($disk->exists($path) && ! $disk->delete($path)) {
            Log::warning("Resume database row was deleted but file [{$path}] could not be removed.");
        }
    }
}
