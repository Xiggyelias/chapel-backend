<!DOCTYPE html>
<html lang="en" class="min-h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#DC2626">
    <meta name="description" content="Kwang-Lim Chapel Fellowship and Ministry Management System - Africa University">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>@yield('title', 'Kwang-Lim Chapel') | Ministry Portal</title>

    <!-- Google Fonts: Plus Jakarta Sans for Crisp Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html { font-size: 100%; -webkit-text-size-adjust: 100%; text-size-adjust: 100%; }
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; min-height: 100vh; }
        .font-cinzel { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important; letter-spacing: -0.01em; }
        [x-cloak] { display: none !important; }

        /* Accessibility Modes */
        html.large-text { font-size: 112% !important; }
        html.high-contrast body { background-color: #000000 !important; color: #ffffff !important; }
        html.high-contrast .card-elevation, html.high-contrast .bg-white { background-color: #080808 !important; color: #ffffff !important; border-color: #DC2626 !important; }
        html.high-contrast .text-slate-900, html.high-contrast .text-slate-800, html.high-contrast .text-slate-700 { color: #ffffff !important; }
        html.high-contrast .text-slate-600, html.high-contrast .text-slate-500, html.high-contrast .text-slate-400 { color: #f1f5f9 !important; }
        html.high-contrast .bg-slate-50, html.high-contrast .bg-slate-100 { background-color: #121212 !important; border-color: #DC2626 !important; }
        html.high-contrast a:hover { text-decoration: underline !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#F8FAFC] text-slate-800 antialiased selection:bg-[#DC2626] selection:text-white">

    @php
        $user = auth()->user();
        $isOfficeUser = $user && $user->isOffice();
        $isClubUser = $user && ($user->isClubExecutive() || $user->isClubPresident());
        $isCampusLife = $user && $user->isCampusLifeSrc();
        $userName = $user ? $user->name : 'Chapel Guest';
        $userRole = $user ? $user->getRoleDisplayName() : 'Guest';
        $userInitial = strtoupper(substr($userName, 0, 1));
    @endphp

    <!-- Main Top Navigation Bar (Africa University Kwang-Lim Chapel - Pure Black Theme) -->
    <header class="sticky top-0 z-40 bg-black text-white border-b-2 border-[#DC2626] shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo & Chapel Branding -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-9 h-9 rounded-xl bg-[#DC2626] flex items-center justify-center p-1.5 shadow-sm group-hover:scale-105 transition-transform shrink-0">
                            <x-umc-logo class="w-full h-full text-white" />
                        </div>
                        <div class="flex flex-col text-left leading-tight">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#EF4444]">AFRICA UNIVERSITY</span>
                            <span class="text-sm font-bold text-white tracking-tight">Kwang-Lim Chapel</span>
                        </div>
                    </a>
                </div>

                <!-- Center: Desktop Navigation Links (Pill Style) -->
                <nav class="hidden lg:flex items-center space-x-1.5">
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition {{ request()->routeIs('dashboard') ? 'bg-[#DC2626] text-white shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/10' }}">
                        <span class="nav-label" data-en="Dashboard" data-sn="Dhibhodhi">Dashboard</span>
                    </a>

                    @if(!$isCampusLife)
                        <a href="{{ route('requests.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition {{ request()->routeIs('requests.*') ? 'bg-[#DC2626] text-white shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/10' }}">
                            <span class="nav-label" data-en="Requests" data-sn="Zvikumbiro">Requests</span>
                        </a>
                    @endif

                    <a href="{{ route('calendar.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition {{ request()->routeIs('calendar.*') ? 'bg-[#DC2626] text-white shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/10' }}">
                        <span class="nav-label" data-en="Calendar" data-sn="Karenda">Calendar</span>
                    </a>

                    @if(!$isCampusLife)
                        <a href="{{ route('worship.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition {{ request()->routeIs('worship.*') ? 'bg-[#DC2626] text-white shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/10' }}">
                            <span class="nav-label" data-en="Call to Services" data-sn="Mabasa eKereke">Call to Services</span>
                        </a>

                        <a href="{{ route('equipment.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition {{ request()->routeIs('equipment.*') ? 'bg-[#DC2626] text-white shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/10' }}">
                            <span class="nav-label" data-en="Sound & Equipment" data-sn="Zvishandiso zveRuzha">Sound &amp; Equipment</span>
                        </a>

                        <!-- More Dropdown (Cascading exact menu) -->
                        <div class="relative">
                            <button type="button" onclick="toggleMoreMenu(event)" class="flex items-center space-x-1 px-3.5 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition cursor-pointer {{ request()->routeIs(['hymns.*', 'vault.*', 'handover.*', 'reports.*', 'members.*', 'clubs.*', 'users.*', 'audit.*', 'guidelines.*']) ? 'bg-[#DC2626] text-white shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/10' }}">
                                <span class="nav-label" data-en="More" data-sn="Zvimwe">More</span>
                                <svg class="w-3.5 h-3.5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div id="moreNavDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 animate-scale-up">
                                <div class="space-y-0.5">
                                    <a href="{{ route('hymns.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('hymns.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                        <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                        <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="Methodist Hymnal" data-sn="Nziyo dzeMethodist">Methodist Hymnal</span>
                                    </a>

                                    <a href="{{ route('clubs.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('clubs.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                        <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v4m-4 0h4"/></svg>
                                        <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="Fellowships Registry" data-sn="Mibatanidzwa">Fellowships Registry</span>
                                    </a>

                                    <a href="{{ route('vault.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('vault.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                        <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="Records Vault" data-sn="Gungwa reMagwaro">Records Vault</span>
                                    </a>

                                    <a href="{{ route('handover.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('handover.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                        <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="Leadership Handover" data-sn="Kugashidzana Masimba">Leadership Handover</span>
                                    </a>

                                    <a href="{{ route('reports.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('reports.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                        <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="Activity Reports" data-sn="Mishumo">Activity Reports</span>
                                    </a>

                                    <a href="{{ route('members.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('members.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                        <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="Members & Attendance" data-sn="Nhengo neKupinda">Members &amp; Attendance</span>
                                    </a>

                                    <div class="my-1.5 border-t border-slate-100"></div>

                                    <a href="{{ route('guidelines.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('guidelines.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="Chapel Policy & Guidelines" data-sn="Mitemo yeChapel">Chapel Policy &amp; Guidelines</span>
                                    </a>

                                    @if($isOfficeUser)
                                        <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('users.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="Staff & User Registry" data-sn="Vashandi neVashandisi">Staff &amp; User Registry</span>
                                        </a>

                                        <a href="{{ route('audit.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition group {{ request()->routeIs('audit.*') ? 'bg-red-50 text-[#DC2626] font-bold' : 'text-slate-800' }}">
                                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                            <span class="text-xs sm:text-[13px] font-medium nav-label" data-en="System Audit Log" data-sn="Gwaro reAudit">System Audit Log</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </nav>

                <!-- Right: Utilities (Search, Bell, Accessibility, Persona) -->
                <div class="flex items-center space-x-2 sm:space-x-2.5 shrink-0">
                    <!-- Quick Search Button (Magnifying Glass) -->
                    <button type="button" onclick="openCommandPalette()" class="p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition cursor-pointer" title="Quick Search (Ctrl + K)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>

                    <!-- Notifications Bell with Red Indicator Dot -->
                    <div class="relative">
                        <button id="notificationButton" type="button" onclick="toggleNotificationDrawer(event)" class="relative p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition cursor-pointer" title="Chapel Notices & Announcements">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#DC2626]"></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notificationDrawer" class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 text-xs animate-scale-up">
                            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900">Chapel Notices</span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Live</span>
                            </div>
                            @php
                                $activeAnnouncements = \App\Models\Announcement::orderBy('created_at', 'desc')->take(5)->get();
                            @endphp
                            <div class="p-2 divide-y divide-slate-100 max-h-64 overflow-y-auto">
                                @forelse($activeAnnouncements as $ann)
                                    <a href="{{ $ann->is_guideline ? route('guidelines.index') : route('calendar.index') }}" class="block p-2.5 hover:bg-slate-50 rounded-xl transition group">
                                        <div class="flex items-center justify-between">
                                            <div class="font-bold text-slate-900 group-hover:text-[#DC2626] transition truncate">{{ $ann->title }}</div>
                                            @if($ann->is_pinned)
                                                <span class="text-[9px] bg-red-100 text-[#DC2626] px-1.5 py-0.5 rounded font-bold uppercase shrink-0 ml-1">Policy</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ Str::limit($ann->message, 85) }}</p>
                                        <span class="text-[10px] text-[#DC2626] font-medium mt-1.5 inline-block group-hover:underline">Open details &rarr;</span>
                                    </a>
                                @empty
                                    <div class="p-6 text-center text-slate-400">
                                        <p class="text-xs">No active notices.</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                                @if($isOfficeUser)
                                    <a href="{{ route('notifications.index') }}" class="font-semibold text-[#DC2626] hover:underline text-[11px]">Dispatch Center &rarr;</a>
                                @else
                                    <a href="{{ route('guidelines.index') }}" class="font-semibold text-[#DC2626] hover:underline text-[11px]">View Guidelines &rarr;</a>
                                @endif
                                <button type="button" onclick="document.getElementById('notificationDrawer').classList.add('hidden')" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 px-2 py-0.5 rounded hover:bg-slate-200 transition cursor-pointer">Close</button>
                            </div>
                        </div>
                    </div>

                    <!-- Blue Accessibility Wheelchair Button -->
                    <button type="button" onclick="document.getElementById('accessibilityModal').classList.remove('hidden')" class="w-7 h-7 rounded-lg bg-[#2563EB] hover:bg-blue-600 text-white flex items-center justify-center transition cursor-pointer shadow-xs shrink-0" title="Accessibility & Language Preferences">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z"/></svg>
                    </button>

                    <!-- Red User Profile Avatar with Initial and Chevron -->
                    @auth
                        <div class="relative">
                            <button id="personaButton" type="button" onclick="togglePersonaDropdown(event)" class="flex items-center space-x-1.5 p-1 rounded-xl hover:bg-white/10 transition cursor-pointer">
                                <div class="w-8 h-8 rounded-xl bg-[#DC2626] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                    {{ $userInitial }}
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <!-- Profile Menu Dropdown -->
                            <div id="personaDropdown" class="hidden absolute right-0 mt-2 w-64 max-w-[calc(100vw-2rem)] bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 text-xs animate-scale-up">
                                <div class="p-3 bg-slate-50 border-b border-slate-200">
                                    <div class="font-bold text-slate-900 truncate">{{ $userName }}</div>
                                    <div class="text-[11px] text-slate-500 truncate">{{ $user->email }}</div>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-[#DC2626]">{{ $userRole }}</span>
                                </div>
                                <div class="p-1.5 space-y-0.5">
                                    <a href="{{ route('profile.index') }}" class="w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 font-medium transition flex items-center justify-between">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>Profile &amp; Password</span>
                                        </span>
                                        @if($user->mustChangePassword())
                                            <span class="w-2 h-2 rounded-full bg-[#DC2626]" title="Password update advised"></span>
                                        @endif
                                    </a>
                                </div>
                                <div class="p-1.5 border-t border-slate-200">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-2 rounded-xl hover:bg-red-50 text-[#DC2626] font-semibold transition flex items-center space-x-2 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            <span>Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth

                    <!-- Mobile Navigation Hamburger Toggle (on screens < lg) -->
                    <button type="button" onclick="toggleMobileNav(event)" class="lg:hidden p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition cursor-pointer" title="Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Navigation Menu (< lg) -->
        <div id="mobileNavMenu" class="hidden lg:hidden bg-black border-t border-white/10 px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('dashboard') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Dashboard</a>
            @if(!$isCampusLife)
                <a href="{{ route('requests.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('requests.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Requests</a>
            @endif
            <a href="{{ route('calendar.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('calendar.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Calendar</a>
            @if(!$isCampusLife)
                <a href="{{ route('worship.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('worship.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Call to Services</a>
                <a href="{{ route('equipment.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('equipment.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Sound &amp; Equipment</a>
                <a href="{{ route('hymns.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('hymns.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Methodist Hymnal</a>
                <a href="{{ route('clubs.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('clubs.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Fellowships Registry</a>
                <a href="{{ route('vault.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('vault.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Records Vault</a>
                <a href="{{ route('handover.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('handover.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Leadership Handover</a>
                <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('reports.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Activity Reports</a>
                <a href="{{ route('members.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('members.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Members &amp; Attendance</a>
                <a href="{{ route('guidelines.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('guidelines.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Chapel Policy &amp; Guidelines</a>
                @if($isOfficeUser)
                    <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('users.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">Staff &amp; User Registry</a>
                    <a href="{{ route('audit.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('audit.*') ? 'bg-[#DC2626] text-white' : 'text-slate-300 hover:bg-white/10' }}">System Audit Log</a>
                @endif
            @endif
        </div>
    </header>

    <!-- Global Alert Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-600 p-3.5 rounded-r-2xl shadow-2xs flex items-start space-x-3 mb-4 animate-fade-in">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs text-emerald-900 font-medium">{!! session('success') !!}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-3.5 rounded-r-2xl shadow-2xs flex items-start space-x-3 mb-4">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="text-xs text-amber-900 font-medium">{!! session('warning') !!}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-600 p-3.5 rounded-r-2xl shadow-2xs flex items-start space-x-3 mb-4">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs text-rose-900 font-medium">{!! session('error') !!}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-sky-50 border-l-4 border-sky-600 p-3.5 rounded-r-2xl shadow-2xs flex items-start space-x-3 mb-4">
                <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs text-sky-900 font-medium">{!! session('info') !!}</div>
            </div>
        @endif
    </div>

    <!-- Main Content Area (Unconstricted Normal Viewport Width) -->
    <main class="flex-1 w-full min-w-0 pb-16">
        @yield('content')
    </main>

    <!-- Quick Command Palette Modal (Ctrl + K) -->
    <div id="commandPaletteModal" class="hidden fixed inset-0 z-50 flex items-start justify-center pt-20 bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full border border-slate-200 overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <div class="p-3 border-b border-slate-200 flex items-center space-x-3 bg-slate-50">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="commandSearchInput" placeholder="Search proposals, clubs, events, hymns, or documents..." oninput="handleCommandSearch(this.value)" class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none">
                <button type="button" onclick="document.getElementById('commandPaletteModal').classList.add('hidden')" class="px-2 py-0.5 text-[10px] bg-white rounded border border-slate-200 text-slate-500 font-mono hover:bg-slate-100 cursor-pointer">ESC</button>
            </div>
            <div id="commandSearchResults" class="p-3 max-h-80 overflow-y-auto space-y-1 text-xs">
                <div class="text-slate-400 text-center py-6">Type to search records across the portal...</div>
            </div>
        </div>
    </div>

    <!-- Accessibility & Language Preferences Modal -->
    <div id="accessibilityModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <div class="bg-black px-6 py-4 flex items-center justify-between border-b border-white/10 text-white">
                <div class="flex items-center space-x-2">
                    <span class="text-lg">♿</span>
                    <div>
                        <h3 class="text-sm font-bold tracking-wide uppercase">Accessibility &amp; Language</h3>
                        <p class="text-[10px] text-slate-300">Zvirongwa Zvekuwanikwa Nemutauro</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('accessibilityModal').classList.add('hidden')" class="text-slate-400 hover:text-white cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <!-- High Contrast AAA Mode -->
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <div class="text-xs font-bold text-slate-900">High Contrast (WCAG AAA)</div>
                        <div class="text-[11px] text-slate-500">Pure black and high-contrast crimson</div>
                    </div>
                    <button type="button" id="a11yHighContrastBtn" onclick="toggleHighContrast()" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer">
                        Off
                    </button>
                </div>

                <!-- Large Text Mode -->
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <div class="text-xs font-bold text-slate-900">Enlarged Typography</div>
                        <div class="text-[11px] text-slate-500">Scale readable font size (+12%)</div>
                    </div>
                    <button type="button" id="a11yLargeTextBtn" onclick="toggleLargeText()" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer">
                        Off
                    </button>
                </div>

                <!-- Language Selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-900 mb-2">Display Language / Mutauro</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" id="langBtnEn" onclick="setLanguage('en')" class="w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-[#DC2626] bg-[#DC2626] text-white shadow-xs cursor-pointer">
                            English (Official)
                        </button>
                        <button type="button" id="langBtnSn" onclick="setLanguage('sn')" class="w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 cursor-pointer">
                            ChiShona (Mutauro)
                        </button>
                    </div>
                </div>

                <!-- Reset Settings -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <button type="button" onclick="resetAccessibility()" class="text-xs font-semibold text-slate-500 hover:text-slate-800 cursor-pointer">Reset Default</button>
                    <button type="button" onclick="document.getElementById('accessibilityModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-semibold cursor-pointer">Apply &amp; Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- UI Interactions Script -->
    <script>
        const defaultQuickActionsHtml = `
            <div class="p-2 space-y-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Quick Navigation</div>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 p-2 hover:bg-slate-100 rounded-lg text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('requests.index') }}" class="flex items-center space-x-2 p-2 hover:bg-slate-100 rounded-lg text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Proposals &amp; Requests</span>
                </a>
                <a href="{{ route('calendar.index') }}" class="flex items-center space-x-2 p-2 hover:bg-slate-100 rounded-lg text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Master Calendar</span>
                </a>
                <a href="{{ route('worship.index') }}" class="flex items-center space-x-2 p-2 hover:bg-slate-100 rounded-lg text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Call to Services</span>
                </a>
                <a href="{{ route('equipment.index') }}" class="flex items-center space-x-2 p-2 hover:bg-slate-100 rounded-lg text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Sound &amp; Equipment</span>
                </a>
                <a href="{{ route('hymns.index') }}" class="flex items-center space-x-2 p-2 hover:bg-slate-100 rounded-lg text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                    <span>Methodist Hymnal</span>
                </a>
            </div>
        `;

        function openCommandPalette() {
            const modal = document.getElementById('commandPaletteModal');
            modal.classList.remove('hidden');
            const input = document.getElementById('commandSearchInput');
            input.value = '';
            document.getElementById('commandSearchResults').innerHTML = defaultQuickActionsHtml;
            setTimeout(() => input.focus(), 50);
        }

        window.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                openCommandPalette();
            }
            if (e.key === 'Escape') {
                document.getElementById('commandPaletteModal').classList.add('hidden');
                document.getElementById('accessibilityModal').classList.add('hidden');
                const moreMenu = document.getElementById('moreNavDropdown');
                if (moreMenu) moreMenu.classList.add('hidden');
                const notif = document.getElementById('notificationDrawer');
                if (notif) notif.classList.add('hidden');
                const persona = document.getElementById('personaDropdown');
                if (persona) persona.classList.add('hidden');
            }
        });

        function toggleMoreMenu(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('moreNavDropdown');
            menu.classList.toggle('hidden');
        }

        function toggleMobileNav(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('mobileNavMenu');
            menu.classList.toggle('hidden');
        }

        function toggleNotificationDrawer(event) {
            if (event) event.stopPropagation();
            const drawer = document.getElementById('notificationDrawer');
            drawer.classList.toggle('hidden');
        }

        function togglePersonaDropdown(event) {
            if (event) event.stopPropagation();
            const dropdown = document.getElementById('personaDropdown');
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const moreMenu = document.getElementById('moreNavDropdown');
            if (moreMenu && !moreMenu.contains(e.target)) {
                moreMenu.classList.add('hidden');
            }
            const notif = document.getElementById('notificationDrawer');
            const notifBtn = document.getElementById('notificationButton');
            if (notif && !notif.contains(e.target) && notifBtn && !notifBtn.contains(e.target)) {
                notif.classList.add('hidden');
            }
            const persona = document.getElementById('personaDropdown');
            const personaBtn = document.getElementById('personaButton');
            if (persona && !persona.contains(e.target) && personaBtn && !personaBtn.contains(e.target)) {
                persona.classList.add('hidden');
            }
        });

        let searchTimeout = null;
        function handleCommandSearch(val) {
            clearTimeout(searchTimeout);
            const container = document.getElementById('commandSearchResults');
            if (!val || val.length < 2) {
                container.innerHTML = defaultQuickActionsHtml;
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch('/api/search?q=' + encodeURIComponent(val))
                    .then(res => res.json())
                    .then(data => {
                        if (!data.results || data.results.length === 0) {
                            container.innerHTML = '<div class="text-slate-400 text-center py-6 text-xs">No matching records found.</div>';
                            return;
                        }
                        let html = '';
                        data.results.forEach(item => {
                            html += `
                                <a href="${item.url}" class="block p-2.5 rounded-xl hover:bg-slate-100 transition border border-transparent hover:border-slate-200">
                                    <div class="flex items-center justify-between">
                                        <div class="font-bold text-slate-900">${item.title}</div>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-red-100 text-[#DC2626] font-bold uppercase">${item.category}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">${item.subtitle}</div>
                                </a>
                            `;
                        });
                        container.innerHTML = html;
                    });
            }, 250);
        }

        function toggleHighContrast() {
            const html = document.documentElement;
            const isHigh = html.classList.toggle('high-contrast');
            localStorage.setItem('au_high_contrast', isHigh ? 'true' : 'false');
            updateA11yUI();
        }

        function toggleLargeText() {
            const html = document.documentElement;
            const isLarge = html.classList.toggle('large-text');
            localStorage.setItem('au_large_text', isLarge ? 'true' : 'false');
            updateA11yUI();
        }

        function setLanguage(lang) {
            localStorage.setItem('au_lang', lang);
            applyLanguage(lang);
            updateA11yUI();
        }

        function applyLanguage(lang) {
            document.querySelectorAll('.nav-label').forEach(el => {
                const text = el.getAttribute('data-' + lang);
                if (text) {
                    el.textContent = text;
                }
            });
        }

        function updateA11yUI() {
            const isHigh = document.documentElement.classList.contains('high-contrast');
            const isLarge = document.documentElement.classList.contains('large-text');
            const lang = localStorage.getItem('au_lang') || 'en';

            const hcBtn = document.getElementById('a11yHighContrastBtn');
            if (hcBtn) {
                hcBtn.textContent = isHigh ? 'Active (ON)' : 'Off';
                hcBtn.className = isHigh 
                    ? 'px-3 py-1.5 rounded-lg text-xs font-bold border transition bg-emerald-600 text-white border-emerald-700 cursor-pointer' 
                    : 'px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer';
            }

            const ltBtn = document.getElementById('a11yLargeTextBtn');
            if (ltBtn) {
                ltBtn.textContent = isLarge ? 'Active (ON)' : 'Off';
                ltBtn.className = isLarge
                    ? 'px-3 py-1.5 rounded-lg text-xs font-bold border transition bg-emerald-600 text-white border-emerald-700 cursor-pointer' 
                    : 'px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer';
            }

            const btnEn = document.getElementById('langBtnEn');
            const btnSn = document.getElementById('langBtnSn');
            if (btnEn && btnSn) {
                if (lang === 'sn') {
                    btnSn.className = 'w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-[#DC2626] bg-[#DC2626] text-white shadow-xs cursor-pointer';
                    btnEn.className = 'w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 cursor-pointer';
                } else {
                    btnEn.className = 'w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-[#DC2626] bg-[#DC2626] text-white shadow-xs cursor-pointer';
                    btnSn.className = 'w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 cursor-pointer';
                }
            }
        }

        function resetAccessibility() {
            localStorage.removeItem('au_high_contrast');
            localStorage.removeItem('au_large_text');
            localStorage.setItem('au_lang', 'en');
            document.documentElement.classList.remove('high-contrast');
            document.documentElement.classList.remove('large-text');
            applyLanguage('en');
            updateA11yUI();
        }

        (function() {
            try {
                // Ensure default normal text scaling
                document.documentElement.classList.remove('large-text');
                localStorage.removeItem('au_large_text');

                if (localStorage.getItem('au_high_contrast') === 'true') {
                    document.documentElement.classList.add('high-contrast');
                }
            } catch (e) {}
            const lang = localStorage.getItem('au_lang') || 'en';
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    applyLanguage(lang);
                    updateA11yUI();
                });
            } else {
                applyLanguage(lang);
                updateA11yUI();
            }
        })();

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').catch(function(err) {
                    console.log('AU Chapel ServiceWorker error:', err);
                });
            });
        }
    </script>
</body>
</html>
