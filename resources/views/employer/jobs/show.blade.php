<x-dashboard-shell title="Job listing details">
    <div class="max-w-4xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.14em] text-neutral-500">
                    {{ $job->company->name }}
                </p>
                <h1 class="mt-2 font-display text-4xl font-extrabold text-neutral-900">
                    {{ $job->title }}
                </h1>
                <p class="mt-2 font-bold text-neutral-600">
                    {{ $job->location }} · {{ str($job->type)->replace('-', ' ')->title() }} · {{ str($job->experience_level)->title() }}
                </p>
            </div>

            <div class="flex gap-2">
                <a
                    href="{{ route('employer.jobs.applicants', $job) }}"
                    class="inline-flex min-h-12 items-center justify-center rounded-xl border-2 border-neutral-200 bg-white px-5 font-black text-neutral-900 shadow-sm transition hover:bg-neutral-50"
                >
                    View Applicants
                </a>
                @if ($job->status !== \App\Enums\JobStatus::Closed->value)
                    <a
                        href="{{ route('employer.jobs.edit', $job) }}"
                        class="inline-flex min-h-12 items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-5 font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5"
                    >
                        Edit Job
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm font-black capitalize text-neutral-700">
                {{ $job->status }}
            </span>
            <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm font-black text-neutral-700">
                {{ $job->salaryRange() }}
            </span>
            <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm font-black text-neutral-700">
                {{ number_format($job->views_count) }} views
            </span>
        </div>

        @if ($job->status === \App\Enums\JobStatus::Rejected->value && $job->rejection_reason)
            <section class="mt-6 rounded-xl border border-red-200 bg-red-50 p-5">
                <h2 class="text-sm font-black uppercase tracking-[0.14em] text-red-700">
                    Rejection reason
                </h2>
                <p class="mt-3 whitespace-pre-line text-sm font-bold leading-6 text-red-800">
                    {{ $job->rejection_reason }}
                </p>
            </section>
        @endif

        <section class="mt-8">
            <h2 class="text-xl font-black text-neutral-950">Description</h2>
            <p class="mt-3 whitespace-pre-line leading-7 text-neutral-700">{{ $job->description }}</p>
        </section>

        <section class="mt-8">
            <h2 class="text-xl font-black text-neutral-950">Requirements</h2>
            <p class="mt-3 whitespace-pre-line leading-7 text-neutral-700">{{ $job->requirements }}</p>
        </section>

        @if (! empty($job->skills_required))
            <section class="mt-8">
                <h2 class="text-xl font-black text-neutral-950">Skills</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($job->skills_required as $skill)
                        <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm font-black text-neutral-700">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-dashboard-shell>
