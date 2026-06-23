<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $jobListing->title }} | Githired</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f3f5f0] text-neutral-950 antialiased">
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('jobs.index') }}" class="text-sm font-black text-[#5f8f22]">Back to jobs</a>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">
            <article class="rounded-2xl border border-neutral-200 bg-white p-6">
                <p class="text-xs font-black uppercase tracking-wide text-[#5f8f22]">{{ $jobListing->category->name ?? 'General' }}</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight">{{ $jobListing->title }}</h1>
                <p class="mt-2 text-base font-bold text-neutral-600">
                    {{ $jobListing->company->name ?? 'Company' }} · {{ $jobListing->location }}
                </p>

                <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-xl bg-neutral-100 p-3">
                        <dt class="text-xs font-black uppercase text-neutral-500">Salary</dt>
                        <dd class="mt-1 font-black">{{ $jobListing->salaryRange() ?? 'Undisclosed' }}</dd>
                    </div>
                    <div class="rounded-xl bg-neutral-100 p-3">
                        <dt class="text-xs font-black uppercase text-neutral-500">Job type</dt>
                        <dd class="mt-1 font-black">{{ str($jobListing->type)->replace('-', ' ')->title() }}</dd>
                    </div>
                    <div class="rounded-xl bg-neutral-100 p-3">
                        <dt class="text-xs font-black uppercase text-neutral-500">Experience</dt>
                        <dd class="mt-1 font-black">{{ str($jobListing->experience_level)->title() }}</dd>
                    </div>
                    <div class="rounded-xl bg-neutral-100 p-3">
                        <dt class="text-xs font-black uppercase text-neutral-500">Work setup</dt>
                        <dd class="mt-1 font-black">{{ str($jobListing->location_type)->title() }}</dd>
                    </div>
                </dl>

                <section class="mt-8">
                    <h2 class="text-lg font-black">Description</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-neutral-700">{{ $jobListing->description }}</p>
                </section>

                <section class="mt-8 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
                    <h2 class="text-lg font-black">Requirements</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-neutral-700">{{ $jobListing->requirements }}</p>
                </section>

                @if($jobListing->skills_required)
                    <section class="mt-8">
                        <h2 class="text-lg font-black">Skills</h2>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($jobListing->skills_required as $skill)
                                <span class="rounded-full border border-neutral-200 bg-white px-3 py-1 text-xs font-bold">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>

            <aside class="rounded-2xl border border-neutral-200 bg-white p-5 lg:sticky lg:top-6 lg:self-start">
                <h2 class="text-lg font-black">Apply for this role</h2>
                <p class="mt-2 text-sm text-neutral-600">Applications use your current resume, or you can upload a new PDF during submission.</p>

                @auth
                    @if(auth()->user()->role === \App\Enums\UserRole::Applicant->value)
                        @if($hasApplied)
                            <div class="mt-5 rounded-xl bg-neutral-100 px-4 py-3 text-sm font-black text-neutral-600">You already applied.</div>
                        @else
                            <a href="{{ route('applicant.job-listings.apply', $jobListing) }}" class="mt-5 block rounded-xl bg-[#91c93c] px-4 py-3 text-center text-sm font-black text-neutral-950">Apply now</a>
                        @endif
                    @else
                        <div class="mt-5 rounded-xl bg-neutral-100 px-4 py-3 text-sm font-bold text-neutral-600">Applicant accounts can apply to this job.</div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="mt-5 block rounded-xl bg-[#91c93c] px-4 py-3 text-center text-sm font-black text-neutral-950">Sign in to apply</a>
                @endauth
            </aside>
        </div>
    </main>
</body>
</html>
