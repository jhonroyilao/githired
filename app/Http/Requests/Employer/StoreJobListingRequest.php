<?php

namespace App\Http\Requests\Employer;

use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->company()->exists() ?? false;
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

            'salary_currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
