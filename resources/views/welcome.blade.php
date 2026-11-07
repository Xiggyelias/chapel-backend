<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#A51C30">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Kwang-Lim Chapel | Ministry Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-brand: #A51C30;
            --color-brand-dark: #861527;
            --color-gold: #B8892B;
            --color-ink: #101118;
            --color-paper: #F7F6F3;
        }

        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-display { font-family: 'Fraunces', Georgia, serif; }

        @keyframes shimmerSweep {
            0%, 65% { left: -70%; }
            100% { left: 160%; }
        }

        @keyframes auraPulse {
            0% { box-shadow: 0 0 20px rgba(165, 28, 48, 0.35); }
            50% { box-shadow: 0 0 35px rgba(165, 28, 48, 0.6), 0 0 60px rgba(184, 137, 43, 0.3); }
            100% { box-shadow: 0 0 20px rgba(165, 28, 48, 0.35); }
        }

        .btn-shimmer {
            position: relative;
            overflow: hidden;
        }

        .btn-shimmer::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -70%;
            width: 45%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transform: rotate(25deg);
            animation: shimmerSweep 4.5s ease-in-out infinite;
            pointer-events: none;
        }

        .feature-card-3d {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease, border-color 0.25s ease;
            transform-style: preserve-3d;
        }

        .feature-card-3d:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px -8px rgba(16, 17, 24, 0.12);
            border-color: rgba(165, 28, 48, 0.3);
        }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-brand selection:text-white">

    <!-- Navigation Header -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-brand flex items-center justify-center p-2 shadow-sm shadow-brand/20">
                    <x-umc-logo class="w-full h-full text-white" />
                </div>
                <div>
                    <span class="text-sm font-bold text-slate-900 block leading-tight font-display">Kwang-Lim Chapel</span>
                    <span class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Africa University</span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('calendar.public') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition">
                    Public Calendar
                </a>
                <a href="{{ route('login') }}" class="btn-shimmer px-4 py-2 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-bold transition shadow-sm active:scale-95 flex items-center gap-1.5">
                    <span>Sign In</span>
                    <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Showcase Section -->
    <main class="flex-1">
        <div class="relative bg-ink text-white py-20 sm:py-28 px-4 sm:px-6 lg:px-8 overflow-hidden">
            {{-- Canvas for subtle particle aurora --}}
            <canvas id="heroCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-0" aria-hidden="true"></canvas>

            <div class="max-w-4xl mx-auto text-center space-y-6 relative z-10">
                @php
                    $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
                @endphp
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-white/[0.06] border border-white/10 backdrop-blur-md text-xs font-semibold text-white/90">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $season['hex'] ?? '#B8892B' }}"></span>
                    <span>{{ $season['title'] ?? 'Chapel Ministry &amp; Fellowship Management' }}</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-display font-semibold tracking-tight text-white leading-tight">
                    Kwang-Lim Chapel <br class="hidden sm:inline">
                    <span class="italic font-light text-white/90">Ministry Portal</span>
                </h1>

                <p class="text-sm sm:text-base text-white/60 max-w-2xl mx-auto font-normal leading-relaxed">
                    A sacred, centralized system for Africa University fellowships, Sunday convocations, liturgical planning, equipment desk, and institutional memory.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3 pt-4">
                    <a href="{{ route('login') }}" class="btn-shimmer px-7 py-3 rounded-xl bg-brand hover:bg-brand-dark active:scale-95 text-white font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-brand/25 flex items-center space-x-2">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Sign In to Portal</span>
                    </a>
                    <a href="{{ route('calendar.public') }}" class="px-6 py-3 rounded-xl bg-white/[0.08] hover:bg-white/[0.14] active:scale-95 text-white font-semibold text-xs transition border border-white/15 backdrop-blur-md flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>View Master Calendar</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Clean Feature Cards -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 -mt-8 relative z-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="feature-card-3d bg-white rounded-2xl p-7 border border-slate-200/90 shadow-sm space-y-3.5">
                    <div class="w-11 h-11 rounded-xl bg-red-50 text-brand flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 font-display">Activity Clearance &amp; Passes</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Enforce the 14-day rule (§4.1), automated venue and time clash detection, VC clearance notes, and cryptographically verified gatekeeper QR passes.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="feature-card-3d bg-white rounded-2xl p-7 border border-slate-200/90 shadow-sm space-y-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-gold flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 font-display">Master Worship Calendar</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Curated schedule for University Convocations, Sunday services, fellowship gatherings, and external iCal calendar feed synchronization.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="feature-card-3d bg-white rounded-2xl p-7 border border-slate-200/90 shadow-sm space-y-3.5">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 font-display">Records Vault &amp; Handover</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Permanent club-isolated archives for constitutions, financial reports, minutes, and structured 4-step presidential leadership transition.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Institutional Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-7 px-4 sm:px-6 lg:px-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div>
                <span class="font-bold text-white text-sm font-display">Kwang-Lim Chapel</span>
                <p class="text-[11px] text-slate-400 mt-0.5">Africa University &bull; Ministry &amp; Fellowship Management Portal</p>
            </div>
            <div class="flex items-center space-x-4 text-[11px]">
                <a href="{{ route('calendar.public') }}" class="hover:text-white transition">Public Calendar</a>
                <span>&bull;</span>
                <a href="{{ route('clubs.register') }}" class="hover:text-white transition">Club Charter</a>
                <span>&bull;</span>
                <a href="{{ route('login') }}" class="hover:text-white transition font-bold text-brand">Sign In</a>
            </div>
        </div>
    </footer>

    <script>
    (function initHeroCanvas() {
        const canvas = document.getElementById('heroCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        let width = canvas.width = canvas.clientWidth;
        let height = canvas.height = canvas.clientHeight;
        const particles = [];
        for (let i = 0; i < 22; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                r: Math.random() * 2 + 1,
                alpha: Math.random() * 0.4 + 0.1,
                vy: Math.random() * 0.4 + 0.2,
                vx: (Math.random() - 0.5) * 0.2
            });
        }

        function draw() {
            ctx.clearRect(0, 0, width, height);
            for (let p of particles) {
                p.y -= p.vy;
                p.x += p.vx;
                if (p.y < 0) p.y = height;
                if (p.x < 0) p.x = width;
                if (p.x > width) p.x = 0;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(229, 195, 120, ${p.alpha})`;
                ctx.fill();
            }
            requestAnimationFrame(draw);
        }
        draw();
    })();
    </script>
</body>
</html>
