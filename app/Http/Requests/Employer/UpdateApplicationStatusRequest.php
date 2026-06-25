<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationStatusRequest extends FormRequest
{
    //Employers may only update applications on jobs they own.
    public function authorize(): bool
    {
        /** @var \App\Models\JobListing $jobListing */
        $jobListing = $this->route('jobListing');

        return $jobListing && $this->user()->id === $jobListing->user_id;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['pending', 'interview', 'hired', 'rejected'])],
            'note'   => ['nullable', 'string', 'max:1000'],
        ];
    }
}