<?php

namespace App\Http\Requests\Applicant\Onboarding;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class StoreSummaryRequest extends FormRequest
{
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
