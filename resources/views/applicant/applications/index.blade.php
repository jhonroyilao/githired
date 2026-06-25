<x-dashboard-shell title="My Job Applications" eyebrow="Applicant Control Center">
    <div class="space-y-6">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-neutral-200 pb-4">
            <div>
                <h1 class="text-2xl font-black text-neutral-950 tracking-tight uppercase">Application Tracker</h1>
                <p class="text-xs text-neutral-500 font-bold">Monitor the status of your active job submissions in real-time.</p>
            </div>
            <div class="text-xs font-mono bg-neutral-950 text-white px-3 py-1.5 rounded-xl border border-neutral-800 self-start sm:self-center shadow-[2px_2px_0px_0px_#91c93c]">
                Total: <span class="text-[#91c93c] font-bold">{{ $applications->total() }}</span>
            </div>
        </div>

        <form action="{{ route('applicant.applications.index') }}" method="GET" class="bg-neutral-50 border-2 border-neutral-200 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-neutral-400 text-sm">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by job title or company..." class="w-full pl-10 pr-4 py-2 text-xs font-bold bg-white border-2 border-neutral-200 rounded-xl focus:border-neutral-950 focus:ring-0 outline-none transition placeholder-neutral-400 text-neutral-900">
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                @foreach(['all' => 'All', 'pending' => 'Pending', 'interview' => 'Interview', 'hired' => 'Hired', 'rejected' => 'Rejected'] as $key => $label)
                    <button type="submit" name="status" value="{{ $key }}" class="text-[10px] font-black uppercase tracking-wider px-3 py-2 rounded-xl border transition-all {{ request('status', 'all') === $key ? 'bg-[#1a2315] text-[#91c93c] border-neutral-900' : 'bg-white text-neutral-600 border-neutral-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </form>

        @forelse($applications as $application)
            @php
                $jobListing = $application->jobListing;
                $isClickable = $jobListing && ! $jobListing->trashed();
                $statusColor = match($application->status) {
                    'hired' => 'bg-green-50 text-green-700 border-green-200',
                    'rejected' => 'bg-red-50 text-red-700 border-red-200',
                    'interview' => 'bg-blue-50 text-blue-700 border-blue-200',
                    default => 'bg-amber-50 text-amber-700 border-amber-200',
                };
            @endphp

            <div @if($isClickable) onclick="window.location='{{ route('jobs.show', $jobListing) }}'" @endif
                 class="bg-white border-2 border-neutral-200 rounded-2xl p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-1 transition-all {{ $isClickable ? 'hover:border-neutral-950 hover:shadow-[4px_4px_0px_0px_#1a2315] cursor-pointer group' : '' }}">

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-neutral-950 text-[#91c93c] font-black flex items-center justify-center shrink-0">
                        {{ substr($jobListing?->company?->name ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-base font-black text-neutral-950 {{ $jobListing ? 'group-hover:text-[#5f8f22]' : '' }}">
                            {{ $jobListing?->title ?? 'Deleted job listing' }}
                        </h2>
                        <p class="text-xs font-bold text-neutral-500">
                            {{ $jobListing?->company?->name ?? 'N/A' }} · {{ $jobListing?->location ?? 'No longer available' }}
                        </p>
                        @if($jobListing && $jobListing->trashed())
                            <span class="mt-2 inline-flex text-[10px] font-black uppercase tracking-wider text-neutral-500">
                                Archived listing
                            </span>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <span class="block text-[10px] text-neutral-400 font-bold uppercase">Applied: {{ $application->created_at->format('M d, Y') }}</span>
                    <span class="inline-block text-[10px] font-black px-2.5 py-1 rounded-lg border uppercase {{ $statusColor }}">
                        {{ $application->status }}
                    </span>
                </div>
            </div>
        @empty
            <div class="text-center py-12 border-2 border-dashed border-neutral-200 rounded-2xl">
                <p class="text-neutral-500 font-bold">No applications found.</p>
                <a href="{{ route('applicant.dashboard') }}" class="text-[#91c93c] font-black underline">Browse jobs</a>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $applications->links() }}
        </div>
    </div>
</x-dashboard-shell>
