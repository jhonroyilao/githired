<x-dashboard-shell title="Pending Listings" eyebrow="Admin Workspace" :user="$user">

    <p class="mt-5 max-w-6xl text-lg font-bold leading-8 text-neutral-600">
        Review submitted job listings and decide whether they should be approved or rejected.
    </p>

    @if (session('success'))
        <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4">
            <p class="font-bold text-green-700">
                {{ session('success') }}
            </p>
        </div>
    @endif

    <div class="mt-8 grid gap-4 sm:grid-cols-3">

        <div class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-xs font-black uppercase tracking-[0.12em] text-neutral-500">
                Pending Jobs
            </p>

            <p class="mt-2 text-3xl font-black text-neutral-950">
                {{ $jobs->total() }}
            </p>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-xs font-black uppercase tracking-[0.12em] text-neutral-500">
                Queue Status
            </p>

            <p class="mt-2 text-lg font-black text-yellow-600">
                Awaiting Review
            </p>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-xs font-black uppercase tracking-[0.12em] text-neutral-500">
                Visibility
            </p>

            <p class="mt-2 text-lg font-black text-neutral-900">
                Hidden from Public
            </p>
        </div>

    </div>

    <div class="mt-8 flex items-center justify-between"> 
        <h2 class="text-2xl font-black text-neutral-950"> 
            Pending Listings 
        </h2> 
        <form method="GET"> 
            <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-neutral-200 bg-white px-4 py-2 font-bold" > 
                <option value="latest" @selected(request('sort', 'latest') === 'latest') > 
                    Newest First 
                </option> 
                <option value="oldest" @selected(request('sort') === 'oldest') > 
                    Oldest First 
                </option> 
            </select> 
        </form> 
    </div>
    
    <section class="mt-8">

        <div class="space-y-4">

            @forelse($jobs as $job)

                <details class="w-full rounded-2xl border border-neutral-200 bg-white shadow-sm">

                    <summary class="flex cursor-pointer items-center justify-between p-5"> <div class="flex flex-col"> <span class="font-black text-neutral-950"> {{ $job->title }} </span> <span class="text-sm font-bold text-neutral-500"> {{ $job->company?->name }} </span> <span class="text-xs text-neutral-400"> Submitted {{ $job->created_at->diffForHumans() }} </span> </div> <div class="flex items-center gap-3"> <span class="rounded-lg bg-yellow-100 px-3 py-1 text-xs font-black text-yellow-700"> Pending </span> <span class="rounded-lg bg-[#91c93c] px-4 py-2 text-xs font-black text-neutral-950"> View Details </span> </div> </summary>

                    <div class="border-t border-neutral-100 p-5">

                        <div class="space-y-2 text-sm text-neutral-600">

                            <p><strong>Location:</strong> {{ $job->location }}</p>

                            <p><strong>Job Type:</strong> {{ $job->type }}</p>

                            <p><strong>Experience Level:</strong> {{ $job->experience_level }}</p>

                            <p><strong>Salary:</strong> {{ $job->salaryRange() }}</p>

                            <p><strong>Description:</strong></p>

                            <p>{{ $job->description }}</p>

                            <p class="font-bold mb-2">Requirements:</p>

                            <ul class="list-disc pl-5 space-y-1 text-neutral-600">
                                @foreach(explode("\n", $job->requirements) as $requirement)
                                    @if(trim($requirement))
                                        <li>{{ trim($requirement) }}</li>
                                    @endif
                                @endforeach
                            </ul>

                        </div>

                        <div class="mt-6 flex gap-3">

                            <form
                                method="POST"
                                action="{{ route('admin.jobs.approve', $job) }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="rounded-lg bg-[#91c93c] px-5 py-2 font-black text-neutral-950"
                                >
                                    Approve
                                </button>
                            </form>

                            <button
                                type="button"
                                onclick="document.getElementById('reject-{{ $job->id }}').classList.toggle('hidden')"
                                class="rounded-lg bg-red-100 px-5 py-2 font-black text-red-700"
                            >
                                Reject
                            </button>

                        </div>

                        <form id="reject-{{ $job->id }}" 
                            method="POST" 
                            action="{{ route('admin.jobs.reject', $job) }}" 
                            class="hidden mt-4" 
                        >     
                            @csrf 
                            <textarea
                                name="rejection_reason"
                                rows="3"
                                required
                                placeholder="Provide a reason for rejecting this listing..."
                                class="w-full rounded-lg border border-neutral-200 p-3"
                            ></textarea>
                            <p class="mt-1 text-xs text-neutral-500"> 
                                This reason will be visible to the employer. 
                            </p> 
                            
                            <div class="mt-4 flex gap-3">

                                <button
                                    type="submit"
                                    style="
                                        background:#dc2626;
                                        color:white;
                                        padding:10px 20px;
                                        border-radius:8px;
                                        font-weight:700;
                                    "
                                >
                                    Submit Rejection
                                </button>

                                <button
                                    type="button"
                                    onclick="document.getElementById('reject-{{ $job->id }}').classList.add('hidden')"
                                    class="rounded-lg bg-neutral-200 px-5 py-2 font-black text-neutral-700"
                                >
                                    Cancel
                                </button>

                            </div>
                            </form>
                    </div>

                </details>

            @empty

                <div class="rounded-2xl border border-neutral-200 bg-white p-8 text-center">
                    <p class="font-bold text-neutral-500">
                        No pending job listings found.
                    </p>
                </div>

            @endforelse

        </div>

        <div class="mt-6">
            {{ $jobs->links() }}
        </div>

    </section>

</x-dashboard-shell>
