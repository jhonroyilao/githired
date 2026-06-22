<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $profile = $user->profile;

        $applicationCount = Application::where('user_id', $user->id)->count();
        $interviewCount = Application::where('user_id', $user->id)->where('status', 'interview')->count();

        $query = JobListing::publiclyVisible()->with(['company', 'category']);

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

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->whereIn('name', $request->input('category'));
            });
        }

        if ($request->filled('job_type')) {
            $query->whereIn('type', $request->input('job_type'));
        }

        if ($request->filled('experience_level')) {
            $query->whereIn('experience_level', $request->input('experience_level'));
        }

        if ($request->filled('date_posted') && !in_array('All', $request->input('date_posted'))) {
            $dates = $request->input('date_posted');
            $query->where(function ($q) use ($dates) {
                foreach ($dates as $date) {
                    if ($date === 'Last Hour') $q->orWhere('created_at', '>=', now()->subHour());
                    if ($date === 'Last 24 Hours') $q->orWhere('created_at', '>=', now()->subDay());
                    if ($date === 'Last 7 Days') $q->orWhere('created_at', '>=', now()->subDays(7));
                    if ($date === 'Last 30 Days') $q->orWhere('created_at', '>=', now()->subDays(30));
                }
            });
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->input('tag'));
            });
        }

        $allListings = JobListing::publiclyVisible()->get();

        $categoryCounts = [];
        foreach (['Data Science', 'UI/UX', 'Cybersecurity', 'Web Development', 'Project Management'] as $cat) {
            $categoryCounts[$cat] = $allListings->where('category.name', $cat)->count();
        }

        $typeCounts = [];
        foreach (['Full-Time', 'Part-Time', 'Freelance', 'Seasonal', 'Fixed-Price'] as $type) {
            $typeCounts[$type] = $allListings->where('type', $type)->count();
        }

        $experienceCounts = [];
        foreach (['No-experience', 'Fresher', 'Intermediate', 'Expert'] as $exp) {
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
        
        $tags = collect(); 

        $aiMatches = (clone $query)->latest()->limit(3)->get();
        $browseListings = $query->latest()->paginate(6)->withQueryString();

        $appliedListingIds = Application::where('user_id', $user->id)
            ->pluck('job_listing_id')
            ->all();

        return view('dashboards.applicant', [
            'user' => $user,
            'profile' => $profile,
            'applicationCount' => $applicationCount,
            'interviewCount' => $interviewCount,
            'aiMatches' => $aiMatches,
            'browseListings' => $browseListings,
            'appliedListingIds' => $appliedListingIds,
            'locations' => $locations,
            'tags' => $tags,
            'categoryCounts' => $categoryCounts,
            'typeCounts' => $typeCounts,
            'experienceCounts' => $experienceCounts,
            'dateCounts' => $dateCounts,
        ]);
    }
}