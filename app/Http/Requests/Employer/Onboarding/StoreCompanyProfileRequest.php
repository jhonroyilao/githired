<?php

namespace App\Http\Requests\Employer\Onboarding;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class StoreCompanyProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => Str::slug((string) $this->input('name')),
            ]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Employer->value;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('companies', 'slug')->ignore($this->user()?->company?->id),
            ],
            'logo' => ['nullable', 'image', 'max:10240'],
            'remove_logo' => ['nullable', 'boolean'],
            'website' => ['nullable', 'url', 'max:255'],
            'industry' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array{name: string, slug: string, logo?: UploadedFile|null, remove_logo: bool, website?: string|null, industry: string, size: string, location: string, description: string}
     */
    public function companyAttributes(): array
    {
        $validated = $this->validated();

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'logo' => $this->file('logo'),
            'remove_logo' => $this->boolean('remove_logo'),
            'website' => $validated['website'] ?? null,
            'industry' => $validated['industry'],
            'size' => $validated['size'],
            'location' => $validated['location'],
            'description' => $validated['description'],
        ];
    }
}
