<?php

namespace App\Http\Requests\Applicant\Onboarding;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class StoreLinksRequest extends FormRequest
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
            'github' => ['nullable', 'url', 'max:255', 'required_without_all:linkedin,website'],
            'linkedin' => ['nullable', 'url', 'max:255', 'required_without_all:github,website'],
            'website' => ['nullable', 'url', 'max:255', 'required_without_all:github,linkedin'],
        ];
    }

    /**
     * @return array{github?: string|null, linkedin?: string|null, website?: string|null}
     */
    public function linkAttributes(): array
    {
        $validated = $this->validated();

        return [
            'github' => $validated['github'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'website' => $validated['website'] ?? null,
        ];
    }
}
