<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#15121A">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Sign In | Kwang-Lim Chapel Ministry Portal</title>

    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* The room is lit by the candle: --flame is written by the flame each frame */
        #sanctum { --flame: .75; }
        .room-light {
            background: radial-gradient(55% 48% at 50% 40%, rgb(255 190 110 / .16), rgb(255 150 70 / .05) 45%, transparent 75%);
            opacity: calc(var(--flame) * .95);
            transition: opacity .9s ease;
        }
        #sanctum.is-lit .room-light { opacity: 1.6; }
        .candle {
            width: 3.4rem;
            height: 13rem;
            border-radius: .5rem .5rem .35rem .35rem / .9rem .9rem .35rem .35rem;
            background:
                radial-gradient(120% 22% at 50% 0%, rgb(255 214 150 / calc(var(--flame) * .55)), transparent 70%),
                linear-gradient(90deg, #CFC3AE 0%, #F4ECDC 28%, #FBF6EC 46%, #EADFCB 72%, #B9AC95 100%);
            box-shadow: inset 0 10px 14px -10px rgb(255 200 120 / .55), 0 30px 60px -20px rgb(0 0 0 / .7);
        }
        .candle::before { /* the molten rim */
            content: ''; position: absolute; left: 6%; right: 6%; top: -.35rem; height: .8rem; border-radius: 50%;
            background: radial-gradient(closest-side, #FFF4DC, #E9DCC2 70%, #CDBFA6);
            box-shadow: 0 0 calc(var(--flame) * 14px) rgb(255 190 110 / .55);
        }
        .candle::after { /* a single drip */
            content: ''; position: absolute; left: 22%; top: .2rem; width: .4rem; height: 2.4rem; border-radius: 0 0 .3rem .3rem;
            background: linear-gradient(180deg, #FBF5E8, #E6DAC4);
        }
        .scripture { font-variation-settings: 'SOFT' 100, 'opsz' 144; }
        .glow-text { text-shadow: 0 0 calc(var(--flame) * 28px) rgb(255 190 110 / .18); }
    </style>
</head>
<body class="min-h-full bg-paper antialiased text-slate-800 selection:bg-brand selection:text-white">
@php $currentSeason = \App\Services\LiturgicalCalendarService::getCurrentSeason(); @endphp

<div class="min-h-screen grid lg:grid-cols-[minmax(0,1.1fr)_minmax(26rem,0.9fr)]">

    {{-- ═══ The sanctum: a single candle, lit by your presence ═══ --}}
    <section id="sanctum" class="hidden lg:flex relative flex-col justify-between bg-ink text-white overflow-hidden grain on-dark p-10 xl:p-14 select-none" aria-label="Kwang-Lim Chapel">
        <div class="room-light absolute inset-0 pointer-events-none" aria-hidden="true"></div>

        <div class="relative z-10 flex items-center justify-between">
            <a href="{{ Route::has('welcome') ? route('welcome') : url('/welcome') }}" class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full grid place-items-center shrink-0"
                      style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px #15121A, 0 0 0 4px rgb(217 183 108 / .45);">
                    <x-umc-logo class="w-[1.15rem] h-[1.15rem]" />
                </span>
                <span class="leading-tight">
                    <span class="block text-[9.5px] font-bold uppercase tracking-[0.24em] text-gold-light/90">Africa University</span>
                    <span class="block font-display text-[16px]">Kwang-Lim Chapel</span>
                </span>
            </a>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] text-white/60 bg-white/[0.04] ring-1 ring-white/10">
                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $currentSeason['hex'] }}"></span>
                {{ $currentSeason['name'] }}
            </span>
        </div>

        {{-- Candle --}}
        <div class="relative z-10 flex flex-col items-center -mt-4">
            <canvas data-flame data-flame-light="#sanctum" data-flame-level="0.7" data-flame-base="0.93" data-flame-height="0.62"
                    class="w-56 h-64 -mb-5" aria-hidden="true"></canvas>
            <div class="candle relative" aria-hidden="true"></div>
            <div class="w-72 h-6 -mt-3 rounded-[50%]" style="background: radial-gradient(closest-side, rgb(0 0 0 / .55), transparent);" aria-hidden="true"></div>
        </div>

        <div class="relative z-10 text-center max-w-lg mx-auto">
            <blockquote class="scripture glow-text font-display text-[1.75rem] xl:text-[2.05rem] leading-[1.2] tracking-[-0.02em] text-white/90"
                        style="font-weight: 300 !important; --wght: 300; --soft: 100;" data-ink data-ink-delay="500">
                Thy word is a lamp unto my feet, <em class="italic text-gold-light">and a light unto my path.</em>
            </blockquote>
            <p class="mt-4 text-[10.5px] font-bold uppercase tracking-[0.24em] text-white/35" data-reveal="1500">Psalm 119:105</p>
        </div>



        <div class="relative z-10 flex items-center justify-between text-[11px] text-white/35 pt-5 border-t border-white/[0.07]">
            <span>Old Mutare · Zimbabwe</span>
            <span>The United Methodist Church</span>
        </div>
    </section>

    {{-- ═══ Sign in ═══ --}}
    <section class="flex flex-col justify-center min-h-screen lg:min-h-0 px-6 py-12 sm:px-12 bg-paper">
        <div class="max-w-[23rem] w-full mx-auto">

            {{-- Mobile: the same candle, small --}}
            <div class="lg:hidden flex items-center gap-3 mb-10" data-reveal>
                <span class="relative w-12 h-14 rounded-2xl bg-ink grid place-items-end justify-center overflow-hidden">
                    <canvas data-flame data-flame-level="0.75" data-flame-base="0.92" data-flame-height="0.7" class="w-12 h-14" aria-hidden="true"></canvas>
                </span>
                <span class="leading-tight">
                    <span class="block text-[9.5px] font-bold uppercase tracking-[0.24em] text-gold">Africa University</span>
                    <span class="block font-display text-[17px] text-slate-900">Kwang-Lim Chapel</span>
                </span>
            </div>

            <p class="eyebrow" data-reveal="60">Ministry portal</p>
            <h1 class="font-display text-[2.4rem] leading-[1.02] tracking-[-0.03em] text-slate-900" style="font-weight: 400 !important; --wght: 400; --soft: 100;" data-ink data-ink-delay="120">
                Welcome back.
            </h1>
            <p class="mt-3 text-[13.5px] text-slate-500 leading-relaxed" data-reveal="300">
                Sign in with your university account to coordinate services, fellowships and records.
            </p>

            @if($errors->any())
                <div class="mt-6 flash-error" role="alert">
                    <svg class="w-4 h-4 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[13px] font-medium">{{ $errors->first() }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mt-6 flash-error" role="alert">
                    <svg class="w-4 h-4 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[13px] font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" id="loginForm" data-reveal="380">
                @csrf

                <div>
                    <label for="email" class="field-label">University email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required autofocus
                           value="{{ old('email') }}" placeholder="name@africau.edu"
                           class="field !py-3 !text-[14px]" />
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="field-label">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[12px] font-semibold text-brand hover:text-brand-dark mb-1.5">Forgot?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="Your password" class="field !py-3 !pr-11 !text-[14px]" />
                        <button type="button" id="togglePasswordBtn" aria-label="Show password" aria-pressed="false"
                                class="absolute inset-y-0 right-0 px-3.5 flex items-center text-slate-400 hover:text-slate-700 transition-colors">
                            <svg class="w-[1.1rem] h-[1.1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="remember" checked class="w-4 h-4 rounded border-slate-300 cursor-pointer" />
                    <span class="text-[13px] text-slate-600">Keep me signed in</span>
                </label>

                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg btn-block !mt-7">
                    <span id="btnText">Sign in</span>
                    <svg id="btnArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                </button>
            </form>

            <div class="mt-9 pt-6 border-t border-slate-200 space-y-4" data-reveal="520">
                <div class="flex items-center justify-between text-[12.5px]">
                    <a href="{{ route('clubs.register') }}" class="card-link">Apply for a club charter</a>
                    <a href="{{ route('calendar.public') }}" class="text-slate-500 hover:text-slate-900 transition">Public calendar</a>
                </div>

                <details class="group rounded-xl bg-white/60 ring-1 ring-slate-200/80 open:bg-white transition-colors">
                    <summary class="flex items-center justify-between px-4 py-3 text-[12.5px] text-slate-500 hover:text-slate-800 cursor-pointer list-none">
                        How do staff and club leaders get access?
                        <svg class="w-3.5 h-3.5 transition-transform duration-300 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div id="regInfo" class="px-4 pb-4 text-[12px] text-slate-500 space-y-2 leading-relaxed">
                        <p><strong class="text-slate-700 font-semibold">Chaplain &amp; Assistant:</strong> accounts are provisioned by the Secretary &amp; ICT. Sign in with your official university email.</p>
                        <p><strong class="text-slate-700 font-semibold">Club leaders:</strong> apply through the club charter application. Once approved, executive credentials are issued by the Chaplaincy Office.</p>
                        <p><strong class="text-slate-700 font-semibold">Campus Life (SRC):</strong> accounts are created by the Chapel Secretary after affiliation is confirmed.</p>
                    </div>
                </details>
            </div>

            <p class="mt-10 text-center text-[11px] text-slate-400">
                Kwang-Lim Chapel · Africa University · <a href="{{ Route::has('welcome') ? route('welcome') : url('/welcome') }}" class="hover:text-slate-700 underline-offset-2 hover:underline">About the portal</a>
            </p>
        </div>
    </section>
</div>

<script>
(function () {
    const pwd = document.getElementById('password');
    const email = document.getElementById('email');
    const toggle = document.getElementById('togglePasswordBtn');
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const sanctum = document.getElementById('sanctum');
    const hadError = {{ $errors->any() ? 'true' : 'false' }};

    if (toggle && pwd) {
        toggle.addEventListener('click', () => {
            const show = pwd.type === 'password';
            pwd.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            toggle.classList.toggle('text-brand', show);
        });
    }

    // Speak to every candle on the page (desktop sanctum + mobile mark)
    const flames = () => [...document.querySelectorAll('canvas[data-flame]')].map(c => c.__flame).filter(Boolean);
    const kindle = () => {
        const filled = (email.value.trim() ? 1 : 0) + (pwd.value ? 1 : 0);
        flames().forEach(f => f.kindle(0.68 + filled * 0.16));
    };

    document.addEventListener('chapel:ready', () => {
        if (hadError) setTimeout(() => flames().forEach(f => f.gutter()), 700);
        kindle();
    });

    [email, pwd].forEach(input => input && input.addEventListener('input', () => {
        flames().forEach(f => f.breathe());
        kindle();
    }));

    if (form && btn) {
        form.addEventListener('submit', () => {
            flames().forEach(f => f.flare());
            sanctum && sanctum.classList.add('is-lit');
            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');
            if (btnText) btnText.textContent = 'Signing in…';
        });
    }
})();
</script>
</body>
</html>
