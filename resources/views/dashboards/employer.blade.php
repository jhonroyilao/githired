<x-dashboard-shell title="Employer dashboard" eyebrow="Recruiter workspace" :user="$user">
    <p class="mt-5 max-w-2xl text-lg font-bold leading-8 text-neutral-600">
        You are signed in as a recruiter. The company profile below represents the hiring organization attached to this account.
    </p>

    <div class="mt-8 rounded-2xl border border-neutral-200 bg-white p-6">
        <p class="text-sm font-black uppercase tracking-[0.14em] text-neutral-600">Company profile</p>
        <h2 class="mt-3 font-display text-4xl font-extrabold tracking-[-0.04em] text-neutral-900">{{ $company?->name }}</h2>
        <p class="mt-3 max-w-2xl font-bold leading-7 text-neutral-600">{{ $company?->description }}</p>
        <a href="{{ route('employer.onboarding.company') }}" class="mt-5 inline-flex font-black text-neutral-900 underline decoration-primarygreen decoration-4 underline-offset-4">
            Edit company profile
        </a>
    </div>
</x-dashboard-shell>
