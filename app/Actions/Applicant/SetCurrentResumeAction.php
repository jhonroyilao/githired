<?php

namespace App\Actions\Applicant;

use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class SetCurrentResumeAction
{
    public function handle(ResumeDocument $resumeDocument): void
    {
        DB::transaction(function () use ($resumeDocument): void {
            $user = User::query()
                ->whereKey($resumeDocument->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            $user->resumeDocuments()
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $resumeDocument->update(['is_current' => true]);

            $user->profile()->firstOrCreate([])->update([
                'resume_path' => $resumeDocument->file_path,
            ]);
        });
    }
}
