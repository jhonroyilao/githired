<?php

namespace App\Http\Requests\Applicant\Onboarding;

use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Enums\WorkPreference;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Applicant->value;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'desired_job_type' => ['required', Rule::enum(JobType::class)],
            'work_preference' => ['required', Rule::enum(WorkPreference::class)],
            'experience_level' => ['required', Rule::enum(ExperienceLevel::class)],
        ];
    }

    /**
     * @return array{desired_job_type: string, work_preference: string, experience_level: string}
     */
    public function preferenceAttributes(): array
    {
        $validated = $this->validated();

        return [
            'desired_job_type' => $validated['desired_job_type'],
            'work_preference' => $validated['work_preference'],
            'experience_level' => $validated['experience_level'],
        ];
    }
}
