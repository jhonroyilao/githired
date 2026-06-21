@php
    $labelClass = 'mb-1.5 block text-base font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $helpClass = 'mt-1.5 text-xs font-bold text-neutral-600/70';
    $errorClass = 'mt-1.5 text-sm font-bold text-signal-red';
    $secondaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-6 py-3 text-lg font-black text-neutral-900 no-underline transition hover:-translate-y-0.5 max-sm:w-full';
    $primaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 text-lg font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
@endphp

<x-onboarding-shell title="Complete your profile" :step="5" :total="5">
    <form method="POST" action="{{ route('applicant.onboarding.links.store') }}" class="mt-5">
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

        <div class="mt-4 flex items-center justify-between gap-4 rounded-2xl border-2 border-dashed border-neutral-950/45 bg-primarygreen-100/60 p-3.5 max-sm:flex-col max-sm:items-start" aria-label="Resume upload placeholder">
            <div>
                <p class="font-black text-neutral-900">Resume upload</p>
                <p class="mt-1 text-sm font-bold text-neutral-600">Coming soon. You can finish onboarding without a resume.</p>
            </div>
            <span class="shrink-0 rounded-xl bg-neutral-50/80 px-4 py-2.5 font-black text-neutral-600/75">Optional</span>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('applicant.onboarding.preferences') }}" class="{{ $secondaryButtonClass }}">Back</a>
            <button type="submit" class="{{ $primaryButtonClass }}">Finish profile</button>
        </div>
    </form>
</x-onboarding-shell>
