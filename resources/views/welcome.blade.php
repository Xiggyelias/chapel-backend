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

        /* ═══ Premium Sacred Motion ═══ */
        @keyframes shimmerSweep {
            0%, 65% { left: -75%; }
            100% { left: 165%; }
        }

        .btn-shimmer {
            position: relative;
            overflow: hidden;
        }

        .btn-shimmer::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -75%;
            width: 45%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.28), transparent);
            transform: rotate(25deg);
            animation: shimmerSweep 4.5s ease-in-out infinite;
            pointer-events: none;
        }

        .hymn-card-3d {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease, border-color 0.35s ease;
            transform-style: preserve-3d;
        }

        .hymn-card-3d:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.6), 0 0 25px -5px rgba(217, 183, 108, 0.2);
            border-color: rgba(217, 183, 108, 0.45);
        }

        @media (prefers-reduced-motion: reduce) {
            .btn-shimmer::after { animation: none !important; }
            .hymn-card-3d:hover { transform: none !important; }
            #sanctuaryHeroCanvas { display: none !important; }
        }
    </style>
</head>
<body class="classic bg-paper text-slate-800 antialiased selection:bg-brand selection:text-white">
@php
    $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
    $hymnCount = rescue(fn () => \App\Models\Hymn::count(), 0, false);
    $fellowshipCount = rescue(fn () => \App\Models\Club::count(), 0, false);
@endphp

{{-- ═══════════════ HERO (ink) with Living Sacred Lightfield ═══════════════ --}}
<section class="relative bg-ink text-white overflow-hidden grain on-dark" id="heroSanctuary">
    {{-- Sacred Lightfield Canvas: Ambient warmth and gentle floating embers --}}
    <canvas id="sanctuaryHeroCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-0" aria-hidden="true"></canvas>

    {{-- Quiet warmth in the corners --}}
    <div class="absolute inset-0 pointer-events-none z-1" aria-hidden="true"
         style="background: radial-gradient(60% 50% at 78% 45%, rgb(217 183 108 / .12), transparent 70%), radial-gradient(50% 60% at 0% 100%, rgb(165 28 48 / .24), transparent 70%);"></div>

    <header class="relative z-10 max-w-7xl mx-auto px-5 sm:px-8 pt-6 flex items-center justify-between">
        <a href="{{ Route::has('welcome') ? route('welcome') : url('/welcome') }}" class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-full grid place-items-center shrink-0 shadow-lg"
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
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-gold btn-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-glass">Sign in</a>
            @endauth
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
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg btn-shimmer">
                        Enter Dashboard
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg btn-shimmer">
                        Sign in to the portal
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                    </a>
                @endauth
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

{{-- ═══════════════ THE COMPLETE LITURGICAL HYMNODY SHELF ═══════════════ --}}
<section class="max-w-7xl mx-auto px-5 sm:px-8 py-16 sm:py-20 border-b border-slate-200/80">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <p class="eyebrow">Liturgical Hymnody · All Three Collections</p>
            <h2 class="font-display text-[2.1rem] sm:text-[2.5rem] leading-[1.08] tracking-[-0.03em] text-slate-900" style="font-weight: 400 !important">
                Every hymn from <em class="italic text-brand">#1 to the end.</em>
            </h2>
            <p class="mt-2 text-[14.5px] text-slate-500 max-w-xl">
                Complete, unabridged digital hymnals for worship planning, projection slides, and devotional praise.
            </p>
        </div>
        <a href="{{ route('hymns.index') }}" class="btn btn-secondary self-start md:self-auto flex items-center gap-2">
            <span>Explore full Digital Hymnary</span>
            <span class="badge badge-gold">2,029 Hymns</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- UMH Card --}}
        <a href="{{ route('hymns.index', ['hymnal' => 'UMH']) }}" class="hymn-card-3d block p-6 sm:p-7 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-[#A51C30]/10 text-[#A51C30]">
                    UMH
                </span>
                <span class="text-xs font-semibold text-slate-400 group-hover:text-brand transition-colors">734 Hymns</span>
            </div>
            <h3 class="mt-4 font-display text-[1.4rem] font-semibold text-slate-900 group-hover:text-[#A51C30] transition-colors leading-tight">
                The United Methodist Hymnal
            </h3>
            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                General services, Christian year, sacraments, Wesley's core hymns &amp; global anthems (1–734).
            </p>
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span class="font-medium text-emerald-600">✓ 100% Texts Entered</span>
                <span class="font-semibold text-slate-700 group-hover:translate-x-1 transition-transform">Browse &rarr;</span>
            </div>
        </a>

        {{-- MHB Card --}}
        <a href="{{ route('hymns.index', ['hymnal' => 'MHB']) }}" class="hymn-card-3d block p-6 sm:p-7 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-sky-100 text-sky-800">
                    MHB
                </span>
                <span class="text-xs font-semibold text-slate-400 group-hover:text-sky-700 transition-colors">984 Hymns</span>
            </div>
            <h3 class="mt-4 font-display text-[1.4rem] font-semibold text-slate-900 group-hover:text-sky-800 transition-colors leading-tight">
                The Methodist Hymn Book
            </h3>
            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                Historic British Wesleyan hymn book with metrical tunes, canticles, and theological depth (1–984).
            </p>
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span class="font-medium text-emerald-600">✓ 100% Texts Entered</span>
                <span class="font-semibold text-slate-700 group-hover:translate-x-1 transition-transform">Browse &rarr;</span>
            </div>
        </a>

        {{-- SHONA Card --}}
        <a href="{{ route('hymns.index', ['hymnal' => 'SHONA']) }}" class="hymn-card-3d block p-6 sm:p-7 rounded-2xl bg-white border border-slate-200/90 shadow-sm relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-amber-100 text-amber-800">
                    SHONA
                </span>
                <span class="text-xs font-semibold text-slate-400 group-hover:text-amber-800 transition-colors">311 Hymns</span>
            </div>
            <h3 class="mt-4 font-display text-[1.4rem] font-semibold text-slate-900 group-hover:text-amber-800 transition-colors leading-tight">
                Ndiyo Nziyo dzeMethodist
            </h3>
            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                Shona Methodist Hymnal resonating with indigenous praise, call-and-response, and communion (1–311).
            </p>
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span class="font-medium text-emerald-600">✓ 100% Texts Entered</span>
                <span class="font-semibold text-slate-700 group-hover:translate-x-1 transition-transform">Browse &rarr;</span>
            </div>
        </a>
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
            @auth
                <a href="{{ route('dashboard') }}" class="font-semibold text-brand hover:text-brand-dark transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-brand-dark transition">Sign in</a>
            @endauth
        </nav>
    </div>
