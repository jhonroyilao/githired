<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Back Button --}}
    <a href="{{ $backUrl }}"
       class="text-xs font-black text-neutral-500 hover:text-[#5f8f22] transition inline-flex items-center gap-1.5 uppercase tracking-wider mb-6">
        ← Back to {{ $backLabel }}
    </a>

    <div class="grid lg:grid-cols-[1fr_360px] gap-8 items-start">

        <article class="bg-white border-2 border-neutral-200 rounded-2xl p-8 shadow-[4px_4px_0px_0px_rgba(26,35,21,0.06)]">
            <span class="inline-block text-[10px] bg-[#91c93c]/20 text-[#5f8f22] font-black px-2 py-0.5 rounded uppercase tracking-wider mb-3">
                {{ $jobListing->category->name ?? 'General' }}
            </span>

            <h1 class="text-4xl font-black tracking-tight text-neutral-950">{{ $jobListing->title }}</h1>
            <p class="mt-3 text-lg font-bold text-neutral-600">
                {{ $jobListing->company->name ?? 'Company' }} · <span class="text-neutral-400 font-medium">{{ $jobListing->location }}</span>
            </p>

            <dl class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach([
                    'Salary' => $jobListing->salaryRange() ?? 'Undisclosed',
                    'Type' => str($jobListing->type)->replace('-', ' ')->title(),
                    'Experience' => str($jobListing->experience_level)->title(),
                    'Setup' => str($jobListing->location_type)->title()
                ] as $label => $value)
                    <div class="border-2 border-neutral-950 bg-white rounded-xl p-3 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <dt class="text-[9px] font-black uppercase text-neutral-400 tracking-wider">{{ $label }}</dt>
                        <dd class="mt-1 font-black text-sm text-neutral-950">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-10 space-y-8">
                <section>
                    <h2 class="text-xs font-black text-neutral-950 uppercase tracking-wider mb-3">Job Description</h2>
                    <p class="whitespace-pre-line text-sm leading-7 text-neutral-700">{{ $jobListing->description }}</p>
                </section>

                <section class="border-2 border-neutral-950 bg-white rounded-2xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <h2 class="text-xs font-black text-neutral-950 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>📌</span> Requirements
                    </h2>
                    <p class="whitespace-pre-line text-sm leading-7 text-neutral-700 font-medium">{{ $jobListing->requirements }}</p>
                </section>

                @if($jobListing->skills_required)
                    <section>
                        <h2 class="text-xs font-black text-neutral-950 uppercase tracking-wider mb-3">Skills</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($jobListing->skills_required as $skill)
                                <span class="text-xs font-bold bg-white border border-neutral-300 text-neutral-800 px-3 py-1.5 rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,0.05)]">
                                    # {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </article>

        <aside class="lg:sticky lg:top-8">
            <div class="bg-neutral-950 border-2 border-neutral-950 rounded-2xl p-6 text-white shadow-[4px_4px_0px_0px_#91c93c]">
                <h2 class="text-lg font-black tracking-tight text-white">Interested?</h2>
                <p class="mt-2 text-sm text-neutral-400">Apply for this role and start your next career move.</p>

                @auth
                    @if(auth()->user()->role === \App\Enums\UserRole::Applicant->value)
                        @if($hasApplied)
                            <div class="mt-6 rounded-xl bg-neutral-800 px-4 py-3 text-sm font-black text-neutral-500 text-center">Already Applied</div>
                        @else
                            <a href="{{ route('applicant.job-listings.apply', $jobListing) }}" class="mt-6 block rounded-xl bg-[#91c93c] hover:bg-[#7fae34] px-4 py-3 text-center text-sm font-black text-neutral-950 transition-all duration-200">
                                Apply Now
                            </a>
                        @endif
                    @else
                        <div class="mt-6 rounded-xl bg-neutral-800 px-4 py-3 text-sm font-bold text-neutral-400 text-center">Applicant access only.</div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="mt-6 block rounded-xl bg-[#91c93c] hover:bg-[#7fae34] px-4 py-3 text-center text-sm font-black text-neutral-950 transition-all duration-200">
                        Sign in to apply
                    </a>
                @endauth
            </div>
        </aside>
    </div>
</div>