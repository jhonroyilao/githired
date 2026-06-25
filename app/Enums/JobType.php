<?php

namespace App\Enums;

enum JobType: string
{
    case FullTime = 'full-time';
    case PartTime = 'part-time';
    case Contract = 'contract';
    case Internship = 'internship';
}
