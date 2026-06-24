<x-dashboard-shell title="Admin Dashboard" eyebrow="Admin Workspace" :user="$user">

    <p class="mt-5 max-w-2xl text-lg font-bold leading-8 text-neutral-600">
        You are signed in as an administrator.
    </p>

    <div class="mt-8 rounded-2xl border border-neutral-200 bg-white p-6">
        <p class="text-sm font-black uppercase tracking-[0.14em] text-neutral-600">
            Admin Profile
        </p>

        <h2 class="mt-3 font-display text-4xl font-extrabold tracking-[-0.04em] text-neutral-900">
            Hello, {{ $user->name }}!
        </h2>

        <p class="mt-3 max-w-2xl font-bold leading-7 text-neutral-600">
            Manage job moderation, platform content, and employer submissions.
        </p>

        <div class="mt-5">
            <a
                href="{{ route('admin.jobs.pending') }}"
                class="font-black text-neutral-900 underline decoration-primarygreen decoration-4 underline-offset-6"
            >
                REVIEW PENDING LISTINGS
            </a>
        </div>
    </div>

</x-dashboard-shell>
