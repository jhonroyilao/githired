@php
    $labelClass = 'mb-2 block text-base font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $errorClass = 'mt-1.5 text-sm font-bold text-signal-red';
    $secondaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-6 py-3 text-lg font-black text-neutral-900 no-underline transition hover:-translate-y-0.5 max-sm:w-full';
    $primaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 text-lg font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
@endphp

<x-onboarding-shell title="What opportunities are you looking for?" :step="4" :total="5">
    <form method="POST" action="{{ route('applicant.onboarding.preferences.store') }}" class="mt-6">
        @csrf

        <div class="grid gap-x-7 gap-y-4 sm:grid-cols-2">
            <div>
                <label for="desired_job_type" class="{{ $labelClass }}">Job type</label>
                <select id="desired_job_type" name="desired_job_type" class="{{ $inputClass }}" required>
                    @foreach ($jobTypes as $jobType)
                        <option value="{{ $jobType->value }}" @selected(old('desired_job_type', $profile?->desired_job_type) === $jobType->value)>
                            {{ \Illuminate\Support\Str::headline($jobType->value) }}
                        </option>
                    @endforeach
                </select>
                @error('desired_job_type')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="work_preference" class="{{ $labelClass }}">Work preference</label>
                <select id="work_preference" name="work_preference" class="{{ $inputClass }}" required>
                    @foreach ($workPreferences as $workPreference)
                        <option value="{{ $workPreference->value }}" @selected(old('work_preference', $profile?->work_preference) === $workPreference->value)>
                            {{ \Illuminate\Support\Str::headline($workPreference->value) }}
                        </option>
                    @endforeach
                </select>
                @error('work_preference')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-5">
            <label for="experience_level" class="{{ $labelClass }}">Experience level</label>
            <select id="experience_level" name="experience_level" class="{{ $inputClass }}" required>
                @foreach ($experienceLevels as $experienceLevel)
                    <option value="{{ $experienceLevel->value }}" @selected(old('experience_level', $profile?->experience_level) === $experienceLevel->value)>
                        {{ \Illuminate\Support\Str::headline($experienceLevel->value) }}
                    </option>
                @endforeach
            </select>
            @error('experience_level')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('applicant.onboarding.summary') }}" class="{{ $secondaryButtonClass }}">Back</a>
            <button type="submit" class="{{ $primaryButtonClass }}">Next</button>
        </div>
    </form>
</x-onboarding-shell>
