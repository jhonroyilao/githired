@props([
    'user' => auth()->user(),
    'homeUrl' => route('login'),
    'navItems' => [],
    'notificationUrl' => null,
    'settingsUrl' => null,
])

<nav class="bg-[#1a2315] text-white px-6 py-4 sticky top-0 z-50 transition-shadow duration-200 hover:shadow-md">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        
        <div class="flex items-center gap-10">
            <a href="{{ $homeUrl }}" class="inline-flex items-center">
                <img src="{{ asset('brand/logo-full.svg') }}" alt="GitHired" class="h-8 w-auto">
            </a>

            {{-- DESKTOP NAV --}}
            @if(count($navItems) > 0)
                <div class="hidden md:flex items-center gap-6 font-semibold text-sm tracking-wide">
                    @foreach($navItems as $item)
                        @php
                            $url = $item['url'] ?? null;
                            if (! $url && isset($item['route']) && \Illuminate\Support\Facades\Route::has($item['route'])) {
                                $url = route($item['route'], $item['parameters'] ?? []);
                            }
                            if (! $url) continue;
                            $active = $item['active'] ?? (isset($item['route']) ? request()->routeIs($item['route']) : request()->fullUrlIs($url));
                        @endphp
                        <a href="{{ $url }}" class="{{ $active ? 'text-[#91c93c]' : 'text-neutral-300 hover:text-white' }} transition">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- HAMBURGER BUTTON (Visible sa mobile lang) --}}
        <button id="menu-toggle" class="md:hidden p-2 text-white hover:bg-neutral-800 rounded transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>

        {{-- RIGHT SIDE (Desktop) --}}
        <div class="hidden md:flex items-center gap-5">
            @if($notificationUrl)
                <a href="{{ $notificationUrl }}" class="text-neutral-300 hover:text-white relative p-1.5 transition flex items-center justify-center" aria-label="Notifications">
                    <img src="{{ asset('assets/notif.svg') }}" alt="Notifications" class="h-5 w-5 object-contain">
                    <span class="absolute top-1 right-1 w-2 h-2 bg-[#91c93c] rounded-full"></span>
                </a>
            @endif
            
            @if($settingsUrl)
                <a href="{{ $settingsUrl }}" class="text-neutral-300 hover:text-white p-1.5 transition flex items-center justify-center" aria-label="Settings">
                    <img src="{{ asset('assets/settings.svg') }}" alt="Settings" class="h-5 w-5 object-contain">
                </a>
            @endif
            
            <div class="flex items-center gap-3 border-l border-neutral-700 pl-4">
                <div class="w-9 h-9 rounded-full overflow-hidden bg-neutral-700 border border-neutral-600 flex items-center justify-center">
                    @if(isset($user->avatar_path) && $user->avatar_path)
                        <img src="{{ \App\Support\StorageUrl::image($user->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @elseif(isset($user->profile->avatar_path) && $user->profile->avatar_path)
                        <img src="{{ \App\Support\StorageUrl::image($user->profile->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <img src="{{ asset('assets/avatar.svg') }}" alt="Default Avatar" class="w-full h-full object-cover">
                    @endif
                </div>
                
                <div class="text-left leading-tight">
                    <p class="text-xs font-black tracking-tight">{{ $user->name }}</p>
                    <p class="text-[10px] font-semibold text-neutral-400">{{ $user->email }}</p>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-[10px] font-bold text-neutral-400 hover:text-red-400 transition">Log out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MOBILE MENU DROPDOWN --}}
    <div id="mobile-menu" class="hidden md:hidden mt-4 pt-4 border-t border-neutral-700 flex flex-col gap-4">
        @foreach($navItems as $item)
            @php
                $url = $item['url'] ?? (isset($item['route']) ? route($item['route']) : '#');
                $active = request()->routeIs($item['route'] ?? '');
            @endphp
            <a href="{{ $url }}" class="{{ $active ? 'text-[#91c93c]' : 'text-neutral-300' }} block font-semibold">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>

<script>
    document.getElementById('menu-toggle').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>