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
<body>
    <main class="min-h-screen bg-neutral-100 px-5 py-6 text-neutral-900 sm:px-8 lg:px-12">
        <section class="mx-auto flex min-h-[calc(100vh-3rem)] max-w-6xl flex-col rounded-2xl bg-neutral-50 p-6 shadow-panel sm:p-8">
            <header class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-200 pb-5">
                <a href="{{ route('login') }}" class="inline-flex items-center">
                    <img src="{{ asset('brand/logo-full.svg') }}" alt="GitHired" class="h-auto w-36">
                </a>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-black text-neutral-900">{{ $user->name }}</p>
                        <p class="text-xs font-bold text-neutral-600">{{ $user->email }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-xl border-2 border-neutral-900 bg-white px-4 py-2 text-sm font-black text-neutral-900 transition hover:-translate-y-0.5">
                            Log out
                        </button>
                    </form>
                </div>
            </header>

            <div class="grid flex-1 gap-8 py-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <section>
                    <p class="text-sm font-black uppercase tracking-[0.18em] text-primarygreen-700">{{ $eyebrow }}</p>
                    <h1 class="mt-3 font-display text-[clamp(2.6rem,5vw,5.25rem)] font-extrabold leading-none tracking-[-0.045em] text-neutral-900">{{ $title }}</h1>

                    {{ $slot }}
                </section>

                <aside class="rounded-2xl bg-primarygreen-100 p-5">
                    <p class="text-sm font-black uppercase tracking-[0.14em] text-primarygreen-700">Session</p>
                    <dl class="mt-5 space-y-4">
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.14em] text-neutral-600">Signed in as</dt>
                            <dd class="mt-1 font-black text-neutral-900">{{ $user->role }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.14em] text-neutral-600">Account</dt>
                            <dd class="mt-1 font-bold text-neutral-900">{{ $user->email }}</dd>
                        </div>
                    </dl>
                </aside>
            </div>
        </section>
    </main>
</body>
</html>