</footer>

{{-- ═══ HIGH-PERFORMANCE SACRED STARDUST LIGHTFIELD SCRIPT ═══ --}}
<script>
(() => {
    const canvas = document.getElementById('sanctuaryHeroCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let width = 0, height = 0, animId = null;
    let time = 0;

    const mouse = {
        x: 0,
        y: 0,
        targetX: 0,
        targetY: 0,
        active: false
    };

    function resize() {
        const dpr = window.devicePixelRatio || 1;
        width = canvas.parentElement.clientWidth;
        height = canvas.parentElement.clientHeight;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        ctx.resetTransform?.();
        ctx.scale(dpr, dpr);
        mouse.targetX = width * 0.5;
        mouse.targetY = height * 0.5;
    }

    const container = document.getElementById('heroSanctuary');
    if (container) {
        container.addEventListener('mousemove', (e) => {
            const rect = container.getBoundingClientRect();
            mouse.targetX = e.clientX - rect.left;
            mouse.targetY = e.clientY - rect.top;
            mouse.active = true;
        });
        container.addEventListener('mouseleave', () => {
            mouse.targetX = width * 0.5;
            mouse.targetY = height * 0.5;
            mouse.active = false;
        });
    }

    // Sacred golden embers
    const particleCount = 38;
    const particles = [];
    for (let i = 0; i < particleCount; i++) {
        particles.push({
            x: Math.random() * 1200,
            y: Math.random() * 800,
            r: 0.8 + Math.random() * 1.8,
            vy: 0.2 + Math.random() * 0.45,
            vx: (Math.random() - 0.5) * 0.3,
            pulse: Math.random() * Math.PI * 2,
            baseAlpha: 0.15 + Math.random() * 0.45
        });
    }

    function render() {
        time += 0.01;
        if (!width || !height) {
            animId = requestAnimationFrame(render);
            return;
        }

        // Smooth mouse lerp
        mouse.x += (mouse.targetX - mouse.x) * 0.05;
        mouse.y += (mouse.targetY - mouse.y) * 0.05;

        ctx.clearRect(0, 0, width, height);

        // 1. Fluid Ambient Warmth Follow
        const warmX = width * 0.72 + (mouse.x - width / 2) * 0.06;
        const warmY = height * 0.45 + (mouse.y - height / 2) * 0.06;
        const gWarm = ctx.createRadialGradient(warmX, warmY, 10, warmX, warmY, width * 0.65);
        gWarm.addColorStop(0, 'rgba(217, 183, 108, 0.16)');
        gWarm.addColorStop(0.6, 'rgba(165, 28, 48, 0.08)');
        gWarm.addColorStop(1, 'transparent');
        ctx.fillStyle = gWarm;
        ctx.fillRect(0, 0, width, height);

        // 2. Rising Stardust Motes
        ctx.save();
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.y -= p.vy;
            p.x += p.vx + Math.sin(time + p.pulse) * 0.35;
            p.pulse += 0.018;

            // Interactive gentle deflection
            if (mouse.active) {
                const dx = p.x - mouse.x;
                const dy = p.y - mouse.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 140) {
                    const force = (1 - dist / 140) * 0.8;
                    p.x += (dx / dist) * force;
                    p.y += (dy / dist) * force;
                }
            }

            if (p.y < -10) { p.y = height + 10; p.x = Math.random() * width; }
            if (p.x < -10) p.x = width + 10;
            if (p.x > width + 10) p.x = -10;

            const currentAlpha = p.baseAlpha * (0.65 + Math.sin(p.pulse) * 0.35);
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(245, 222, 156, ${currentAlpha})`;
            ctx.shadowBlur = 10;
            ctx.shadowColor = 'rgba(217, 183, 108, 0.6)';
            ctx.fill();
            ctx.shadowBlur = 0;
        }
        ctx.restore();

        animId = requestAnimationFrame(render);
    }

    window.addEventListener('resize', resize);
    resize();
    render();

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (animId) cancelAnimationFrame(animId);
        } else {
            animId = requestAnimationFrame(render);
        }
    });
})();
</script>
</body>
</html>
