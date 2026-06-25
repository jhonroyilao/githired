@php
    $labelClass = 'mb-1.5 block text-base font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $helpClass = 'mt-1.5 text-xs font-bold text-neutral-600/70';
    $errorClass = 'mt-1.5 text-sm font-bold text-signal-red';
    $secondaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-6 py-3 text-lg font-black text-neutral-900 no-underline transition hover:-translate-y-0.5 max-sm:w-full';
    $primaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 text-lg font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
@endphp

<x-dashboard-shell title="Edit applicant profile" eyebrow="Job seeker workspace" :user="$user">
    <p class="mt-5 max-w-2xl text-lg font-bold leading-8 text-neutral-600">
        Keep the details employers and future matching features use to understand your work, preferences, and links.
    </p>

    @if (session('status'))
        <div class="mt-5 rounded-2xl border-2 border-primarygreen bg-primarygreen-100 px-4 py-3 font-black text-neutral-900">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('applicant.profile.update') }}" class="mt-7">
        @csrf
        @method('PUT')

        <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
            <div>
                <label for="name" class="{{ $labelClass }}">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="{{ $inputClass }}" autocomplete="name" required>
                @error('name')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="headline" class="{{ $labelClass }}">Professional headline</label>
                <input id="headline" name="headline" type="text" value="{{ old('headline', $profile?->headline) }}" class="{{ $inputClass }}" placeholder="Frontend Developer | UI/UX Designer" required>
                @error('headline')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="location" class="{{ $labelClass }}">Location</label>
                <input id="location" name="location" type="text" value="{{ old('location', $profile?->location) }}" class="{{ $inputClass }}" placeholder="Philippines" autocomplete="address-level2" required>
                @error('location')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="phone" class="{{ $labelClass }}">Phone number</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $profile?->phone) }}" class="{{ $inputClass }}" placeholder="+63 912 345 6789" autocomplete="tel" required>
                @error('phone')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <label for="bio" class="{{ $labelClass }}">Short bio</label>
            <textarea id="bio" name="bio" rows="4" class="{{ $inputClass }} min-h-[8rem] resize-y" placeholder="Keep it concise and specific." required>{{ old('bio', $profile?->bio) }}</textarea>
            @error('bio')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="skills" class="{{ $labelClass }}">Skills</label>
            <input id="skills" name="skills" type="text" value="{{ old('skills', implode(', ', $profile?->skills ?? [])) }}" class="{{ $inputClass }}" placeholder="Laravel, Tailwind CSS, React" required>
            <p class="{{ $helpClass }}">Separate skills with commas. Duplicates are saved once.</p>
            @error('skills')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-5 grid gap-x-6 gap-y-4 sm:grid-cols-3">
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

            <div>
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
        </div>

        <div class="mt-5 grid gap-x-6 gap-y-4 sm:grid-cols-3">
            <div>
                <label for="github" class="{{ $labelClass }}">GitHub</label>
                <input id="github" name="github" type="url" value="{{ old('github', $profile?->github) }}" class="{{ $inputClass }}" placeholder="https://github.com/username">
                @error('github')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="linkedin" class="{{ $labelClass }}">LinkedIn</label>
                <input id="linkedin" name="linkedin" type="url" value="{{ old('linkedin', $profile?->linkedin) }}" class="{{ $inputClass }}" placeholder="https://www.linkedin.com/in/username">
                @error('linkedin')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="website" class="{{ $labelClass }}">Website or portfolio</label>
                <input id="website" name="website" type="url" value="{{ old('website', $profile?->website) }}" class="{{ $inputClass }}" placeholder="https://yourportfolio.com">
                @error('website')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-7 flex flex-wrap gap-3">
            <a href="{{ route('applicant.dashboard') }}" class="{{ $secondaryButtonClass }}">Back to dashboard</a>
            <button type="submit" class="{{ $primaryButtonClass }}">Save profile</button>
        </div>
    </form>
</x-dashboard-shell>
