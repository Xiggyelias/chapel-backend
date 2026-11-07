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
            --color-ink: #12131A;
            --color-paper: #F7F6F3;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: var(--color-paper);
        }

        .font-display {
            font-family: 'Fraunces', Georgia, serif;
        }

        /* Subtle, respectful motion keyframes */
        @keyframes subtleFade {
            0%, 100% { opacity: 0.12; transform: scale(1); }
            50% { opacity: 0.18; transform: scale(1.04); }
        }

        .ambient-glow {
            animation: subtleFade 8s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .ambient-glow {
                animation: none !important;
            }
        }
    </style>
</head>
<body class="min-h-full antialiased text-slate-800 selection:bg-brand selection:text-white">

<div class="min-h-screen grid lg:grid-cols-[1fr_480px]">

    {{-- ═══ LEFT PANEL: Clean, Dignified Sacred Sanctuary (Desktop Only) ═══ --}}
    <div class="hidden lg:flex flex-col justify-between p-12 relative overflow-hidden select-none"
         style="background: var(--color-ink)">

        {{-- Calm ambient warmth background --}}
        <div class="absolute -bottom-32 -left-32 w-[30rem] h-[30rem] rounded-full ambient-glow pointer-events-none blur-3xl"
             style="background: radial-gradient(circle, var(--color-brand) 0%, transparent 70%)"></div>
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full ambient-glow pointer-events-none blur-3xl"
             style="background: radial-gradient(circle, var(--color-gold) 0%, transparent 70%); animation-delay: -4s;"></div>

        {{-- Top: Official UMC Cross & Flame Insignia --}}
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-brand/25 ring-1 ring-brand/40 flex items-center justify-center p-2.5 shadow-sm">
                    <x-umc-logo class="w-full h-full text-white" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gold opacity-90">Africa University</p>
                    <p class="text-base font-semibold text-white/90 font-display">Kwang-Lim Chapel</p>
                </div>
            </div>

            {{-- Liturgical Season Badge --}}
            @php
                $currentSeason = \App\Services\LiturgicalCalendarService::getCurrentSeason();
            @endphp
            <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/[0.04] border border-white/10 text-xs text-white/70">
                <span class="w-2 h-2 rounded-full" style="background-color: {{ $currentSeason['hex'] ?? '#B8892B' }}"></span>
                <span class="text-[11px] font-medium">{{ $currentSeason['title'] ?? 'Sacred Season' }}</span>
            </div>
        </div>

        {{-- Center: Clean, Dignified Scripture Quote --}}
        <div class="relative z-10 my-auto py-12 max-w-md">
            <div class="w-10 h-0.5 mb-6 rounded-full" style="background: var(--color-gold)"></div>

            <blockquote class="text-3xl xl:text-4xl font-display font-light text-white leading-tight tracking-tight">
                "Worship the Lord<br>
                <span class="italic text-white/95">in the splendour</span><br>
                of his holiness."
            </blockquote>

            <p class="mt-5 text-xs text-white/40 font-medium">
                Psalm 29:2 &bull; Ministry &amp; Fellowship Management Portal
            </p>
        </div>

        {{-- Bottom Footer Tagline --}}
        <div class="relative z-10 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-white/40">
            <span>Africa University &bull; Mutare, Zimbabwe</span>
            <span class="text-[11px] text-white/30">United Methodist Church</span>
        </div>
    </div>

    {{-- ═══ RIGHT PANEL: Official Portal Gateway & Sign-In Card ═══ --}}
    <div class="flex flex-col justify-center min-h-screen lg:min-h-0 p-6 sm:p-12 bg-paper relative z-10">

        {{-- Mobile Header Logo --}}
        <div class="flex lg:hidden items-center justify-center gap-3 mb-8">
            <div class="w-12 h-12 rounded-2xl bg-brand flex items-center justify-center shadow-md p-2.5">
                <x-umc-logo class="w-full h-full text-white" />
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900 font-display">Kwang-Lim Chapel</p>
                <p class="text-[11px] text-slate-500 font-medium">Ministry Management Portal</p>
            </div>
        </div>

        <div class="max-w-sm w-full mx-auto space-y-6">

            {{-- Heading --}}
            <div class="space-y-1.5">
                <h1 class="text-2xl sm:text-3xl font-display font-semibold text-slate-900 tracking-tight">Welcome back</h1>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sign in with your official university credentials to access ministry coordination, services and documents.
                </p>
            </div>

            {{-- Validation Error Alert --}}
            @if($errors->any())
                <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs">
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
                               checked
                               class="w-4 h-4 rounded border-slate-300 text-brand focus:ring-brand/30 transition-colors cursor-pointer" />
                        <span class="text-xs text-slate-600 font-medium">Keep me signed in</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        id="submitBtn"
                        class="w-full mt-2 py-3 px-4 rounded-xl font-bold text-sm text-white bg-brand hover:bg-brand-dark active:scale-[0.99] transition shadow-md shadow-brand/20 flex items-center justify-center gap-2 cursor-pointer">
                    <span id="btnText">Sign In to Chapel Portal</span>
                    <svg id="btnArrow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            {{-- Links --}}
            <div class="pt-4 border-t border-slate-200/80 flex items-center justify-between text-xs">
                <a href="{{ route('clubs.register') }}" class="font-semibold text-brand hover:underline">
                    Club charter application &rarr;
                </a>
                <a href="{{ route('calendar.public') }}" target="_blank" rel="noopener" class="text-slate-400 hover:text-slate-700 transition">
                    Public calendar
                </a>
            </div>

            {{-- Onboarding Accordion --}}
            <div>
                <button type="button"
                        onclick="document.getElementById('regInfo').classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180')"
                        class="w-full flex items-center justify-between text-[11px] text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <span>How do staff &amp; club owners get access?</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="regInfo" class="hidden mt-3 text-[11px] text-slate-500 space-y-2 leading-relaxed bg-slate-50 rounded-xl p-3.5 border border-slate-200/60">
                    <p><strong class="text-slate-700">Chaplain &amp; Assistant:</strong> Accounts are provisioned by the Secretary &amp; ICT. Sign in with your official university email.</p>
                    <p><strong class="text-slate-700">Club leaders:</strong> Apply via "Club charter application" above. Once approved, executive credentials are issued by the Chaplaincy Office.</p>
                    <p><strong class="text-slate-700">Campus Life (SRC):</strong> Accounts are created by the Chapel Secretary after affiliation confirmation.</p>
                </div>
            </div>

            {{-- Institutional Footer --}}
            <p class="pt-4 text-center text-[11px] text-slate-400">
                Kwang-Lim Chapel &bull; Africa University &bull; Ministry Management System
            </p>

        </div>
    </div>
</div>

<script>
// Password visibility toggle
const toggleBtn = document.getElementById('togglePasswordBtn');
const pwdInput = document.getElementById('password');
if (toggleBtn && pwdInput) {
    toggleBtn.addEventListener('click', () => {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';
        toggleBtn.classList.toggle('text-brand', isPassword);
    });
}

// Form submit loading state
const form = document.getElementById('loginForm');
const btn = document.getElementById('submitBtn');
const btnText = document.getElementById('btnText');
if (form && btn) {
    form.addEventListener('submit', () => {
        btn.disabled = true;
        btn.classList.add('opacity-75');
        if (btnText) btnText.textContent = 'Signing in...';
    });
}
</script>
</body>
</html>
