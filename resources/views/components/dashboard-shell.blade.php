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
            ['label' => 'Applications', 'route' => 'applicant.applications.index'],
            ['label' => 'Resume', 'route' => 'applicant.resume'],
            ['label' => 'Profile', 'route' => 'applicant.profile.edit'],
        ],
    };
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | GitHired</title>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=cabinet-grotesk@700,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f3f5f0] text-neutral-900 font-sans antialiased">

    <x-dashboard-navbar :user="$user" :home-url="$homeUrl" :nav-items="$navItems" />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

</body>
</html>
