<x-dashboard-shell title="Applicant dashboard" eyebrow="Job seeker workspace" :user="$user">
    <div class="lg:flex lg:gap-8">
        
        <aside class="w-full lg:w-64 shrink-0 mb-6 lg:mb-0">
            <form action="{{ route('applicant.dashboard') }}" method="GET" class="space-y-6">
                
                <div>
                    <label class="block text-xs font-black text-neutral-900 uppercase tracking-wider mb-2">Search by Job Title</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Job title or company" class="w-full pl-9 pr-3 py-2.5 text-sm border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#91c93c] bg-white" />
                        <span class="absolute left-3 top-3 text-neutral-400 text-sm">🔍</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-neutral-900 uppercase tracking-wider mb-2">Location</label>
                    <div class="relative">
                        <select name="location" onchange="this.form.submit()" class="w-full pl-3 pr-8 py-2.5 text-sm border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#91c93c] bg-white appearance-none cursor-pointer">
                            <option value="">Choose city</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="border-neutral-300" />

                <div class="space-y-2">
                    <h4 class="text-xs font-black text-neutral-900 uppercase tracking-wider mb-1">Categories</h4>
                    <div class="space-y-1.5">
                        @foreach($categories as $category)
                            <label class="flex items-center justify-between text-sm font-semibold text-neutral-700 cursor-pointer group">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="category[]" value="{{ $category->slug }}" onchange="this.form.submit()" 
                                           class="rounded border-neutral-300 text-[#91c93c] focus:ring-[#91c93c]" 
                                           {{ is_array(request('category')) && in_array($category->slug, request('category')) ? 'checked' : '' }}>
                                    <span class="group-hover:text-neutral-900 transition">{{ $category->name }}</span>
                                </div>
                                <span class="text-xs font-bold text-neutral-500 bg-neutral-200/60 px-1.5 py-0.5 rounded-md">
                                    {{ $categoryCounts[$category->name] ?? 0 }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>


                @php
                    $otherFilters = [
                        'Job Type' => ['param' => 'job_type', 'items' => $jobTypeOptions, 'counts' => $typeCounts],
                        'Experience Level' => ['param' => 'experience_level', 'items' => $experienceOptions, 'counts' => $experienceCounts],
                        'Date Posted' => ['param' => 'date_posted', 'items' => $datePostedOptions, 'counts' => $dateCounts]
                    ];
                @endphp

                @foreach($otherFilters as $groupLabel => $group)
                    <hr class="border-neutral-300" />
                    <div class="space-y-2">
                        <h4 class="text-xs font-black text-neutral-900 uppercase tracking-wider mb-1">{{ $groupLabel }}</h4>
                        <div class="space-y-1.5">
                            @foreach($group['items'] as $option => $label)
                                <label class="flex items-center justify-between text-sm font-semibold text-neutral-700 cursor-pointer group">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" name="{{ $group['param'] }}[]" value="{{ $option }}" onchange="this.form.submit()" class="rounded border-neutral-300 text-[#91c93c] focus:ring-[#91c93c]" {{ is_array(request($group['param'])) && in_array($option, request($group['param'])) ? 'checked' : '' }}>
                                        <span class="group-hover:text-neutral-900 transition">{{ $label }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-neutral-500 bg-neutral-200/60 px-1.5 py-0.5 rounded-md">
                                        {{ $group['counts'][$option] ?? 0 }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                            <hr class="border-neutral-300" />

            <div>
                    <!-- <h4 class="text-xs font-black text-neutral-900 uppercase tracking-wider mb-3">Tags</h4>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($categories as $category)
                            <button type="submit" name="category[]" value="{{ $category->slug }}" 
                                    class="text-xs font-bold transition px-3 py-1 rounded-full 
                                    {{ (is_array(request('category')) && in_array($category->slug, request('category'))) 
                                        ? 'bg-[#91c93c] text-neutral-950' 
                                        : 'bg-neutral-900 text-white hover:bg-neutral-800' }}">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div> -->
                </div>
            </form>
        </aside>

        <main class="flex-1 space-y-8">
            
            <div class="bg-white border border-neutral-200/80 rounded-2xl p-6 flex flex-col md:flex-row items-center gap-8">
                <div class="w-32 h-32 rounded-full overflow-hidden bg-neutral-100 flex items-center justify-center shrink-0 border border-neutral-200">
                    @if(isset($user->avatar_url) && $user->avatar_url)
                        <img src="{{ asset($user->avatar_url) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @elseif(isset($profile->avatar_url) && $profile->avatar_url)
                        <img src="{{ asset($profile->avatar_url) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <img src="{{ asset('assets/avatar.svg') }}" alt="Default Avatar" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-black text-neutral-950 tracking-tight">{{ $user->name }}</h1>
                    <p class="text-neutral-600 font-bold text-lg mt-0.5">{{ $profile?->headline ?? 'Full Stack Developer' }}</p>
                    
                    <div class="flex justify-center md:justify-start gap-10 mt-4 text-center md:text-left">
                        <div>
                            <span class="block text-2xl font-black text-neutral-950 leading-none">{{ $applicationCount }}</span>
                            <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider mt-1 block">Applications</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-black text-neutral-950 leading-none">{{ $interviewCount }}</span>
                            <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider mt-1 block">Upcoming Interviews</span>
                        </div>
                    </div>
                    <a href="{{ route('applicant.resume') }}" class="mt-5 inline-flex rounded-xl border-2 border-neutral-900 bg-white px-4 py-2 text-sm font-black text-neutral-900 no-underline transition hover:-translate-y-0.5">
                        Manage resume
                    </a>
                </div>
            </div>

            <section>
                <h2 class="text-3xl font-black text-neutral-950 tracking-tight mb-5">AI Matches</h2>
                <div class="grid gap-4 sm:grid-cols-3">
                    @forelse($aiMatches as $index => $job)
                        <div class="{{ $index === 0 ? 'bg-[#eef8df] border-[#91c93c]' : 'bg-white border-neutral-200/80' }} border rounded-2xl p-5 relative flex flex-col justify-between hover:shadow-md transition duration-200">
                            <div>
                                <span class="inline-block text-[10px] font-black bg-[#91c93c] text-neutral-950 px-2 py-0.5 rounded-md mb-3">
                                    {{ $job->match_percentage ?? (100 - ($index * 8)) }}% Job Match
                                </span>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-neutral-950 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($job->company && $job->company->logo_path)
                                            <img src="{{ asset('storage/' . $job->company->logo_path) }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-sm text-white font-bold uppercase">{{ substr($job->company->name ?? 'J', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('jobs.show', $job->slug) }}" class="hover:text-[#91c93c] transition">
                                        <h3 class="font-black text-sm text-neutral-950 leading-tight">{{ $job->title }}</h3>
                                        </a>
                                        <p class="text-xs font-bold text-neutral-500">{{ $job->company->name ?? 'Anonymous Company' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1 mb-3">
                                    <span class="text-[10px] bg-white border border-neutral-200 text-neutral-700 px-2 py-0.5 rounded font-bold capitalize">{{ str_replace('_', ' ', $job->type) }}</span>
                                    <span class="text-[10px] bg-white border border-neutral-200 text-neutral-700 px-2 py-0.5 rounded font-bold capitalize">{{ $job->location_type }}</span>
                                </div>
                                <p class="text-xs text-neutral-600 line-clamp-3 mb-4">{{ $job->description }}</p>
                            </div>
                            <div class="border-t border-neutral-100/70 pt-3 flex items-center justify-between mt-auto">
                                <span class="text-xs font-black text-neutral-950">{{ $job->salaryRange() ?? 'Salary Undisclosed' }}</span>
                                
                                @if(in_array($job->id, $appliedListingIds))
                                    <span class="bg-neutral-200 text-neutral-500 font-black text-xs px-3 py-1.5 rounded-lg select-none">Applied</span>
                                @else
                                    <a href="{{ route('applicant.job-listings.apply', $job->id) }}" class="bg-[#91c93c] hover:bg-[#7fae34] text-neutral-950 font-black text-xs px-4 py-1.5 rounded-lg transition">Apply</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500 col-span-3 bg-white p-6 rounded-2xl border border-neutral-200 text-center">No matches found matching your preferences yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="bg-white border border-neutral-200/80 rounded-2xl p-6">
                <h2 class="text-3xl font-black text-neutral-950 tracking-tight mb-5">Browse Jobs</h2>
                <div class="grid gap-4 sm:grid-cols-3 mb-6">
                    @forelse($browseListings as $job)
                        <div class="bg-white border border-neutral-200 rounded-2xl p-5 flex flex-col justify-between hover:shadow-md transition duration-200">
                            <div>
                                <span class="inline-block text-[10px] font-bold bg-neutral-100 text-neutral-500 px-2 py-0.5 rounded mb-3">
                                    {{ $job->published_at?->diffForHumans() ?? $job->created_at->diffForHumans() }}
                                </span>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-neutral-950 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($job->company && $job->company->logo_path)
                                            <img src="{{ asset('storage/' . $job->company->logo_path) }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-sm text-white font-bold uppercase">{{ substr($job->company->name ?? 'J', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-black text-sm text-neutral-950 leading-tight">{{ $job->title }}</h3>
                                        <p class="text-xs font-bold text-neutral-500">{{ $job->company->name ?? 'Anonymous Company' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1 mb-3">
                                    <span class="text-[10px] bg-neutral-100 text-neutral-700 px-2 py-0.5 rounded font-bold capitalize">{{ str_replace('_', ' ', $job->type) }}</span>
                                    <span class="text-[10px] bg-neutral-100 text-neutral-700 px-2 py-0.5 rounded font-bold capitalize">{{ $job->location_type }}</span>
                                </div>
                                <p class="text-xs text-neutral-600 line-clamp-3 mb-4">{{ $job->description }}</p>
                            </div>
                            <div class="border-t border-neutral-100 pt-3 flex items-center justify-between">
                                <span class="text-xs font-black text-neutral-950">{{ $job->salaryRange() ?? 'Salary Undisclosed' }}</span>
                                
                                @if(in_array($job->id, $appliedListingIds))
                                    <span class="bg-neutral-200 text-neutral-500 font-black text-xs px-3 py-1.5 rounded-lg select-none">Applied</span>
                                @else
                                    <a href="{{ route('applicant.job-listings.apply', $job->id) }}" class="bg-[#91c93c] hover:bg-[#7fae34] text-neutral-950 font-black text-xs px-4 py-1.5 rounded-lg transition">Details</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500 col-span-3 bg-neutral-50 p-6 rounded-2xl border border-neutral-200 text-center">No active job listings available right now.</p>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $browseListings->links() }}
                </div>
            </section>
        </main>

    </div>
</x-dashboard-shell>
