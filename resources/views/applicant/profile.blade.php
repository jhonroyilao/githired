@php
    $labelClass = 'mb-1.5 block text-base font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $helpClass = 'mt-1.5 text-xs font-bold text-neutral-600/70';
    $errorClass = 'mt-1.5 text-sm font-bold text-signal-red';
    $secondaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-6 py-3 text-lg font-black text-neutral-900 no-underline transition hover:-translate-y-0.5 max-sm:w-full';
    $primaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 text-lg font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
    $avatarMarkedForRemoval = old('remove_avatar') === '1';
    $hasAvatar = filled($profile?->avatar_path) && ! $avatarMarkedForRemoval;
    $avatarUrl = $hasAvatar ? \App\Support\StorageUrl::image($profile->avatar_path) : null;
    $avatarPlaceholder = asset('assets/avatar.svg');
@endphp

<x-dashboard-shell title="Edit applicant profile" eyebrow="Job seeker workspace" :user="$user">
    <p class="mt-5 max-w-7xl text-lg font-bold leading-8 text-neutral-600">
        Keep the details employers and future matching features use to understand your work, preferences, and links.
    </p>

    @if (session('status'))
        <div class="mt-5 rounded-2xl border-2 border-primarygreen bg-primarygreen-100 px-4 py-3 font-black text-neutral-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-8">
        <h2 class="text-3xl font-black text-neutral-950">
            Edit Profile
        </h2>

        <p class="mt-2 text-neutral-600">
            Update your personal information, work preferences, and professional links.
        </p>
    </div>

    <form method="POST" action="{{ route('applicant.profile.update') }}" enctype="multipart/form-data" class="mt-7">
        @csrf
        @method('PUT')

        <div class="mb-6 flex items-center gap-4 max-sm:flex-col max-sm:items-start">
            <div class="size-24 shrink-0 overflow-hidden rounded-full border-[0.35rem] border-primarygreen-100 bg-primarygreen-100">
                <img
                    id="avatar-preview"
                    src="{{ $avatarUrl ?? $avatarPlaceholder }}"
                    alt="{{ $user->name }}"
                    class="h-full w-full object-cover"
                    data-placeholder-src="{{ $avatarPlaceholder }}"
                >
            </div>

            <div>
                <label for="avatar" class="{{ $labelClass }}">Profile picture</label>
                <div class="flex flex-wrap items-center gap-3">
                    <label for="avatar" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-5 text-base font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5">
                        Upload image
                    </label>
                    <span id="avatar-file-name" class="text-sm font-extrabold text-neutral-600" aria-live="polite">
                        {{ $avatarMarkedForRemoval ? 'File will be cleared on save' : ($hasAvatar ? 'Current image saved' : 'No image selected') }}
                    </span>
                    <button
                        type="button"
                        class="inline-flex size-11 items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 text-neutral-900 transition hover:-translate-y-0.5 hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primarygreen/25"
                        data-remove-upload="avatar"
                        data-remove-button="avatar"
                        aria-label="Clear uploaded image"
                        title="Clear uploaded image"
                        @unless($hasAvatar) hidden @endunless
                    >
                        <i class="bi bi-x-lg text-lg" aria-hidden="true"></i>
                        <span class="sr-only">Clear uploaded image</span>
                    </button>
                </div>
                <input id="avatar-remove" name="remove_avatar" type="hidden" value="{{ $avatarMarkedForRemoval ? '1' : '0' }}">
                <input
                    id="avatar"
                    name="avatar"
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    data-file-name-target="avatar-file-name"
                    data-preview-target="avatar-preview"
                    data-remove-target="avatar-remove"
                >
                <p class="{{ $helpClass }}">PNG or JPEG under 10 MB.</p>
                @error('avatar')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>
        </div>

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

    <div class="mt-20 border-t border-neutral-200 pt-12">

        <h2 class="text-3xl font-black text-neutral-950">
            Change Password
        </h2>

        <p class="mt-2 text-neutral-600">
            Update your password to keep your account secure.
        </p>

        <form
            method="POST"
            action="{{ route('applicant.password.update') }}"
            class="mt-6"
        >
            @csrf
            @method('PUT')

            <div class="space-y-5">

                <div>
                    <label for="current_password" class="{{ $labelClass }}">
                        Current Password
                    </label>

                    <div class="relative">
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            class="{{ $inputClass }} pr-12"
                            placeholder="Enter your current password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="absolute inset-y-0 right-2 my-auto inline-flex size-9 items-center justify-center rounded-full text-neutral-600 transition hover:bg-neutral-200/70 hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primarygreen/25"
                            data-password-toggle="current_password"
                            aria-label="Show password"
                            title="Show password"
                        >
                            <i
                                class="bi bi-eye-fill text-lg"
                                aria-hidden="true"
                                data-password-icon
                            ></i>

                            <span class="sr-only" data-password-label>
                                Show password
                            </span>
                        </button>
                    </div>

                    @error('current_password')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="password" class="{{ $labelClass }}">
                        New Password
                    </label>

                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            minlength="8"
                            class="{{ $inputClass }} pr-12"
                            placeholder="Enter a new password"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="absolute inset-y-0 right-2 my-auto inline-flex size-9 items-center justify-center rounded-full text-neutral-600 transition hover:bg-neutral-200/70 hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primarygreen/25"
                            data-password-toggle="password"
                            aria-label="Show password"
                            title="Show password"
                        >
                            <i
                                class="bi bi-eye-fill text-lg"
                                aria-hidden="true"
                                data-password-icon
                            ></i>

                            <span class="sr-only" data-password-label>
                                Show password
                            </span>
                        </button>
                    </div>

                    <p class="{{ $helpClass }}">
                        Password must be at least 8 characters long.
                    </p>

                    @error('password')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="{{ $labelClass }}">
                        Confirm Password
                    </label>

                    <div class="relative">
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            minlength="8"
                            class="{{ $inputClass }} pr-12"
                            placeholder="Confirm your new password"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="absolute inset-y-0 right-2 my-auto inline-flex size-9 items-center justify-center rounded-full text-neutral-600 transition hover:bg-neutral-200/70 hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primarygreen/25"
                            data-password-toggle="password_confirmation"
                            aria-label="Show password"
                            title="Show password"
                        >
                            <i
                                class="bi bi-eye-fill text-lg"
                                aria-hidden="true"
                                data-password-icon
                            ></i>

                            <span class="sr-only" data-password-label>
                                Show password
                            </span>
                        </button>
                    </div>
                </div>

            </div>

            <button
                type="submit"
                class="mt-6 {{ $primaryButtonClass }}"
            >
                Update Password
            </button>

        </form>
    </div>
</x-dashboard-shell>
