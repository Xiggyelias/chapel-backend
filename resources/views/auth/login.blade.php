<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#A51C30">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Sign In | Kwang-Lim Chapel Ministry Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-brand: #A51C30;
            --color-brand-dark: #861527;
            --color-gold: #B8892B;
            --color-gold-light: #E5C378;
            --color-ink: #101118;
            --color-paper: #F7F6F3;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: var(--color-paper);
        }

        .font-display {
            font-family: 'Fraunces', Georgia, serif;
        }

        /* ═══ Premium Motion Keyframes ═══ */
        @keyframes auraPulse {
            0% {
                box-shadow: 0 0 25px rgba(165, 28, 48, 0.4), 0 0 50px rgba(184, 137, 43, 0.2);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 45px rgba(165, 28, 48, 0.65), 0 0 85px rgba(184, 137, 43, 0.35);
                transform: scale(1.02);
            }
            100% {
                box-shadow: 0 0 25px rgba(165, 28, 48, 0.4), 0 0 50px rgba(184, 137, 43, 0.2);
                transform: scale(1);
            }
        }

        @keyframes drawLine {
            0% { width: 0; opacity: 0; }
            100% { width: 3rem; opacity: 1; }
        }

        @keyframes entranceSlideUp {
            0% { opacity: 0; transform: translateY(22px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes shimmerSweep {
            0%, 60% { left: -70%; }
            100% { left: 160%; }
        }

        @keyframes floatGentle {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        /* Motion utility classes */
        .animate-aura {
            animation: auraPulse 5s ease-in-out infinite;
        }

        .animate-gold-line {
            animation: drawLine 1.4s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards;
        }

        .animate-entrance {
            opacity: 0;
            animation: entranceSlideUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

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



        /* Respect accessibility reduced-motion preference */
        @media (prefers-reduced-motion: reduce) {
            .animate-aura,
            .animate-gold-line,
            .animate-entrance,
            .btn-shimmer::after {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
                width: auto !important;
            }
            #sacredCanvas {
                display: none;
            }
        }
    </style>
</head>
<body class="min-h-full antialiased text-slate-800 selection:bg-brand selection:text-white">

<div class="min-h-screen grid lg:grid-cols-[1.1fr_490px]">

    {{-- ═══ LEFT PANEL: Sacred Motion Canvas & Sanctuary Showcase ═══ --}}
    <div id="sanctuaryStage" class="hidden lg:flex flex-col justify-between p-12 relative overflow-hidden select-none"
         style="background: var(--color-ink)">

        {{-- Dynamic WebGL/Canvas Aurora Layer --}}
        <canvas id="sacredCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-0" aria-hidden="true"></canvas>

        {{-- Subtle geometric cathedral grid overlay --}}
        <div class="absolute inset-0 opacity-[0.035] pointer-events-none z-1" aria-hidden="true"
             style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2244%22 height=%2244%22%3E%3Crect width=%2244%22 height=%2244%22 fill=%22none%22/%3E%3Cpath d=%22M0 22h44M22 0v44%22 stroke=%22%23ffffff%22 stroke-width=%220.5%22/%3E%3Ccircle cx=%2222%22 cy=%2222%22 r=%222%22 fill=%22%23ffffff%22 opacity=%220.3%22/%3E%3C/svg%3E')"></div>

        {{-- Radiant radial warmth anchor --}}
        <div class="absolute -bottom-36 -left-36 w-[32rem] h-[32rem] rounded-full opacity-20 pointer-events-none blur-3xl"
             style="background: radial-gradient(circle, var(--color-brand) 0%, transparent 70%)"></div>
        <div class="absolute -top-28 -right-28 w-96 h-96 rounded-full opacity-15 pointer-events-none blur-3xl"
             style="background: radial-gradient(circle, var(--color-gold) 0%, transparent 70%)"></div>

        {{-- Top: UMC Cross & Flame Insignia with Living Glow --}}
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3.5 group cursor-default">
                <div class="w-12 h-12 rounded-2xl bg-brand/25 ring-1 ring-brand/50 flex items-center justify-center p-2.5 animate-aura backdrop-blur-md transition-all duration-300 group-hover:scale-105">
                    <x-umc-logo class="w-full h-full drop-shadow-md" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-gold opacity-90">Africa University</span>
                        <span class="w-1 h-1 rounded-full bg-gold/60"></span>
                        <span class="text-[10px] text-white/40 font-medium">Zimbabwe</span>
                    </div>
                    <p class="text-base font-bold text-white/90 tracking-tight font-display">Kwang-Lim Chapel</p>
                </div>
            </div>

            {{-- Live Liturgical Indicator Pill --}}
            @php
                $currentSeason = \App\Services\LiturgicalCalendarService::getCurrentSeason();
            @endphp
            <div class="hidden xl:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.04] border border-white/10 backdrop-blur-md text-xs text-white/80 transition-all hover:bg-white/[0.08]">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: {{ $currentSeason['hex'] ?? '#B8892B' }}"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2" style="background-color: {{ $currentSeason['hex'] ?? '#B8892B' }}"></span>
                </span>
                <span class="text-[11px] font-medium tracking-wide text-white/70">{{ $currentSeason['title'] ?? 'Sacred Season' }}</span>
            </div>
        </div>

        {{-- Center: Kinetic Scripture Quote with Elegant Reveal --}}
        <div class="relative z-10 my-auto py-8">
            <div class="h-0.5 mb-6 rounded-full animate-gold-line" style="background: linear-gradient(90deg, var(--color-gold), transparent)"></div>

            <blockquote class="text-4xl xl:text-5xl font-display font-light text-white leading-[1.18] max-w-lg tracking-tight">
                "Worship the Lord<br>
                <span class="font-normal italic" style="color: rgba(255, 255, 255, 0.96)">in the splendour</span><br>
                of his holiness."
            </blockquote>

            <p class="mt-5 text-xs text-white/45 font-medium flex items-center gap-2">
                <span class="text-gold opacity-80 font-semibold tracking-wider">PSALM 29:2</span>
                <span class="text-white/20">&bull;</span>
                <span>Ministry, Fellowship &amp; Liturgical Management</span>
            </p>
        </div>


    </div>

    {{-- ═══ RIGHT PANEL: The Gateway & Sign-In Card ═══ --}}
    <div class="flex flex-col justify-center min-h-screen lg:min-h-0 p-6 sm:p-12 bg-paper relative z-10">

        {{-- Mobile Header Logo --}}
        <div class="flex lg:hidden items-center justify-center gap-3 mb-8">
            <div class="w-12 h-12 rounded-2xl bg-brand flex items-center justify-center shadow-lg shadow-brand/20 p-2.5">
                <x-umc-logo class="w-full h-full text-white" />
            </div>
            <div>
                <p class="text-xs font-extrabold uppercase tracking-wider text-brand">Africa University</p>
                <p class="text-sm font-bold text-slate-900 font-display">Kwang-Lim Chapel Portal</p>
            </div>
        </div>

        <div class="max-w-sm w-full mx-auto space-y-6 animate-entrance">

            {{-- Heading & Liturgical Season Badge --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-soft text-brand border border-brand/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                        Official Gateway
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium">{{ now()->format('M j, Y') }}</span>
                </div>

                <h1 class="text-3xl font-display font-semibold text-slate-900 tracking-tight">Welcome back</h1>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sign in with your official university credentials to access ministry coordination, services and documents.
                </p>
            </div>

            {{-- Validation Error Alert --}}
            @if($errors->any())
                <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs animate-entrance">
                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Authentication Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                @csrf

                {{-- Email Input --}}
                <div class="space-y-1.5">
                    <label for="login_email" class="block text-xs font-bold text-slate-700 tracking-wide">University Email</label>
                    <div class="relative group">
                        <input type="email" id="login_email" name="email" value="{{ old('email') }}" required
                               autofocus autocomplete="email"
                               placeholder="user@africau.edu"
                               class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand text-slate-900 bg-white transition-all shadow-2xs group-hover:border-slate-400">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Password Input --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="login_password" class="block text-xs font-bold text-slate-700 tracking-wide">Password</label>
                    </div>
                    <div class="relative group">
                        <input type="password" id="login_password" name="password" required
                               autocomplete="current-password"
                               placeholder="Enter your account password"
                               class="w-full pl-10 pr-11 py-3 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand text-slate-900 bg-white transition-all shadow-2xs group-hover:border-slate-400">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <button type="button" id="togglePwd" aria-label="Toggle password visibility"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition">
                            <svg id="pwdEye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Switch --}}
                <div class="flex items-center justify-between text-xs text-slate-600 pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" checked
                               class="rounded border-slate-300 text-brand focus:ring-brand focus:ring-offset-0 transition">
                        <span class="font-medium text-slate-600">Keep me signed in</span>
                    </label>
                </div>

                {{-- Premium Shimmer Button --}}
                <button type="submit" id="loginBtn"
                        class="btn-shimmer w-full py-3 px-4 rounded-xl bg-brand hover:bg-brand-dark active:scale-[0.98] text-white text-sm font-bold tracking-wide transition-all shadow-md shadow-brand/20 mt-2 flex items-center justify-center gap-2">
                    <span id="btnText">Sign in to Chapel Portal</span>
                    <svg id="btnArrow" class="w-4 h-4 stroke-[2.5] transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                    <svg id="btnSpinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            {{-- Quick Links --}}
            <div class="pt-4 border-t border-slate-200/80 flex items-center justify-between text-xs">
                <a href="{{ route('clubs.register') }}"
                   class="font-semibold text-brand hover:underline inline-flex items-center gap-1 group">
                    <span>Apply for club charter</span>
                    <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                </a>
                <a href="{{ route('calendar.public') }}" target="_blank" rel="noopener"
                   class="text-slate-400 hover:text-slate-700 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Public calendar</span>
                </a>
            </div>

            {{-- Expandable Access Onboarding Accordion --}}
            <div class="border border-slate-200/70 rounded-2xl p-3.5 bg-slate-50/70 backdrop-blur-xs transition">
                <button type="button" id="regToggle"
                        onclick="document.getElementById('regInfo').classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180')"
                        class="w-full flex items-center justify-between text-[11px] font-semibold text-slate-500 hover:text-slate-800 transition cursor-pointer">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        How do staff &amp; club executives get access?
                    </span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="regInfo" class="hidden mt-3 text-[11px] text-slate-500 space-y-2 leading-relaxed pt-2 border-t border-slate-200/60">
                    <p><strong class="text-slate-700">Chaplaincy Office:</strong> Staff accounts are provisioned directly by the Chapel Secretary and ICT.</p>
                    <p><strong class="text-slate-700">Fellowships &amp; Clubs:</strong> Apply via "Apply for club charter" above. Upon approval, presidential credentials are delivered securely.</p>
                    <p><strong class="text-slate-700">Campus Life / SRC:</strong> Observer credentials issued upon affiliation confirmation.</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <p class="mt-8 text-center text-[11px] text-slate-400 max-w-sm mx-auto flex items-center justify-center gap-2">
            <span>Kwang-Lim Chapel</span>
            <span>&bull;</span>
            <span>Africa University</span>
            <span>&bull;</span>
            <span>Mutare, Zimbabwe</span>
        </p>
    </div>
</div>

{{-- ═══ Interactive Motion Scripts ═══ --}}
<script>
// Password peek
function togglePassword() {
    const field = document.getElementById('login_password');
    const eye = document.getElementById('pwdEye');
    if (field.type === 'password') {
        field.type = 'text';
        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        field.type = 'password';
        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

// Form submission loading transition
document.getElementById('loginForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    const text = document.getElementById('btnText');
    const arrow = document.getElementById('btnArrow');
    const spinner = document.getElementById('btnSpinner');

    btn.disabled = true;
    text.textContent = 'Authenticating…';
    arrow?.classList.add('hidden');
    spinner?.classList.remove('hidden');
    btn.classList.add('opacity-85');
});

// ═══ Sacred Aurora & Embers Particle Engine ═══
(function initSacredCanvas() {
    const canvas = document.getElementById('sacredCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let width = 0;
    let height = 0;
    let animId = null;
    let mouse = { x: -1000, y: -1000, targetX: 0, targetY: 0 };

    function resize() {
        const rect = canvas.getBoundingClientRect();
        width = canvas.width = rect.width * (window.devicePixelRatio || 1);
        height = canvas.height = rect.height * (window.devicePixelRatio || 1);
        ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);
    }

    const stage = document.getElementById('sanctuaryStage');
    if (stage) {
        stage.addEventListener('mousemove', (e) => {
            const rect = stage.getBoundingClientRect();
            mouse.targetX = e.clientX - rect.left;
            mouse.targetY = e.clientY - rect.top;
        });
        stage.addEventListener('mouseleave', () => {
            mouse.targetX = stage.clientWidth / 2;
            mouse.targetY = stage.clientHeight / 2;
        });
    }

    // Sacred light motes (embers / stardust)
    const particleCount = 28;
    const particles = [];
    for (let i = 0; i < particleCount; i++) {
        particles.push({
            x: Math.random() * 800,
            y: Math.random() * 1000,
            r: 1 + Math.random() * 2.2,
            baseAlpha: 0.12 + Math.random() * 0.38,
            speedY: 0.25 + Math.random() * 0.55,
            speedX: (Math.random() - 0.5) * 0.25,
            pulse: Math.random() * Math.PI * 2,
            hue: Math.random() > 0.4 ? 'gold' : 'crimson'
        });
    }

    let time = 0;
    function render() {
        time += 0.008;

        const w = canvas.clientWidth;
        const h = canvas.clientHeight;
        if (!w || !h) {
            animId = requestAnimationFrame(render);
            return;
        }

        // Smooth mouse lerp
        mouse.x += (mouse.targetX - mouse.x) * 0.05;
        mouse.y += (mouse.targetY - mouse.y) * 0.05;

        ctx.clearRect(0, 0, w, h);

        // 1. Organic breathing ambient aurora fields
        const aurora1X = w * 0.3 + Math.sin(time * 0.7) * 60 + (mouse.x - w / 2) * 0.08;
        const aurora1Y = h * 0.75 + Math.cos(time * 0.5) * 45 + (mouse.y - h / 2) * 0.08;
        const grad1 = ctx.createRadialGradient(aurora1X, aurora1Y, 10, aurora1X, aurora1Y, w * 0.75);
        grad1.addColorStop(0, 'rgba(165, 28, 48, 0.22)'); // Collegiate Crimson
        grad1.addColorStop(0.5, 'rgba(110, 16, 32, 0.09)');
        grad1.addColorStop(1, 'transparent');
        ctx.fillStyle = grad1;
        ctx.fillRect(0, 0, w, h);

        const aurora2X = w * 0.75 + Math.cos(time * 0.6) * 55;
        const aurora2Y = h * 0.25 + Math.sin(time * 0.8) * 40;
        const grad2 = ctx.createRadialGradient(aurora2X, aurora2Y, 10, aurora2X, aurora2Y, w * 0.65);
        grad2.addColorStop(0, 'rgba(184, 137, 43, 0.16)'); // Warm Cathedral Gold
        grad2.addColorStop(0.5, 'rgba(140, 95, 25, 0.06)');
        grad2.addColorStop(1, 'transparent');
        ctx.fillStyle = grad2;
        ctx.fillRect(0, 0, w, h);

        // 2. Rising glowing embers / stardust
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.y -= p.speedY;
            p.x += p.speedX + Math.sin(time + p.pulse) * 0.3;
            p.pulse += 0.02;

            // Loop reset
            if (p.y < -20) {
                p.y = h + 10;
                p.x = Math.random() * w;
            }
            if (p.x < -20) p.x = w + 10;
            if (p.x > w + 20) p.x = -10;

            const alpha = p.baseAlpha * (0.6 + Math.sin(p.pulse) * 0.4);
            const color = p.hue === 'gold'
                ? `rgba(229, 195, 120, ${alpha})`
                : `rgba(235, 125, 145, ${alpha * 0.85})`;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = color;
            ctx.shadowBlur = 10;
            ctx.shadowColor = p.hue === 'gold' ? 'rgba(212, 175, 55, 0.6)' : 'rgba(165, 28, 48, 0.6)';
            ctx.fill();
            ctx.shadowBlur = 0;
        }

        animId = requestAnimationFrame(render);
    }

    window.addEventListener('resize', resize);
    resize();
    render();

    // Pause when tab is not visible to preserve battery
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
