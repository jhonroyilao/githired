<?php

namespace App\Http\Controllers\Employer;

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

        if ($destination !== 'employer.dashboard') {
            return redirect()->route($destination);
        }

        return view('dashboards.employer', [
            'user' => $request->user(),
            'company' => $request->user()->company,
        ]);
    }
}
