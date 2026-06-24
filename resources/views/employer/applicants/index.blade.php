<x-dashboard-shell title="Applicants">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-neutral-200 pb-4">
            <div>
                <h1 class="text-2xl font-black text-neutral-950 tracking-tight uppercase">Applicants for {{ $job->title }}</h1>
                <p class="text-xs text-neutral-500 font-bold">Reviewing candidates for this position.</p>
            </div>
            <div class="text-xs font-mono bg-neutral-950 text-white px-3 py-1.5 rounded-xl border border-neutral-800 shadow-[2px_2px_0px_0px_#91c93c]">
                Total: <span class="text-[#91c93c] font-bold">{{ $applications->total() }}</span>
            </div>
        </div>

        <form action="{{ route('employer.jobs.applicants', $job) }}" method="GET" class="bg-neutral-50 border-2 border-neutral-200 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-neutral-400 text-sm">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-2 text-xs font-bold bg-white border-2 border-neutral-200 rounded-xl focus:border-neutral-950 outline-none transition">
            </div>

            <div class="flex items-center gap-2">
                <select name="sort" onchange="this.form.submit()" class="text-[10px] font-black uppercase tracking-wider px-3 py-2 rounded-xl border-2 border-neutral-200 bg-white">
                    <option value="newest" @selected(request('sort') == 'newest')>Newest First</option>
                    <option value="oldest" @selected(request('sort') == 'oldest')>Oldest First</option>
                </select>
                
               <div class="flex items-center gap-2">
                    @foreach(['all' => 'Active', 'pending' => 'Pending', 'interview' => 'Interview', 'hired' => 'Hired', 'rejected' => 'Rejected'] as $key => $label)
                        <button type="submit" name="status" value="{{ $key }}" 
                            class="text-[10px] font-black uppercase tracking-wider px-3 py-2 rounded-xl border-2 transition-all 
                            {{ request('status', 'all') == $key ? 'bg-[#1a2315] text-[#91c93c] border-neutral-900' : 'bg-white text-neutral-600 border-neutral-200' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse ($applications as $application)
                @php
                    $statusColor = match($application->status) {
                        'hired' => 'bg-green-50 text-green-700 border-green-200',
                        'rejected' => 'bg-red-50 text-red-700 border-red-200',
                        'interview' => 'bg-blue-50 text-blue-700 border-blue-200',
                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                    };
                @endphp
                <div class="bg-white border-2 border-neutral-200 rounded-2xl p-5 flex flex-col justify-between hover:border-neutral-950 hover:shadow-[4px_4px_0px_0px_#1a2315] transition-all group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-neutral-100 border-2 border-neutral-200 flex items-center justify-center overflow-hidden mb-3">
                            <img src="{{ $application->user->profile_photo_path ? asset('storage/'.$application->user->profile_photo_path) : asset('assets/avatar.svg') }}" class="w-full h-full object-cover">
                        </div>
                        <h2 class="text-sm font-black text-neutral-950">{{ $application->user->name }}</h2>
                        <p class="text-[10px] font-bold text-neutral-500">{{ $application->user->email }}</p>
                    </div>

                    <div class="space-y-2 my-4">
                        <div class="text-[10px] text-neutral-600 font-bold flex justify-between border-b border-neutral-100 pb-1">
                            <span>Applied</span>
                            <span>{{ $application->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="text-[10px] text-neutral-600 font-bold flex justify-between">
                            <span>Status</span>
                            <span class="px-2 py-0.5 rounded-lg border uppercase font-black tracking-wider {{ $statusColor }}">
                                {{ $application->status }}
                            </span>
                        </div>
                    </div>

                <a href="{{ route('employer.jobs.employer.applicants.show', [$job, $application]) }}" 
                class="w-full text-center py-2 bg-neutral-950 text-[#91c93c] text-[10px] font-black uppercase rounded-xl hover:bg-[#1a2315] transition">
                    View Details
                </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12 border-2 border-dashed border-neutral-200 rounded-2xl">
                    <p class="text-neutral-500 font-bold">No applicants found matching your criteria.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $applications->withQueryString()->links() }}
        </div>
    </div>
</x-dashboard-shell>