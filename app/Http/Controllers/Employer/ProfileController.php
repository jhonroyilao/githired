<?php

namespace App\Http\Controllers\Employer;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Actions\Onboarding\UpdateEmployerCompanyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\Onboarding\StoreCompanyProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request, ResolveUserDestinationRouteAction $resolveDestination): View|RedirectResponse
    {
        $user = $request->user();
        $destination = $resolveDestination->handle($user);

        if ($destination !== 'employer.dashboard') {
            return redirect()->route($destination);
        }

        return view('employer.profile.edit', [
            'company' => $user->company,
        ]);
    }

    public function update(
        StoreCompanyProfileRequest $request,
        UpdateEmployerCompanyAction $updateCompany,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): RedirectResponse {
        $user = $request->user();
        $destination = $resolveDestination->handle($user);

        if ($destination !== 'employer.dashboard') {
            return redirect()->route($destination);
        }

        $updateCompany->handle($user, $request->companyAttributes());

        return redirect()
            ->route('employer.company.edit')
            ->with('status', 'Company profile updated successfully!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with(
            'status',
            'Password updated successfully.'
        );
    }
}
