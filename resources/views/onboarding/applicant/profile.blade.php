@php
    $labelClass = 'mb-1.5 block text-base font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25 disabled:text-neutral-600';
    $helpClass = 'mt-1.5 text-xs font-bold text-neutral-600/70';
    $errorClass = 'mt-1.5 text-sm font-bold text-signal-red';
    $secondaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-6 py-3 text-lg font-black text-neutral-900 no-underline transition hover:-translate-y-0.5 max-sm:w-full';
    $primaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 text-lg font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
@endphp

<x-onboarding-shell title="Tell us about yourself" :step="2" :total="5">
    <form method="POST" action="{{ route('applicant.onboarding.profile.store') }}" enctype="multipart/form-data" class="mt-5">
        @csrf

        <div class="mb-4 flex items-center gap-4 max-sm:flex-col max-sm:items-start">
            <div class="size-24 shrink-0 overflow-hidden rounded-full border-[0.35rem] border-primarygreen-100 bg-primarygreen-100">
                <img
                    id="avatar-preview"
                    src="{{ $profile?->avatar_path ? asset('storage/'.$profile->avatar_path) : asset('assets/avatar.svg') }}"
                    alt=""
                    class="h-full w-full object-cover"
                    data-placeholder-src="{{ asset('assets/avatar.svg') }}"
                >
            </div>

            <div>
                <label for="avatar" class="{{ $labelClass }}">Profile picture</label>
                <div class="flex flex-wrap items-center gap-3">
                    <label for="avatar" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-5 text-base font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5">
                        Upload image
                    </label>
                    <span id="avatar-file-name" class="text-sm font-extrabold text-neutral-600" aria-live="polite">
                        {{ $profile?->avatar_path ? 'Current image saved' : 'No image selected' }}
                    </span>
                    <button
                        type="button"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-5 text-base font-black text-neutral-900 transition hover:-translate-y-0.5"
                        data-remove-upload="avatar"
                    >
                        Remove
                    </button>
                </div>
                <input id="avatar-remove" name="remove_avatar" type="hidden" value="0">
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
                <p class="{{ $helpClass }}">PNG or JPEG under 10 MB. Optional for now.</p>
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

            <div>
                <label for="email" class="{{ $labelClass }}">Email address</label>
                <input id="email" type="email" value="{{ $user->email }}" class="{{ $inputClass }}" disabled>
                <p class="{{ $helpClass }}">Email changes will be handled in account settings.</p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('register') }}" class="{{ $secondaryButtonClass }}">Back</a>
            <button type="submit" class="{{ $primaryButtonClass }}">Save changes</button>
        </div>
    </form>
</x-onboarding-shell>
