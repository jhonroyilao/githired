<x-app-shell title="Browse Jobs" body-class="bg-[#f3f5f0] text-neutral-950 antialiased">
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('login') }}" class="text-sm font-black text-[#5f8f22]">Githired</a>
                <h1 class="mt-2 text-4xl font-black tracking-tight">Browse jobs</h1>
                <p class="mt-2 max-w-2xl text-sm font-medium text-neutral-600">Find active, approved roles from hiring teams.</p>
            </div>
            @auth
                <a href="{{ route($dashboardRoute) }}" class="rounded-xl border-2 border-neutral-900 bg-white px-4 py-2 text-sm font-black">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="rounded-xl border-2 border-neutral-900 bg-white px-4 py-2 text-sm font-black">Sign in</a>
            @endauth
        </header>

        <form method="GET" action="{{ route('jobs.index') }}" class="mb-8 grid gap-3 rounded-2xl border border-neutral-200 bg-white p-4 md:grid-cols-5">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Title or company" class="rounded-xl border border-neutral-200 px-3 py-2 text-sm font-semibold md:col-span-2">

            <select name="location" class="rounded-xl border border-neutral-200 px-3 py-2 text-sm font-semibold">
                <option value="">All locations</option>
                @foreach($locations as $location)
                    <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                @endforeach
            </select>

            <select name="category" class="rounded-xl border border-neutral-200 px-3 py-2 text-sm font-semibold">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>

            <button class="rounded-xl bg-[#91c93c] px-4 py-2 text-sm font-black text-neutral-950">Search</button>

            <div class="md:col-span-5 grid gap-3 md:grid-cols-2">
                <div class="flex flex-wrap gap-2">
                    @foreach($jobTypes as $value => $label)
                        <label class="inline-flex items-center gap-2 rounded-full border border-neutral-200 px-3 py-1.5 text-xs font-bold">
                            <input type="checkbox" name="job_type[]" value="{{ $value }}" @checked(in_array($value, (array) request('job_type'), true))>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <div class="flex flex-wrap gap-2 md:justify-end">
                    @foreach($experienceLevels as $value => $label)
                        <label class="inline-flex items-center gap-2 rounded-full border border-neutral-200 px-3 py-1.5 text-xs font-bold">
                            <input type="checkbox" name="experience_level[]" value="{{ $value }}" @checked(in_array($value, (array) request('experience_level'), true))>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </form>

        <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($jobs as $job)
                <article class="flex min-h-72 flex-col rounded-2xl border border-neutral-200 bg-white p-5">
                    <div class="mb-4 flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-neutral-950 text-sm font-black uppercase text-[#91c93c]">
                            {{ substr($job->company->name ?? 'G', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-wide text-[#5f8f22]">{{ $job->category->name ?? 'General' }}</p>
                            <h2 class="text-lg font-black leading-tight">{{ $job->title }}</h2>
                            <p class="text-sm font-bold text-neutral-500">{{ $job->company->name ?? 'Company' }} · {{ $job->location }}</p>
                        </div>
                    </div>

                    <p class="mb-4 line-clamp-3 text-sm text-neutral-600">{{ $job->description }}</p>

                    <div class="mb-5 flex flex-wrap gap-2 text-xs font-bold text-neutral-700">
                        <span class="rounded-full bg-neutral-100 px-3 py-1">{{ $jobTypes[$job->type] ?? ucfirst($job->type) }}</span>
                        <span class="rounded-full bg-neutral-100 px-3 py-1">{{ $experienceLevels[$job->experience_level] ?? ucfirst($job->experience_level) }}</span>
                        <span class="rounded-full bg-neutral-100 px-3 py-1">{{ $job->salaryRange() ?? 'Salary undisclosed' }}</span>
                    </div>

                    <a href="{{ route('jobs.show', $job) }}" class="mt-auto rounded-xl border-2 border-neutral-900 px-4 py-2 text-center text-sm font-black">View details</a>
                </article>
            @empty
                <div class="rounded-2xl border border-neutral-200 bg-white p-8 text-center md:col-span-2 lg:col-span-3">
                    <h2 class="text-xl font-black">No jobs found</h2>
                    <p class="mt-2 text-sm text-neutral-600">Try clearing one or more filters.</p>
                </div>
            @endforelse
        </section>

        <div class="mt-8">
            {{ $jobs->links() }}
        </div>
    </main>
</x-app-shell>
