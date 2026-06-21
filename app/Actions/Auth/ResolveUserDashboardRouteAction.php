<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\User;

final class ResolveUserDashboardRouteAction
{
    public function handle(User $user): string
    {
        return match (UserRole::tryFrom((string) $user->role)) {
            UserRole::Employer => 'employer.dashboard',
            UserRole::Admin => 'admin.dashboard',
            UserRole::Applicant, null => 'applicant.dashboard',
        };
    }
}
