<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case Interview = 'interview';
    case Hired = 'hired';
    case Rejected = 'rejected';
}
