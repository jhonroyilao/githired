<?php

namespace App\Http\Requests\Employer;

use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Models\JobListing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $companyId = $this->user()?->company?->id;

        if (! $companyId) {
            return false;
        }

        $jobListing = $this->route('jobListing');

        if (! $jobListing instanceof JobListing) {
            return true;
        }

        return $jobListing->company_id === $companyId
            && $jobListing->status !== JobStatus::Closed->value;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:job_categories,id'],

            'title' => ['required', 'string', 'max:255'],

            'location' => ['required', 'string', 'max:255'],

            'location_type' => [
                'required',
                'in:remote,onsite,hybrid',
            ],

            'type' => [
                'required',
                Rule::in(array_map(fn (JobType $type): string => $type->value, JobType::cases())),
            ],

            'experience_level' => [
                'required',
                Rule::in(array_map(fn (ExperienceLevel $level): string => $level->value, ExperienceLevel::cases())),
            ],

            'description' => ['required', 'string'],

            'requirements' => ['required', 'string'],

            'skills_required' => ['nullable', 'string'],

            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],

            'expires_at' => ['nullable', 'date', 'after:today'],

            'salary_currency' => ['nullable', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $input = [];

        foreach ([
            'title',
            'location',
            'location_type',
            'type',
            'experience_level',
            'description',
            'requirements',
            'skills_required',
            'salary_currency',
            'expires_at',
        ] as $field) {
            if (is_string($this->input($field))) {
                $input[$field] = trim($this->input($field));
            }
        }

        if (isset($input['salary_currency'])) {
            $input['salary_currency'] = strtoupper($input['salary_currency']);

            if ($input['salary_currency'] === '') {
                $input['salary_currency'] = null;
            }
        }

        $this->merge($input);
    }

    /**
     * @return array<string, mixed>
     */
    public function jobListingAttributes(): array
    {
        $validated = $this->validated();

        return [
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'location' => $validated['location'],
            'location_type' => $validated['location_type'],
            'type' => $validated['type'],
            'experience_level' => $validated['experience_level'],
            'description' => $validated['description'],
            'requirements' => $validated['requirements'],
            'skills_required' => $this->skillsRequired(),
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'salary_currency' => $validated['salary_currency'] ?? 'PHP',
            'expires_at' => $validated['expires_at'] ?? null,
        ];
    }

    /**
     * @return list<string>
     */
    private function skillsRequired(): array
    {
        $skills = $this->validated('skills_required');

        if (! is_string($skills) || $skills === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $skills)),
            fn (string $skill): bool => $skill !== '',
        ));
    }
}
