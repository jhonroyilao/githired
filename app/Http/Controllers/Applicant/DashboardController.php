<?php

namespace App\Http\Controllers\Applicant;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Queries\JobListingBrowseQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        ResolveUserDestinationRouteAction $resolveDestination,
        JobListingBrowseQuery $browseQuery,
    ): View|RedirectResponse {
        $user = $request->user();

        $destination = $resolveDestination->handle($request->user());
        if ($destination !== 'applicant.dashboard') {
            return redirect()->route($destination);
        }

        $profile = $user->profile;

        $applicationCount = Application::where('user_id', $user->id)->count();
        $interviewCount = Application::where('user_id', $user->id)->where('status', 'interview')->count();

        $allCategories = JobCategory::all();
        $query = JobListing::publiclyVisible()->with(['company', 'category']);

        $browseQuery->apply($query, $request, includeDatePosted: true);

        $allListings = JobListing::publiclyVisible()->with('category')->get();

        $categoryCounts = [];
        foreach ($allCategories as $cat) {
            $categoryCounts[$cat->name] = $allListings->where('category_id', $cat->id)->count();
        }

        $jobTypeOptions = $browseQuery->jobTypeOptions();
        $experienceOptions = $browseQuery->experienceOptions();

        $typeCounts = [];
        foreach (array_keys($jobTypeOptions) as $type) {
            $typeCounts[$type] = $allListings->where('type', $type)->count();
        }

        $experienceCounts = [];
        foreach (array_keys($experienceOptions) as $exp) {
            $experienceCounts[$exp] = $allListings->where('experience_level', $exp)->count();
        }

        $dateCounts = [
            'All' => $allListings->count(),
            'Last Hour' => $allListings->where('created_at', '>=', now()->subHour())->count(),
            'Last 24 Hours' => $allListings->where('created_at', '>=', now()->subDay())->count(),
            'Last 7 Days' => $allListings->where('created_at', '>=', now()->subDays(7))->count(),
            'Last 30 Days' => $allListings->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $locations = JobListing::publiclyVisible()->whereNotNull('location')->pluck('location')->unique()->all();

        $aiMatches = (clone $query)->latest()->limit(3)->get();
        $browseListings = $query->latest()->paginate(6)->withQueryString();

        $appliedListingIds = Application::where('user_id', $user->id)
            ->pluck('job_listing_id')
            ->all();

        return view('dashboards.applicant', [
            'user' => $user,
            'profile' => $profile,
            'categories' => $allCategories,
            'applicationCount' => $applicationCount,
            'interviewCount' => $interviewCount,
            'aiMatches' => $aiMatches,
            'browseListings' => $browseListings,
            'appliedListingIds' => $appliedListingIds,
            'locations' => $locations,
            'jobTypeOptions' => $jobTypeOptions,
            'experienceOptions' => $experienceOptions,
            'datePostedOptions' => $browseQuery->datePostedOptions(),
            'categoryCounts' => $categoryCounts,
            'typeCounts' => $typeCounts,
            'experienceCounts' => $experienceCounts,
            'dateCounts' => $dateCounts,
        ]);
    }
}
