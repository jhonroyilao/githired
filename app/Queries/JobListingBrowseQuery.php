<?php

namespace App\Queries;

use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

final class JobListingBrowseQuery
{
    public function apply(Builder $query, Request $request, bool $includeDatePosted = false): Builder
    {
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $operator = $this->caseInsensitiveLikeOperator($query);
            $pattern = "%{$search}%";

            $query->where(function (Builder $query) use ($operator, $pattern): void {
                $query->where('title', $operator, $pattern)
                    ->orWhereHas('company', function (Builder $query) use ($operator, $pattern): void {
                        $query->where('name', $operator, $pattern);
                    });
            });
        }

        if ($request->filled('location')) {
            $query->where('location', $request->string('location')->toString());
        }

        $categories = $this->values($request, 'category');
        if ($categories !== []) {
            $query->whereHas('category', function (Builder $query) use ($categories): void {
                $query->whereIn('slug', $categories);
            });
        }

        $jobTypes = array_intersect($this->values($request, 'job_type'), array_keys($this->jobTypeOptions()));
        if ($jobTypes !== []) {
            $query->whereIn('type', $jobTypes);
        }

        $experienceLevels = array_intersect($this->values($request, 'experience_level'), array_keys($this->experienceOptions()));
        if ($experienceLevels !== []) {
            $query->whereIn('experience_level', $experienceLevels);
        }

        if ($includeDatePosted) {
            $this->applyDatePostedFilter($query, $this->values($request, 'date_posted'));
        }

        return $query;
    }

    public function jobTypeOptions(): array
    {
        return [
            JobType::FullTime->value => 'Full-time',
            JobType::PartTime->value => 'Part-time',
            JobType::Contract->value => 'Contract',
            JobType::Internship->value => 'Internship',
        ];
    }

    public function experienceOptions(): array
    {
        return [
            ExperienceLevel::Entry->value => 'Entry level',
            ExperienceLevel::Mid->value => 'Mid level',
            ExperienceLevel::Senior->value => 'Senior level',
        ];
    }

    public function datePostedOptions(): array
    {
        return [
            'All' => 'All',
            'Last Hour' => 'Last hour',
            'Last 24 Hours' => 'Last 24 hours',
            'Last 7 Days' => 'Last 7 days',
            'Last 30 Days' => 'Last 30 days',
        ];
    }

    private function applyDatePostedFilter(Builder $query, array $dates): void
    {
        $dates = array_intersect($dates, array_keys($this->datePostedOptions()));

        if ($dates === [] || in_array('All', $dates, true)) {
            return;
        }

        $query->where(function (Builder $query) use ($dates): void {
            foreach ($dates as $date) {
                match ($date) {
                    'Last Hour' => $query->orWhere('created_at', '>=', now()->subHour()),
                    'Last 24 Hours' => $query->orWhere('created_at', '>=', now()->subDay()),
                    'Last 7 Days' => $query->orWhere('created_at', '>=', now()->subDays(7)),
                    'Last 30 Days' => $query->orWhere('created_at', '>=', now()->subDays(30)),
                    default => null,
                };
            }
        });
    }

    private function values(Request $request, string $key): array
    {
        return array_values(array_filter(
            Arr::wrap($request->input($key)),
            fn ($value): bool => is_string($value) && $value !== '',
        ));
    }

    private function caseInsensitiveLikeOperator(Builder $query): string
    {
        return $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
    }
}
