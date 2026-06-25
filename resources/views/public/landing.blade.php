<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GitHired | Career Hub</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');
        :root {
            --font-display: 'Cabinet Grotesk', 'Manrope', ui-sans-serif, system-ui, sans-serif;
            --font-sans: 'Manrope', ui-sans-serif, system-ui, sans-serif;
        }
        body { font-family: var(--font-sans); }
        h1, h2, h3, h4 { font-family: var(--font-display); }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#eef8df] text-neutral-900">

    {{-- NAVBAR --}}
    @php
        $navItems = [
            ['label' => 'Browse Jobs', 'route' => 'jobs.index'],
            ['label' => 'Login', 'route' => 'login'],
            ['label' => 'Sign Up', 'route' => 'register'],
        ];
    @endphp
    
   <nav class="bg-[#1a2315] text-white px-6 py-4 sticky top-0 z-50 transition-shadow duration-200 hover:shadow-md border-b border-neutral-800">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            
            <div class="flex items-center gap-10">
                <a href="/" class="inline-flex items-center">
                    <img src="{{ asset('brand/logo-full.svg') }}" alt="GitHired" class="h-8 w-auto">
                </a>
                
                <div class="hidden md:flex items-center gap-6 font-semibold text-sm tracking-wide">
                    <a href="{{ route('jobs.index') }}" class="{{ request()->routeIs('jobs.index') ? 'text-[#91c93c]' : 'text-neutral-300 hover:text-[#91c93c]' }} transition">
                        Browse Jobs
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="text-neutral-300 hover:text-white font-semibold text-sm transition px-4 py-2">
                    Login
                </a>
                <a href="{{ route('register') }}" class="bg-[#91c93c] text-[#1a2315] px-5 py-2 rounded-full font-bold text-sm hover:bg-white transition-all duration-300 shadow-[0_0_0_0_rgba(145,201,60,0.5)] hover:shadow-[0_0_15px_2px_rgba(145,201,60,0.3)]">
                    Sign Up
                </a>
            </div>
        </div>
    </nav>
    <main class="space-y-32">
        
        <section class="py-20 px-15 bg-white flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1 space-y-6">
                <h1 class="text-6xl md:text-6xl font-black tracking-tighter text-[#1a2315] leading-[0.9]">
                    Connecting Great Talent <br/> <span class="text-[#91c93c]">With Great Opportunities.</span>
                </h1>
                <p class="text-lg text-neutral-600 font-medium max-w-2xl">
                    GitHired brings job discovery, applications, and recruiter workflows into one structured platform. No inbox chaos. No scattered spreadsheets.
                </p>
                <div class="flex">
                    <a href="{{ route('register') }}" class="bg-[#1a2315] text-white px-8 py-4 rounded-xl font-bold hover:shadow-[4px_4px_0px_0px_#91c93c] transition">
                        Create an account
                    </a>
                </div>
            </div>

    
            <div class="w-full md:w-1/2 flex items-center justify-end overflow-visible">
                <img src="{{ asset('assets/hero.svg') }}" alt="Hero" class="w-full max-w-2xl h-auto object-contain">
            </div>
        </section>

        <section id="how-it-works" class=" max-w-7xl mx-auto px-6 py-20 bg-white py-20 px-15 rounded-3xl border border-neutral-800 border-3">
            <div class="max-w-7xl  mx-auto space-y-16">
                <div class="text-center md:text-left">
                    <h2 class="text-5xl font-black text-[#1a2315]">How it works</h2>
                    <p class="text-neutral-600 font-medium mt-3 text-lg">Simplify your career journey in three easy steps.</p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    @php
                        $steps = [
                            ['icon' => 'build.svg', 'title' => 'Build your profile', 'desc' => 'Create a professional profile that highlights your tech stack, projects, and career goals.'],
                            ['icon' => 'discover.svg', 'title' => 'Discover roles', 'desc' => 'Browse through ai-curated job opportunities that match your specific skills and experience level.'],
                            ['icon' => 'track.svg', 'title' => 'Track progress', 'desc' => 'Keep tabs on your applications, manage interview schedules, and never miss a follow-up.']
                        ];
                    @endphp

                    @foreach($steps as $step)
                    <div class="p-10 bg-white border-2 border-neutral-800 rounded-3xl transition-all hover:border-neutral-950 hover:shadow-[4px_4px_0px_0px_#1a2315] flex flex-col items-start">
                        <div class="w-20 h-20 mb-8 flex items-center justify-center">
                            <img src="{{ asset('assets/' . $step['icon']) }}" alt="{{ $step['title'] }}" class="w-full h-full object-contain">
                        </div>
                        <h3 class="text-2xl font-black text-neutral-950 mb-4">{{ $step['title'] }}</h3>
                        <p class="text-neutral-600 text-sm leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="features" class="max-w-7xl mx-auto px-20 space-y-10">
            <div class="grid md:grid-cols-2 gap-12 items-center p-10 bg-white border-2 border-neutral-200 rounded-3xl hover:border-neutral-950 hover:shadow-[4px_4px_0px_0px_#1a2315] transition-all">
                <div>
                    <h2 class="text-3xl font-black mb-6">For job seekers</h2>
                    <ul class="space-y-3 font-semibold text-neutral-600">
                        @foreach(['AI-powered job recommendations', 'Skills and tech-stack matching', 'Resume upload', 'Real-time tracking'] as $feat)
                        <li class="flex items-center gap-3"><span class="text-[#91c93c] text-xl">✔</span> {{ $feat }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <img src="{{ asset('assets/illustration-my-applications.svg') }}" alt="My Applications" class="w-full h-full object-contain">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center p-10 bg-[#1a2315] text-white rounded-3xl hover:shadow-[4px_4px_0px_0px_#91c93c] transition-all">
                <div class="h-64 flex items-center justify-center order-2 md:order-1">
                    <img src="{{ asset('assets/illustration-applicants.svg') }}" alt="Recruiter Applicants" class="w-full h-full object-contain">
                </div>
                <div class="order-1 md:order-2">
                    <h2 class="text-3xl font-black mb-6">For recruiters</h2>
                    <ul class="space-y-3 font-semibold text-neutral-300">
                        @foreach(['Centralized applicant pipeline', 'One-click status updates', 'In-platform profile review'] as $feat)
                        <li class="flex items-center gap-3"><span class="text-[#91c93c] text-xl">✔</span> {{ $feat }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        <section id="team" class="py-20 px-15 bg-white space-y-12">
            <div class="text-center mb-12">
            <h2 class="text-4xl font-black">Meet the Developers</h2>
            <p class="mt-2 text-neutral-600 max-w-2xl mx-auto">
                Meet the passionate developers who designed, built, and brought this project to life.
            </p>
             </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @php
                    $team = [
                        ['name' => 'John Alejandro De Vera', 'role' => 'System Architect & AI Engineer', 'image' => 'ale.svg'],
                        ['name' => 'Jhon Roy Ilao', 'role' => 'Full-stack Developer', 'image' => 'roy.svg'],
                        ['name' => 'Frances Lorraine Montemayor', 'role' => 'Full-stack Developer', 'image' => 'frances.svg'],
                        ['name' => 'Noreen Yau', 'role' => 'Full-stack Developer', 'image' => 'noreen.svg']
                    ];
                @endphp

                @foreach($team as $member)
                <div class="bg-white p-4 rounded-[2rem] border-2 border-neutral-200 hover:border-neutral-950 hover:shadow-[4px_4px_0px_0px_#1a2315] transition-all flex flex-col">
                    {{-- Dynamic Image Container --}}
                    <div class="w-full h-72 mb-4 bg-neutral-100 rounded-[1.5rem] overflow-hidden">
                        <img src="{{ asset('assets/' . $member['image']) }}" class="w-full h-full object-cover" alt="{{ $member['name'] }}">
                    </div>
                    
            
                    <div class="px-2 pb-2">
                        <h4 class="font-black text-lg text-neutral-950">{{ $member['name'] }}</h4>
                        <p class="text-sm font-semibold text-neutral-500 mt-1">{{ $member['role'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-[#1a2315] text-white py-16 mt-20">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-12">
            <div>
                <a href="/" class="inline-flex items-center">
                    <img src="{{ asset('brand/logo-full.svg') }}" alt="GitHired" class="h-8 w-auto">
                </a>
                <p class="text-xs text-neutral-400 mt-2">Connecting talent with opportunities.</p>
            </div>

            <div>
                <h4 class="font-black text-sm mb-4">Platform</h4>
                <a href="/jobs" class="block text-sm text-neutral-400 mb-2 hover:text-[#91c93c]">Browse jobs</a>
                <a href="/post-job" class="block text-sm text-neutral-400 mb-2 hover:text-[#91c93c]">Post a job</a>
            </div>

            <div>
                <h4 class="font-black text-sm mb-4">Useful links</h4>
                <a href="/#features" class="block text-sm text-neutral-400 mb-2 hover:text-[#91c93c]">Features</a>
                <a href="/#how-it-works" class="block text-sm text-neutral-400 mb-2 hover:text-[#91c93c]">How it works</a>
                <a href="/#team" class="block text-sm text-neutral-400 mb-2 hover:text-[#91c93c]">Team</a>
            </div>
        </div>
    </footer>
</body>
</html>