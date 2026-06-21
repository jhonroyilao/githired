<?php

namespace App\Actions\Onboarding;

use App\Models\Company;
use App\Models\User;

final class DetermineEmployerOnboardingStepAction
{
    public function handle(User $user): ?string
    {
        $company = $user->company;

        if (! $company instanceof Company) {
            return 'employer.onboarding.company';
        }

        foreach (['name', 'slug', 'industry', 'size', 'location', 'description'] as $attribute) {
            $value = $company->{$attribute};

            if ($value === null || trim((string) $value) === '') {
                return 'employer.onboarding.company';
            }
        }

        return null;
    }
}
