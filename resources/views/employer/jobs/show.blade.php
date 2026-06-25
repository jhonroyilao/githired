<x-dashboard-shell title="Job Listing Details">
    <div class="space-y-6">
        
        {{-- Header Navigation Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 bg-white border-2 border-neutral-200 rounded-xl p-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.05)]">
            <a href="{{ route('employer.dashboard') }}" class="text-xs font-black text-neutral-500 hover:text-[#5f8f22] transition inline-flex items-center gap-1.5 uppercase tracking-wider">
                ← Back to Dashboard
            </a>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('employer.jobs.applicants', $job) }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-900 text-xs font-black rounded-lg transition">
                    View Applicants
                </a>
                @if ($job->status !== \App\Enums\JobStatus::Closed->value)
                    <a href="{{ route('employer.jobs.edit', $job) }}" class="px-4 py-2 bg-[#91c93c] hover:bg-[#5f8f22] text-neutral-950 font-black text-xs rounded-lg shadow-[2px_2px_0px_0px_#1a2315] transition">
                        Edit Job Listing
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            {{-- Main Content Column --}}
            <div class="lg:col-span-2 bg-white border-2 border-neutral-200 rounded-2xl p-6 space-y-6 shadow-[4px_4px_0px_0px_rgba(26,35,21,0.06)]">
                
                <div class="space-y-1">
                    <span class="inline-block text-[10px] bg-neutral-100 text-neutral-600 font-black px-2 py-0.5 rounded uppercase tracking-wider">
                        {{ $job->company->name }}
                    </span>
                    <h1 class="text-3xl font-black text-neutral-950 tracking-tight leading-none">
                        {{ $job->title }}
                    </h1>
                    <p class="text-sm font-bold text-neutral-500">
                        {{ $job->location }} · {{ str($job->type)->replace('-', ' ')->title() }} · {{ str($job->experience_level)->title() }}
                    </p>
                </div>

                @if ($job->status === \App\Enums\JobStatus::Rejected->value && $job->rejection_reason)
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-red-700 mb-1">Rejection Reason</h2>
                        <p class="text-sm font-medium text-red-800 leading-6">{{ $job->rejection_reason }}</p>
                    </div>
                @endif

                <div class="space-y-4">
                    <h3 class="text-xs font-black text-neutral-400 uppercase tracking-wider">Job Description</h3>
                    <p class="text-sm text-neutral-700 leading-relaxed whitespace-pre-line">{{ $job->description }}</p>
                </div>

                <div class="space-y-4 pt-4 border-t border-neutral-100">
                    <h3 class="text-xs font-black text-neutral-400 uppercase tracking-wider">Requirements</h3>
                    <p class="text-sm text-neutral-700 leading-relaxed whitespace-pre-line bg-neutral-50 p-4 rounded-xl border border-neutral-100">{{ $job->requirements }}</p>
                </div>

                @if (! empty($job->skills_required))
                    <div class="space-y-3 pt-4 border-t border-neutral-100">
                        <h3 class="text-xs font-black text-neutral-400 uppercase tracking-wider">Tech Stack & Skills</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($job->skills_required as $skill)
                                <span class="text-xs font-bold bg-white border border-neutral-200 text-neutral-700 px-3 py-1.5 rounded-lg">
                                    # {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar Column --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-neutral-950 border-2 border-neutral-950 rounded-2xl p-5 text-white shadow-[4px_4px_0px_0px_#91c93c]">
                    <span class="text-[9px] uppercase tracking-widest font-black text-[#91c93c] block mb-2">Listing Insights</span>
                    <div class="text-xl font-black tracking-tight text-white mb-4">
                        {{ $job->salaryRange() }}
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 border-t border-neutral-800 pt-3 text-xs font-bold text-neutral-400">
                        <div>
                            <span class="block text-[10px] uppercase font-normal text-neutral-500">Views</span>
                            <span class="text-white">{{ number_format($job->views_count) }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-normal text-neutral-500">Status</span>
                            <span class="text-white capitalize">{{ $job->status }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border-2 border-neutral-200 rounded-2xl p-5 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)]">
                    <h3 class="text-xs font-black text-neutral-950 uppercase tracking-wider">Manage Listing</h3>
                    <p class="text-[11px] text-neutral-500 leading-relaxed">
                        You can edit your job details or view the list of applicants who have applied for this position.
                    </p>
                    <div class="flex flex-col gap-2 pt-2">
                        <a href="{{ route('employer.jobs.applicants', $job) }}" class="w-full text-center py-2.5 bg-neutral-100 hover:bg-neutral-200 text-neutral-900 text-xs font-black rounded-xl transition">
                            See Applicants
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-dashboard-shell>