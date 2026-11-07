<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#15121A">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Forgot Password | Kwang-Lim Chapel Ministry Portal</title>

    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- Part of the sign-in flow, so it shares the sign-in page's classic look --}}
<body class="classic min-h-full bg-paper antialiased text-slate-800 selection:bg-brand selection:text-white">
    <main class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-3 mb-8">
                <span class="w-10 h-10 rounded-full grid place-items-center"
                      style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px #F6F3EE, 0 0 0 4px rgb(184 137 43 / .45);">
                    <x-umc-logo class="w-[1.15rem] h-[1.15rem]" />
                </span>
                <span class="leading-tight">
                    <span class="block text-[9.5px] font-bold uppercase tracking-[0.24em] text-gold">Africa University</span>
                    <span class="block font-display text-[16px] text-slate-900">Kwang-Lim Chapel</span>
                </span>
            </a>

            <div class="bg-white rounded-3xl ring-1 ring-black/5 shadow-sm p-7 sm:p-8">
                <p class="eyebrow">Ministry Portal</p>
                <h1 class="font-display text-[1.9rem] leading-tight text-slate-900">Forgot your password?</h1>
                <p class="mt-2 text-[14px] leading-relaxed text-slate-600">Portal accounts are issued by the Chapel Office. Enter your university email and we will ask them to send you a new temporary password.</p>

                @if(session('status'))
                    <div class="mt-6 flex items-start gap-3 rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4 text-[13.5px] leading-relaxed text-emerald-900" role="status">
                        <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="flash-error mt-6" role="alert">
                        <svg class="w-4 h-4 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[13px] font-medium">{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="field-label">University email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="name@africau.edu" class="field">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Ask for a new password</button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 text-[13px] text-slate-600 leading-relaxed">
                    <p class="font-semibold text-slate-800">Need it sooner?</p>
                    <p class="mt-1">Visit the Chaplaincy Office at the Kwang-Lim Chapel Center during office hours with your student or staff ID.</p>
                </div>
            </div>

            <p class="mt-6 text-center">
                <a href="{{ route('login') }}" class="card-link">Back to sign in</a>
            </p>
        </div>
    </main>
</body>
</html>
