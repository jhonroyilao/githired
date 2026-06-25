<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class JobModerationController extends Controller
{
    public function index(): View
    {
        $jobs = JobListing::with(['company', 'user'])
            ->where('status', JobStatus::Pending->value)
            ->when(
                request('sort') === 'oldest',
                fn ($query) => $query->oldest(),
                fn ($query) => $query->latest()
            )
            ->paginate(20);

        return view('admin.jobs.pending', ['jobs' => $jobs, 'user' => auth()->user()]);
    }

    public function approve(Request $request, JobListing $jobListing): RedirectResponse
    {
        $this->updatePendingJob($jobListing, [
            'status' => JobStatus::Active->value,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'published_at' => now(),
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Job approved successfully.');
    }

    public function reject(Request $request, JobListing $jobListing): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->updatePendingJob($jobListing, [
            'status' => JobStatus::Rejected->value,
            'approved_at' => null,
            'approved_by' => null,
            'published_at' => null,
            'rejected_at' => now(),
            'rejected_by' => $request->user()->id,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Job rejected successfully.');
    }

    private function updatePendingJob(JobListing $jobListing, array $attributes): void
    {
        $updated = JobListing::query()
            ->whereKey($jobListing->getKey())
            ->where('status', JobStatus::Pending->value)
            ->update($attributes);

        abort_if($updated === 0, Response::HTTP_CONFLICT);
    }
}
