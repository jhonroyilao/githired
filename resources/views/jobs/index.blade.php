<x-app-shell title="Browse Jobs" body-class="bg-[#f3f5f0] text-neutral-950 antialiased">
    
    {{-- NAVBAR --}}
    <nav class="bg-[#1a2315] text-white px-6 py-4 sticky top-0 z-50 transition-shadow duration-200 hover:shadow-md border-b border-neutral-800">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-10">
                <a href="/" class="inline-flex items-center">
                    <img src="{{ asset('brand/logo-full.svg') }}" alt="GitHired" class="h-8 w-auto">
                </a>
                <div class="hidden md:flex items-center gap-6 font-semibold text-sm tracking-wide">
                    <a href="{{ route('jobs.index') }}" class="{{ request()->routeIs('jobs.index') ? 'text-[#91c93c]' : 'text-neutral-300 hover:text-[#91c93c]' }} transition">
                        Browse Jobs
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route($dashboardRoute) }}" class="text-neutral-300 hover:text-white font-semibold text-sm transition px-4 py-2">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-neutral-300 hover:text-white font-semibold text-sm transition px-4 py-2">Login</a>
                    <a href="{{ route('register') }}" class="bg-[#91c93c] text-[#1a2315] px-5 py-2 rounded-full font-bold text-sm hover:bg-white transition-all duration-300 shadow-[0_0_0_0_rgba(145,201,60,0.5)] hover:shadow-[0_0_15px_2px_rgba(145,201,60,0.3)]">
                        Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="lg:flex lg:gap-8">
            
            {{-- SIDEBAR FILTERS --}}
            <aside class="w-full lg:w-64 shrink-0 mb-8 lg:mb-0">
                <form method="GET" action="{{ route('jobs.index') }}" class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-neutral-900 uppercase tracking-wider mb-2">Search</label>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Title or company" class="w-full px-3 py-2.5 text-sm border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#91c93c] bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-neutral-900 uppercase tracking-wider mb-2">Location</label>
                        <select name="location" onchange="this.form.submit()" class="w-full px-3 py-2.5 text-sm border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#91c93c] bg-white cursor-pointer">
                            <option value="">All locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="border-neutral-300" />

                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-neutral-900 uppercase tracking-wider">Categories</h4>
                        @foreach($categories as $category)
                            <label class="flex items-center gap-2.5 text-sm font-semibold text-neutral-700 cursor-pointer">
                                <input type="checkbox" name="category" value="{{ $category->slug }}" onchange="this.form.submit()" class="rounded border-neutral-300 text-[#91c93c] focus:ring-[#91c93c]" @checked(request('category') === $category->slug)>
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </form>
            </aside>

            {{-- JOB LISTINGS --}}
            <section class="flex-1">
                <header class="mb-8">
                    <h1 class="text-4xl font-black tracking-tight">Browse jobs</h1>
                    <p class="text-neutral-600 font-bold mt-2">Find active, approved roles from hiring teams.</p>
                </header>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
                    @forelse($jobs as $job)
                        <article class="flex flex-col rounded-2xl border border-neutral-200 bg-white p-5 hover:shadow-md transition">
                            <div class="mb-4 flex items-start gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-neutral-950 text-sm font-black uppercase text-[#91c93c]">
                                    {{ substr($job->company->name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-wider text-[#5f8f22]">{{ $job->category->name ?? 'General' }}</p>
                                    <h2 class="text-sm font-black leading-tight mt-0.5">{{ $job->title }}</h2>
                                    <p class="text-xs font-bold text-neutral-500">{{ $job->company->name ?? 'Company' }}</p>
                                </div>
                            </div>

                            <p class="mb-4 line-clamp-3 text-xs text-neutral-600 leading-relaxed">{{ $job->description }}</p>

                            <div class="mt-auto space-y-2">
                                <div class="flex flex-wrap gap-1 text-[10px] font-bold text-neutral-700">
                                    <span class="rounded bg-neutral-100 px-2 py-0.5">{{ $jobTypes[$job->type] ?? ucfirst($job->type) }}</span>
                                    <span class="rounded bg-neutral-100 px-2 py-0.5">{{ $experienceLevels[$job->experience_level] ?? ucfirst($job->experience_level) }}</span>
                                </div>
                                <a href="{{ route('jobs.show', $job) }}" class="block w-full rounded-xl border border-neutral-200 px-4 py-2 text-center text-xs font-black hover:bg-neutral-50 transition">View details</a>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-neutral-200 bg-white p-12 text-center">
                            <h2 class="text-xl font-black">No jobs found</h2>
                            <p class="mt-2 text-sm text-neutral-600">Try clearing one or more filters.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $jobs->links() }}
                </div>
            </section>
        </div>
    </main>
</x-app-shell>