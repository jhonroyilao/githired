<?php

namespace App\Http\Controllers;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Queries\JobListingBrowseQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class JobController extends Controller
{
    public function index(
        Request $request,
        JobListingBrowseQuery $browseQuery,
        ResolveUserDestinationRouteAction $resolveDestination,
    ): View {
        $query = JobListing::query()
            ->publiclyVisible()
            ->with(['company', 'category']);

        $browseQuery->apply($query, $request);

        return view('jobs.index', [
            'jobs' => $query->latest('published_at')->paginate(9)->withQueryString(),
            'categories' => JobCategory::query()->orderBy('name')->get(),
            'locations' => JobListing::query()
                ->publiclyVisible()
                ->whereNotNull('location')
                ->distinct()
                ->orderBy('location')
                ->pluck('location'),
            'jobTypes' => $browseQuery->jobTypeOptions(),
            'experienceLevels' => $browseQuery->experienceOptions(),
            'dashboardRoute' => $request->user()
                ? $resolveDestination->dashboard($request->user())
                : null,
        ]);
    }

    public function show(Request $request, JobListing $jobListing): View
    {
        abort_unless($jobListing->isPubliclyVisible(), Response::HTTP_NOT_FOUND);

        $jobListing->load(['company', 'category']);

        $user = $request->user();
        $hasApplied = $user?->role === UserRole::Applicant->value
            && Application::query()
                ->where('user_id', $user->id)
                ->where('job_listing_id', $jobListing->id)
                ->exists();

        return view('jobs.show', [
            'jobListing' => $jobListing,
            'hasApplied' => $hasApplied,
        ]);
    }
}
