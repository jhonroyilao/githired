@php
    $labelClass = 'mb-1.5 block text-base font-extrabold text-neutral-950';
    $inputClass = 'w-full rounded-[0.875rem] border-2 border-neutral-950/70 bg-neutral-50/80 px-4 py-3 text-base text-neutral-900 transition focus:border-neutral-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primarygreen/25';
    $helpClass = 'mt-1.5 text-xs font-bold text-neutral-600/70';
    $errorClass = 'mt-1.5 text-sm font-bold text-signal-red';
    $secondaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-neutral-900 bg-neutral-50/75 px-6 py-3 text-lg font-black text-neutral-900 no-underline transition hover:-translate-y-0.5 max-sm:w-full';
    $primaryButtonClass = 'inline-flex min-h-12 min-w-[11.5rem] items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-6 py-3 text-lg font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5 max-sm:w-full';
@endphp

<x-onboarding-shell title="Tell employers about yourself" :step="3" :total="5">
    <form method="POST" action="{{ route('applicant.onboarding.summary.store') }}" class="mt-5">
        @csrf

        <div>
            <label for="headline" class="{{ $labelClass }}">Professional headline</label>
            <input id="headline" name="headline" type="text" value="{{ old('headline', $profile?->headline) }}" class="{{ $inputClass }}" placeholder="Frontend Developer | UI/UX Designer" required>
            @error('headline')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="bio" class="{{ $labelClass }}">Short bio</label>
            <textarea id="bio" name="bio" rows="3" class="{{ $inputClass }} min-h-[6.5rem] resize-y" placeholder="Keep it concise and specific." required>{{ old('bio', $profile?->bio) }}</textarea>
            @error('bio')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="skills" class="{{ $labelClass }}">Skills</label>
            <input id="skills" name="skills" type="text" value="{{ old('skills', implode(', ', $profile?->skills ?? [])) }}" class="{{ $inputClass }}" placeholder="Laravel, Tailwind CSS, React" required>
            <p class="{{ $helpClass }}">Separate skills with commas.</p>
            @error('skills')
                <div class="{{ $errorClass }}">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('applicant.onboarding.profile') }}" class="{{ $secondaryButtonClass }}">Back</a>
            <button type="submit" class="{{ $primaryButtonClass }}">Next</button>
        </div>
    </form>
</x-onboarding-shell>
