<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found | Kwang-Lim Chapel</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-paper text-slate-800 flex flex-col">

    <main class="flex-1 grid place-items-center px-4 py-12 sm:py-16">
        <div class="w-full max-w-md text-center">

            {{-- Crest --}}
            <span class="mx-auto w-12 h-12 rounded-full grid place-items-center"
                  style="background: radial-gradient(circle at 35% 30%, #EF4444, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px var(--color-paper), 0 0 0 4px rgb(184 137 43 / .5), 0 10px 22px -10px rgb(220 38 38 / .6);">
                <x-umc-logo class="w-5 h-5" />
            </span>

            <p class="mt-7 text-[10px] font-bold uppercase tracking-[0.26em] text-brand">Africa University · Kwang-Lim Chapel</p>
            <p class="mt-5 font-display text-[4.5rem] leading-none text-slate-300 tabular-nums" style="font-weight: 300 !important; font-variation-settings: 'SOFT' 100, 'opsz' 144;" aria-hidden="true">404</p>
            <h1 class="mt-3 font-display text-[2rem] leading-tight text-slate-900" style="font-variation-settings: 'SOFT' 80, 'opsz' 96;" data-ink>Page not found</h1>
            <p class="mt-3 text-[13.5px] text-slate-500 leading-relaxed">
                The page you were looking for doesn't exist or has been moved.
            </p>

            <div class="rule-ornament my-7 text-[8px] max-w-[12rem] mx-auto" aria-hidden="true">◆</div>

            <blockquote class="font-display italic text-[15px] leading-relaxed text-slate-600">
                “The Lord shall guide thee continually.”
                <footer class="mt-1.5 not-italic font-sans text-[10.5px] font-semibold uppercase tracking-[0.18em] text-gold">Isaiah 58:11</footer>
            </blockquote>

            {{-- Quick links --}}
            <nav class="mt-9 grid grid-cols-2 gap-2 text-left" aria-label="Quick links">
                @foreach([
                    [route('dashboard'),       'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'Dashboard'],
                    [route('calendar.index'),  'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',                                                              'Calendar'],
                    [route('requests.index'),  'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',               'Proposals'],
                    [route('hymns.index'),     'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3', 'Hymnary'],
                ] as [$url, $icon, $label])
                    <a href="{{ $url }}" class="card group flex items-center gap-3 px-3.5 py-3 hover:shadow-md">
                        <svg class="w-4 h-4 shrink-0 text-slate-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/>
                        </svg>
                        <span class="text-[13px] font-semibold text-slate-700 group-hover:text-slate-900">{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            <a href="{{ route('dashboard') }}" class="btn btn-primary mt-6">
                Back to dashboard
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </main>

    <footer class="pb-6 text-center text-[10.5px] uppercase tracking-[0.2em] text-slate-400">Kwang-Lim Chapel · Africa University</footer>

</body>
</html>
