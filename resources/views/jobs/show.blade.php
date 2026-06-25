@auth
    <x-dashboard-shell :title="$jobListing->title" eyebrow="Job Details" :user="auth()->user()">
        @include('jobs.partials.details', ['jobListing' => $jobListing, 'hasApplied' => $hasApplied])
    </x-dashboard-shell>
@else
    <x-app-shell :title="$jobListing->title" body-class="bg-[#f3f5f0] text-neutral-950 antialiased">
        <x-landing-navbar />
        @include('jobs.partials.details', ['jobListing' => $jobListing, 'hasApplied' => $hasApplied])
    </x-app-shell>
@endauth