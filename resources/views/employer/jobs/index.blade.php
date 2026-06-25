<x-dashboard-shell title="Your job listings">
    <div class="max-w-6xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-4xl font-extrabold text-neutral-900">
                    Your Job Listings
                </h1>
                <p class="mt-2 text-neutral-600">
                    Manage job posts attached to your company profile.
                </p>
            </div>

            <a
                href="{{ route('employer.jobs.create') }}"
                class="inline-flex min-h-12 items-center justify-center rounded-xl border-2 border-primarygreen bg-primarygreen px-5 font-black text-neutral-900 shadow-pressed transition hover:-translate-y-0.5"
            >
                Create Job
            </a>
        </div>

        <div class="mt-8 overflow-hidden rounded-2xl border border-neutral-200 bg-white">
            <div class="grid grid-cols-12 border-b border-neutral-200 px-5 py-3 text-xs font-black uppercase tracking-[0.12em] text-neutral-500">
                <div class="col-span-5">Job</div>
                <div class="col-span-2">Status</div>
                <div class="col-span-2">Views</div>
                <div class="col-span-3 text-right">Actions</div>
            </div>

            @forelse ($jobs as $job)
                <div class="grid grid-cols-12 items-center gap-3 border-b border-neutral-100 px-5 py-4 last:border-b-0">
                    <div class="col-span-12 sm:col-span-5">
                        <p class="font-black text-neutral-950">{{ $job->title }}</p>
                        <p class="mt-1 text-sm font-bold text-neutral-500">{{ $job->location }}</p>
                    </div>

                    <div class="col-span-4 sm:col-span-2">
                        <span class="inline-flex rounded-full bg-neutral-100 px-3 py-1 text-xs font-black capitalize text-neutral-700">
                            {{ $job->status }}
                        </span>
                    </div>

                    <div class="col-span-4 sm:col-span-2 text-sm font-bold text-neutral-600">
                        {{ number_format($job->views_count) }}
                    </div>

                    <div class="col-span-4 sm:col-span-3 flex justify-end gap-3 text-sm font-black">
                        <a href="{{ route('employer.jobs.show', $job) }}" class="text-neutral-900 underline decoration-primarygreen decoration-4 underline-offset-4">
                            View
                        </a>

                        @if ($job->status !== \App\Enums\JobStatus::Closed->value)
                            <a href="{{ route('employer.jobs.edit', $job) }}" class="text-neutral-900 underline decoration-primarygreen decoration-4 underline-offset-4">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center">
                    <p class="font-bold text-neutral-500">
                        You haven't created any job listings yet.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    </div>
</x-dashboard-shell>
