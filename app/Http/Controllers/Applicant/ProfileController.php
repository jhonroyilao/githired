<?php

namespace App\Http\Controllers\Applicant;

use App\Actions\Applicant\UpdateProfileAction;
use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use App\Enums\WorkPreference;
use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('applicant.profile', [
            'user' => $request->user(),
            'profile' => $request->user()->profile,
            'jobTypes' => JobType::cases(),
            'workPreferences' => WorkPreference::cases(),
            'experienceLevels' => ExperienceLevel::cases(),
        ]);
    }

    public function update(UpdateProfileRequest $request, UpdateProfileAction $updateProfile): RedirectResponse
    {
        $updateProfile->handle($request->user(), $request->profileAttributes());

        return redirect()
            ->route('applicant.profile.edit')
            ->with('status', 'Profile updated.');
    }
}
