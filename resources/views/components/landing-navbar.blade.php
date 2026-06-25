<nav class="bg-[#1a2315] text-white px-6 py-4 sticky top-0 z-50 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-10">
            <a href="/" class="inline-flex items-center gap-2">
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
                <a href="{{ route('dashboard') }}" class="text-neutral-300 hover:text-white font-semibold text-sm transition px-4 py-2">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-neutral-300 hover:text-white font-semibold text-sm transition px-4 py-2">Login</a>
                <a href="{{ route('register') }}" class="bg-[#91c93c] text-[#1a2315] px-5 py-2 rounded-full font-bold text-sm hover:bg-white transition-all duration-300">
                    Sign Up
                </a>
            @endauth
        </div>
    </div>
</nav>