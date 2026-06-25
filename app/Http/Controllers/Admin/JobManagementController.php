<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class JobManagementController extends Controller
{
    public function index(): View
    {
        $query = JobListing::query();

        $status = request('status');

        if ($status === 'deleted') {
            $query->onlyTrashed();
        } elseif (!empty($status)) {
            $query->where('status', $status);
        }

        $query->with(['company', 'user']);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($company) use ($search) {
                        $company->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $query->when(
            request('sort') === 'oldest',
            fn ($q) => $q->oldest(),
            fn ($q) => $q->latest()
        );

        $jobs = $query->paginate(20)->withQueryString();

        return view('admin.jobs.all', [
            'user'          => auth()->user(),
            'jobs'          => $jobs,
            'pendingCount'  => JobListing::where('status', JobStatus::Pending->value)->count(),
            'activeCount'   => JobListing::where('status', JobStatus::Active->value)->count(),
            'closedCount'   => JobListing::where('status', JobStatus::Closed->value)->count(),
            'rejectedCount' => JobListing::where('status', JobStatus::Rejected->value)->count(),
            'deletedCount'  => JobListing::onlyTrashed()->count(),
            'totalCount'    => JobListing::withTrashed()->count(),
        ]);
    }

    public function hide(
        Request $request,
        JobListing $jobListing
    ): RedirectResponse
    {
        $jobListing->update([
            'status' => JobStatus::Closed->value,
            'closed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Job listing has been hidden successfully.'
        );
    }

    public function destroy(
        Request $request,
        JobListing $jobListing
    ): RedirectResponse
    {
        $request->validate([
            'delete_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $jobListing->update([
            'deleted_by' => $request->user()->id,
            'delete_reason' => $request->delete_reason,
        ]);

        $jobListing->delete();

        return back()->with(
            'success',
            'Job listing has been soft deleted.'
        );
    }

    public function restore(JobListing $jobListing): RedirectResponse
    {
        abort_unless($jobListing->trashed(), Response::HTTP_CONFLICT);

        $jobListing->restore();
        $jobListing->update([
            'deleted_by' => null,
            'delete_reason' => null,
        ]);

        return back()->with(
            'success',
            'Job listing has been restored.'
        );
    }
}
