<x-dashboard-shell :title="'Apply for ' . $jobListing->title" eyebrow="Job Application Workspace" :user="$user ?? auth()->user()">
    <div class="min-h-screen bg-[#f3f5f0] -mx-4 sm:-mx-6 lg:-mx-8 p-4 sm:p-6 lg:p-8 font-sans">
        
        <div class="mx-auto max-w-5xl space-y-6">
            
            <!-- Navigation & Upper Analytics Toolbar (Inspired by image_63d67b.jpg) -->
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white border-2 border-neutral-200 rounded-xl p-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.05)]">
                <a href="{{ route('applicant.dashboard') }}" class="text-xs font-black text-neutral-500 hover:text-[#5f8f22] transition inline-flex items-center gap-1.5 uppercase tracking-wider">
                    ← Back to Jobs
                </a>
                
                <div class="flex items-center gap-4 text-xs font-bold text-neutral-500">
                    <span class="flex items-center gap-1">👁️ <strong class="text-neutral-900">{{ number_format($jobListing->views_count ?? 0) }}</strong> views</span>
                    <span class="text-neutral-300">|</span>
                    <span class="flex items-center gap-1">📅 Expires: <strong class="text-neutral-900">{{ \Carbon\Carbon::parse($jobListing->expires_at)->format('M d, Y') }}</strong></span>
                </div>
            </div>

            <!-- MAIN DUAL-COLUMN LAYOUT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- LEFT COLUMN: Detailed Job Specification (Reference: image_63d67b.jpg structural content) -->
                <div class="lg:col-span-2 bg-white border-2 border-neutral-200 rounded-2xl p-6 space-y-6 shadow-[4px_4px_0px_0px_rgba(26,35,21,0.06)]">
                    
                    <!-- Header Segment -->
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-neutral-950 border-2 border-neutral-800 flex items-center justify-center text-lg text-[#91c93c] font-black shrink-0">
                            {{ substr($jobListing->company->name ?? 'C', 0, 1) }}
                        </div>
                        <div class="space-y-1">
                            <span class="inline-block text-[10px] bg-[#91c93c]/20 text-[#5f8f22] font-black px-2 py-0.5 rounded uppercase tracking-wider">
                                {{ $jobListing->category->name ?? 'Technology' }}
                            </span>
                            <h1 class="text-2xl font-black text-neutral-950 tracking-tight leading-none">
                                {{ $jobListing->title }}
                            </h1>
                            <p class="text-sm font-bold text-neutral-600">
                                {{ $jobListing->company->name ?? 'Company Record' }} · <span class="text-neutral-400 font-normal">{{ $jobListing->location }}</span>
                            </p>
                        </div>
                    </div>

                    <hr class="border-neutral-200/60">

                    <!-- Core Body Description -->
                    <div class="space-y-2">
                        <h3 class="text-xs font-black text-neutral-400 uppercase tracking-wider">Job Context & Description</h3>
                        <p class="text-sm text-neutral-700 leading-relaxed whitespace-pre-line">
                            {{ $jobListing->description }}
                        </p>
                    </div>

                    <!-- Core Requirements Field -->
                    <div class="space-y-3 bg-neutral-50 p-4 border border-neutral-200 rounded-xl">
                        <h3 class="text-xs font-black text-neutral-950 uppercase tracking-wider flex items-center gap-1.5">
                            <span>📌</span> Key Requirements & Expectations
                        </h3>
                        <p class="text-sm text-neutral-700 leading-relaxed whitespace-pre-line font-medium">
                            {{ $jobListing->requirements }}
                        </p>
                    </div>

                    <!-- Required Skills Badges Array -->
                    @if($jobListing->skills_required && count($jobListing->skills_required) > 0)
                        <div class="space-y-2">
                            <h3 class="text-xs font-black text-neutral-400 uppercase tracking-wider">Target Tech Stack & Competencies</h3>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($jobListing->skills_required as $skill)
                                    <span class="text-xs font-bold bg-white border border-neutral-300 text-neutral-800 px-2.5 py-1 rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,0.05)]">
                                        # {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- RIGHT COLUMN: Metadata & Application Dynamic Form Panel (Reference: image_63d602.png Layout Modules) -->
                <div class="lg:col-span-1 space-y-6">
                    
                    <!-- Quick Specs Card -->
                    <div class="bg-neutral-950 border-2 border-neutral-950 rounded-2xl p-5 text-white shadow-[4px_4px_0px_0px_#91c93c]">
                        <span class="text-[9px] uppercase tracking-widest font-black text-[#91c93c] block mb-2">Compensation Bracket</span>
                        <div class="text-xl font-black tracking-tight text-white mb-4">
                            {{ $jobListing->salaryRange() ?? 'Undisclosed Package' }}
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 border-t border-neutral-800 pt-3 text-xs font-bold text-neutral-400">
                            <div>
                                <span class="block text-[10px] uppercase font-normal text-neutral-500">Workspace</span>
                                <span class="text-white capitalize">{{ $jobListing->location_type }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] uppercase font-normal text-neutral-500">Employment</span>
                                <span class="text-white capitalize">{{ str_replace('_', ' ', $jobListing->type) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Concrete Form Component Container -->
                    <form action="{{ route('applicant.job-listings.apply.store', $jobListing->id) }}" method="POST" enctype="multipart/form-data" class="bg-white border-2 border-neutral-200 rounded-2xl p-5 space-y-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)]">
                        @csrf
                        
                        <div class="border-b border-neutral-100 pb-2">
                            <h2 class="text-sm font-black text-neutral-950 uppercase tracking-tight">Setup Application</h2>
                            <p class="text-[11px] text-neutral-400">Verify details below to initiate submission.</p>
                        </div>

                        <!-- User Profile Checkbox Verification -->
                        <div class="p-3 bg-[#f3f5f0]/50 border border-neutral-200 rounded-xl space-y-1.5 text-xs">
                            <span class="block text-[10px] uppercase font-black text-neutral-400 tracking-wider">Applicant Account</span>
                            <div class="leading-tight">
                                <p class="font-black text-neutral-900">{{ auth()->user()->name }}</p>
                                <p class="text-neutral-500 font-medium text-[11px]">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        <!-- Document Upload Field Wrapper -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-black text-neutral-950 uppercase tracking-wide">Attach System CV / Resume</label>
                            
                            <div class="relative group border-2 border-dashed border-neutral-200 hover:border-[#91c93c] bg-neutral-50 p-4 rounded-xl text-center transition-all cursor-pointer">
                                <input type="file" name="resume" id="resume" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                <div class="text-xs font-bold text-neutral-600">
                                    📁 <span class="text-[#5f8f22] group-hover:text-[#91c93c] underline">Upload modern file</span>
                                </div>
                                <p class="text-[9px] text-neutral-400 mt-0.5">PDF or DOCX up to 5MB</p>
                            </div>

                            @if($profile?->resume_path)
                                <div class="p-2 bg-[#91c93c]/10 border border-[#91c93c]/20 rounded-lg text-[10px] text-[#5f8f22] font-semibold flex items-center gap-1.5">
                                    <span>✓</span> <span>Profile default CV is active.</span>
                                </div>
                            @endif

                            @error('resume')
                                <p class="text-[11px] text-red-600 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cover Letter Block -->
                        <div class="space-y-1">
                            <div class="flex justify-between items-baseline">
                                <label for="cover_letter" class="block text-xs font-black text-neutral-950 uppercase tracking-wide">Cover Letter</label>
                                <span class="text-[9px] font-mono text-neutral-400">Optional</span>
                            </div>
                            <textarea name="cover_letter" id="cover_letter" rows="5" placeholder="Why are you a perfect match for the team stack?..." class="w-full p-3 text-xs border-2 border-neutral-200 rounded-xl focus:outline-none focus:border-[#91c93c] bg-neutral-50 placeholder-neutral-400 focus:ring-0 font-sans resize-y transition-colors">{{ old('cover_letter') }}</textarea>

                            @error('cover_letter')
                                <p class="text-[11px] text-red-600 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Portfolio Link Injector Option -->
                        <label class="flex items-start gap-2.5 p-2.5 border border-neutral-200 rounded-xl cursor-pointer bg-neutral-50/50 hover:bg-neutral-50 transition">
                            <input type="checkbox" name="sync_portfolio" value="1" checked class="mt-0.5 accent-[#5f8f22] h-3.5 w-3.5 rounded border-neutral-300 text-[#5f8f22] focus:ring-0">
                            <span class="text-[11px] font-medium text-neutral-600 leading-tight">
                                Append my linked <strong class="text-neutral-900">GitHub repositories</strong> & profile socials.
                            </span>
                        </label>

                        <!-- Action Submit Triggers -->
                        <div class="pt-2 grid grid-cols-3 gap-2">
                            <a href="{{ route('applicant.dashboard') }}" class="col-span-1 py-2 rounded-xl border-2 border-neutral-200 text-center text-xs font-black text-neutral-600 hover:text-neutral-950 transition bg-white">
                                Back
                            </a>
                            <button type="submit" class="col-span-2 py-2 rounded-xl bg-[#91c93c] hover:bg-[#5f8f22] text-neutral-950 hover:text-white font-black text-xs transition tracking-wide shadow-[2px_2px_0px_0px_#1a2315]">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-dashboard-shell>