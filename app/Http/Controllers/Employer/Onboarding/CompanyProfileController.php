<?php

namespace App\Http\Controllers\Employer\Onboarding;

use App\Actions\Onboarding\DetermineEmployerOnboardingStepAction;
use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Actions\Onboarding\UpdateEmployerCompanyAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\Onboarding\StoreCompanyProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CompanyProfileController extends Controller
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

        if ($determineStep->handle($user) === null) {
            return redirect()->route('employer.dashboard');
        }

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

        return redirect()->route($resolveDestination->handle($request->user()->refresh()));
    }
}
