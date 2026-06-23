<?php

namespace App\Policies;

use App\Models\ResumeDocument;
use App\Models\User;

final class ResumeDocumentPolicy
{
    public function view(User $user, ResumeDocument $resumeDocument): bool
    {
        return $user->id === $resumeDocument->user_id;
    }

    public function update(User $user, ResumeDocument $resumeDocument): bool
    {
        return $user->id === $resumeDocument->user_id;
    }

    public function delete(User $user, ResumeDocument $resumeDocument): bool
    {
        return $user->id === $resumeDocument->user_id;
    }
}
