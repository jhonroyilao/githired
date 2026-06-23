@props([
    'title',
    'step',
    'total',
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
<body class="min-h-screen">
    <main class="min-h-screen bg-[#f0f2f8]">
        <section class="min-h-screen bg-[#f0f2f8]">
            <div class="grid min-h-screen gap-4 p-4 sm:p-5 lg:grid-cols-[minmax(0,1fr)_minmax(21rem,0.5fr)] lg:gap-12 lg:py-8 lg:pl-20 lg:pr-8 xl:gap-16 xl:py-10 xl:pl-24 xl:pr-10">
                <div class="min-h-0 w-full max-w-[44rem] self-center">
                    <img src="{{ asset('brand/logo-full-dark.svg') }}" alt="GitHired" class="mb-6 h-11 w-auto">

                    <div class="flex w-full max-w-[17.25rem] gap-2" aria-label="Step {{ $step }} of {{ $total }}">
                        @for ($index = 1; $index <= $total; $index++)
                            <span @class([
                                'h-2 flex-1 rounded-full',
                                'bg-neutral-900' => $index < $step,
                                'bg-primarygreen' => $index === $step,
                                'bg-neutral-600/20' => $index > $step,
                            ])></span>
                        @endfor
                    </div>
                    <p class="mt-1.5 text-sm font-bold text-neutral-600">{{ $step }} of {{ $total }}</p>

                    <h1 class="mt-5 font-display text-[clamp(2.35rem,4.15vw,4.35rem)] font-extrabold leading-[1.04] tracking-[-0.045em] text-neutral-900">{{ $title }}</h1>

                    @if ($errors->any())
                        <div class="mt-3 rounded-xl border border-signal-red bg-signal-red-100 p-3 text-sm font-bold text-[#7f2c20]" role="alert">
                            <ul class="m-0 list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>

                <aside class="hidden h-full min-h-0 items-end justify-center overflow-hidden rounded-[1.625rem] bg-[radial-gradient(circle_at_50%_20%,rgb(255_255_255_/_32%),transparent_18rem),linear-gradient(180deg,#bde57f_0%,#91c93c_100%)] lg:flex" aria-label="Onboarding illustration">
                    <img src="{{ asset('assets/onboarding-image.svg') }}" alt="" class="h-full w-full object-cover object-bottom">
                </aside>
            </div>
        </section>
    </main>
</body>
</html>
