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

        $company = $request->user()->company;

        if (! $company) {
            return redirect()->route($destination);
        }

        $jobs = $company->jobListings();

        $search = trim((string) $request->input('search'));

        if ($search !== '') {
            $operator = $this->likeOperator($jobs->getConnection()->getDriverName());
            $pattern = "%{$search}%";

            $jobs->where(function ($query) use ($operator, $pattern): void {
                $query->where('title', $operator, $pattern)
                    ->orWhere('location', $operator, $pattern)
                    ->orWhere('status', $operator, $pattern)
                    ->orWhere('description', $operator, $pattern)
                    ->orWhere('requirements', $operator, $pattern);
            });
        }

        return view('dashboards.employer', [
            'user' => $request->user(),
            'company' => $company,
            'jobs' => $jobs
                ->latest()
                ->paginate(9)
                ->withQueryString(),
        ]);
    }

    private function likeOperator(string $driverName): string
    {
        return $driverName === 'pgsql' ? 'ilike' : 'like';
    }
}
