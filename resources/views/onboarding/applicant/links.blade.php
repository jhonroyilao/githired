@php
    $labelClass = 'mb-1.5 block text-base font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $helpClass = 'mt-1.5 text-xs font-bold text-neutral-600/70';
    $errorClass = 'mt-1.5 text-sm font-bold text-signal-red';
    $secondaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-6 py-3 text-lg font-black text-neutral-900 no-underline transition hover:-translate-y-0.5 max-sm:w-full';
    $primaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 text-lg font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
    $compactButtonClass = 'inline-flex min-h-10 items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50 px-4 py-2 text-sm font-black text-neutral-900 no-underline transition hover:-translate-y-0.5';
    $dangerButtonClass = 'inline-flex min-h-10 items-center justify-center rounded-xl border-2 border-signal-red bg-neutral-50 px-4 py-2 text-sm font-black text-signal-red transition hover:-translate-y-0.5';
@endphp

<x-onboarding-shell title="Complete your profile" :step="5" :total="5">
    <form id="onboarding-links-form" method="POST" action="{{ route('applicant.onboarding.links.store') }}" class="mt-5">
        @csrf

        <div>
            <label for="github" class="{{ $labelClass }}">GitHub</label>
            <input id="github" name="github" type="url" value="{{ old('github', $profile?->github) }}" class="{{ $inputClass }}" placeholder="https://github.com/username">
            @error('github')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="linkedin" class="{{ $labelClass }}">LinkedIn</label>
            <input id="linkedin" name="linkedin" type="url" value="{{ old('linkedin', $profile?->linkedin) }}" class="{{ $inputClass }}" placeholder="https://www.linkedin.com/in/username">
            @error('linkedin')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="website" class="{{ $labelClass }}">Website or portfolio</label>
            <input id="website" name="website" type="url" value="{{ old('website', $profile?->website) }}" class="{{ $inputClass }}" placeholder="https://yourportfolio.com">
            <p class="{{ $helpClass }}">Add at least one link so employers have somewhere to learn more.</p>
            @error('website')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

    </form>

    <section class="mt-4 rounded-2xl border-2 border-neutral-950/70 bg-neutral-50 p-4 shadow-pressed" aria-label="Resume upload">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-primarygreen-700">Resume packet</p>
                <h2 class="mt-1 text-xl font-black text-neutral-950">Upload your PDF resume</h2>
                <p class="mt-1 text-sm font-bold leading-6 text-neutral-600">Optional for onboarding. The newest upload becomes your current resume for applications.</p>
            </div>
            <span class="shrink-0 rounded-xl bg-primarygreen-100 px-4 py-2 text-sm font-black text-neutral-900">Optional</span>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-xl border-2 border-primarygreen bg-primarygreen-100 px-4 py-3 text-sm font-black text-neutral-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($currentResume)
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-neutral-100 p-3">
                <div>
                    <p class="font-black text-neutral-900">{{ $currentResume->original_name ?? 'Resume PDF' }}</p>
                    <p class="text-xs font-bold text-neutral-600">Current resume</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('applicant.resume.show', $currentResume) }}" class="{{ $compactButtonClass }}">Download</a>
                    <form method="POST" action="{{ route('applicant.resume.destroy', $currentResume) }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="redirect_to" value="applicant.onboarding.links">
                        <button type="submit" class="{{ $dangerButtonClass }}">Delete</button>
                    </form>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('applicant.resume.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3" data-resume-upload-form>
            @csrf
            <input type="hidden" name="redirect_to" value="applicant.onboarding.links">
            <input type="hidden" name="github" data-preserve-link="github">
            <input type="hidden" name="linkedin" data-preserve-link="linkedin">
            <input type="hidden" name="website" data-preserve-link="website">

            <div>
                <label for="resume" class="{{ $labelClass }}">Resume file</label>
                <input id="resume" name="resume" type="file" accept="application/pdf" class="{{ $inputClass }} file:mr-4 file:rounded-lg file:border-0 file:bg-primarygreen file:px-4 file:py-2 file:text-sm file:font-black file:text-neutral-900">
                <div class="mt-1.5 flex flex-wrap items-center gap-3">
                    <p class="text-xs font-bold text-neutral-600/70">PDF only, 5 MB maximum.</p>
                    <button type="button" class="hidden text-xs font-black text-signal-red underline decoration-2 underline-offset-4" data-clear-resume-file>
                        Remove selected file
                    </button>
                </div>
                @error('resume')
                    <div class="{{ $errorClass }}">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="{{ $primaryButtonClass }}">Upload resume</button>
        </form>
    </section>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('applicant.onboarding.preferences') }}" class="{{ $secondaryButtonClass }}">Back</a>
        <button type="submit" form="onboarding-links-form" class="{{ $primaryButtonClass }}">Finish profile</button>
    </div>

    <script>
        document.querySelectorAll('[data-resume-upload-form]').forEach((form) => {
            const input = form.querySelector('input[type="file"]');
            const clearButton = form.querySelector('[data-clear-resume-file]');

            if (! input || ! clearButton) {
                return;
            }

            input.addEventListener('change', () => {
                clearButton.classList.toggle('hidden', input.files.length === 0);
            });

            clearButton.addEventListener('click', () => {
                input.value = '';
                clearButton.classList.add('hidden');
                input.focus();
            });

            form.addEventListener('submit', () => {
                form.querySelectorAll('[data-preserve-link]').forEach((field) => {
                    const source = document.querySelector(`[name="${field.dataset.preserveLink}"]`);

                    field.value = source ? source.value : '';
                });
            });
        });
    </script>
</x-onboarding-shell>
