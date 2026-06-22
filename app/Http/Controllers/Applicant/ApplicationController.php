<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreApplicationRequest;
use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class ApplicationController extends Controller
{
    /**
     * Show the application form for a publicly visible job listing.
     */
    public function create(Request $request, JobListing $jobListing): View|RedirectResponse
    {
        // Block access to any listing that is not status=active or is soft-deleted
        abort_unless($jobListing->isPubliclyVisible(), Response::HTTP_NOT_FOUND);

        $user = $request->user();

        // Redirect if this applicant already applied
        if (Application::where('job_listing_id', $jobListing->id)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            return redirect()
                ->route('applicant.dashboard')
                ->with('info', 'You have already applied to this job.');
        }

        return view('applicant.applications.create', [
            'jobListing' => $jobListing,
            'profile'    => $user->profile,
        ]);
    }

    /**
     * Store a new application for a publicly visible job listing.
     */
    public function store(StoreApplicationRequest $request, JobListing $jobListing): RedirectResponse
    {
        // Guard against direct POST to a non-visible listing
        abort_unless($jobListing->isPubliclyVisible(), Response::HTTP_NOT_FOUND);

        $user = $request->user();

        // Duplicate check at the application layer
        // (the DB unique constraint on user_id + job_listing_id is the final guard)
        if (Application::where('job_listing_id', $jobListing->id)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            return redirect()
                ->route('applicant.dashboard')
                ->with('info', 'You have already applied to this job.');
        }

        // Handle optional resume upload
        $resumePath = null;

        if ($request->hasFile('resume')) {
            // Store uploaded file on the private disk under resumes/{user_id}/
            $resumePath = $request->file('resume')->store(
                "resumes/{$user->id}",
                'private'
            );
        } elseif ($user->profile?->resume_path) {
            // Fall back to the resume saved on their profile
            $resumePath = $user->profile->resume_path;
        }

        Application::create([
            'user_id'        => $user->id,
            'job_listing_id' => $jobListing->id,
            'cover_letter'   => $request->input('cover_letter'),
            'resume_path'    => $resumePath,
            'status'         => 'pending',
        ]);

        return redirect()
            ->route('applicant.dashboard')
            ->with('success', 'Your application has been submitted successfully!');
    }
}