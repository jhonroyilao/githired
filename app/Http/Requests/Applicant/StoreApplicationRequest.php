<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

final class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Route middleware enforces applicant role
    }

    public function rules(): array
    {
        return [
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'resume' => ['nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'resume.mimes' => 'Your resume must be a PDF file.',
            'resume.mimetypes' => 'Your resume must be a PDF file.',
            'resume.max' => 'Your resume may not be larger than 5 MB.',
        ];
    }
}
