<?php

namespace App\Http\Controllers\Employer\Onboarding;

use App\Actions\Onboarding\DetermineEmployerOnboardingStepAction;
use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Actions\Onboarding\UpdateEmployerCompanyAction;
use App\Enums\UserRole;
use App\Http\Controllers\Onboarding\AbstractOnboardingController;
use App\Http\Requests\Employer\Onboarding\StoreCompanyProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CompanyProfileController extends AbstractOnboardingController
{
    public function create(
        Request $request,
        DetermineEmployerOnboardingStepAction $determineStep,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): View|RedirectResponse {
        $user = $request->user();

        if ($user->role !== UserRole::Employer->value) {
            return redirect()->route($resolveDestination->handle($user));
        }

        if ($redirect = $this->redirectIfUnavailable(
            $request,
            $resolveDestination,
            'employer.onboarding.company',
            $determineStep->handle($user),
            UserRole::Employer->value,
        )) {
            return $redirect;
        }

        $this->rememberStep($request, 'employer.onboarding.company');

        return view('onboarding.employer.company', [
            'company' => $user->company,
        ]);
    }

    public function store(
        StoreCompanyProfileRequest $request,
        UpdateEmployerCompanyAction $updateCompany,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): RedirectResponse {
        $updateCompany->handle($request->user(), $request->companyAttributes());

        $destination = $resolveDestination->handle($request->user()->refresh());
        $this->rememberStep($request, $destination);

        return redirect()->route($destination);
    }

    protected function stepOrder(): array
    {
        return [
            'employer.onboarding.company' => 1,
        ];
    }

    protected function flowKey(): string
    {
        return 'employer';
    }
}
