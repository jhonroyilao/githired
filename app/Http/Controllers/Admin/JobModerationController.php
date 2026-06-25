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
        $this->ensurePending($jobListing);

        $jobListing->update([
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
        $this->ensurePending($jobListing);

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $jobListing->update([
            'status' => JobStatus::Rejected->value,
            'rejected_at' => now(),
            'rejected_by' => $request->user()->id,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Job rejected successfully.');
    }

    private function ensurePending(JobListing $jobListing): void
    {
        abort_unless(
            $jobListing->status === JobStatus::Pending->value,
            Response::HTTP_CONFLICT
        );
    }

        public function reapprove(Request $request, JobListing $jobListing): RedirectResponse
    {
        $jobListing->update([
            'status'           => JobStatus::Active->value,
            'approved_at'      => now(),
            'approved_by'      => $request->user()->id,
            'published_at'     => now(),
            'rejected_at'      => null,
            'rejected_by'      => null,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Job listing re-approved successfully.');
    }

    public function reactivate(Request $request, JobListing $jobListing): RedirectResponse
    {
        $jobListing->update([
            'status'      => JobStatus::Active->value,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'published_at' => now(),
            'closed_at'   => null,
        ]);

        return back()->with('success', 'Job listing is now active.');
    }
}

