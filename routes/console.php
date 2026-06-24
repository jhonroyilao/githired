<?php

use App\Jobs\ExtractResumeText;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('resumes:backfill-documents {--no-dispatch : Create records without queueing extraction jobs}', function () {
    $disk = Storage::disk(config('filesystems.resume_disk', 'local'));
    $created = 0;
    $skipped = 0;
    $missing = 0;

    Profile::query()
        ->with('user')
        ->whereNotNull('resume_path')
        ->orderBy('id')
        ->each(function (Profile $profile) use ($disk, &$created, &$skipped, &$missing): void {
            $user = $profile->user;

            if (! $user || $user->currentResumeDocument) {
                $skipped++;

                return;
            }

            if (! $disk->exists($profile->resume_path)) {
                $missing++;
                $this->warn("Resume file missing for profile #{$profile->id}: {$profile->resume_path}");

                return;
            }

            $bytes = $disk->get($profile->resume_path);
            $hash = hash('sha256', $bytes);

            $resumeDocument = DB::transaction(function () use ($user, $profile, $disk, $hash) {
                $lockedUser = User::query()
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedUser->resumeDocuments()->current()->exists()) {
                    return null;
                }

                return $lockedUser->resumeDocuments()->create([
                    'file_path' => $profile->resume_path,
                    'original_name' => basename($profile->resume_path),
                    'mime_type' => $disk->mimeType($profile->resume_path) ?: 'application/pdf',
                    'file_size' => $disk->size($profile->resume_path),
                    'content_hash' => $hash,
                    'extraction_status' => 'pending',
                    'is_current' => true,
                ]);
            });

            if (! $resumeDocument) {
                $skipped++;

                return;
            }

            $created++;

            if (! $this->option('no-dispatch')) {
                ExtractResumeText::dispatch($resumeDocument);
            }
        });

    $this->info("Backfill complete. Created: {$created}. Skipped: {$skipped}. Missing files: {$missing}.");
})->purpose('Create resume document records for existing profile resume paths');
