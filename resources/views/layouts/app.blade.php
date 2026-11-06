<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0F1117">
    <meta name="description" content="Kwang-Lim Chapel Fellowship and Ministry Management System - Africa University">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>@yield('title', 'Kwang-Lim Chapel') | Ministry Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html { font-size: 100%; -webkit-text-size-adjust: 100%; text-size-adjust: 100%; }
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
        html.large-text { font-size: 112% !important; }
        html.high-contrast body { background-color: #000 !important; color: #fff !important; }
        html.high-contrast .sidebar-shell { background: #0a0a0a !important; border-color: #A51C30 !important; }
        html.high-contrast .card-elevation, html.high-contrast .bg-white { background: #080808 !important; color: #fff !important; border-color: #A51C30 !important; }
        html.high-contrast .text-slate-900, html.high-contrast .text-slate-800, html.high-contrast .text-slate-700 { color: #fff !important; }
        html.high-contrast .text-slate-600, html.high-contrast .text-slate-500, html.high-contrast .text-slate-400 { color: #f1f5f9 !important; }
        html.high-contrast .bg-slate-50, html.high-contrast .bg-slate-100 { background: #121212 !important; border-color: #A51C30 !important; }

        /* ── Sidebar drawer (mobile slide-over) ── */
        #sidebarDrawer { transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        #sidebarOverlay { transition: opacity 0.25s ease; }

        /* ── Modals ── */
        #commandPaletteModal.hidden,
        #accessibilityModal.hidden,
        #notificationDrawer.hidden,
        #personaDropdown.hidden { display: none !important; }

        @media (max-width: 1023px) {
            #sidebarDrawer { transform: translateX(-100%); }
            #sidebarDrawer.open { transform: translateX(0); }
        }
    </style>
</head>
<body class="h-full bg-paper text-slate-800 antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[60] focus:px-4 focus:py-2 focus:rounded-xl focus:bg-white focus:text-brand-dark focus:font-bold focus:text-xs focus:shadow-lg">Skip to main content</a>

    @php
        $user = auth()->user();
        $isOfficeUser = $user && $user->isOffice();
        $isClubUser = $user && ($user->isClubExecutive() || $user->isClubPresident());
        $isCampusLife = $user && $user->isCampusLifeSrc();
        $userName = $user ? $user->name : 'Chapel Guest';
        $userRole = $user ? $user->getRoleDisplayName() : 'Guest';
        $userInitial = mb_strtoupper(mb_substr($userName, 0, 1));
        $activeAnnouncements = $user ? \App\Models\Announcement::orderBy('created_at', 'desc')->take(5)->get() : collect();

        $liturgical = \App\Services\LiturgicalCalendarService::getCurrentSeason();
    @endphp

    <div class="flex h-full overflow-hidden">

        {{-- ═══════════════════════════════════════════════════════
             SIDEBAR (desktop: always visible | mobile: slide-over)
             ═══════════════════════════════════════════════════════ --}}

        {{-- Mobile overlay --}}
        <div id="sidebarOverlay" class="hidden fixed inset-0 z-40 bg-black/60 backdrop-blur-xs lg:hidden" onclick="closeSidebar()" aria-hidden="true"></div>

        <aside id="sidebarDrawer"
               class="sidebar-shell fixed lg:static inset-y-0 left-0 z-50 flex flex-col w-64 shrink-0 overflow-hidden"
               aria-label="Site navigation">

            {{-- ── Top: Brand identity ── --}}
            <div class="sidebar-brand flex items-center gap-3 px-5 py-4 border-b border-white/8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-b from-brand to-brand-deep ring-1 ring-white/15 flex items-center justify-center p-1.5 shadow-sm shrink-0 group-hover:scale-105 transition-transform">
                        <x-umc-logo class="w-full h-full text-white" />
                    </div>
                    <div class="min-w-0 leading-tight">
                        <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-gold">Africa University</span>
                        <span class="block font-display text-[14px] text-white leading-tight truncate">Kwang-Lim Chapel</span>
                    </div>
                </a>
                {{-- Mobile close --}}
                <button id="sidebarCloseBtn" onclick="closeSidebar()" class="ml-auto lg:hidden p-1.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition shrink-0" aria-label="Close menu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- ── Scrollable nav body ── --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-5" aria-label="Primary">

                {{-- OVERVIEW --}}
                <div>
                    <p class="sidebar-section-label">Overview</p>
                    <div class="mt-1 space-y-0.5">
                        <x-sidebar-link route="dashboard" icon="home" label="Dashboard" :active="request()->routeIs('dashboard')" />
                        <x-sidebar-link route="inbox.index" icon="inbox" label="Inbox" :active="request()->routeIs('inbox.*')" :badge="$unreadInboxCount ?? 0" />
                    </div>
                </div>

                @if(!$isCampusLife)
                {{-- MINISTRY --}}
                <div>
                    <p class="sidebar-section-label">Ministry</p>
                    <div class="mt-1 space-y-0.5">
                        <x-sidebar-link route="requests.index" icon="requests" label="Proposals & Requests" :active="request()->routeIs('requests.*')" />
                        <x-sidebar-link route="calendar.index" icon="calendar" label="Master Calendar" :active="request()->routeIs('calendar.*')" />
                        <x-sidebar-link route="worship.index" icon="worship" label="Call to Services" :active="request()->routeIs('worship.*')" />
                        <x-sidebar-link route="equipment.index" icon="equipment" label="Sound & Equipment" :active="request()->routeIs('equipment.*')" />
                    </div>
                </div>

                {{-- RESOURCES --}}
                <div>
                    <p class="sidebar-section-label">Resources</p>
                    <div class="mt-1 space-y-0.5">
                        <x-sidebar-link route="hymns.index" icon="hymns" label="Methodist Hymnal" :active="request()->routeIs('hymns.*')" />
                        <x-sidebar-link route="vault.index" icon="vault" label="Records Vault" :active="request()->routeIs('vault.*')" />
                        <x-sidebar-link route="members.index" icon="members" label="Members & Attendance" :active="request()->routeIs('members.*')" />
                    </div>
                </div>

                {{-- OPERATIONS --}}
                <div>
                    <p class="sidebar-section-label">Operations</p>
                    <div class="mt-1 space-y-0.5">
                        <x-sidebar-link route="reports.index" icon="reports" label="Activity Reports" :active="request()->routeIs('reports.*')" />
                        <x-sidebar-link route="handover.index" icon="handover" label="Leadership Handover" :active="request()->routeIs('handover.*')" />
                        <x-sidebar-link route="guidelines.index" icon="guidelines" label="Policy & Guidelines" :active="request()->routeIs('guidelines.*')" />
                    </div>
                </div>

                @if($isOfficeUser)
                {{-- ADMINISTRATION (Office only) --}}
                <div>
                    <p class="sidebar-section-label text-gold/70">Administration</p>
                    <div class="mt-1 space-y-0.5">
                        <x-sidebar-link route="clubs.index" icon="clubs" label="Fellowships Registry" :active="request()->routeIs('clubs.*')" />
                        <x-sidebar-link route="users.index" icon="users" label="Staff & User Registry" :active="request()->routeIs('users.*')" />
                        <x-sidebar-link route="audit.index" icon="audit" label="System Audit Log" :active="request()->routeIs('audit.*')" />
                        <x-sidebar-link route="notifications.index" icon="notifications" label="Dispatch Centre" :active="request()->routeIs('notifications.*')" />
                    </div>
                </div>
                @endif

                @else
                {{-- Campus Life / SRC: limited nav --}}
                <div>
                    <p class="sidebar-section-label">Chapel Calendar</p>
                    <div class="mt-1 space-y-0.5">
                        <x-sidebar-link route="calendar.index" icon="calendar" label="Master Calendar" :active="request()->routeIs('calendar.*')" />
                        <x-sidebar-link route="guidelines.index" icon="guidelines" label="Policy & Guidelines" :active="request()->routeIs('guidelines.*')" />
                    </div>
                </div>
                @endif

            </nav>

            {{-- ── Bottom: Liturgical season + user card ── --}}
            <div class="border-t border-white/8">
                {{-- Liturgical season pill --}}
                <div class="px-4 py-2.5">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/6 border border-white/8">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $liturgical['hex'] }}"></span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-white/80 leading-tight truncate">{{ $liturgical['name'] }}</p>
                            <p class="text-[9px] text-white/40 leading-tight truncate">{{ $liturgical['color'] }}</p>
                        </div>
                        <button type="button" onclick="showLiturgicalInfo()" class="ml-auto text-white/30 hover:text-white/70 transition" aria-label="Liturgical season info">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>
                </div>

                {{-- User card + profile --}}
                @auth
                <div class="px-4 pb-4">
                    <div class="relative">
                        <button id="personaButton" type="button" onclick="togglePersonaDropdown(event)" aria-haspopup="true" aria-expanded="false" aria-controls="personaDropdown"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/8 transition text-left" aria-label="Account menu for {{ $userName }}">
                            <div class="w-8 h-8 rounded-lg bg-brand text-white flex items-center justify-center font-bold text-xs shrink-0">{{ $userInitial }}</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-white truncate">{{ \Illuminate\Support\Str::of($userName)->explode(' ')->first() }} {{ \Illuminate\Support\Str::of($userName)->explode(' ')->last() }}</p>
                                <p class="text-[10px] text-white/40 truncate">{{ $userRole }}</p>
                            </div>
                            <svg class="w-3.5 h-3.5 text-white/30 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                        </button>

                        <div id="personaDropdown" class="hidden absolute bottom-full left-0 right-0 mb-2 bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50 text-xs animate-scale-up">
                            <div class="p-3 bg-slate-50 border-b border-slate-100">
                                <div class="font-bold text-slate-900 truncate">{{ $userName }}</div>
                                <div class="text-[11px] text-slate-500 truncate">{{ $user->email }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-brand">{{ $userRole }}</span>
                            </div>
                            <div class="p-1.5 space-y-0.5">
                                <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 font-medium transition">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Profile &amp; Password</span>
                                    @if($user->mustChangePassword())
                                        <span class="ml-auto w-2 h-2 rounded-full bg-brand shrink-0"></span>
                                    @endif
                                </a>
                                <a href="{{ route('guidelines.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 font-medium transition">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    <span>Chapel Policy</span>
                                </a>
                            </div>
                            <div class="p-1.5 border-t border-slate-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-red-50 text-brand font-semibold transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </aside>

        {{-- ═══════════════════════════════════════════════════════
             MAIN AREA: slim topbar + scrollable content
             ═══════════════════════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- ── Top Bar ── --}}
            <header class="topbar sticky top-0 z-30 flex items-center gap-3 px-4 sm:px-6 h-14 shrink-0">
                {{-- Mobile hamburger --}}
                <button onclick="openSidebar()" class="lg:hidden p-2 -ml-1 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition" aria-label="Open navigation menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                {{-- Page title slot (optional) --}}
                <div class="flex-1 min-w-0 hidden sm:block">
                    <div class="topbar-breadcrumb text-xs text-slate-400 font-medium"></div>
                </div>

                {{-- Right utilities --}}
                <div class="ml-auto flex items-center gap-1.5">
                    {{-- Search --}}
                    <button type="button" onclick="openCommandPalette()" class="topbar-action" title="Quick Search (Ctrl+K)" aria-label="Quick search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>

                    {{-- Inbox --}}
                    <a href="{{ route('inbox.index') }}" class="topbar-action relative" title="Inbox" aria-label="Inbox{{ ($unreadInboxCount ?? 0) ? ' (' . $unreadInboxCount . ' unread)' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        @if($unreadInboxCount ?? 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-brand text-white text-[9px] font-bold leading-[1.1rem] text-center">{{ ($unreadInboxCount > 9) ? '9+' : $unreadInboxCount }}</span>
                        @endif
                    </a>

                    {{-- Notices --}}
                    <div class="relative">
                        <button id="notificationButton" type="button" onclick="toggleNotificationDrawer(event)" aria-haspopup="true" aria-expanded="false" class="topbar-action relative" aria-label="Chapel notices ({{ $activeAnnouncements->count() }})">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if($activeAnnouncements->isNotEmpty())
                                <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full bg-brand"></span>
                            @endif
                        </button>
                        <div id="notificationDrawer" class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 text-xs animate-scale-up">
                            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900">Chapel Notices</span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Latest {{ $activeAnnouncements->count() }}</span>
                            </div>
                            <div class="p-2 divide-y divide-slate-100 max-h-72 overflow-y-auto">
                                @forelse($activeAnnouncements as $ann)
                                    <a href="{{ $ann->is_guideline ? route('guidelines.index') : route('calendar.index') }}" class="block p-2.5 hover:bg-slate-50 rounded-xl transition group">
                                        <div class="flex items-center justify-between">
                                            <div class="font-bold text-slate-900 group-hover:text-brand transition truncate">{{ $ann->title }}</div>
                                            @if($ann->is_pinned)
                                                <span class="text-[9px] bg-red-100 text-brand px-1.5 py-0.5 rounded font-bold uppercase shrink-0 ml-1">Policy</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ Str::limit($ann->message, 85) }}</p>
                                        <span class="text-[10px] text-brand font-medium mt-1.5 inline-block group-hover:underline">Open details &rarr;</span>
                                    </a>
                                @empty
                                    <div class="p-6 text-center text-slate-500"><p class="text-xs">No active notices.</p></div>
                                @endforelse
                            </div>
                            <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                                @if($isOfficeUser)
                                    <a href="{{ route('notifications.index') }}" class="font-semibold text-brand hover:underline text-[11px]">Dispatch Centre &rarr;</a>
                                @else
                                    <a href="{{ route('guidelines.index') }}" class="font-semibold text-brand hover:underline text-[11px]">View Guidelines &rarr;</a>
                                @endif
                                <button type="button" onclick="document.getElementById('notificationDrawer').classList.add('hidden')" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 px-2 py-0.5 rounded hover:bg-slate-200 transition cursor-pointer">Close</button>
                            </div>
                        </div>
                    </div>

                    {{-- Accessibility --}}
                    <button type="button" onclick="openAccessibilityModal()" class="topbar-action" aria-label="Accessibility preferences">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z"/></svg>
                    </button>

                    {{-- Mobile user initial (links to profile) --}}
                    <a href="{{ route('profile.index') }}" class="lg:hidden w-8 h-8 rounded-lg bg-brand text-white flex items-center justify-center font-bold text-xs shrink-0 hover:bg-brand-dark transition">{{ $userInitial }}</a>
                </div>
            </header>

            {{-- ── Global flash alerts ── --}}
            @if(session('success') || session('warning') || session('error') || session('info') || ($errors->any() && !request()->routeIs('clubs.register', 'profile.index')))
            <div class="px-4 sm:px-6 pt-4 space-y-2">
                @if(session('success'))
                    <div class="flash-success" role="alert">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="flash-warning" role="alert">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="text-xs font-medium">{{ session('warning') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flash-error" role="alert">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-medium">{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="flash-info" role="alert">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-medium">{{ session('info') }}</span>
                    </div>
                @endif
                @if($errors->any() && !request()->routeIs('clubs.register', 'profile.index'))
                    <div class="flash-error" role="alert">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-xs">
                            <p class="font-bold">Please correct {{ $errors->count() === 1 ? 'this issue' : 'these issues' }}:</p>
                            <ul class="list-disc pl-4 mt-1 space-y-0.5">
                                @foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            {{-- ── Main content ── --}}
            <main id="main-content" tabindex="-1" class="flex-1 overflow-y-auto focus:outline-none pb-16">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         GLOBAL MODALS (command palette, a11y, liturgical)
         ══════════════════════════════════════════ --}}

    {{-- Command Palette --}}
    <div id="commandPaletteModal" role="dialog" aria-modal="true" aria-label="Quick search" class="hidden fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24 bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full border border-slate-200 overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <div class="p-3 border-b border-slate-100 flex items-center gap-3 bg-slate-50">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" id="commandSearchInput" aria-label="Search the portal" autocomplete="off" placeholder="Search proposals, clubs, events, hymns…" oninput="handleCommandSearch(this.value)" class="flex-1 bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none">
                <button type="button" onclick="document.getElementById('commandPaletteModal').classList.add('hidden')" aria-label="Close" class="px-2 py-0.5 text-[10px] bg-white rounded border border-slate-200 text-slate-500 font-mono hover:bg-slate-100 cursor-pointer">ESC</button>
            </div>
            <div id="commandSearchResults" aria-live="polite" class="p-3 max-h-80 overflow-y-auto space-y-1 text-xs">
                <div class="text-slate-400 text-center py-6">Type to search across the portal…</div>
            </div>
        </div>
    </div>

    {{-- Accessibility Modal --}}
    <div id="accessibilityModal" role="dialog" aria-modal="true" aria-labelledby="a11yModalTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <div class="bg-ink px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-lg">♿</span>
                    <div>
                        <h3 id="a11yModalTitle" class="text-sm font-bold text-white uppercase tracking-wide">Accessibility &amp; Language</h3>
                        <p class="text-[10px] text-white/50">Zvirongwa Zvekuwanikwa Nemutauro</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('accessibilityModal').classList.add('hidden')" class="text-white/50 hover:text-white transition cursor-pointer" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <div class="text-xs font-bold text-slate-900">High Contrast (WCAG AAA)</div>
                        <div class="text-[11px] text-slate-500">Pure black and high-contrast crimson</div>
                    </div>
                    <button type="button" id="a11yHighContrastBtn" aria-pressed="false" onclick="toggleHighContrast()" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer">Off</button>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <div class="text-xs font-bold text-slate-900">Enlarged Typography</div>
                        <div class="text-[11px] text-slate-500">Scale readable font size (+12%)</div>
                    </div>
                    <button type="button" id="a11yLargeTextBtn" aria-pressed="false" onclick="toggleLargeText()" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer">Off</button>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 mb-2">Display Language / Mutauro</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" id="langBtnEn" onclick="setLanguage('en')" class="py-2 px-3 rounded-xl text-xs font-bold border border-brand bg-brand text-white cursor-pointer">English (Official)</button>
                        <button type="button" id="langBtnSn" onclick="setLanguage('sn')" class="py-2 px-3 rounded-xl text-xs font-bold border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 cursor-pointer">ChiShona (Mutauro)</button>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <button type="button" onclick="resetAccessibility()" class="text-xs font-semibold text-slate-500 hover:text-slate-800 cursor-pointer">Reset Default</button>
                    <button type="button" onclick="document.getElementById('accessibilityModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-ink hover:bg-black text-white text-xs font-semibold cursor-pointer">Apply &amp; Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Liturgical Season Info Modal --}}
    <div id="liturgicalModal" role="dialog" aria-modal="true" aria-labelledby="liturgicalTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden animate-scale-up">
            <div class="px-6 py-5 text-white relative overflow-hidden" style="background: {{ $liturgical['hex'] }}">
                <div class="absolute inset-0 opacity-10" style="background: radial-gradient(circle at 80% 50%, white, transparent 70%)"></div>
                <div class="relative">
                    <div class="text-xs font-bold uppercase tracking-widest opacity-80 mb-1">Liturgical Season</div>
                    <h3 id="liturgicalTitle" class="text-xl font-display font-semibold">{{ $liturgical['name'] }}</h3>
                    <p class="text-sm mt-1 opacity-80">{{ $liturgical['color'] }}</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <blockquote class="text-sm italic text-slate-700 border-l-4 pl-4" style="border-color: {{ $liturgical['hex'] }}">{{ $liturgical['verse'] }}</blockquote>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $liturgical['parament'] }}</p>
                <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ $liturgical['focus'] }}</p>
                <div class="pt-3 border-t border-slate-100 text-right">
                    <button onclick="document.getElementById('liturgicalModal').classList.add('hidden')" class="px-5 py-2 rounded-xl bg-ink text-white text-xs font-semibold hover:bg-black transition cursor-pointer">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════
         SCRIPTS
         ════════════════════ --}}
    <script>
        window.__chapelLayoutUi = true;

        // Sidebar
        function openSidebar() {
            const d = document.getElementById('sidebarDrawer');
            const o = document.getElementById('sidebarOverlay');
            d.classList.add('open');
            o.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.getElementById('sidebarCloseBtn')?.focus();
        }
        function closeSidebar() {
            const d = document.getElementById('sidebarDrawer');
            const o = document.getElementById('sidebarOverlay');
            d.classList.remove('open');
            o.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function showLiturgicalInfo() {
            document.getElementById('liturgicalModal').classList.remove('hidden');
        }

        // Command palette
        let lastFocusBeforeModal = null;
        function openCommandPalette() {
            const modal = document.getElementById('commandPaletteModal');
            lastFocusBeforeModal = document.activeElement;
            modal.classList.remove('hidden');
            const input = document.getElementById('commandSearchInput');
            input.value = '';
            document.getElementById('commandSearchResults').innerHTML = '<div class="text-slate-400 text-center py-6">Type to search across the portal…</div>';
            setTimeout(() => input.focus(), 50);
        }

        function openAccessibilityModal() {
            lastFocusBeforeModal = document.activeElement;
            document.getElementById('accessibilityModal').classList.remove('hidden');
        }

        // Dropdowns
        const chapelDropdowns = [
            ['notificationDrawer', 'notificationButton'],
            ['personaDropdown', 'personaButton'],
        ];

        function setDropdown(panelId, open) {
            const panel = document.getElementById(panelId);
            if (!panel) return;
            panel.classList.toggle('hidden', !open);
            const entry = chapelDropdowns.find(d => d[0] === panelId);
            const btn = entry ? document.getElementById(entry[1]) : null;
            if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        function toggleDropdown(panelId, event) {
            if (event) event.stopPropagation();
            const panel = document.getElementById(panelId);
            if (!panel) return;
            const willOpen = panel.classList.contains('hidden');
            chapelDropdowns.forEach(d => { if (d[0] !== panelId) setDropdown(d[0], false); });
            setDropdown(panelId, willOpen);
        }

        function toggleNotificationDrawer(event) { toggleDropdown('notificationDrawer', event); }
        function togglePersonaDropdown(event) { toggleDropdown('personaDropdown', event); }

        // Global keyboard shortcuts
        window.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                openCommandPalette();
            }
            if (e.key === 'Escape') {
                let restoreTo = null;
                ['commandPaletteModal', 'accessibilityModal', 'liturgicalModal'].forEach(id => {
                    const m = document.getElementById(id);
                    if (m && !m.classList.contains('hidden')) {
                        m.classList.add('hidden');
                        restoreTo = lastFocusBeforeModal;
                    }
                });
                chapelDropdowns.forEach(d => {
                    const panel = document.getElementById(d[0]);
                    if (panel && !panel.classList.contains('hidden')) {
                        setDropdown(d[0], false);
                        restoreTo = restoreTo || document.getElementById(d[1]);
                    }
                });
                // Close mobile sidebar
                const drawer = document.getElementById('sidebarDrawer');
                if (drawer && drawer.classList.contains('open')) {
                    closeSidebar();
                }
                if (restoreTo && typeof restoreTo.focus === 'function') restoreTo.focus();
            }
        });

        document.addEventListener('click', function(e) {
            chapelDropdowns.forEach(d => {
                const panel = document.getElementById(d[0]);
                const btn = document.getElementById(d[1]);
                if (panel && !panel.classList.contains('hidden') && !panel.contains(e.target) && !(btn && btn.contains(e.target))) {
                    setDropdown(d[0], false);
                }
            });
        });

        document.addEventListener('focusin', function(e) {
            chapelDropdowns.forEach(d => {
                const panel = document.getElementById(d[0]);
                const btn = document.getElementById(d[1]);
                if (panel && !panel.classList.contains('hidden') && !panel.contains(e.target) && !(btn && btn.contains(e.target))) {
                    setDropdown(d[0], false);
                }
            });
        });

        // Form helpers
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) { e.preventDefault(); return; }
            if (e.defaultPrevented || (form.getAttribute('method') || 'get').toLowerCase() === 'get' || form.hasAttribute('data-no-lock')) return;
            if (form.dataset.submitting === '1') { e.preventDefault(); return; }
            form.dataset.submitting = '1';
            setTimeout(() => {
                form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]').forEach(b => {
                    b.disabled = true;
                    b.setAttribute('aria-busy', 'true');
                    b.classList.add('opacity-60', 'cursor-wait');
                });
            }, 0);
        });

        window.addEventListener('pageshow', function(e) {
            if (!e.persisted) return;
            document.querySelectorAll('form[data-submitting="1"]').forEach(form => {
                delete form.dataset.submitting;
                form.querySelectorAll('[aria-busy="true"]').forEach(b => {
                    b.disabled = false;
                    b.removeAttribute('aria-busy');
                    b.classList.remove('opacity-60', 'cursor-wait');
                });
            });
        });

        // Search
        function buildSearchResult(item) {
            const a = document.createElement('a');
            let href = '#';
            try {
                const u = new URL(String(item.url || ''), window.location.origin);
                if (u.origin === window.location.origin) href = u.href;
            } catch (err) {}
            a.href = href;
            a.className = 'block p-2.5 rounded-xl hover:bg-slate-100 transition border border-transparent hover:border-slate-200';
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2';
            const title = document.createElement('div');
            title.className = 'font-bold text-slate-900 truncate';
            title.textContent = item.title || '';
            const cat = document.createElement('span');
            cat.className = 'text-[9px] px-1.5 py-0.5 rounded bg-red-100 text-brand-dark font-bold uppercase shrink-0';
            cat.textContent = item.category || '';
            row.append(title, cat);
            const sub = document.createElement('div');
            sub.className = 'text-[11px] text-slate-500 mt-0.5';
            sub.textContent = item.subtitle || '';
            a.append(row, sub);
            return a;
        }

        let searchTimeout = null, searchSeq = 0;
        function handleCommandSearch(val) {
            clearTimeout(searchTimeout);
            const container = document.getElementById('commandSearchResults');
            if (!val || val.length < 2) {
                container.innerHTML = '<div class="text-slate-400 text-center py-6">Type to search…</div>';
                return;
            }
            const seq = ++searchSeq;
            searchTimeout = setTimeout(() => {
                fetch('/api/search?q=' + encodeURIComponent(val), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
                    .then(data => {
                        if (seq !== searchSeq) return;
                        container.replaceChildren();
                        if (!data.results || !data.results.length) {
                            container.innerHTML = '<div class="text-slate-500 text-center py-6 text-xs">No matching records found.</div>';
                            return;
                        }
                        data.results.forEach(item => container.appendChild(buildSearchResult(item)));
                    })
                    .catch(() => {
                        if (seq !== searchSeq) return;
                        container.innerHTML = '<div class="text-rose-700 text-center py-4 text-xs">Search unavailable. Please try again.</div>';
                    });
            }, 250);
        }

        // Accessibility helpers
        function safeStorageGet(k) { try { return localStorage.getItem(k); } catch { return null; } }
        function safeStorageSet(k, v) { try { localStorage.setItem(k, v); } catch {} }
        function safeStorageRemove(k) { try { localStorage.removeItem(k); } catch {} }

        function toggleHighContrast() {
            const on = document.documentElement.classList.toggle('high-contrast');
            safeStorageSet('au_high_contrast', on ? 'true' : 'false');
            updateA11yUI();
        }
        function toggleLargeText() {
            const on = document.documentElement.classList.toggle('large-text');
            safeStorageSet('au_large_text', on ? 'true' : 'false');
            updateA11yUI();
        }
        function setLanguage(lang) { safeStorageSet('au_lang', lang); applyLanguage(lang); updateA11yUI(); }
        function applyLanguage(lang) {
            document.querySelectorAll('.nav-label').forEach(el => {
                const t = el.getAttribute('data-' + lang);
                if (t) el.textContent = t;
            });
        }
        function updateA11yUI() {
            const isHigh = document.documentElement.classList.contains('high-contrast');
            const isLarge = document.documentElement.classList.contains('large-text');
            const lang = safeStorageGet('au_lang') || 'en';
            const onCls = 'px-3 py-1.5 rounded-lg text-xs font-bold border transition bg-emerald-600 text-white border-emerald-700 cursor-pointer';
            const offCls = 'px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer';
            const hcBtn = document.getElementById('a11yHighContrastBtn');
            if (hcBtn) { hcBtn.textContent = isHigh ? 'Active (ON)' : 'Off'; hcBtn.className = isHigh ? onCls : offCls; hcBtn.setAttribute('aria-pressed', isHigh ? 'true' : 'false'); }
            const ltBtn = document.getElementById('a11yLargeTextBtn');
            if (ltBtn) { ltBtn.textContent = isLarge ? 'Active (ON)' : 'Off'; ltBtn.className = isLarge ? onCls : offCls; ltBtn.setAttribute('aria-pressed', isLarge ? 'true' : 'false'); }
            const btnEn = document.getElementById('langBtnEn');
            const btnSn = document.getElementById('langBtnSn');
            if (btnEn && btnSn) {
                const activeCls = 'py-2 px-3 rounded-xl text-xs font-bold border border-brand bg-brand text-white cursor-pointer';
                const idleCls = 'py-2 px-3 rounded-xl text-xs font-bold border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 cursor-pointer';
                btnSn.className = lang === 'sn' ? activeCls : idleCls;
                btnEn.className = lang === 'sn' ? idleCls : activeCls;
                btnSn.setAttribute('aria-pressed', lang === 'sn' ? 'true' : 'false');
                btnEn.setAttribute('aria-pressed', lang === 'sn' ? 'false' : 'true');
            }
        }
        function resetAccessibility() {
            safeStorageRemove('au_high_contrast'); safeStorageRemove('au_large_text'); safeStorageSet('au_lang', 'en');
            document.documentElement.classList.remove('high-contrast', 'large-text');
            applyLanguage('en'); updateA11yUI();
        }

        (function() {
            document.documentElement.classList.toggle('high-contrast', safeStorageGet('au_high_contrast') === 'true');
            document.documentElement.classList.toggle('large-text', safeStorageGet('au_large_text') === 'true');
            const lang = safeStorageGet('au_lang') || 'en';
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => { applyLanguage(lang); updateA11yUI(); });
            } else {
                applyLanguage(lang); updateA11yUI();
            }
        })();
    </script>
</body>
</html>
