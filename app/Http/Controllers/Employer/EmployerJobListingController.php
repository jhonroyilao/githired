<?php

namespace App\Http\Controllers\Employer;

use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\StoreJobListingRequest;
use App\Models\JobCategory;
use App\Models\JobListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmployerJobListingController extends Controller
{
    public function index(): View
    {
        $jobs = auth()->user()
            ->company
            ->jobListings()
            ->latest()
            ->paginate(10);

        return view('employer.jobs.index', compact('jobs'));
    }

    public function create(): View
    {
        $categories = JobCategory::orderBy('name')->get();

        return view('employer.jobs.create', compact('categories'));
    }

    public function store(StoreJobListingRequest $request): RedirectResponse
    {
        $company = $request->user()->company;

        abort_if(! $company, 403);

        $job = JobListing::create([
            'user_id' => $request->user()->id,
            'company_id' => $company->id,
            'category_id' => $request->category_id,

            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . uniqid(),

            'location' => $request->location,
            'location_type' => $request->location_type,
            'type' => $request->type,
            'experience_level' => $request->experience_level,

            'description' => $request->description,
            'requirements' => $request->requirements,

            'skills_required' => $request->skills_required
                ? array_map('trim', explode(',', $request->skills_required))
                : [],

            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,

            'salary_currency' => $request->filled('salary_currency')
                ? $request->salary_currency
                : 'PHP',

            'expires_at' => $request->expires_at,

            'status' => JobStatus::Pending->value,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('employer.dashboard')
            ->with('success', 'Job listing submitted for review.');
    }

    public function show(JobListing $jobListing): View
    {
        abort_if(
            $jobListing->company_id !== auth()->user()->company?->id,
            403
        );

        return view('employer.jobs.show', [
            'job' => $jobListing,
        ]);
    }

    public function edit(JobListing $jobListing): View
    {
        abort_if(
            $jobListing->company_id !== auth()->user()->company?->id,
            403
        );

        $categories = JobCategory::orderBy('name')->get();

        return view('employer.jobs.edit', [
            'job' => $jobListing,
            'categories' => $categories,
        ]);
    }

    public function update(
        StoreJobListingRequest $request,
        JobListing $jobListing
    ): RedirectResponse {
        abort_if(
            $jobListing->company_id !== auth()->user()->company?->id,
            403
        );

        $jobListing->update([
            'category_id' => $request->category_id,

            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . uniqid(),

            'location' => $request->location,
            'location_type' => $request->location_type,
            'type' => $request->type,
            'experience_level' => $request->experience_level,

            'description' => $request->description,
            'requirements' => $request->requirements,

            'skills_required' => $request->skills_required
                ? array_map('trim', explode(',', $request->skills_required))
                : [],

            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,

            'expires_at' => $request->expires_at,

            'salary_currency' => $request->filled('salary_currency')
                ? $request->salary_currency
                : 'PHP',
        ]);

        return redirect()
            ->route('employer.dashboard')
            ->with('success', 'Job listing updated successfully.');    }
}