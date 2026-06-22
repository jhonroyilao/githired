//askdmlkas
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Githired') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        
        {{-- Main top navigation bar --}}
        <nav class="bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    
                    <div class="flex">
                        {{-- Website Logo/Brand --}}
                        <div class="shrink-0 flex items-center">
                            <a href="/" class="font-bold text-2xl text-blue-600 tracking-tight">
                                Githired
                            </a>
                        </div>

                        {{-- Navigation Links --}}
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('applicant.resume') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-900 hover:border-blue-500 focus:outline-none transition duration-150 ease-in-out">
                                My Resume
                            </a>
                        </div>
                    </div>
                    
                    {{-- Show logged in user's name on the right side --}}
                    <div class="hidden sm:flex sm:items-center sm:ml-6 text-sm text-gray-500">
                        @auth
                            <span>{{ Auth::user()->name }}</span>
                        @endauth
                    </div>
                    
                </div>
            </div>
        </nav>

        {{-- Page header section (only shows if the specific page provides one) --}}
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        {{-- Where the actual page content gets injected --}}
        <main>
            {{ $slot }}
        </main>
        
    </div>
</body>
</html>