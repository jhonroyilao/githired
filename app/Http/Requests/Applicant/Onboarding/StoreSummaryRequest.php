<?php

namespace App\Http\Requests\Applicant\Onboarding;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class StoreSummaryRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'headline' => is_string($this->input('headline')) ? trim($this->input('headline')) : $this->input('headline'),
            'bio' => is_string($this->input('bio')) ? trim($this->input('bio')) : $this->input('bio'),
            'skills' => is_string($this->input('skills')) ? trim($this->input('skills')) : $this->input('skills'),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Applicant->value;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'headline' => ['required', 'string', 'max:160'],
            'bio' => ['required', 'string', 'max:1000'],
            'skills' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array{headline: string, bio: string, skills: array<int, string>}
     */
    public function summaryAttributes(): array
    {
        $validated = $this->validated();

        $skills = collect(explode(',', $validated['skills']))
            ->map(fn (string $skill): string => trim($skill))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'headline' => $validated['headline'],
            'bio' => $validated['bio'],
            'skills' => $skills,
        ];
    }
}
