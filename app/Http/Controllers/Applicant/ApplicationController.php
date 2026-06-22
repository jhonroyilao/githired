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
        // FIX: stricter validation. 
        abort_unless(
            $jobListing->status === 'active' && 
            $jobListing->approved_at !== null && 
            $jobListing->published_at !== null && 
            $jobListing->closed_at === null && 
            $jobListing->deleted_at === null, 
            Response::HTTP_NOT_FOUND
        );

        $user = $request->user();

        //redirect if this applicant already applied
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
            'profile'    => $user->profile,
        ]);
    }

    /**
     * store a new application for a publicly visible job listing.
     */
    public function store(StoreApplicationRequest $request, JobListing $jobListing): RedirectResponse
    {
        // 1. FIX guard against direct POST to an unapproved or inactive listing
        abort_unless(
            $jobListing->status === 'active' && 
            $jobListing->approved_at !== null && 
            $jobListing->published_at !== null && 
            $jobListing->closed_at === null && 
            $jobListing->deleted_at === null, 
            Response::HTTP_NOT_FOUND
        );

        $user = $request->user();

        // duplicate check at the application layer
        if (Application::where('job_listing_id', $jobListing->id)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            return redirect()
                ->route('applicant.dashboard')
                ->with('info', 'You have already applied to this job.');
        }

        $resumePath = null;
        $resumeDocumentId = null;

        // FIX: changed driver from 'private' to 'local'
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store(
                "resumes/{$user->id}",
                'local' 
            );
            
            // NOTE: kung may live tracking na si noreen para sa bagong upload, dito mag-cre-create ng bagong ResumeDocument record para makuha yung ID
        } else {
            // FIX: fallback sequence to user's current valid ResumeDocument record
            $currentDoc = $user->currentResumeDocument ?? $user->resumeDocuments()->where('is_current', true)->first();

            if ($currentDoc) {
                $resumeDocumentId = $currentDoc->id;
                $resumePath = $currentDoc->file_path;
            } elseif ($user->profile?->resume_path) {
                // legacy secondary fallback to raw profile path
                $resumePath = $user->profile->resume_path;
            }
        }

        Application::create([
            'user_id'            => $user->id,
            'job_listing_id'     => $jobListing->id,
            'resume_document_id' => $resumeDocumentId, // dynamic assignment fixed
            'cover_letter'       => $request->input('cover_letter'),
            'resume_path'        => $resumePath,
            'status'             => 'pending',
        ]);

        return redirect()
            ->route('applicant.dashboard')
            ->with('success', 'Your application has been submitted successfully!');
    }
}