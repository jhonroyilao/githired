<x-dashboard-shell title="ALL JOB LISTINGS" eyebrow="Admin Workspace" :user="$user">

    @if (session('success'))
        <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4">
            <p class="font-bold text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mt-8">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by job title, company, or location..."
                class="flex-1 rounded-xl border border-neutral-200 bg-white px-4 py-3 font-medium">

            <select name="sort" onchange="this.form.submit()"
                class="rounded-xl border border-neutral-200 bg-white px-4 py-3 font-bold">
                <option value="latest" @selected(request('sort','latest') === 'latest')>Newest</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
            </select>

            <button type="submit"
                class="rounded-xl bg-[#91c93c] px-6 py-3 font-black text-neutral-900">
                Search
            </button>
        </form>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.jobs.all', ['status' => 'pending']) }}"
            class="inline-flex items-center gap-2 rounded-full border border-yellow-300 bg-yellow-50 px-4 py-2 text-sm font-bold text-yellow-800 hover:shadow transition">
            Pending <span class="text-base font-black">{{ $pendingCount }}</span>
        </a>
        <a href="{{ route('admin.jobs.all', ['status' => 'active']) }}"
            class="inline-flex items-center gap-2 rounded-full border border-green-300 bg-green-50 px-4 py-2 text-sm font-bold text-green-800 hover:shadow transition">
            Active <span class="text-base font-black">{{ $activeCount }}</span>
        </a>
        <a href="{{ route('admin.jobs.all', ['status' => 'closed']) }}"
            class="inline-flex items-center gap-2 rounded-full border border-orange-300 bg-orange-50 px-4 py-2 text-sm font-bold text-orange-800 hover:shadow transition">
            Hidden <span class="text-base font-black">{{ $hiddenCount }}</span>
        </a>
        <a href="{{ route('admin.jobs.all', ['status' => 'rejected']) }}"
            class="inline-flex items-center gap-2 rounded-full border border-red-300 bg-red-50 px-4 py-2 text-sm font-bold text-red-800 hover:shadow transition">
            Rejected <span class="text-base font-black">{{ $rejectedCount }}</span>
        </a>
        <span class="inline-flex items-center gap-2 rounded-full border border-pink-300 bg-pink-50 px-4 py-2 text-sm font-bold text-pink-800 hover:shadow transition">
            Deleted <span class="text-base font-black">{{ $deletedCount }}</span>
        </span>
        <a href="{{ route('admin.jobs.all') }}"
            class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-4 py-2 text-sm font-bold text-neutral-600 hover:shadow transition">
            Total <span class="text-base font-black">{{ $totalCount }}</span>
        </a>
    </div>

    <section class="mt-8">
        <div class="space-y-2">
            @forelse($jobs as $job)

                <details class="w-full rounded-2xl border border-neutral-200 bg-white shadow-sm group">

                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">

                        <div class="flex min-w-0 flex-1 flex-col">
                            <span class="truncate font-black text-neutral-950">{{ $job->title }}</span>
                            <span class="text-sm font-bold text-neutral-500">
                                {{ $job->company?->name }}
                                <span class="font-normal text-neutral-400">·</span>
                                {{ $job->location }}
                                <span class="font-normal text-neutral-400">·</span>
                                {{ $job->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">

                            <span class="rounded-lg px-3 py-1 text-xs font-black
                                @if($job->trashed())
                                    bg-red-100 text-red-700
                                @elseif($job->status === \App\Enums\JobStatus::Active->value)
                                    bg-green-100 text-green-700
                                @elseif($job->status === \App\Enums\JobStatus::Pending->value)
                                    bg-yellow-100 text-yellow-700
                                @elseif($job->status === \App\Enums\JobStatus::Closed->value)
                                    bg-orange-100 text-orange-700
                                @elseif($job->status === \App\Enums\JobStatus::Rejected->value)
                                    bg-red-100 text-red-700
                                @else
                                    bg-neutral-100 text-neutral-600
                                @endif
                            ">
                                {{ $job->trashed() ? 'Deleted' : ucfirst($job->status) }}
                            </span>

                            <span class="rounded-lg bg-neutral-100 px-3 py-1 text-xs font-black text-neutral-600 group-open:hidden">
                                More actions ▾
                            </span>
                            <span class="hidden rounded-lg bg-neutral-100 px-3 py-1 text-xs font-black text-neutral-600 group-open:inline">
                                Less ▴
                            </span>

                        </div>

                    </summary>

                    {{-- EXPANDED CONTENT - everything inside this div --}}
                    <div class="border-t border-neutral-100 px-5 py-4">

                        {{-- Job details --}}
                        <div class="space-y-1 text-sm text-neutral-600">
                            <p><strong>Job Type:</strong> {{ $job->type }}</p>
                            <p><strong>Experience Level:</strong> {{ $job->experience_level }}</p>
                            <p><strong>Salary:</strong> {{ $job->salaryRange() }}</p>
                            <p class="mt-2"><strong>Description:</strong> {{ $job->description }}</p>

                            @if($job->requirements)
                                <p class="mt-2 font-bold">Requirements:</p>
                                <ul class="list-disc space-y-1 pl-5 text-neutral-600">
                                    @foreach(explode("\n", $job->requirements) as $req)
                                        @if(trim($req))
                                            <li>{{ trim($req) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        <div class="mt-5 flex flex-wrap gap-3">

                            @if($job->trashed())

                                <form method="POST" action="{{ route('admin.jobs.restore', $job->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-green-100 px-5 py-2 font-black text-green-700 hover:bg-green-200">
                                        Restore
                                    </button>
                                </form>

                            @elseif($job->status === \App\Enums\JobStatus::Pending->value)

                                <form method="POST" action="{{ route('admin.jobs.approve', $job) }}">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-[#91c93c] px-5 py-2 font-black text-neutral-950">
                                        Approve
                                    </button>
                                </form>

                                <button type="button"
                                    onclick="document.getElementById('reject-{{ $job->id }}').classList.toggle('hidden')"
                                    class="rounded-lg bg-red-100 px-5 py-2 font-black text-red-700 hover:bg-red-200">
                                    Reject
                                </button>

                            @elseif($job->status === \App\Enums\JobStatus::Closed->value)
                                <form method="POST" action="{{ route('admin.jobs.reactivate', $job) }}">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-green-100 px-5 py-2 font-black text-green-700 hover:bg-green-200">
                                        Make Active
                                    </button>
                                </form>

                                <button type="button"
                                    onclick="document.getElementById('delete-{{ $job->id }}').classList.toggle('hidden')"
                                    class="rounded-lg bg-red-100 px-5 py-2 font-black text-red-700 hover:bg-red-200">
                                    Soft Delete
                                </button>

                            @elseif($job->status === \App\Enums\JobStatus::Active->value)

                                <form method="POST" action="{{ route('admin.jobs.hide', $job) }}">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-orange-100 px-5 py-2 font-black text-orange-700 hover:bg-orange-200">
                                        Hide Listing
                                    </button>
                                </form>

                                <button type="button"
                                    onclick="document.getElementById('delete-{{ $job->id }}').classList.toggle('hidden')"
                                    class="rounded-lg bg-red-100 px-5 py-2 font-black text-red-700 hover:bg-red-200">
                                    Soft Delete
                                </button>

                            @elseif($job->status === \App\Enums\JobStatus::Rejected->value)

                                <form method="POST" action="{{ route('admin.jobs.reapprove', $job) }}">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-[#91c93c] px-5 py-2 font-black text-neutral-950">
                                        Re-approve
                                    </button>
                                </form>

                                <button type="button"
                                    onclick="document.getElementById('delete-{{ $job->id }}').classList.toggle('hidden')"
                                    class="rounded-lg bg-red-100 px-5 py-2 font-black text-red-700 hover:bg-red-200">
                                    Soft Delete
                                </button>

                            @endif

                        </div>
                        {{-- END action buttons --}}

                        {{-- Reject form — pending only --}}
                        @if(!$job->trashed() && $job->status === \App\Enums\JobStatus::Pending->value)
                            <form id="reject-{{ $job->id }}"
                                method="POST"
                                action="{{ route('admin.jobs.reject', $job) }}"
                                class="hidden mt-4">
                                @csrf
                                <textarea name="rejection_reason" rows="3" required
                                    placeholder="Provide a reason for rejecting this listing..."
                                    class="w-full rounded-lg border border-neutral-200 p-3"></textarea>
                                <p class="mt-1 text-xs text-neutral-500">This reason will be visible to the employer.</p>
                                <div class="mt-3 flex gap-3">
                                    <button type="submit"
                                        class="rounded-lg bg-red-600 px-5 py-2 font-black text-white hover:bg-red-700">
                                        Submit Rejection
                                    </button>
                                    <button type="button"
                                        onclick="document.getElementById('reject-{{ $job->id }}').classList.add('hidden')"
                                        class="rounded-lg bg-neutral-200 px-5 py-2 font-black text-neutral-700">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        @endif

                        {{-- Delete panel — all non-deleted statuses --}}
                        @if(!$job->trashed())
                            <div id="delete-{{ $job->id }}"
                                class="hidden mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
                                <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}">
                                    @csrf
                                    @method('DELETE')
                                    <textarea name="delete_reason" rows="3" required
                                        placeholder="Reason for deleting this listing..."
                                        class="w-full rounded-lg border border-neutral-200 bg-white p-3"></textarea>
                                    <p class="mt-2 text-xs text-neutral-500">
                                        This reason will be stored for audit purposes.
                                    </p>
                                    <div class="mt-4 flex gap-3">
                                        <button type="submit"
                                                class="rounded-lg bg-red-600 hover:bg-red-700 px-5 py-2 font-black text-neutral-50 border border-red-700">
                                            Confirm Deletion
                                        </button>
                                        <button type="button"
                                            onclick="document.getElementById('delete-{{ $job->id }}').classList.add('hidden')"
                                            class="rounded-lg bg-neutral-200 px-5 py-2 font-black text-neutral-700">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                    </div>
                    {{-- END expanded content --}}

                </details>

            @empty
                <div class="rounded-2xl border border-neutral-200 bg-white p-8 text-center">
                    <p class="font-bold text-neutral-500">No job listings found.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $jobs->links() }}</div>
    </section>

</x-dashboard-shell>