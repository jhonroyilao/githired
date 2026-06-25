<?php

namespace App\Enums;

enum UserRole: string
{
    case Applicant = 'applicant';
    case Employer = 'employer';
    case Admin = 'admin';
}
