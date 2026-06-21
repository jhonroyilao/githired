<?php

namespace App\Http\Controllers\Applicant\Onboarding;

use App\Actions\Onboarding\DetermineApplicantOnboardingStepAction;
use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Actions\Onboarding\UpdateApplicantSummaryAction;
use App\Enums\UserRole;
use App\Http\Controllers\Onboarding\AbstractOnboardingController;
use App\Http\Requests\Applicant\Onboarding\StoreSummaryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SummaryController extends AbstractOnboardingController
{
    public function create(
        Request $request,
        DetermineApplicantOnboardingStepAction $determineStep,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): View|RedirectResponse {
        if ($redirect = $this->redirectIfUnavailable(
            $request,
            $resolveDestination,
            'applicant.onboarding.summary',
            $determineStep->handle($request->user()),
            UserRole::Applicant->value,
        )) {
            return $redirect;
        }

        $this->rememberStep($request, 'applicant.onboarding.summary');

        return view('onboarding.applicant.summary', [
            'profile' => $request->user()->profile,
        ]);
    }

    public function store(
        StoreSummaryRequest $request,
        UpdateApplicantSummaryAction $updateSummary,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): RedirectResponse {
        $updateSummary->handle($request->user(), $request->summaryAttributes());

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
