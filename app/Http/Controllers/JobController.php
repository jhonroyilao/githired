<?php

namespace App\Http\Controllers;

use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\JobCategory;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class JobController extends Controller
{
    public function index(Request $request): View
    {
        $query = JobListing::query()
            ->publiclyVisible()
            ->with(['company', 'category']);

        $this->applyFilters($query, $request);

        return view('jobs.index', [
            'jobs' => $query->latest('published_at')->paginate(9)->withQueryString(),
            'categories' => JobCategory::query()->orderBy('name')->get(),
            'locations' => JobListing::query()
                ->publiclyVisible()
                ->whereNotNull('location')
                ->distinct()
                ->orderBy('location')
                ->pluck('location'),
            'jobTypes' => $this->jobTypeOptions(),
            'experienceLevels' => $this->experienceLevelOptions(),
        ]);
    }

    public function show(Request $request, JobListing $jobListing): View
    {
        abort_unless($jobListing->isPubliclyVisible(), Response::HTTP_NOT_FOUND);

        $jobListing->load(['company', 'category']);

        $user = $request->user();
        $hasApplied = $user?->role === UserRole::Applicant->value
            && Application::query()
                ->where('user_id', $user->id)
                ->where('job_listing_id', $jobListing->id)
                ->exists();

        return view('jobs.show', [
            'jobListing' => $jobListing,
            'hasApplied' => $hasApplied,
        ]);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('location')) {
            $query->where('location', $request->string('location')->toString());
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($query) use ($request): void {
                $query->whereIn('slug', (array) $request->input('category'));
            });
        }

        if ($request->filled('job_type')) {
            $query->whereIn('type', array_intersect(
                (array) $request->input('job_type'),
                array_keys($this->jobTypeOptions()),
            ));
        }

        if ($request->filled('experience_level')) {
            $query->whereIn('experience_level', array_intersect(
                (array) $request->input('experience_level'),
                array_keys($this->experienceLevelOptions()),
            ));
        }
    }

    private function jobTypeOptions(): array
    {
        return [
            JobType::FullTime->value => 'Full-time',
            JobType::PartTime->value => 'Part-time',
            JobType::Contract->value => 'Contract',
            JobType::Internship->value => 'Internship',
        ];
    }

    private function experienceLevelOptions(): array
    {
        return [
            ExperienceLevel::Entry->value => 'Entry level',
            ExperienceLevel::Mid->value => 'Mid level',
            ExperienceLevel::Senior->value => 'Senior level',
        ];
    }
}
