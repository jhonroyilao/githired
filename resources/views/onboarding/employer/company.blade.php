@php
    $labelClass = 'mb-2 block text-[0.98rem] font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $helpClass = 'mt-2 text-sm font-bold text-neutral-600/70';
    $errorClass = 'mt-2 text-sm font-bold text-signal-red';
    $secondaryButtonClass = 'inline-flex min-h-[3.35rem] min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-6 py-3 font-black text-neutral-900 no-underline transition hover:-translate-y-0.5 max-sm:w-full';
    $primaryButtonClass = 'inline-flex min-h-[3.35rem] min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
    $logoMarkedForRemoval = old('remove_logo') === '1';
    $hasLogo = filled($company?->logo_path) && ! $logoMarkedForRemoval;
    $logoPlaceholder = asset('assets/avatar.svg');
@endphp

<x-onboarding-shell title="Set up your company" :step="2" :total="2">
    <form method="POST" action="{{ route('employer.onboarding.company.store') }}" enctype="multipart/form-data" class="mt-8">
        @csrf

        <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
            <div>
                <label for="name" class="{{ $labelClass }}">Company name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $company?->name) }}" class="{{ $inputClass }}" placeholder="Acme Careers" required>
                @error('name')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="slug" class="{{ $labelClass }}">Company slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $company?->slug) }}" class="{{ $inputClass }}" placeholder="acme-careers">
                <p class="{{ $helpClass }}">Leave blank to generate from the company name.</p>
                @error('slug')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="industry" class="{{ $labelClass }}">Industry</label>
                <input id="industry" name="industry" type="text" value="{{ old('industry', $company?->industry) }}" class="{{ $inputClass }}" placeholder="Software" required>
                @error('industry')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="size" class="{{ $labelClass }}">Company size</label>
                <input id="size" name="size" type="text" value="{{ old('size', $company?->size) }}" class="{{ $inputClass }}" placeholder="11-50 employees" required>
                @error('size')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-5">
            <label for="location" class="{{ $labelClass }}">Location</label>
            <input id="location" name="location" type="text" value="{{ old('location', $company?->location) }}" class="{{ $inputClass }}" placeholder="Manila, Philippines" required>
            @error('location')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-5">
            <label for="website" class="{{ $labelClass }}">Website</label>
            <input id="website" name="website" type="url" value="{{ old('website', $company?->website) }}" class="{{ $inputClass }}" placeholder="https://company.com">
            @error('website')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-5">
            <label for="logo" class="{{ $labelClass }}">Company logo</label>
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex size-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-neutral-950/45 bg-primarygreen-100/60">
                    <img
                        id="logo-preview"
                        src="{{ $hasLogo ? asset('storage/'.$company->logo_path) : $logoPlaceholder }}"
                        alt=""
                        class="h-full w-full object-cover"
                        data-placeholder-src="{{ $logoPlaceholder }}"
                    >
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <label for="logo" class="inline-flex min-h-10 cursor-pointer items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-4 text-sm font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5">
                            Upload logo
                        </label>
                        <button
                            type="button"
                            class="inline-flex size-10 items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 text-neutral-900 transition hover:-translate-y-0.5 hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primarygreen/25"
                            data-remove-upload="logo"
                            data-remove-button="logo"
                            aria-label="Clear uploaded logo"
                            title="Clear uploaded logo"
                            @unless($hasLogo) hidden @endunless
                        >
                            <i class="bi bi-x-lg text-base" aria-hidden="true"></i>
                            <span class="sr-only">Clear uploaded logo</span>
                        </button>
                    </div>
                    <span id="logo-file-name" class="mt-2 block text-sm font-extrabold text-neutral-600" aria-live="polite">
                        {{ $logoMarkedForRemoval ? 'File will be cleared on save' : ($hasLogo ? 'Current logo saved' : 'No logo selected') }}
                    </span>
                </div>
            </div>
            <input id="logo-remove" name="remove_logo" type="hidden" value="{{ $logoMarkedForRemoval ? '1' : '0' }}">
            <input
                id="logo"
                name="logo"
                type="file"
                accept="image/*"
                class="sr-only"
                data-file-name-target="logo-file-name"
                data-preview-target="logo-preview"
                data-remove-target="logo-remove"
            >
            <p class="{{ $helpClass }}">Optional PNG or JPEG under 10 MB.</p>
            @error('logo')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-5">
            <label for="description" class="{{ $labelClass }}">Company description</label>
            <textarea id="description" name="description" rows="5" class="{{ $inputClass }} min-h-[8.5rem] resize-y" placeholder="Tell job seekers what your team builds and how you work." required>{{ old('description', $company?->description) }}</textarea>
            @error('description')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-10 flex flex-wrap gap-3">
            <a href="{{ route('register') }}" class="{{ $secondaryButtonClass }}">Back</a>
            <button type="submit" class="{{ $primaryButtonClass }}">Finish setup</button>
        </div>
    </form>
</x-onboarding-shell>
