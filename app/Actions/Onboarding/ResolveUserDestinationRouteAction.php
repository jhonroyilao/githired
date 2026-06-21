<?php

namespace App\Actions\Onboarding;

use App\Enums\UserRole;
use App\Models\User;

final class ResolveUserDestinationRouteAction
{
    public function __construct(
        private readonly DetermineApplicantOnboardingStepAction $determineApplicantStep,
        private readonly DetermineEmployerOnboardingStepAction $determineEmployerStep,
    ) {}

    public function handle(User $user): string
    {
        return match (UserRole::tryFrom((string) $user->role)) {
            UserRole::Employer => $this->determineEmployerStep->handle($user) ?? 'employer.dashboard',
            UserRole::Admin => 'admin.dashboard',
            UserRole::Applicant, null => $this->determineApplicantStep->handle($user) ?? 'applicant.dashboard',
        };
    }

    public function dashboard(User $user): string
    {
        return match (UserRole::tryFrom((string) $user->role)) {
            UserRole::Employer => 'employer.dashboard',
            UserRole::Admin => 'admin.dashboard',
            UserRole::Applicant, null => 'applicant.dashboard',
        };
    }
}
