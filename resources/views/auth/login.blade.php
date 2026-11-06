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
        :root { --color-brand: #A51C30; --color-gold: #B8892B; --color-ink: #12131A; --color-paper: #F7F6F3; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: var(--color-paper); }
        .font-display { font-family: 'Fraunces', Georgia, serif; }
    </style>
</head>
<body class="min-h-full antialiased text-slate-800 selection:bg-brand selection:text-white">

<div class="min-h-screen grid lg:grid-cols-[1fr_480px]">

    {{-- ═══ LEFT PANEL (decorative, desktop only) ═══ --}}
    <div class="hidden lg:flex flex-col justify-between p-10 relative overflow-hidden"
         style="background: var(--color-ink)">

        {{-- cross-hatch pattern --}}
        <div class="absolute inset-0 opacity-[0.04]" aria-hidden="true"
             style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22%3E%3Crect width=%2240%22 height=%2240%22 fill=%22none%22/%3E%3Cpath d=%22M0 20h40M20 0v40%22 stroke=%22%23fff%22 stroke-width=%220.4%22/%3E%3C/svg%3E')"></div>

        {{-- decorative circle gradient --}}
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full opacity-10"
             style="background: radial-gradient(circle, var(--color-brand) 0%, transparent 70%)"></div>
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full opacity-8"
             style="background: radial-gradient(circle, var(--color-gold) 0%, transparent 70%)"></div>

        {{-- Logo + name --}}
        <div class="relative flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand/25 ring-1 ring-brand/40 flex items-center justify-center">
                <x-umc-logo class="w-6 h-6" />
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gold opacity-80">Africa University</p>
                <p class="text-sm font-semibold text-white/80">Kwang-Lim Chapel</p>
            </div>
        </div>

        {{-- Centre quote --}}
        <div class="relative">
            <div class="w-8 h-0.5 mb-5" style="background: var(--color-gold)"></div>
            <blockquote class="text-3xl font-display font-light text-white leading-tight max-w-xs" style="color: rgba(255,255,255,.9)">
                "Worship the Lord<br>in the splendour<br>of his holiness."
            </blockquote>
            <p class="mt-4 text-xs text-white/35 font-medium">Psalm 29:2 &bull; Ministry &amp; Fellowship Management Portal</p>
        </div>

        {{-- Bottom stats --}}
        <div class="relative grid grid-cols-3 gap-6 border-t border-white/8 pt-6">
            @php
                $stats = [
                    ['UMH', 'United Methodist', '734'],
                    ['MHB', 'Methodist Hymn Book', '984'],
                    ['SHO', 'Shona Hymnal', '311'],
                ];
            @endphp
            @foreach($stats as [$code, $label, $count])
                <div>
                    <p class="text-xl font-bold text-white tabular-nums">{{ $count }}</p>
                    <p class="text-[10px] text-white/35 mt-0.5">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ RIGHT PANEL — auth card ═══ --}}
    <div class="flex flex-col justify-center min-h-screen lg:min-h-0 p-6 sm:p-10 bg-paper">

        {{-- mobile logo --}}
        <div class="flex lg:hidden items-center justify-center gap-3 mb-8">
            <div class="w-12 h-12 rounded-2xl bg-brand flex items-center justify-center shadow-md">
                <x-umc-logo class="w-7 h-7 text-white" />
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">Kwang-Lim Chapel</p>
                <p class="text-[11px] text-slate-500">Ministry Management Portal</p>
            </div>
        </div>

        <div class="max-w-sm w-full mx-auto space-y-6">

            {{-- heading --}}
            <div>
                <h1 class="text-2xl font-display font-semibold text-slate-900">Welcome back</h1>
                <p class="text-sm text-slate-500 mt-1">Sign in with your official university credentials.</p>
            </div>

            {{-- error --}}
            @if($errors->any())
                <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs">
                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                @csrf

                <div>
                    <label for="login_email" class="block text-xs font-semibold text-slate-700 mb-1.5">University email</label>
                    <div class="relative">
                        <input type="email" id="login_email" name="email" value="{{ old('email') }}" required
                               autofocus autocomplete="email"
                               placeholder="you@africau.edu"
                               class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand/25 focus:border-brand text-slate-900 bg-white transition">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="login_password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="login_password" name="password" required
                               autocomplete="current-password"
                               placeholder="Enter your password"
                               class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand/25 focus:border-brand text-slate-900 bg-white transition">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
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

                <div class="flex items-center justify-between text-xs text-slate-600">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" checked
                               class="rounded border-slate-300 text-brand focus:ring-brand focus:ring-offset-0">
                        <span>Keep me signed in</span>
                    </label>
                </div>

                <button type="submit" id="loginBtn"
                        class="w-full py-2.5 px-4 rounded-xl bg-brand hover:bg-brand-dark active:scale-[0.99] text-white text-sm font-bold transition shadow-sm mt-1">
                    Sign in to Chapel Portal
                </button>
            </form>

            {{-- links --}}
            <div class="pt-4 border-t border-slate-200/80 flex items-center justify-between text-xs">
                <a href="{{ route('clubs.register') }}"
                   class="font-semibold text-brand hover:underline">
                    Club charter application &rarr;
                </a>
                <a href="{{ route('calendar.public') }}" target="_blank" rel="noopener"
                   class="text-slate-400 hover:text-slate-700 transition">
                    Public calendar
                </a>
            </div>

            {{-- onboarding accordion --}}
            <div>
                <button type="button" id="regToggle"
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
        </div>

        {{-- footer --}}
        <p class="mt-10 text-center text-[11px] text-slate-400 max-w-sm mx-auto">
            Kwang-Lim Chapel &bull; Africa University &bull; Ministry Management System
        </p>
    </div>
</div>

<script>
function togglePassword() {
    const field = document.getElementById('login_password');
    const eye   = document.getElementById('pwdEye');
    if (field.type === 'password') {
        field.type = 'text';
        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        field.type = 'password';
        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.textContent = 'Signing in…';
    btn.classList.add('opacity-70');
});
</script>
</body>
</html>
