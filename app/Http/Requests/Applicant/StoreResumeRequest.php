<?php

namespace App\Http\Requests\Applicant;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class StoreResumeRequest extends FormRequest
{
    private const MAX_FILE_SIZE_KB = 5120;

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
            'resume' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf',
                'max:'.self::MAX_FILE_SIZE_KB,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'resume.required' => 'Choose a resume PDF to upload.',
            'resume.mimes' => 'The resume must be a PDF file.',
            'resume.mimetypes' => 'The resume must be a PDF file.',
            'resume.max' => 'The resume must be 5 MB or smaller.',
        ];
    }
}
