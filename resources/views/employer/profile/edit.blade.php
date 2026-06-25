@php
    $labelClass = 'mb-1.5 block text-base font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $helpClass = 'mt-1.5 text-xs font-bold text-neutral-600/70';
    $errorClass = 'mt-1.5 text-sm font-bold text-signal-red';
    $primaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 text-lg font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
    $logoMarkedForRemoval = old('remove_logo') === '1';
    $hasLogo = filled($company?->logo_path) && ! $logoMarkedForRemoval;
    $logoUrl = $hasLogo ? \App\Support\StorageUrl::image($company->logo_path) : null;
    $logoPlaceholder = asset('assets/avatar.svg');
@endphp

<x-dashboard-shell title="Edit Company Profile">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white border-2 border-neutral-200 rounded-2xl p-8 shadow-[4px_4px_0px_0px_rgba(26,35,21,0.06)]">
            
            <div class="mb-8">
                <h1 class="text-3xl font-black text-neutral-950">Company Profile</h1>
                <p class="mt-2 text-neutral-600">Update your company details to help attract top talent.</p>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-2xl border-2 border-primarygreen bg-primarygreen-100 px-4 py-3 font-black text-neutral-900">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('employer.company.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="{{ $labelClass }}">Company Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $company?->name) }}" class="{{ $inputClass }}" required>
                        @error('name')
                            <div class="{{ $errorClass }}">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="slug" class="{{ $labelClass }}">Company Slug</label>
                        <input id="slug" name="slug" type="text" value="{{ old('slug', $company?->slug) }}" class="{{ $inputClass }}">
                        <p class="{{ $helpClass }}">Leave blank to generate from the company name.</p>
                        @error('slug')
                            <div class="{{ $errorClass }}">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="industry" class="{{ $labelClass }}">Industry</label>
                        <input id="industry" name="industry" type="text" value="{{ old('industry', $company?->industry) }}" class="{{ $inputClass }}" required>
                        @error('industry')
                            <div class="{{ $errorClass }}">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="size" class="{{ $labelClass }}">Company Size</label>
                        <input id="size" name="size" type="text" value="{{ old('size', $company?->size) }}" class="{{ $inputClass }}" required>
                        @error('size')
                            <div class="{{ $errorClass }}">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="location" class="{{ $labelClass }}">Location</label>
                    <input id="location" name="location" type="text" value="{{ old('location', $company?->location) }}" class="{{ $inputClass }}" required>
                    @error('location')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="website" class="{{ $labelClass }}">Website</label>
                    <input id="website" name="website" type="url" value="{{ old('website', $company?->website) }}" class="{{ $inputClass }}">
                    @error('website')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div class="border-t border-neutral-100 pt-6">
                    <label for="logo" class="{{ $labelClass }}">Company Logo</label>
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="size-24 rounded-2xl overflow-hidden border-2 border-neutral-200">
                            <img
                                id="logo-preview"
                                src="{{ $logoUrl ?? $logoPlaceholder }}"
                                alt=""
                                class="h-full w-full object-cover"
                                data-placeholder-src="{{ $logoPlaceholder }}"
                            >
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <label for="logo" class="cursor-pointer bg-neutral-900 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-neutral-800">
                                    Change Logo
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

                <div>
                    <label for="description" class="{{ $labelClass }}">Company Description</label>
                    <textarea id="description" name="description" rows="5" class="{{ $inputClass }}">{{ old('description', $company?->description) }}</textarea>
                    @error('description')
                        <div class="{{ $errorClass }}">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <a href="{{ route('employer.dashboard') }}" class="px-6 py-3 font-black text-neutral-600 hover:text-neutral-900">Cancel</a>
                    <button type="submit" class="{{ $primaryButtonClass }}">Save Changes</button>
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
            action="{{ route('employer.company.password.update') }}"
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
        
    </div>
</x-dashboard-shell>
