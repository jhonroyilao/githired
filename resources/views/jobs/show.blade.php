@auth
    <x-dashboard-shell :title="$jobListing->title" eyebrow="Job Details" :user="auth()->user()">
        @include('jobs.partials.details', ['jobListing' => $jobListing, 'hasApplied' => $hasApplied])
    </x-dashboard-shell>
@else
    <x-app-shell :title="$jobListing->title">
        @include('jobs.partials.details', ['jobListing' => $jobListing, 'hasApplied' => $hasApplied])
    </x-app-shell>
@endauth
