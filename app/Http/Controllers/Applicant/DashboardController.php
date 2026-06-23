<?php

namespace App\Http\Controllers\Applicant;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobCategory;
use App\Models\JobListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request, ResolveUserDestinationRouteAction $resolveDestination): View|RedirectResponse
    {
        $user = $request->user();

        // redirect users na hindi pa tapos sa onboarding
        $destination = $resolveDestination->handle($request->user());
        if ($destination !== 'applicant.dashboard') {
            return redirect()->route($destination);
        }

        $profile = $user->profile;

        $applicationCount = Application::where('user_id', $user->id)->count();
        $interviewCount = Application::where('user_id', $user->id)->where('status', 'interview')->count();

        // kunin ang lahat ng categories para sa dynamic filtering
        $allCategories = JobCategory::all();
        $query = JobListing::publiclyVisible()->with(['company', 'category']);

        // search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($compQ) use ($search) {
                        $compQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('location')) {
            $query->where('location', $request->input('location'));
        }

        // category Filter (Dynamic gamit ang slugs)
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->whereIn('slug', (array) $request->input('category'));
            });
        }

        if ($request->filled('job_type')) {
            $query->whereIn('type', $request->input('job_type'));
        }

        if ($request->filled('experience_level')) {
            $query->whereIn('experience_level', $request->input('experience_level'));
        }

        if ($request->filled('date_posted') && ! in_array('All', $request->input('date_posted'))) {
            $dates = $request->input('date_posted');
            $query->where(function ($q) use ($dates) {
                foreach ($dates as $date) {
                    if ($date === 'Last Hour') {
                        $q->orWhere('created_at', '>=', now()->subHour());
                    }
                    if ($date === 'Last 24 Hours') {
                        $q->orWhere('created_at', '>=', now()->subDay());
                    }
                    if ($date === 'Last 7 Days') {
                        $q->orWhere('created_at', '>=', now()->subDays(7));
                    }
                    if ($date === 'Last 30 Days') {
                        $q->orWhere('created_at', '>=', now()->subDays(30));
                    }
                }
            });
        }

        $allListings = JobListing::publiclyVisible()->with('category')->get();

        $categoryCounts = [];
        foreach ($allCategories as $cat) {
            $categoryCounts[$cat->name] = $allListings->where('category_id', $cat->id)->count();
        }

        $jobTypeOptions = [
            JobType::FullTime->value => 'Full-time',
            JobType::PartTime->value => 'Part-time',
            JobType::Contract->value => 'Contract',
            JobType::Internship->value => 'Internship',
        ];

        $experienceOptions = [
            ExperienceLevel::Entry->value => 'Entry level',
            ExperienceLevel::Mid->value => 'Mid level',
            ExperienceLevel::Senior->value => 'Senior level',
        ];

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
            'categoryCounts' => $categoryCounts,
            'typeCounts' => $typeCounts,
            'experienceCounts' => $experienceCounts,
            'dateCounts' => $dateCounts,
        ]);
    }
}
