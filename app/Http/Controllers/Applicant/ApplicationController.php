<?php

namespace App\Http\Controllers\Applicant;

use App\Actions\Applicant\PrepareAiJobMatchAction;
use App\Actions\Applicant\StoreResumeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreApplicationRequest;
use App\Jobs\ExtractResumeText;
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
        PrepareAiJobMatchAction $prepareAiJobMatch,
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
        $shouldDispatchExtraction = false;
        if ($request->hasFile('resume')) {
            $resumeDocument = $storeResume->handle($user, $request->file('resume'), dispatchExtraction: false);
            $shouldDispatchExtraction = $resumeDocument->wasRecentlyCreated
                && $resumeDocument->extraction_status === 'pending';
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

        $resumeDocument = $resumeDocument?->fresh();

        if ($shouldDispatchExtraction) {
            ExtractResumeText::dispatch($resumeDocument);
        } elseif ($resumeDocument === null || $resumeDocument->extraction_status !== 'pending') {
            $prepareAiJobMatch->handle($user, $jobListing, $resumeDocument);
        }

        return redirect()
            ->route('applicant.dashboard')
            ->with('success', 'Your application has been submitted successfully!');
    }
}
