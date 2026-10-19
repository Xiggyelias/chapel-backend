<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#DC2626">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Kwang-Lim Chapel | Ministry Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-[#DC2626] selection:text-white">

    <!-- Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-[#DC2626] flex items-center justify-center p-1.5 shadow-sm">
                    <x-umc-logo class="w-full h-full" />
                </div>
                <div>
                    <span class="text-sm font-bold text-slate-900 block leading-tight">Kwang-Lim Chapel</span>
                    <span class="text-[11px] text-slate-500 font-medium">Ministry Portal</span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('calendar.public') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition">
                    Public Calendar
                </a>
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold transition shadow-xs active:scale-95">
                    Sign In
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Showcase Section -->
    <main class="flex-1">
        <div class="relative bg-white border-b border-slate-200 py-16 sm:py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <div class="max-w-4xl mx-auto text-center space-y-5 relative z-10">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-red-50 border border-red-100 text-[#DC2626] text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626]"></span>
                    <span>Chapel Fellowship &amp; Ministry Management</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                    Kwang-Lim Chapel <br class="hidden sm:inline">Ministry Portal
                </h1>

                <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto font-normal leading-relaxed">
                    A centralized workspace for university fellowships, Sunday services, room bookings, equipment loans, and leadership transitions.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3 pt-3">
                    <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] active:scale-95 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center space-x-2">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Sign In to Portal</span>
                    </a>
                    <a href="{{ route('calendar.public') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-800 font-semibold text-xs transition border border-slate-200 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>View Master Calendar</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Clean Feature Cards -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Event Requests & Approvals</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Submit and track venue reservations, sound equipment requests, and event proposals with real-time clash prevention.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Master Worship Calendar</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Accurate schedule for Sunday convocations, mid-week services, and student fellowships with calendar subscription support.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Fellowship Records & Handover</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Secure repository for constitutions, executive contacts, and activity reports with seamless annual leadership transitions.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Institutional Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-6 px-4 sm:px-6 lg:px-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div>
                <span class="font-bold text-white text-sm">Kwang-Lim Chapel</span>
                <p class="text-[11px] text-slate-400 mt-0.5">Ministry &amp; Fellowship Management Portal</p>
            </div>
            <div class="flex items-center space-x-4 text-[11px]">
                <a href="{{ route('calendar.public') }}" class="hover:text-white transition">Public Calendar</a>
                <span>&bull;</span>
                <a href="{{ route('login') }}" class="hover:text-white transition font-bold text-[#DC2626]">Sign In</a>
            </div>
        </div>
    </footer>

</body>
</html>
