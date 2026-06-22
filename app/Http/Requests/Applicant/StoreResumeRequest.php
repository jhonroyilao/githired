<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
{
    public const MAX_FILE_SIZE_KB = 5120; //5MB max file size

    //Allow anyone logged in to use request
    public function authorize()
    {
        return true; 
    }

    //Validation for the file upload
    public function rules()
    {
        return [
            'resume' => [
                'required',
                'file',
                'mimes:pdf', 
                'mimetypes:application/pdf', //Make sure it is really a PDF
                'max:'.self::MAX_FILE_SIZE_KB,
            ],
        ];
    }

    
    //Error messages if they upload the wrong thing
    public function messages()
    {
        return [
            'resume.required' => 'Please choose a resume file to upload.',
            'resume.mimes' => 'Your resume must be a PDF file.',
            'resume.mimetypes' => 'Your resume must be a PDF file.',
            'resume.max' => 'Your resume must be smaller than '.(self::MAX_FILE_SIZE_KB / 1024).' MB.',
        ];
    }
}