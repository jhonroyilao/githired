<x-dashboard-shell title="Employer dashboard" eyebrow="Recruiter workspace" :user="$user">
    <p class="mt-5 max-w-2xl text-lg font-bold leading-8 text-neutral-600">
        You are signed in as a recruiter. The company profile below represents the hiring organization attached to this account.
    </p>

    <div class="mt-8 rounded-2xl border border-neutral-200 bg-white p-6">
        <p class="text-sm font-black uppercase tracking-[0.14em] text-neutral-600">Company profile</p>
        <h2 class="mt-3 font-display text-4xl font-extrabold tracking-[-0.04em] text-neutral-900">{{ $company?->name }}</h2>
        <p class="mt-3 max-w-2xl font-bold leading-7 text-neutral-600">{{ $company?->description }}</p>
        
        <div class="mt-5 flex flex-col gap-3">
            <a href="{{ route('employer.onboarding.company') }}" class="font-black text-neutral-900 underline decoration-primarygreen decoration-4 underline-offset-4">Edit company profile</a>

            <a href="{{ route('employer.jobs.create') }}" class="font-black text-neutral-900 underline decoration-primarygreen decoration-4 underline-offset-4">Create Job</a>
        </div>
    </div>

    <section class="mt-8">
        <h2 class="text-3xl font-black text-neutral-950 tracking-tight mb-5">
            Your Job Listings
        </h2>

        <div class="grid gap-4 sm:grid-cols-3">
            @forelse($jobs as $job)
                <div class="bg-white border border-neutral-200 rounded-2xl p-5 flex flex-col justify-between hover:shadow-md transition duration-200">

                    <div>
                        <span class="inline-block text-[10px] font-bold bg-neutral-100 text-neutral-500 px-2 py-0.5 rounded mb-3">
                            {{ $job->created_at->diffForHumans() }}
                        </span>

                        <h3 class="font-black text-lg text-neutral-950">
                            {{ $job->title }}
                        </h3>

                        <p class="text-sm font-bold text-neutral-500 mt-1">
                            {{ $job->location }}
                        </p>

                        <div class="flex flex-wrap gap-1 mt-3 mb-3">
                            <span class="text-[10px] bg-neutral-100 text-neutral-700 px-2 py-0.5 rounded font-bold capitalize">
                                {{ $job->type }}
                            </span>

                            <span class="text-[10px] bg-neutral-100 text-neutral-700 px-2 py-0.5 rounded font-bold capitalize">
                                {{ $job->location_type }}
                            </span>

                            <span
                                class="text-[10px] px-2 py-0.5 rounded font-bold
                                @if($job->status === 'pending')
                                    bg-yellow-100 text-yellow-700
                                @elseif($job->status === 'active')
                                    bg-green-100 text-green-700
                                @elseif($job->status === 'rejected')
                                    bg-red-100 text-red-700
                                @else
                                    bg-neutral-100 text-neutral-700
                                @endif"
                            >
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>

                        <p class="text-xs text-neutral-600 line-clamp-3 mb-4">
                            {{ $job->description }}
                        </p>
                    </div>

                    <div class="border-t border-neutral-100 pt-3 flex items-center justify-between">
                        <span class="text-xs font-black text-neutral-950">
                            {{ $job->salaryRange() }}
                        </span>

                        <span class="text-xs font-bold text-neutral-500">{{ number_format($job->views_count) }} views</span>

                        <a
                            href="{{ route('employer.jobs.show', $job) }}"
                            class="bg-[#91c93c] hover:bg-[#7fae34] text-neutral-950 font-black text-xs px-4 py-1.5 rounded-lg transition"
                        >
                            View
                        </a>

                        @if ($job->status !== \App\Enums\JobStatus::Closed->value)
                            <a
                                href="{{ route('employer.jobs.edit', $job) }}"
                                class="bg-neutral-100 hover:bg-neutral-200 text-neutral-950 font-black text-xs px-4 py-1.5 rounded-lg transition"
                            >
                                Edit
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white border border-neutral-200 rounded-2xl p-8 text-center">
                    <p class="font-bold text-neutral-500">
                        You haven't created any job listings yet.
                    </p>

                    <a
                        href="{{ route('employer.jobs.create') }}"
                        class="mt-4 inline-flex bg-[#91c93c] text-neutral-950 px-4 py-2 rounded-lg font-black"
                    >
                        Create Your First Job
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    </section>

</x-dashboard-shell>
