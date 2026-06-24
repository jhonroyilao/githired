<?php

namespace App\Http\Controllers\Applicant;

use App\Actions\Applicant\StoreResumeAction;
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
        abort_unless($jobListing->isPubliclyVisible(), Response::HTTP_NOT_FOUND);

        $user = $request->user();

        if (Application::where('job_listing_id', $jobListing->id)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            return redirect()
                ->route('applicant.dashboard')
                ->with('info', 'You have already applied to this job.');
        }

        return view('applicant.applications.create', [

            'jobListing' => $jobListing->load(['company', 'category']),
            'profile' => $user->profile,
        ]);
    }

    /**
     * store a new application for a publicly visible job listing.
     */
    public function store(
        StoreApplicationRequest $request,
        JobListing $jobListing,
        StoreResumeAction $storeResume,
    ): RedirectResponse {
        abort_unless($jobListing->isPubliclyVisible(), Response::HTTP_NOT_FOUND);

        $user = $request->user();

        if (Application::where('job_listing_id', $jobListing->id)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            return redirect()
                ->route('applicant.dashboard')
                ->with('info', 'You have already applied to this job.');
        }

        $resumeDocument = null;
        if ($request->hasFile('resume')) {
            $resumeDocument = $storeResume->handle($user, $request->file('resume'));
        } else {
            $resumeDocument = $user->currentResumeDocument
                ?? $user->resumeDocuments()->current()->first();
        }

        Application::create([
            'user_id' => $user->id,
            'job_listing_id' => $jobListing->id,
            'resume_document_id' => $resumeDocument?->id,
            'cover_letter' => $request->input('cover_letter'),
            'resume_path' => $resumeDocument?->file_path ?? $user->profile?->resume_path,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('applicant.dashboard')
            ->with('success', 'Your application has been submitted successfully!');
    }

    /**
     * display the user's job applications with filtering and pagination
     * switched from simple 'get()' to 'paginate()' to improve performance 
     * and user experience when handling a large number of applications
     * also added conditional filtering for status and job search
     */
   public function index(Request $request): View
    {
    $query = $request->user()->applications()->with(['jobListing.company']);
    // filter by Status
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }
    // filter by search 
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->whereHas('jobListing', function ($q) use ($searchTerm) {
            $q->where('title', 'like', "%{$searchTerm}%")
              ->orWhereHas('company', function ($c) use ($searchTerm) {
                  $c->where('name', 'like', "%{$searchTerm}%");
              });
        });
    }

    $applications = $query->latest()->paginate(10)->withQueryString();

    return view('applicant.applications.index', compact('applications'));
    }
}
