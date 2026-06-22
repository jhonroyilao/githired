@props([
    'user' => auth()->user()
])

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
                    @if(isset($user->avatar_path) && $user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @elseif(isset($user->profile->avatar_path) && $user->profile->avatar_path)
                        <img src="{{ asset('storage/' . $user->profile->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <img src="{{ asset('images/icon.svg') }}" alt="Default Avatar Placeholder" class="w-5 h-5 object-contain opacity-80">
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