<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Application stats for this applicant ──────────────
        $applications = Application::where('user_id', $user->id)->get();

        $stats = [
            'total'     => $applications->count(),
            'pending'   => $applications->where('status', 'pending')->count(),
            'interview' => $applications->where('status', 'interview')->count(),
            'hired'     => $applications->where('status', 'hired')->count(),
            'rejected'  => $applications->where('status', 'rejected')->count(),
        ];

        // ── Recent applications (latest 5, with job + company info) ──
        $recentApplications = Application::where('user_id', $user->id)
            ->with(['jobListing.company'])
            ->latest()
            ->take(5)
            ->get();

        // ── Recommended jobs: active jobs the user hasn't applied to ──
        $appliedJobIds = $applications->pluck('job_listing_id');

        $recommendedJobs = JobListing::active()
            ->whereNotIn('id', $appliedJobIds)
            ->with('company')
            ->latest('published_at')
            ->take(4)
            ->get();

        // ── Profile completeness (basic check) ─────────────────
        $profile = $user->profile;
        $profileFields = [
            $profile?->headline,
            $profile?->bio,
            $profile?->location,
            $profile?->resume_path,
            $profile?->skills,
        ];
        $filledFields = count(array_filter($profileFields));
        $profileCompleteness = (int) round(($filledFields / count($profileFields)) * 100);

        return view('applicant.dashboard', compact(
            'stats',
            'recentApplications',
            'recommendedJobs',
            'profileCompleteness',
            'profile'
        ));
    }
}