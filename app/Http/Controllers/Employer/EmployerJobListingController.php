<?php

namespace App\Http\Controllers\Employer;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\StoreJobListingRequest;
use App\Models\JobCategory;
use App\Models\JobListing; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Models\Application; 

class EmployerJobListingController extends Controller
{
    public function index(ResolveUserDestinationRouteAction $resolveDestination): View|RedirectResponse
    {
        $company = auth()->user()->company;

        if (! $company) {
            return redirect()->route($resolveDestination->handle(auth()->user()));
        }

        $jobs = $company
            ->jobListings()
            ->latest()
            ->paginate(10);

        return view('employer.jobs.index', compact('jobs'));
    }

    public function create(ResolveUserDestinationRouteAction $resolveDestination): View|RedirectResponse
    {
        if (! auth()->user()->company) {
            return redirect()->route($resolveDestination->handle(auth()->user()));
        }

        $categories = JobCategory::orderBy('name')->get();

        return view('employer.jobs.create', compact('categories'));
    }

    public function store(StoreJobListingRequest $request): RedirectResponse
    {
        $company = $request->user()->company;

        abort_if(! $company, 403);

        $attributes = $request->jobListingAttributes();

        JobListing::create([
            'user_id' => $request->user()->id,
            'company_id' => $company->id,
            ...$attributes,
            'slug' => Str::slug($attributes['title']).'-'.uniqid(),
            'status' => JobStatus::Pending->value,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('employer.dashboard')
            ->with('success', 'Job listing submitted for review.');
    }

    public function show(JobListing $jobListing): View
    {
        $this->authorizeOwnedJob($jobListing);

        return view('employer.jobs.show', [
            'job' => $jobListing,
        ]);
    }

    public function edit(JobListing $jobListing): View
    {
        $this->authorizeEditableJob($jobListing);

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
        $this->authorizeEditableJob($jobListing);

        $attributes = $request->jobListingAttributes();

        $jobListing->update([
            ...$attributes,
            'slug' => Str::slug($attributes['title']).'-'.uniqid(),
            'status' => JobStatus::Pending->value,
            'submitted_at' => now(),
            'approved_at' => null,
            'approved_by' => null,
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
            'published_at' => null,
        ]);

        return redirect()
            ->route('employer.dashboard')
            ->with('success', 'Job listing updated and submitted for review.');
    }

    private function authorizeOwnedJob(JobListing $jobListing): void
    {
        abort_if(
            $jobListing->company_id !== auth()->user()->company?->id,
            Response::HTTP_FORBIDDEN
        );
    }

    private function authorizeEditableJob(JobListing $jobListing): void
    {
        $this->authorizeOwnedJob($jobListing);

        abort_if($jobListing->status === JobStatus::Closed->value, Response::HTTP_FORBIDDEN);
    }

    public function applicants(Request $request, JobListing $jobListing): View{
    
    $this->authorizeOwnedJob($jobListing);
    $query = $jobListing->applications()->with('user');

    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    } else {
        $query->where('status', '!=', 'hired');
    }

    $query->when($request->search, function($q) use ($request) {
        $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
    });

    $sort = $request->sort === 'oldest' ? 'asc' : 'desc';
    $query->orderBy('created_at', $sort);

    return view('employer.applicants.index', [
        'job' => $jobListing,
        'applications' => $query->paginate(12)->withQueryString(),
    ]);
    }

    public function showApplication(JobListing $jobListing, Application $application)
    {
    if ($application->job_listing_id !== $jobListing->id || $jobListing->user_id !== auth()->id()) {
        abort(403, 'Unauthorized access.');
    }

    return view('employer.applicants.show', [
        'job' => $jobListing,
        'application' => $application,
    ]);
}
}
