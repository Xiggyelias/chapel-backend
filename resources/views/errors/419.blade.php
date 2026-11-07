<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Expired | Kwang-Lim Chapel - Africa University</title>
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

            <p class="mt-7 text-[10px] font-bold uppercase tracking-[0.26em] text-brand">Africa University Chaplaincy</p>
            <p class="mt-4 inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 tabular-nums">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Error 419
            </p>
            <h1 class="mt-2 font-display text-[2rem] leading-tight text-slate-900" style="font-variation-settings: 'SOFT' 80, 'opsz' 96;" data-ink>Session Expired</h1>
            <p class="mt-3 text-[13.5px] text-slate-500 leading-relaxed">
                For security reasons, your session has timed out. Please sign in again to continue.
            </p>

            <div class="rule-ornament my-7 text-[8px] max-w-[12rem] mx-auto" aria-hidden="true">◆</div>

            <blockquote class="font-display italic text-[15px] leading-relaxed text-slate-600">
                “They that wait upon the Lord shall renew their strength.”
                <footer class="mt-1.5 not-italic font-sans text-[10.5px] font-semibold uppercase tracking-[0.18em] text-gold">Isaiah 40:31</footer>
            </blockquote>

            <div class="mt-9">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    Sign In Again
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>

            <p class="mt-4 text-[11.5px] text-slate-400" aria-live="polite">
                Redirecting automatically in <span id="countdown" class="font-semibold text-slate-700 tabular-nums">3</span> seconds...
            </p>
        </div>
    </main>

    <footer class="pb-6 text-center text-[10.5px] uppercase tracking-[0.2em] text-slate-400">Kwang-Lim Chapel · Africa University</footer>

    <script>
        let seconds = 3;
        const countdownEl = document.getElementById('countdown');
        const interval = setInterval(() => {
            seconds--;
            if (countdownEl) countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    </script>
</body>
</html>
