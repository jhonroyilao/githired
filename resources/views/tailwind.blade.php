<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Frontend Decision | GitHired</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-white antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col justify-center px-6 py-16">
        <p class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-sky-300">
            GitHired MVP frontend
        </p>

        <section class="rounded-2xl border border-white/10 bg-white/10 p-8 shadow-2xl shadow-sky-950/30 backdrop-blur md:p-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl font-bold tracking-tight text-white md:text-6xl">
                    Blade plus Tailwind is the selected frontend path.
                </h1>
                <p class="mt-6 text-lg leading-8 text-slate-200">
                    New MVP screens should be server-rendered with Laravel Blade,
                    routed through Laravel controllers, and styled with Tailwind
                    utilities from the Vite asset pipeline.
                </p>
            </div>

            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-white/10 bg-slate-900/80 p-5">
                    <h2 class="text-base font-semibold text-sky-200">Views</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Build product screens as Blade templates and reusable
                        Blade partials/components.
                    </p>
                </div>
                <div class="rounded-xl border border-white/10 bg-slate-900/80 p-5">
                    <h2 class="text-base font-semibold text-sky-200">Routes</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Let Laravel routes and controllers own page requests,
                        authorization, validation, and redirects.
                    </p>
                </div>
                <div class="rounded-xl border border-white/10 bg-slate-900/80 p-5">
                    <h2 class="text-base font-semibold text-sky-200">Styles</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Use Tailwind for new UI. Keep legacy Bootstrap pages
                        working until they are intentionally refactored.
                    </p>
                </div>
            </div>

            <div class="mt-10 flex flex-wrap items-center gap-3">
                <a href="{{ url('/') }}" class="rounded-lg bg-sky-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-sky-300">
                    Back to GitHired
                </a>
                <span class="text-sm text-slate-400">
                    Proof route: /frontend-decision
                </span>
            </div>
        </section>
    </main>
</body>
</html>
