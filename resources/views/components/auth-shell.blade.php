@props([
    'title',
    'subtitle',
    'cardWidth' => 'default',
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
    <main class="min-h-screen bg-[#e8eaee]">
        <section class="min-h-screen overflow-hidden bg-[#e8eaee]">
            <div class="grid min-h-screen lg:grid-cols-[minmax(0,1fr)_minmax(24rem,0.98fr)]">
                <div class="flex items-center justify-center p-4 sm:p-6 lg:p-10">
                    <div @class([
                        'w-full rounded-[1.375rem] bg-neutral-50/80 p-6 shadow-panel backdrop-blur md:p-8 lg:p-10',
                        'max-w-[38.5rem]' => $cardWidth === 'narrow',
                        'max-w-[41rem]' => $cardWidth !== 'narrow',
                    ])>
                        <h1 class="font-display text-[clamp(2.25rem,4vw,3.5rem)] font-extrabold leading-none tracking-[-0.055em] text-neutral-900">{{ $title }}</h1>
                        <p class="mt-2 text-base text-neutral-600">{{ $subtitle }}</p>

                        {{ $slot }}
                    </div>
                </div>

                <aside class="relative flex items-center overflow-hidden bg-primarygreen p-10 text-white before:absolute before:inset-0 before:bg-[linear-gradient(120deg,rgb(255_255_255_/_18%),transparent_38%),radial-gradient(circle_at_90%_15%,rgb(255_255_255_/_18%),transparent_20rem)] lg:p-20" aria-label="GitHired promise">
                    <div class="relative max-w-[33rem]">
                        <div class="grid size-17 place-items-center rounded-full bg-white font-serif text-[3.25rem] font-black leading-none text-neutral-900" aria-hidden="true">“</div>
                        <p class="mt-7 font-display text-[clamp(3.5rem,5.2vw,5.75rem)] font-extrabold leading-none tracking-[-0.055em]">Find opportunities. Get hired.</p>
                    </div>
                </aside>
            </div>
        </section>
    </main>
</body>
</html>
