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
            'resume'       => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5 MB
        ];
    }

    public function messages(): array
    {
        return [
            'resume.mimes' => 'Your résumé must be a PDF, DOC, or DOCX file.',
            'resume.max'   => 'Your résumé may not be larger than 5 MB.',
        ];
    }
}