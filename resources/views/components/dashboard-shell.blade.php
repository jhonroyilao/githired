@props([
    'title',
    'eyebrow',
    'user',
])

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

    <nav class="bg-[#1a2315] text-white px-6 py-4 sticky top-0 z-50 transition-shadow duration-200 hover:shadow-md">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            
            <div class="flex items-center gap-10">
                <a href="{{ route('applicant.dashboard') }}" class="inline-flex items-center">
                    <img src="{{ asset('brand/logo-full.svg') }}" alt="GitHired" class="h-8 w-auto">
                </a>
                <div class="hidden md:flex items-center gap-6 font-semibold text-sm tracking-wide">
                    <a href="{{ route('applicant.dashboard') }}" class="{{ request()->routeIs('applicant.dashboard') ? 'text-[#91c93c]' : 'text-neutral-300 hover:text-white' }} transition">Find Jobs</a>
                    <a href="#" class="text-neutral-300 hover:text-white transition">My Applications</a>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <button class="text-neutral-300 hover:text-white relative p-1.5 transition flex items-center justify-center">
                    <img src="{{ asset('assets/notif.svg') }}" alt="Notifications" class="h-5 w-5 object-contain">
                    <span class="absolute top-1 right-1 w-2 h-2 bg-[#91c93c] rounded-full"></span>
                </button>
                
                <button class="text-neutral-300 hover:text-white p-1.5 transition flex items-center justify-center">
                    <img src="{{ asset('assets/settings.svg') }}" alt="Settings" class="h-5 w-5 object-contain">
                </button>
                
                <div class="flex items-center gap-3 border-l border-neutral-700 pl-4">
                    <div class="w-9 h-9 rounded-full overflow-hidden bg-neutral-700 border border-neutral-600 flex items-center justify-center">
                        @if(isset($user->avatar_url) && $user->avatar_url)
                            <img src="{{ asset($user->avatar_url) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @elseif(isset($user->profile->avatar_url) && $user->profile->avatar_url)
                            <img src="{{ asset($user->profile->avatar_url) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('assets/icon.png') }}" alt="Default Avatar" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="hidden sm:block text-left leading-tight">
                        <p class="text-xs font-black tracking-tight">{{ $user->name }}</p>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold text-neutral-400 hover:text-red-400 transition">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

</body>
</html>