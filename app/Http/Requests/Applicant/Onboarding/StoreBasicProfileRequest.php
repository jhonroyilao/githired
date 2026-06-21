<?php

namespace App\Http\Requests\Applicant\Onboarding;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

final class StoreBasicProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'avatar' => ['nullable', 'image', 'max:10240'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array{name: string, location: string, phone: string, avatar?: UploadedFile|null, remove_avatar: bool}
     */
    public function profileAttributes(): array
    {
        $validated = $this->validated();

        return [
            'name' => $validated['name'],
            'location' => $validated['location'],
            'phone' => $validated['phone'],
            'avatar' => $this->file('avatar'),
            'remove_avatar' => $this->boolean('remove_avatar'),
        ];
    }
}
