<?php

namespace App\Policies;

use App\Models\ResumeDocument;
use App\Models\User;

class ResumeDocumentPolicy
{
    public function view(User $user, ResumeDocument $resumeDocument)
    {
        return $user->id === $resumeDocument->user_id; //Verify the person trying to view/download is the owner
    }

    public function delete(User $user, ResumeDocument $resumeDocument) 
    {
        return $user->id === $resumeDocument->user_id; //Verify the person trying to delete is the owner
    }

    public function update(User $user, ResumeDocument $resumeDocument) 
    {
        return $user->id === $resumeDocument->user_id; //Verify the person trying to update/set current is the owner
    }
}