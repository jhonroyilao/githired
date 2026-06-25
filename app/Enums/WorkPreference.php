<?php

namespace App\Enums;

enum WorkPreference: string
{
    case Remote = 'remote';
    case Onsite = 'onsite';
    case Hybrid = 'hybrid';
}
