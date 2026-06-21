<?php

namespace App\Http\Controllers\Applicant;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request, ResolveUserDestinationRouteAction $resolveDestination): View|RedirectResponse
    {
        $destination = $resolveDestination->handle($request->user());

        if ($destination !== 'applicant.dashboard') {
            return redirect()->route($destination);
        }

        return view('dashboards.applicant', [
            'user' => $request->user(),
            'profile' => $request->user()->profile,
        ]);
    }
}
