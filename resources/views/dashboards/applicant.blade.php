<x-dashboard-shell title="Applicant dashboard" eyebrow="Job seeker workspace" :user="$user">
    <p class="mt-5 max-w-2xl text-lg font-bold leading-8 text-neutral-600">
        Your onboarding profile is complete enough to start using GitHired. Resume upload is still coming later, so it is not blocking dashboard access.
    </p>

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-sm font-black text-neutral-600">Headline</p>
            <p class="mt-2 font-black text-neutral-900">{{ $profile?->headline ?? 'Not set' }}</p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-sm font-black text-neutral-600">Preference</p>
            <p class="mt-2 font-black text-neutral-900">{{ $profile?->work_preference ?? 'Not set' }}</p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-sm font-black text-neutral-600">Links</p>
            <a href="{{ route('applicant.profile.edit') }}" class="mt-2 inline-flex font-black text-neutral-900 underline decoration-primarygreen decoration-4 underline-offset-4">
                Edit profile
            </a>
        </div>
    </div>
</x-dashboard-shell>
