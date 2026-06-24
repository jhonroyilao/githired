<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:job_categories,id'],

            'title' => ['required', 'string', 'max:255'],

            'location' => ['required', 'string', 'max:255'],

            'location_type' => [
                'required',
                'in:remote,onsite,hybrid',
            ],

            'type' => [
                'required',
                'in:full-time,part-time,contract,internship',
            ],

            'experience_level' => [
                'required',
                'in:entry,mid,senior',
            ],

            'description' => ['required', 'string'],

            'requirements' => ['required', 'string'],

            'skills_required' => ['nullable', 'string'],

            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],

            'expires_at' => ['nullable', 'date', 'after:today'],
            
            'salary_currency' => ['nullable', 'string', 'max:10'],
        ];
    }
}