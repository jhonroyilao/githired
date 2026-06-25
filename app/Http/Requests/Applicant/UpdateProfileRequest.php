<?php

namespace App\Http\Requests\Applicant;

use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Enums\WorkPreference;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

final class UpdateProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->trimString('name'),
            'headline' => $this->trimString('headline'),
            'bio' => $this->trimString('bio'),
            'location' => $this->trimString('location'),
            'phone' => $this->trimString('phone'),
            'website' => $this->trimString('website'),
            'linkedin' => $this->trimString('linkedin'),
            'github' => $this->trimString('github'),
            'desired_job_type' => $this->trimString('desired_job_type'),
            'work_preference' => $this->trimString('work_preference'),
            'experience_level' => $this->trimString('experience_level'),
            'skills' => $this->trimString('skills'),
        ]);
    }

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
            'name' => ['required', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:160'],
            'bio' => ['required', 'string', 'max:1000'],
            'location' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'website' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'desired_job_type' => ['required', Rule::enum(JobType::class)],
            'work_preference' => ['required', Rule::enum(WorkPreference::class)],
            'experience_level' => ['required', Rule::enum(ExperienceLevel::class)],
            'skills' => ['required', 'string', 'max:500', 'regex:/[^,\s]/'],
            'avatar' => ['nullable', 'image', 'max:10240'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array{name: string, headline: string, bio: string, location: string, phone: string, website?: string|null, linkedin?: string|null, github?: string|null, desired_job_type: string, work_preference: string, experience_level: string, skills: array<int, string>, avatar?: UploadedFile|null, remove_avatar: bool}
     */
    public function profileAttributes(): array
    {
        $validated = $this->validated();

        return [
            'name' => $validated['name'],
            'headline' => $validated['headline'],
            'bio' => $validated['bio'],
            'location' => $validated['location'],
            'phone' => $validated['phone'],
            'website' => $validated['website'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'github' => $validated['github'] ?? null,
            'desired_job_type' => $validated['desired_job_type'],
            'work_preference' => $validated['work_preference'],
            'experience_level' => $validated['experience_level'],
            'skills' => $this->normalizedSkills($validated['skills']),
            'avatar' => $this->file('avatar'),
            'remove_avatar' => $this->boolean('remove_avatar'),
        ];
    }

    private function trimString(string $key): mixed
    {
        $value = $this->input($key);

        return is_string($value) ? trim($value) : $value;
    }

    /**
     * @return array<int, string>
     */
    private function normalizedSkills(string $skills): array
    {
        return collect(explode(',', $skills))
            ->map(fn (string $skill): string => trim($skill))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
