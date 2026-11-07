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
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-brand: #A51C30;
            --color-brand-dark: #861527;
            --color-gold: #C59B3F;
            --color-ink: #0D0B12;
            --color-paper: #F8F7F4;
        }

        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-display { font-family: 'Fraunces', Georgia, serif; }
        .font-cinzel { font-family: 'Cinzel', serif; }

        @keyframes shimmerSweep {
            0%, 65% { left: -75%; }
            100% { left: 165%; }
        }

        @keyframes flameBreathe {
            0%, 100% {
                box-shadow: 0 0 20px rgba(165, 28, 48, 0.45), 0 0 45px rgba(197, 155, 63, 0.3);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 35px rgba(165, 28, 48, 0.7), 0 0 70px rgba(229, 184, 92, 0.45);
                transform: scale(1.03);
            }
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
            width: 48%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.28), transparent);
            transform: rotate(25deg);
            animation: shimmerSweep 4.5s ease-in-out infinite;
            pointer-events: none;
        }

        .feature-card-3d {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, border-color 0.3s ease;
            transform-style: preserve-3d;
        }

        .feature-card-3d:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 36px -10px rgba(13, 11, 18, 0.15);
            border-color: rgba(165, 28, 48, 0.35);
        }

        .gold-foil-text {
            background: linear-gradient(120deg, #F5DE9C 0%, #C59B3F 25%, #FFFFFF 50%, #C59B3F 75%, #F5DE9C 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: goldFoilSweep 7s linear infinite;
        }

        @keyframes goldFoilSweep {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-brand selection:text-white">

    <!-- Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40 transition-all">
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
        <div class="relative bg-[#0D0B12] text-white py-20 sm:py-28 px-4 sm:px-6 lg:px-8 overflow-hidden select-none">
            {{-- Dynamic Cathedral Light Engine Canvas --}}
            <canvas id="heroCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-0" aria-hidden="true"></canvas>

            <div class="relative z-10 max-w-4xl mx-auto text-center space-y-6">
                <!-- Season Badge -->
                @php
                    $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
                @endphp
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.05] border border-white/10 backdrop-blur-md text-xs text-white/80 shadow-lg">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $season['hex'] ?? '#C59B3F' }}"></span>
                    <span class="font-medium tracking-wide">{{ $season['title'] ?? 'Ordinary Time' }}</span>
                    <span class="text-white/30">&bull;</span>
                    <span class="text-white/60 font-mono text-[11px]">{{ now()->format('Y') }} Liturgical Calendar</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-display font-light tracking-tight leading-[1.12]">
                    Worship, Ministry &amp;<br>
                    <span class="font-normal italic gold-foil-text font-cinzel">Liturgical Sanctuary</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed font-light">
                    Africa University’s spiritual center in Old Mutare, Zimbabwe. Coordinating student fellowships, university chapels, choir liturgies, and records.
                </p>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('login') }}" class="btn-shimmer w-full sm:w-auto px-7 py-3 rounded-xl bg-brand hover:bg-brand-dark text-white font-bold text-sm transition shadow-lg shadow-brand/30 flex items-center justify-center gap-2">
                        <span>Sign In to Portal</span>
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('calendar.public') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-white font-semibold text-sm transition backdrop-blur-md border border-white/10">
                        View Chapel Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Three Pillars Feature Showcase -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-brand">Institutional Coordination</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2 font-display">Dedicated to Campus Ministry</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature 1 -->
                <div class="feature-card-3d bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-3.5">
                    <div class="w-11 h-11 rounded-xl bg-brand-soft text-brand flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 font-display">Service Scheduling</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Weekly Wednesday University Chapels, Sunday Celebrations, and 14+ student denominational club fellowship reservations.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card-3d bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 font-display">Liturgical Hymnody &amp; Media</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Complete digital hymn catalogs across UMH, MHB, and Shona hymnals with instant broadcast projection and audio-visual curation.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card-3d bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-3.5">
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

        let width = 0, height = 0, time = 0;
        function resize() {
            const dpr = window.devicePixelRatio || 1;
            width = canvas.parentElement.clientWidth;
            height = canvas.parentElement.clientHeight;
            canvas.width = width * dpr;
            canvas.height = height * dpr;
            ctx.resetTransform?.();
            ctx.scale(dpr, dpr);
        }

        const rays = [
            { angle: -0.45, originX: 0.1, spread: 0.22, speed: 0.007, phase: 0 },
            { angle: -0.32, originX: 0.35, spread: 0.26, speed: 0.009, phase: 1.8 },
            { angle: -0.20, originX: 0.65, spread: 0.24, speed: 0.006, phase: 3.6 }
        ];

        const embers = [];
        for (let i = 0; i < 24; i++) {
            embers.push({
                x: Math.random() * 1200,
                y: Math.random() * 600,
                r: Math.random() * 2 + 0.8,
                vy: Math.random() * 0.4 + 0.25,
                vx: (Math.random() - 0.5) * 0.3,
                pulse: Math.random() * Math.PI * 2
            });
        }

        function draw() {
            time += 0.012;
            if (!width || !height) {
                requestAnimationFrame(draw);
                return;
            }
            ctx.clearRect(0, 0, width, height);

            // 1. Ambient Warmth Blobs
            const g1 = ctx.createRadialGradient(width * 0.2, height * 0.4, 10, width * 0.2, height * 0.4, width * 0.8);
            g1.addColorStop(0, 'rgba(165, 28, 48, 0.25)');
            g1.addColorStop(1, 'transparent');
            ctx.fillStyle = g1;
            ctx.fillRect(0, 0, width, height);

            const g2 = ctx.createRadialGradient(width * 0.8, height * 0.7, 10, width * 0.8, height * 0.7, width * 0.6);
            g2.addColorStop(0, 'rgba(197, 155, 63, 0.20)');
            g2.addColorStop(1, 'transparent');
            ctx.fillStyle = g2;
            ctx.fillRect(0, 0, width, height);

            // 2. Volumetric God-Rays
            ctx.save();
            ctx.globalCompositeOperation = 'screen';
            rays.forEach((ray, i) => {
                const dynamicAlpha = 0.65 + Math.sin(time * ray.speed * 60 + ray.phase) * 0.35;
                const originX = width * ray.originX;
                const originY = -30;
                const bottomX1 = originX + Math.tan(ray.angle) * height * 1.5 - (width * ray.spread);
                const bottomX2 = originX + Math.tan(ray.angle) * height * 1.5 + (width * ray.spread);

                const rayGrad = ctx.createLinearGradient(originX, originY, bottomX1, height);
                const color = i === 1 ? 'rgba(165, 28, 48, 0.18)' : 'rgba(229, 184, 92, 0.22)';
                rayGrad.addColorStop(0, color);
                rayGrad.addColorStop(0.7, color.replace(/[\d.]+\)$/, (0.07 * dynamicAlpha) + ')'));
                rayGrad.addColorStop(1, 'transparent');

                ctx.beginPath();
                ctx.moveTo(originX, originY);
                ctx.lineTo(bottomX1, height);
                ctx.lineTo(bottomX2, height);
                ctx.closePath();
                ctx.fillStyle = rayGrad;
                ctx.fill();
            });
            ctx.restore();

            // 3. Sacred Embers
            ctx.save();
            for (let p of embers) {
                p.y -= p.vy;
                p.x += p.vx + Math.sin(time + p.pulse) * 0.3;
                p.pulse += 0.02;
                if (p.y < 0) p.y = height;
                if (p.x < 0) p.x = width;
                if (p.x > width) p.x = 0;

                const alpha = 0.2 + (0.5 * Math.sin(p.pulse) + 0.5) * 0.4;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(245, 222, 156, ${alpha})`;
                ctx.shadowBlur = 8;
                ctx.shadowColor = 'rgba(229, 184, 92, 0.6)';
                ctx.fill();
                ctx.shadowBlur = 0;
            }
            ctx.restore();

            requestAnimationFrame(draw);
        }

        window.addEventListener('resize', resize);
        resize();
        draw();
    })();
    </script>
</body>
</html>
