<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error (500) | Kwang-Lim Chapel - Africa University</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-paper text-slate-800 flex flex-col">

    <main class="flex-1 grid place-items-center px-4 py-12 sm:py-16">
        <div class="w-full max-w-md text-center">

            {{-- Crest --}}
            <span class="mx-auto w-12 h-12 rounded-full grid place-items-center"
                  style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px var(--color-paper), 0 0 0 4px rgb(184 137 43 / .5), 0 10px 22px -10px rgb(165 28 48 / .6);">
                <x-umc-logo class="w-5 h-5" />
            </span>

            <p class="mt-7 text-[10px] font-bold uppercase tracking-[0.26em] text-brand">Africa University Chaplaincy</p>
            <p class="mt-4 inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 tabular-nums">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Error 500
            </p>
            <h1 class="mt-2 font-display text-[2rem] leading-tight text-slate-900" style="font-variation-settings: 'SOFT' 80, 'opsz' 96;" data-ink>Something Went Wrong</h1>
            <p class="mt-3 text-[13.5px] text-slate-500 leading-relaxed">
                An unexpected error occurred while processing your request. Please return to the dashboard or try again in a few moments.
            </p>

            @if(config('app.debug') && isset($exception))
                <div class="mt-5 p-3.5 rounded-xl bg-white text-left text-[11.5px] font-mono text-rose-900 overflow-x-auto max-h-48 whitespace-pre-wrap" style="box-shadow: inset 0 0 0 1px rgb(225 29 72 / .2);">
                    <div class="font-semibold">Error: {{ $exception->getMessage() }}</div>
                    <div class="text-[10.5px] text-rose-700/80 mt-1">File: {{ $exception->getFile() }}:{{ $exception->getLine() }}</div>
                </div>
            @endif

            <div class="rule-ornament my-7 text-[8px] max-w-[12rem] mx-auto" aria-hidden="true">◆</div>

            <blockquote class="font-display italic text-[15px] leading-relaxed text-slate-600">
                “Let not your heart be troubled, neither let it be afraid.”
                <footer class="mt-1.5 not-italic font-sans text-[10.5px] font-semibold uppercase tracking-[0.18em] text-gold">John 14:27</footer>
            </blockquote>

            <div class="mt-9 flex flex-col-reverse sm:flex-row items-center justify-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-secondary">Sign In</a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    Dashboard
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </main>

    <footer class="pb-6 text-center text-[10.5px] uppercase tracking-[0.2em] text-slate-400">Kwang-Lim Chapel · Africa University</footer>

</body>
</html>
