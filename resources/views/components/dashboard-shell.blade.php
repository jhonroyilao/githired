@props([
    'title',
    'eyebrow' => null,
    'user' => auth()->user(),
    'homeUrl' => null,
    'navItems' => null,
])

@php
    $role = $user?->role;
    $homeUrl ??= match ($role) {
        \App\Enums\UserRole::Employer->value => route('employer.dashboard'),
        \App\Enums\UserRole::Admin->value => route('admin.dashboard'),
        default => route('applicant.dashboard'),
    };

    $navItems ??= match ($role) {
        \App\Enums\UserRole::Employer->value => [
            ['label' => 'Dashboard', 'route' => 'employer.dashboard'],
            ['label' => 'Company profile', 'route' => 'employer.onboarding.company'],
        ],
        \App\Enums\UserRole::Admin->value => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ],
        default => [
            ['label' => 'Find jobs', 'route' => 'applicant.dashboard'],
            ['label' => 'Resume', 'route' => 'applicant.resume'],
            ['label' => 'Profile', 'route' => 'applicant.profile.edit'],
        ],
    };
@endphp

<x-app-shell :title="$title" body-class="bg-[#f3f5f0] text-neutral-900 font-sans antialiased">
    <x-dashboard-navbar :user="$user" :home-url="$homeUrl" :nav-items="$navItems" />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>
</x-app-shell>
