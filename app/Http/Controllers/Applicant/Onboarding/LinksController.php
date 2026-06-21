<?php

namespace App\Http\Controllers\Applicant\Onboarding;

use App\Actions\Onboarding\DetermineApplicantOnboardingStepAction;
use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Actions\Onboarding\UpdateApplicantLinksAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\Onboarding\StoreLinksRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LinksController extends Controller
{
    public function create(
        Request $request,
        DetermineApplicantOnboardingStepAction $determineStep,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): View|RedirectResponse {
        if ($redirect = $this->redirectIfUnavailable($request, $determineStep, $resolveDestination, 'applicant.onboarding.links')) {
            return $redirect;
        }

        return view('onboarding.applicant.links', [
            'profile' => $request->user()->profile,
        ]);
    }

    public function store(
        StoreLinksRequest $request,
        UpdateApplicantLinksAction $updateLinks,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): RedirectResponse {
        $updateLinks->handle($request->user(), $request->linkAttributes());

        return redirect()->route($resolveDestination->handle($request->user()->refresh()));
    }

    private function redirectIfUnavailable(
        Request $request,
        DetermineApplicantOnboardingStepAction $determineStep,
        ResolveUserDestinationRouteAction $resolveDestination,
        string $currentRoute,
    ): ?RedirectResponse {
        $user = $request->user();

        if ($user->role !== UserRole::Applicant->value) {
            return redirect()->route($resolveDestination->handle($user));
        }

        $nextRoute = $determineStep->handle($user);

        if ($nextRoute === null) {
            return redirect()->route('applicant.dashboard');
        }

        if ($this->stepOrder($currentRoute) > $this->stepOrder($nextRoute)) {
            return redirect()->route($nextRoute);
        }

        return null;
    }

    private function stepOrder(string $route): int
    {
        return [
            'applicant.onboarding.profile' => 1,
            'applicant.onboarding.summary' => 2,
            'applicant.onboarding.preferences' => 3,
            'applicant.onboarding.links' => 4,
        ][$route] ?? 999;
    }
}
