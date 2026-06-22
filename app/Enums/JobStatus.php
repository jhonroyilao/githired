<?php

namespace App\Enums;

enum JobStatus: string
{
    case Draft    = 'draft';
    case Pending  = 'pending';
    case Active   = 'active';
    case Closed   = 'closed';
    case Rejected = 'rejected';

    /**
     * a job is publicly visible to applicants when it is active
     * and has not been soft-deleted.
     */
    public function isPubliclyVisible(): bool
    {
        return $this === self::Active;
    }
}