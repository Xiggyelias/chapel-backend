<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#DC2626">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Sign In | Kwang-Lim Chapel Ministry Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between p-4 sm:p-6 bg-[#F8FAFC] text-slate-800 antialiased selection:bg-[#DC2626] selection:text-white">

    <div class="max-w-md w-full mx-auto my-auto py-8 space-y-6">
        
        <!-- Header & Crest -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#DC2626] text-white p-2.5 shadow-md">
                <x-umc-logo class="w-full h-full" />
            </div>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Kwang-Lim Chapel
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Ministry &amp; Fellowship Management Portal
                </p>
            </div>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Sign In</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Enter your official credentials</p>
                </div>
                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span>
                    <span>Online</span>
                </span>
            </div>

            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-start space-x-2">
                    <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Official University Email</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. chaplain@africau.edu" autofocus class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white transition">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Password</label>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" value="" required placeholder="Enter your password" class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white transition">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" checked class="rounded text-[#DC2626] focus:ring-[#DC2626]">
                        <span>Remember me</span>
                    </label>

                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] active:scale-[0.99] text-white text-xs font-bold transition shadow-xs mt-2 cursor-pointer">
                    Sign In
                </button>
            </form>

            <!-- Useful Links -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <a href="{{ route('clubs.register') }}" class="font-semibold text-[#DC2626] hover:underline">
                    Apply for Club Charter &rarr;
                </a>
                <a href="{{ route('calendar.public') }}" target="_blank" class="text-slate-500 hover:text-slate-800 transition">
                    Public Calendar
                </a>
            </div>

            <!-- Onboarding Info Dropdown -->
            <div class="pt-2">
                <button type="button" onclick="document.getElementById('regInfo').classList.toggle('hidden')" class="w-full text-left text-[11px] text-slate-400 hover:text-slate-600 transition flex items-center justify-between cursor-pointer">
                    <span>How do Staff &amp; Club Owners register?</span>
                    <span>&darr;</span>
                </button>
                <div id="regInfo" class="hidden pt-2 text-[11px] text-slate-500 space-y-1.5 leading-relaxed">
                    <p><strong>Chaplain &amp; Assistant:</strong> Accounts are provisioned by the Secretary &amp; ICT. Sign in with official university email.</p>
                    <p><strong>Club Owners:</strong> Apply via "Apply for Club Charter". Once approved, executive credentials are issued automatically.</p>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-slate-400">
            Kwang-Lim Chapel &bull; Ministry Management System
        </div>

    </div>

</body>
</html>
