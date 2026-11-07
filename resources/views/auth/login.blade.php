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
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-brand: #A51C30;
            --color-brand-dark: #861527;
            --color-gold: #C59B3F;
            --color-gold-light: #F5DE9C;
            --color-ink: #0D0B12;
            --color-paper: #F8F7F4;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: var(--color-paper);
        }

        .font-display {
            font-family: 'Fraunces', Georgia, serif;
        }

        .font-cinzel {
            font-family: 'Cinzel', serif;
        }

        /* ═══ Sacred Light & Motion Keyframes ═══ */
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

        @keyframes goldFoilSweep {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        @keyframes drawLineGently {
            0% { width: 0; opacity: 0; }
            100% { width: 4.5rem; opacity: 1; }
        }

        @keyframes entranceSlideUp {
            0% { opacity: 0; transform: translateY(18px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes shimmerSweep {
            0%, 55% { left: -75%; }
            100% { left: 165%; }
        }

        .animate-flame {
            animation: flameBreathe 4.5s ease-in-out infinite;
        }

        .animate-gold-line {
            animation: drawLineGently 1.6s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards;
        }

        .animate-entrance {
            opacity: 0;
            animation: entranceSlideUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .gold-foil-text {
            background: linear-gradient(120deg, #F5DE9C 0%, #C59B3F 25%, #FFFFFF 50%, #C59B3F 75%, #F5DE9C 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: goldFoilSweep 8s linear infinite;
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
            width: 50%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.28), transparent);
            transform: rotate(25deg);
            animation: shimmerSweep 4.5s ease-in-out infinite;
            pointer-events: none;
        }

        /* Gothic Arched Stained Glass Traceries */
        .gothic-tracery {
            stroke-dasharray: 600;
            stroke-dashoffset: 600;
            animation: drawTracery 3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes drawTracery {
            to { stroke-dashoffset: 0; }
        }

        /* Accessibility: reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .animate-flame,
            .animate-gold-line,
            .animate-entrance,
            .gold-foil-text,
            .btn-shimmer::after,
            .gothic-tracery {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
                stroke-dashoffset: 0 !important;
            }
            #cathedralCanvas {
                display: none;
            }
        }
    </style>
</head>
<body class="min-h-full antialiased text-slate-800 selection:bg-brand selection:text-white">

<div class="min-h-screen grid lg:grid-cols-[1.18fr_480px]">

    {{-- ═══ LEFT PANEL: The Living Sacred Sanctuary Canvas ═══ --}}
    <div id="sanctuaryStage" class="hidden lg:flex flex-col justify-between p-10 xl:p-14 relative overflow-hidden select-none"
         style="background: var(--color-ink)">

        {{-- Dynamic Cathedral Light Engine Canvas (God Rays, Choral Waves, Sacred Embers) --}}
        <canvas id="cathedralCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-0" aria-hidden="true"></canvas>

        {{-- Fine Gothic Rose Window & Tracery Overlay --}}
        <div class="absolute inset-0 pointer-events-none z-1 flex items-center justify-center opacity-[0.06] overflow-hidden" aria-hidden="true">
            <svg class="w-[48rem] h-[48rem] text-gold" viewBox="0 0 400 400" fill="none" stroke="currentColor" stroke-width="0.8">
                <circle cx="200" cy="200" r="180" class="gothic-tracery"/>
                <circle cx="200" cy="200" r="140" class="gothic-tracery" style="animation-delay: 0.3s"/>
                <circle cx="200" cy="200" r="90" class="gothic-tracery" style="animation-delay: 0.6s"/>
                <circle cx="200" cy="200" r="40" class="gothic-tracery" style="animation-delay: 0.9s"/>
                <!-- Petal arcs of the rose window -->
                <path d="M200 20 C140 100 140 300 200 380 C260 300 260 100 200 20 Z" class="gothic-tracery" style="animation-delay: 1.1s"/>
                <path d="M20 200 C100 140 300 140 380 200 C300 260 100 260 20 200 Z" class="gothic-tracery" style="animation-delay: 1.3s"/>
                <path d="M72 72 C170 110 290 230 328 328 C230 290 110 170 72 72 Z" class="gothic-tracery" style="animation-delay: 1.5s"/>
                <path d="M328 72 C230 110 110 230 72 328 C170 290 290 170 328 72 Z" class="gothic-tracery" style="animation-delay: 1.7s"/>
            </svg>
        </div>

        {{-- Top Bar: UMC Living Insignia + Interactive Sanctuary Atmosphere Selector --}}
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-4 group cursor-default">
                <div class="w-13 h-13 rounded-2xl bg-[#8A1526]/30 ring-1 ring-[#C59B3F]/40 flex items-center justify-center p-2.5 animate-flame backdrop-blur-md transition-all duration-500 group-hover:scale-105">
                    <x-umc-logo class="w-full h-full drop-shadow-[0_0_12px_rgba(229,184,92,0.4)]" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#E5B85C]">Africa University</span>
                        <span class="w-1 h-1 rounded-full bg-[#C59B3F]"></span>
                        <span class="text-[10px] text-white/40 font-medium">Mutare, Zimbabwe</span>
                    </div>
                    <p class="text-lg font-bold text-white tracking-tight font-cinzel">Kwang-Lim Chapel</p>
                </div>
            </div>

            <!-- Atmosphere Mode Selector (Interactive Time-of-Day Light Mode) -->
            <div class="flex items-center gap-2">
                @php
                    $currentSeason = \App\Services\LiturgicalCalendarService::getCurrentSeason();
                @endphp
                <div class="hidden xl:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.04] border border-white/10 backdrop-blur-md text-xs text-white/80">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: {{ $currentSeason['hex'] ?? '#C59B3F' }}"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2" style="background-color: {{ $currentSeason['hex'] ?? '#C59B3F' }}"></span>
                    </span>
                    <span class="text-[11px] font-medium tracking-wide text-white/70">{{ $currentSeason['title'] ?? 'Sacred Season' }}</span>
                </div>

                <!-- Interactive Atmosphere Pill Switcher -->
                <div class="flex items-center bg-black/40 backdrop-blur-md p-1 rounded-full border border-white/10 text-[11px]" id="atmosphereSelector">
                    <button type="button" data-mode="dawn" class="atm-btn px-2.5 py-1 rounded-full text-white/60 hover:text-white transition-all font-medium active">
                        🌅 Dawn
                    </button>
                    <button type="button" data-mode="chancel" class="atm-btn px-2.5 py-1 rounded-full text-white/60 hover:text-white transition-all font-medium">
                        ⛪ Chancel
                    </button>
                    <button type="button" data-mode="vespers" class="atm-btn px-2.5 py-1 rounded-full text-white/60 hover:text-white transition-all font-medium">
                        🕯️ Vespers
                    </button>
                </div>
            </div>
        </div>

        {{-- Center: Majestic Illuminated Scripture & Sacred Choral Waveform --}}
        <div class="relative z-10 my-auto py-10 max-w-xl">
            <!-- Kinetic Gold Accent Line -->
            <div class="h-0.5 mb-7 rounded-full animate-gold-line" style="background: linear-gradient(90deg, #E5B85C, #8A1526, transparent)"></div>

            <!-- Illuminated Drop Cap Verse -->
            <div class="flex items-start gap-4">
                <span class="text-6xl sm:text-7xl font-cinzel font-bold text-white/95 leading-none select-none drop-shadow-[0_4px_16px_rgba(197,155,63,0.35)] -mt-1">
                    W
                </span>
                <div>
                    <blockquote class="text-3xl sm:text-4xl xl:text-5xl font-display font-light text-white leading-[1.18] tracking-tight">
                        orship the Lord<br>
                        <span class="font-normal italic gold-foil-text">in the splendour</span><br>
                        of his holiness.
                    </blockquote>
                </div>
            </div>

            <!-- Sacred Scripture Attribution & Pan-African Choral Tag -->
            <div class="mt-8 flex flex-col sm:flex-row sm:items-center gap-3 text-xs text-white/50">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded bg-[#C59B3F]/15 border border-[#C59B3F]/30 text-[#E5B85C] font-mono font-bold tracking-wider text-[11px]">
                        PSALM 29:2
                    </span>
                    <span class="text-white/30">•</span>
                    <span class="text-white/60 italic font-cinzel">"Mwari Ari Pano"</span>
                </div>
                <span class="hidden sm:inline text-white/20">•</span>
                <span class="text-white/40">Africa University Sacred Chancel</span>
            </div>
        </div>

        {{-- Bottom: Mutare Mountain Silhouette & Kwang-Lim Chapel Sanctuary Roofline --}}
        <div class="relative z-10 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-white/40">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                <span class="text-white/60 font-medium">Sacred Sanctuary Sanctuary Mode Active</span>
            </div>
            <div class="text-right text-[11px] font-mono text-white/30 hidden sm:block">
                18°58′S 32°40′E • Old Mutare
            </div>
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
                    <label for="email" class="block text-xs font-bold text-slate-700 tracking-wide">
                        University Email
                    </label>
                    <div class="relative rounded-xl shadow-xs transition-all duration-200 focus-within:ring-2 focus-within:ring-brand/20 focus-within:border-brand">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email"
                               name="email"
                               type="email"
                               autocomplete="email"
                               required
                               value="{{ old('email') }}"
                               placeholder="chaplain@africau.edu"
                               class="block w-full pl-10 pr-3.5 py-2.5 text-sm rounded-xl border border-slate-200 bg-white placeholder-slate-400 focus:outline-none transition-colors" />
                    </div>
                </div>

                {{-- Password Input with Peek Button --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-slate-700 tracking-wide">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand hover:text-brand-dark transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative rounded-xl shadow-xs transition-all duration-200 focus-within:ring-2 focus-within:ring-brand/20 focus-within:border-brand">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="current-password"
                               required
                               placeholder="••••••••••••"
                               class="block w-full pl-10 pr-10 py-2.5 text-sm rounded-xl border border-slate-200 bg-white placeholder-slate-400 focus:outline-none transition-colors" />
                        <button type="button"
                                id="togglePasswordBtn"
                                aria-label="Toggle password visibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox"
                               name="remember"
                               class="w-4 h-4 rounded border-slate-300 text-brand focus:ring-brand/30 transition-colors cursor-pointer" />
                        <span class="text-xs text-slate-600 font-medium">Keep me signed in</span>
                    </label>
                </div>

                {{-- Submit Button with Motion Shimmer --}}
                <button type="submit"
                        id="submitBtn"
                        class="btn-shimmer w-full mt-2 py-3 px-4 rounded-xl font-bold text-sm text-white shadow-lg shadow-brand/25 transition-all duration-200 hover:shadow-xl hover:shadow-brand/35 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 cursor-pointer"
                        style="background: linear-gradient(135deg, var(--color-brand) 0%, var(--color-brand-dark) 100%)">
                    <span id="btnText">Sign In to Chapel Portal</span>
                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <svg id="btnArrow" class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            {{-- Footer Security Badge --}}
            <div class="pt-6 border-t border-slate-200/70 text-center space-y-2">
                <p class="text-[11px] text-slate-400 font-medium">
                    Protected by Africa University Institutional SSO
                </p>
                <div class="flex items-center justify-center gap-4 text-xs text-slate-500">
                    <a href="https://library.africau.ac.zw" target="_blank" rel="noopener" class="hover:text-brand transition-colors">Library</a>
                    <span class="text-slate-300">&bull;</span>
                    <a href="https://africau.edu" target="_blank" rel="noopener" class="hover:text-brand transition-colors">Main Site</a>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-400">Harare / Mutare Time</span>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ═══ HIGH-CREATIVITY CATHEDRAL LIGHT & CHORAL MOTION ENGINE ═══ --}}
<script>
(() => {
    // 1. Password visibility toggle
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const pwdInput = document.getElementById('password');
    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', () => {
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            toggleBtn.classList.toggle('text-brand', isPassword);
        });
    }

    // 2. Form submit loading state
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnArrow = document.getElementById('btnArrow');
    if (form && btn) {
        form.addEventListener('submit', () => {
            btn.disabled = true;
            btn.classList.add('opacity-85', 'cursor-not-allowed');
            if (btnSpinner) btnSpinner.classList.remove('hidden');
            if (btnArrow) btnArrow.classList.add('hidden');
            if (btnText) btnText.textContent = 'Verifying credentials...';
        });
    }

    // 3. Cathedral Light Engine (God Rays, Choral Harmonics, Sacred Embers)
    const canvas = document.getElementById('cathedralCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let animId = null;
    let width = 0;
    let height = 0;
    let time = 0;

    const mouse = {
        x: 0,
        y: 0,
        targetX: 0,
        targetY: 0
    };

    // Atmosphere Color Profiles
    const atmospheres = {
        dawn: {
            rayColors: [
                'rgba(229, 184, 92, 0.22)',  // Warm dawn gold
                'rgba(165, 28, 48, 0.16)',   // Mutare sunrise crimson
                'rgba(245, 222, 156, 0.20)', // Soft sunlight
                'rgba(197, 155, 63, 0.15)'
            ],
            ambient1: 'rgba(165, 28, 48, 0.24)',
            ambient2: 'rgba(229, 184, 92, 0.18)',
            choralColor: 'rgba(229, 184, 92, 0.45)',
            emberColor: 'rgba(245, 222, 156, '
        },
        chancel: {
            rayColors: [
                'rgba(197, 155, 63, 0.24)',  // Cathedral gold
                'rgba(138, 21, 38, 0.22)',   // Collegiate crimson
                'rgba(42, 67, 116, 0.18)',   // Celestial azure
                'rgba(240, 210, 140, 0.16)'
            ],
            ambient1: 'rgba(138, 21, 38, 0.28)',
            ambient2: 'rgba(42, 67, 116, 0.20)',
            choralColor: 'rgba(197, 155, 63, 0.55)',
            emberColor: 'rgba(229, 184, 92, '
        },
        vespers: {
            rayColors: [
                'rgba(110, 14, 25, 0.25)',   // Deep garnet
                'rgba(74, 21, 75, 0.20)',    // Sacred amethyst
                'rgba(235, 160, 60, 0.22)',  // Candlelight amber
                'rgba(180, 80, 30, 0.15)'
            ],
            ambient1: 'rgba(74, 21, 75, 0.30)',
            ambient2: 'rgba(235, 140, 40, 0.16)',
            choralColor: 'rgba(235, 160, 60, 0.50)',
            emberColor: 'rgba(235, 160, 60, '
        }
    };

    let activeAtmosphere = 'dawn';

    // Interactive Mode Button Binding
    const atmButtons = document.querySelectorAll('#atmosphereSelector .atm-btn');
    atmButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            atmButtons.forEach(b => {
                b.classList.remove('bg-white/20', 'text-white', 'shadow-sm');
                b.classList.add('text-white/60');
            });
            btn.classList.add('bg-white/20', 'text-white', 'shadow-sm');
            btn.classList.remove('text-white/60');
            activeAtmosphere = btn.getAttribute('data-mode') || 'dawn';
        });
    });

    // Initialize first button as active
    if (atmButtons.length > 0) {
        atmButtons[0].classList.add('bg-white/20', 'text-white', 'shadow-sm');
        atmButtons[0].classList.remove('text-white/60');
    }

    function resize() {
        const dpr = window.devicePixelRatio || 1;
        width = canvas.parentElement.clientWidth;
        height = canvas.parentElement.clientHeight;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        ctx.resetTransform?.();
        ctx.scale(dpr, dpr);
        mouse.targetX = width * 0.4;
        mouse.targetY = height * 0.4;
    }

    const stage = document.getElementById('sanctuaryStage');
    if (stage) {
        stage.addEventListener('mousemove', (e) => {
            const rect = stage.getBoundingClientRect();
            mouse.targetX = e.clientX - rect.left;
            mouse.targetY = e.clientY - rect.top;
        });
        stage.addEventListener('mouseleave', () => {
            mouse.targetX = width * 0.4;
            mouse.targetY = height * 0.4;
        });

        // Click adds sacred ember burst
        stage.addEventListener('click', (e) => {
            const rect = stage.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;
            for (let i = 0; i < 16; i++) {
                embers.push({
                    x: clickX,
                    y: clickY,
                    r: 1 + Math.random() * 2.5,
                    vx: (Math.random() - 0.5) * 3,
                    vy: -1.2 - Math.random() * 2.5,
                    alpha: 0.9,
                    decay: 0.015 + Math.random() * 0.02
                });
            }
        });
    }

    // Sacred Embers
    const emberCount = 32;
    const embers = [];
    for (let i = 0; i < emberCount; i++) {
        embers.push({
            x: Math.random() * 900,
            y: Math.random() * 1200,
            r: 0.8 + Math.random() * 2.2,
            vx: (Math.random() - 0.5) * 0.4,
            vy: -0.3 - Math.random() * 0.6,
            alpha: 0.2 + Math.random() * 0.5,
            pulse: Math.random() * Math.PI * 2
        });
    }

    // Volumetric Cathedral Light Shafts
    const rays = [
        { angle: -0.52, originX: -0.1, spread: 0.18, speed: 0.007, phase: 0 },
        { angle: -0.42, originX: 0.15, spread: 0.22, speed: 0.009, phase: 1.6 },
        { angle: -0.32, originX: 0.38, spread: 0.16, speed: 0.006, phase: 3.2 },
        { angle: -0.22, originX: 0.65, spread: 0.24, speed: 0.008, phase: 4.8 }
    ];

    function drawVolumetricRays(currentAtm) {
        ctx.save();
        ctx.globalCompositeOperation = 'screen';

        const mouseParallaxX = (mouse.x - width / 2) * 0.08;
        const mouseParallaxY = (mouse.y - height / 2) * 0.05;

        rays.forEach((ray, index) => {
            const dynamicAlpha = 0.65 + Math.sin(time * ray.speed * 60 + ray.phase) * 0.35;
            const originX = width * ray.originX + mouseParallaxX * 0.6;
            const originY = -50;

            const bottomX1 = originX + Math.tan(ray.angle) * height * 1.5 - (width * ray.spread);
            const bottomX2 = originX + Math.tan(ray.angle) * height * 1.5 + (width * ray.spread);

            const rayGrad = ctx.createLinearGradient(originX, originY, bottomX1, height);
            const colorBase = currentAtm.rayColors[index % currentAtm.rayColors.length];
            rayGrad.addColorStop(0, colorBase);
            rayGrad.addColorStop(0.7, colorBase.replace(/[\d.]+\)$/, (0.08 * dynamicAlpha) + ')'));
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
    }

    function drawChoralWave(currentAtm) {
        ctx.save();
        ctx.globalCompositeOperation = 'screen';

        const baseLineY = height * 0.82;
        const voices = [
            { freq: 0.004, amp: 28, speed: 1.2, color: currentAtm.choralColor, alpha: 0.4 },
            { freq: 0.007, amp: 18, speed: 1.7, color: 'rgba(245, 222, 156, 0.35)', alpha: 0.3 },
            { freq: 0.003, amp: 35, speed: 0.8, color: 'rgba(165, 28, 48, 0.35)', alpha: 0.35 }
        ];

        voices.forEach(voice => {
            ctx.beginPath();
            ctx.moveTo(0, baseLineY);

            for (let x = 0; x <= width; x += 6) {
                const distToMouse = Math.abs(x - mouse.x);
                const mouseInfluence = Math.max(0, 1 - distToMouse / 260) * 22;
                const y = baseLineY + Math.sin(x * voice.freq + time * voice.speed) * (voice.amp + mouseInfluence)
                                    + Math.cos(x * 0.002 - time * 0.5) * 8;
                ctx.lineTo(x, y);
            }

            ctx.lineTo(width, height);
            ctx.lineTo(0, height);
            ctx.closePath();

            const waveGrad = ctx.createLinearGradient(0, baseLineY - 40, 0, height);
            waveGrad.addColorStop(0, voice.color);
            waveGrad.addColorStop(1, 'transparent');
            ctx.fillStyle = waveGrad;
            ctx.fill();
        });

        ctx.restore();
    }

    function render() {
        time += 0.012;

        if (!width || !height) {
            animId = requestAnimationFrame(render);
            return;
        }

        // Smooth mouse lerp
        mouse.x += (mouse.targetX - mouse.x) * 0.06;
        mouse.y += (mouse.targetY - mouse.y) * 0.06;

        ctx.clearRect(0, 0, width, height);

        const currentAtm = atmospheres[activeAtmosphere] || atmospheres.dawn;

        // 1. Cathedral Atmosphere Radial Warmth
        const aura1X = width * 0.25 + (mouse.x - width / 2) * 0.06;
        const aura1Y = height * 0.3 + (mouse.y - height / 2) * 0.06;
        const grad1 = ctx.createRadialGradient(aura1X, aura1Y, 10, aura1X, aura1Y, width * 0.85);
        grad1.addColorStop(0, currentAtm.ambient1);
        grad1.addColorStop(0.6, 'rgba(13, 11, 18, 0.4)');
        grad1.addColorStop(1, 'transparent');
        ctx.fillStyle = grad1;
        ctx.fillRect(0, 0, width, height);

        const aura2X = width * 0.8 + Math.cos(time * 0.5) * 40;
        const aura2Y = height * 0.85 + Math.sin(time * 0.6) * 30;
        const grad2 = ctx.createRadialGradient(aura2X, aura2Y, 10, aura2X, aura2Y, width * 0.65);
        grad2.addColorStop(0, currentAtm.ambient2);
        grad2.addColorStop(1, 'transparent');
        ctx.fillStyle = grad2;
        ctx.fillRect(0, 0, width, height);

        // 2. Volumetric Stained Glass God Rays
        drawVolumetricRays(currentAtm);

        // 3. Polyphonic Choral Waveform
        drawChoralWave(currentAtm);

        // 4. Sacred Incense Embers with Optical Flare
        ctx.save();
        for (let i = embers.length - 1; i >= 0; i--) {
            const p = embers[i];

            if (p.decay) {
                // Click burst ember
                p.x += p.vx;
                p.y += p.vy;
                p.alpha -= p.decay;
                if (p.alpha <= 0) {
                    embers.splice(i, 1);
                    continue;
                }
            } else {
                // Ambient rising incense ember
                p.y += p.vy;
                p.x += p.vx + Math.sin(time + p.pulse) * 0.4;
                p.pulse += 0.02;

                if (p.y < -20) {
                    p.y = height + 10;
                    p.x = Math.random() * width;
                }
                if (p.x < -20) p.x = width + 10;
                if (p.x > width + 20) p.x = -10;
            }

            const currentAlpha = Math.max(0, Math.min(1, p.alpha * (0.7 + Math.sin(time * 2 + (p.pulse || 0)) * 0.3)));
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = currentAtm.emberColor + currentAlpha + ')';
            ctx.shadowBlur = 12;
            ctx.shadowColor = 'rgba(229, 184, 92, 0.8)';
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
