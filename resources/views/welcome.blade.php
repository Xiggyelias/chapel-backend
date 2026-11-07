<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#15121A">
    <meta name="description" content="Kwang-Lim Chapel, Africa University — the ministry portal for worship, fellowships, hymnody and records, ordered through the Christian year.">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Kwang-Lim Chapel | Ministry Portal</title>

    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hero-title { font-variation-settings: 'SOFT' 100, 'opsz' 144; }
        .pillar-num { font-variation-settings: 'SOFT' 100, 'WONK' 1, 'opsz' 144; }
        .placard {
            background: linear-gradient(180deg, #1E1820, #120E14);
            box-shadow: inset 0 0 0 1px rgb(217 183 108 / .28), inset 0 1px 0 rgb(255 255 255 / .06), 0 18px 40px -20px rgb(0 0 0 / .8);
        }
    </style>
</head>
<body class="bg-paper text-slate-800 antialiased selection:bg-brand selection:text-white">
@php
    $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
    $hymnCount = rescue(fn () => \App\Models\Hymn::count(), 0, false);
    $fellowshipCount = rescue(fn () => \App\Models\Club::count(), 0, false);
@endphp

{{-- ═══════════════ HERO (ink) ═══════════════ --}}
<section class="relative bg-ink text-white overflow-hidden grain on-dark">
    {{-- Quiet warmth in the corners — no beams, no shapes --}}
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true"
         style="background: radial-gradient(60% 50% at 78% 45%, rgb(217 183 108 / .10), transparent 70%), radial-gradient(50% 60% at 0% 100%, rgb(165 28 48 / .22), transparent 70%);"></div>

    <header class="relative z-10 max-w-7xl mx-auto px-5 sm:px-8 pt-6 flex items-center justify-between">
        <a href="{{ route('welcome') }}" class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-full grid place-items-center shrink-0"
                  style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px #15121A, 0 0 0 4px rgb(217 183 108 / .45);">
                <x-umc-logo class="w-[1.15rem] h-[1.15rem]" />
            </span>
            <span class="leading-tight">
                <span class="block text-[9.5px] font-bold uppercase tracking-[0.24em] text-gold-light/90">Africa University</span>
                <span class="block font-display text-[16px]">Kwang-Lim Chapel</span>
            </span>
        </a>
        <nav class="flex items-center gap-1 sm:gap-2" aria-label="Site">
            <a href="{{ route('calendar.public') }}" class="hidden sm:inline-flex btn btn-ghost !text-white/70 hover:!text-white hover:!bg-white/5">Public calendar</a>
            <a href="{{ route('clubs.register') }}" class="hidden md:inline-flex btn btn-ghost !text-white/70 hover:!text-white hover:!bg-white/5">Charter a fellowship</a>
            <a href="{{ route('login') }}" class="btn btn-glass">Sign in</a>
        </nav>
    </header>

    <div class="relative z-10 max-w-7xl mx-auto px-5 sm:px-8 pt-14 sm:pt-20 lg:pt-16 pb-16 lg:pb-24 grid grid-cols-1 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] gap-14 lg:gap-6 items-center">
        <div class="max-w-xl">
            <p class="eyebrow eyebrow-gold" data-reveal="100">Old Mutare · Zimbabwe</p>
            <h1 class="hero-title font-display text-[3.1rem] sm:text-[4.4rem] xl:text-[5.2rem] leading-[0.98] tracking-[-0.035em]"
                style="font-weight: 300 !important; --wght: 300; --soft: 100;" data-ink data-ink-delay="200">
                Worship, kept in <em class="italic text-gold-light">good order.</em>
            </h1>
            <p class="mt-7 text-[15.5px] sm:text-[17px] leading-relaxed text-white/60 max-w-lg" data-reveal="700">
                The ministry portal of Kwang-Lim Chapel — proposals and clearances, orders of service,
                hymnody and the records of every fellowship, kept through the Christian year.
            </p>
            <div class="mt-9 flex flex-wrap items-center gap-3" data-reveal="850">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                    Sign in to the portal
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                </a>
                <a href="{{ route('calendar.public') }}" class="btn btn-glass btn-lg">View the chapel calendar</a>
            </div>
            <p class="mt-10 flex items-center gap-2.5 text-[12px] text-white/45" data-reveal="1000">
                <span class="w-2 h-2 rounded-full breathe" style="background: {{ $season['hex'] }}; --breathe: 217 183 108"></span>
                Today is {{ now()->format('l j F') }}, in the {{ $season['name'] }}.
            </p>
        </div>

        {{-- The Liturgical Dial --}}
        <div class="relative mx-auto w-full max-w-[34rem]" data-reveal="300">
            <div class="dial" data-dial style="transform-style: preserve-3d;">
                <div class="dial-center" data-dial-centre>
                    <canvas data-flame data-flame-level="0.7" data-flame-base="0.9" data-flame-height="0.72" class="w-14 h-20 -mb-1" aria-hidden="true"></canvas>
                    <p class="text-[9.5px] font-bold uppercase tracking-[0.24em] text-white/40 mt-1">The Christian year</p>
                    <p class="font-display text-[1.45rem] sm:text-[1.7rem] leading-tight mt-1.5 text-white" data-dial-name>{{ $season['name'] }}</p>
                    <p class="text-[11.5px] text-white/50 mt-1.5 tabular-nums" data-dial-meta>{{ $season['color'] }}</p>
                    <p class="font-display italic text-[13px] text-white/40 mt-2" data-dial-blurb></p>
                </div>
            </div>
            <p class="mt-4 text-center text-[11px] text-white/30">Point at a season to read its dates.</p>
        </div>
    </div>
</section>

{{-- ═══════════════ WHAT THE PORTAL KEEPS ═══════════════ --}}
<main>
    <section class="max-w-7xl mx-auto px-5 sm:px-8 py-20 sm:py-28 grid grid-cols-1 lg:grid-cols-[22rem_minmax(0,1fr)] gap-12 lg:gap-20">
        <div class="lg:sticky lg:top-12 self-start" data-reveal>
            <p class="eyebrow">The portal</p>
            <h2 class="font-display text-[2.2rem] sm:text-[2.6rem] leading-[1.05] tracking-[-0.03em] text-slate-900" style="font-weight: 400 !important" data-ink>
                One place for the whole life of the chapel.
            </h2>
            <p class="mt-5 text-[14.5px] leading-relaxed text-slate-500">
                From a fellowship's first proposal to the hymns sung on Sunday and the handover of its records,
                every part of chapel ministry has a home, and a clear next step.
            </p>
        </div>

        <ol class="border-t border-slate-200">
            @foreach([
                ['I', 'Proposals & clearance', 'Fellowships submit activities fourteen days ahead. Clashes are caught as they type; the Chaplaincy reviews, seals and issues a verifiable clearance pass.'],
                ['II', 'Orders of service', 'Wednesday University Chapel and Sunday worship planned in one place, printed as a bulletin and projected as slides from the same page.'],
                ['III', 'The hymnal', number_format($hymnCount ?: 2029) . ' hymns across the United Methodist Hymnal, the Methodist Hymn Book and the Shona hymnal, ready for any service.'],
                ['IV', 'Records & handover', "Each fellowship's constitution, minutes and reports kept safe in its vault, and passed on intact when leadership changes hands."],
            ] as [$num, $title, $text])
                <li class="group grid grid-cols-[3.5rem_minmax(0,1fr)] sm:grid-cols-[5rem_minmax(0,1fr)] gap-4 py-9 border-b border-slate-200" data-reveal>
                    <span class="pillar-num font-display italic text-[2rem] sm:text-[2.6rem] leading-none text-gold transition-colors duration-500 group-hover:text-brand">{{ $num }}</span>
                    <div>
                        <h3 class="font-display text-[1.45rem] sm:text-[1.65rem] leading-tight text-slate-900 tracking-[-0.02em]">{{ $title }}</h3>
                        <p class="mt-2.5 text-[14.5px] leading-relaxed text-slate-500 max-w-xl">{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- ═══════════════ THE HYMN BOARD ═══════════════ --}}
    <section class="px-5 sm:px-8 pb-20 sm:pb-28">
        <div class="max-w-5xl mx-auto rounded-[1.75rem] bg-ink text-white grain relative overflow-hidden px-6 sm:px-14 py-14 sm:py-16" data-reveal>
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true" style="background: radial-gradient(70% 80% at 50% 0%, rgb(217 183 108 / .10), transparent 70%);"></div>
            <div class="relative text-center">
                <p class="eyebrow eyebrow-gold eyebrow-plain justify-center">As it hangs on the chapel wall</p>
                <h2 class="font-display text-[1.9rem] sm:text-[2.3rem] leading-tight tracking-[-0.02em]" style="font-weight: 400 !important">The board, today</h2>
            </div>
            <dl class="relative mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                @foreach([
                    [$hymnCount ?: 2029, 'Hymns in the portal'],
                    [3, 'Hymnals: UMH · MHB · Shona'],
                    [$fellowshipCount, 'Chartered fellowships'],
                ] as [$value, $label])
                    <div class="placard rounded-2xl px-6 py-7 text-center">
                        <dd class="font-display text-[3.4rem] sm:text-[3.8rem] leading-none text-gold-bright tracking-[-0.02em] tabular-nums" style="font-weight: 400 !important" data-reel>{{ $value }}</dd>
                        <dt class="mt-4 text-[10.5px] font-bold uppercase tracking-[0.2em] text-white/45">{{ $label }}</dt>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- ═══════════════ SCRIPTURE ═══════════════ --}}
    <section class="px-5 sm:px-8 pb-24 sm:pb-32 text-center" data-reveal>
        <div class="rule-ornament max-w-xs mx-auto text-[10px]">✦</div>
        <blockquote class="mt-8 font-display italic text-[1.7rem] sm:text-[2.4rem] leading-[1.2] tracking-[-0.02em] text-slate-800 max-w-3xl mx-auto" style="font-weight: 300 !important; --wght: 300; --soft: 100;" data-ink>
            “Let all things be done decently and in order.”
        </blockquote>
        <p class="mt-5 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">1 Corinthians 14:40</p>
    </section>
</main>

<footer class="border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[12px] text-slate-500">
        <div class="flex items-center gap-3">
            <x-umc-logo variant="official" class="w-3.5 h-6" />
            <span><span class="font-display text-[14px] text-slate-800">Kwang-Lim Chapel</span> · Africa University · The United Methodist Church</span>
        </div>
        <nav class="flex items-center gap-5" aria-label="Footer">
            <a href="{{ route('calendar.public') }}" class="hover:text-slate-900 transition">Public calendar</a>
            <a href="{{ route('clubs.register') }}" class="hover:text-slate-900 transition">Club charter</a>
            <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-brand-dark transition">Sign in</a>
        </nav>
    </div>
</footer>
</body>
</html>
