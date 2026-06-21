<?php

namespace App\Http\Controllers\Applicant\Onboarding;

use App\Actions\Onboarding\DetermineApplicantOnboardingStepAction;
use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Actions\Onboarding\UpdateApplicantPreferencesAction;
use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Enums\WorkPreference;
use App\Http\Controllers\Onboarding\AbstractOnboardingController;
use App\Http\Requests\Applicant\Onboarding\StorePreferencesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PreferencesController extends AbstractOnboardingController
{
    public function create(
        Request $request,
        DetermineApplicantOnboardingStepAction $determineStep,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): View|RedirectResponse {
        if ($redirect = $this->redirectIfUnavailable(
            $request,
            $resolveDestination,
            'applicant.onboarding.preferences',
            $determineStep->handle($request->user()),
            UserRole::Applicant->value,
        )) {
            return $redirect;
        }

        $this->rememberStep($request, 'applicant.onboarding.preferences');

        return view('onboarding.applicant.preferences', [
            'profile' => $request->user()->profile,
            'jobTypes' => JobType::cases(),
            'workPreferences' => WorkPreference::cases(),
            'experienceLevels' => ExperienceLevel::cases(),
        ]);
    }

    public function store(
        StorePreferencesRequest $request,
        UpdateApplicantPreferencesAction $updatePreferences,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): RedirectResponse {
        $updatePreferences->handle($request->user(), $request->preferenceAttributes());

        $destination = $resolveDestination->handle($request->user()->refresh());
        $this->rememberStep($request, $destination);

        return redirect()->route($destination);
    }

    protected function stepOrder(): array
    {
        return [
            'applicant.onboarding.profile' => 1,
            'applicant.onboarding.summary' => 2,
            'applicant.onboarding.preferences' => 3,
            'applicant.onboarding.links' => 4,
        ];
    }

    protected function flowKey(): string
    {
        return 'applicant';
    }
}
