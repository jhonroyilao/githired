<?php

namespace App\Http\Controllers\Employer;

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\StoreJobListingRequest;
use App\Http\Requests\Employer\UpdateApplicationStatusRequest;
use App\Models\Application;
use App\Models\ApplicationStatusLog;
use App\Models\JobCategory;
use App\Models\JobListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function applicants(Request $request, JobListing $jobListing): View
    {
        $this->authorizeOwnedJob($jobListing);

        $query = $jobListing->applications()->with('user.profile');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'hired');
        }

        $query->when($request->search, function ($q) use ($request) {
            $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$request->search}%"));
        });

        $sort = $request->sort === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sort);

        return view('employer.applicants.index', [
            'job' => $jobListing,
            'applications' => $query->paginate(12)->withQueryString(),
        ]);
    }

    public function showApplication(JobListing $jobListing, Application $application): View
    {
        $this->authorizeApplication($jobListing, $application);

        $application->loadMissing(['user.profile', 'resumeDocument', 'statusLogs.changedByUser']);

        return view('employer.applicants.show', [
            'job' => $jobListing,
            'application' => $application,
        ]);
    }

    public function downloadApplicationResume(JobListing $jobListing, Application $application): StreamedResponse
    {
        $this->authorizeApplication($jobListing, $application);

        $application->loadMissing('resumeDocument');

        $path = $application->resumeDocument?->file_path ?? $application->resume_path;
        abort_if(blank($path), Response::HTTP_NOT_FOUND);

        $disk = Storage::disk(config('filesystems.resume_disk', 'local'));
        abort_unless($disk->exists($path), Response::HTTP_NOT_FOUND);

        return $disk->download(
            $path,
            $application->resumeDocument?->original_name ?? basename($path),
        );
    }

    public function updateApplicationStatus(UpdateApplicationStatusRequest $request, JobListing $jobListing, Application $application): RedirectResponse
    {
        if ($application->job_listing_id !== $jobListing->id) {
            abort(404);
        }

        $oldStatus = $application->status;
        $newStatus = $request->validated('status');

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'No change. The status is already '.ucfirst($newStatus).'.');
        }

        DB::transaction(function () use ($application, $oldStatus, $newStatus, $request): void {
            $application->update([
                'status' => $newStatus,
                'status_updated_at' => now(),
            ]);

            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $request->user()->id,
                'changed_by_name' => $request->user()->name,
                'changed_by_email' => $request->user()->email,
                'note' => $request->validated('note'),
            ]);
        });

        return redirect()->route('employer.jobs.applicants.show', [
            'jobListing' => $jobListing->id,
            'application' => $application->id,
        ])->with('success', 'Status updated to '.ucfirst($newStatus).'.');
    }

    private function authorizeApplication(JobListing $jobListing, Application $application): void
    {
        abort_if(
            $application->job_listing_id !== $jobListing->id
                || $jobListing->user_id !== auth()->id(),
            Response::HTTP_FORBIDDEN
        );
    }
}
