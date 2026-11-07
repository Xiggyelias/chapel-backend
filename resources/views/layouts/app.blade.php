<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FFFFFF">
    <meta name="description" content="Kwang-Lim Chapel Fellowship and Ministry Management System - Africa University">
    <meta name="view-transition" content="same-origin">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>@yield('title', 'Kwang-Lim Chapel') | Ministry Portal</title>

    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html { font-size: 100%; -webkit-text-size-adjust: 100%; text-size-adjust: 100%; }
        [x-cloak] { display: none !important; }
        html.large-text { font-size: 112% !important; }
        html.high-contrast body { background-color: #000 !important; color: #fff !important; }
        html.high-contrast .sidebar-shell { background: #0a0a0a !important; border-color: #DC2626 !important; color: #fff !important; }
        html.high-contrast .sidebar-nav-item { color: #f1f5f9 !important; }
        html.high-contrast .sidebar-nav-item.active { background: #DC2626 !important; color: #fff !important; }
        html.high-contrast .topbar-search { background: #121212 !important; color: #fff !important; }
        html.high-contrast .card-elevation, html.high-contrast .bg-white, html.high-contrast .card { background: #080808 !important; color: #fff !important; border-color: #DC2626 !important; }
        html.high-contrast .text-slate-900, html.high-contrast .text-slate-800, html.high-contrast .text-slate-700, html.high-contrast .page-title { color: #fff !important; }
        html.high-contrast .text-slate-600, html.high-contrast .text-slate-500, html.high-contrast .text-slate-400 { color: #f1f5f9 !important; }
        html.high-contrast .bg-slate-50, html.high-contrast .bg-slate-100 { background: #121212 !important; border-color: #DC2626 !important; }
        html.high-contrast .topbar { background: #000 !important; }

        #sidebarDrawer { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        #sidebarOverlay { transition: opacity 0.25s ease; }

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
<body class="workspace h-full bg-paper text-slate-800 antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[60] focus:px-4 focus:py-2 focus:rounded-xl focus:bg-white focus:text-brand-dark focus:font-bold focus:text-xs focus:shadow-lg">Skip to main content</a>

    @php
        $user = auth()->user();
        $isOfficeUser = $user && $user->isOffice();
        $isClubUser = $user && ($user->isClubExecutive() || $user->isClubPresident());
        $isCampusLife = $user && $user->isCampusLifeSrc();
        $userName = $user ? $user->name : 'Chapel Guest';
        $userRole = $user ? $user->getRoleDisplayName() : 'Guest';
        $userInitial = mb_strtoupper(mb_substr($userName, 0, 1));
        $nameParts = \Illuminate\Support\Str::of($userName)->explode(' ');
        $shortName = trim($nameParts->first() . ' ' . ($nameParts->count() > 1 ? $nameParts->last() : ''));
        $activeAnnouncements = $user ? \App\Models\Announcement::orderBy('created_at', 'desc')->take(5)->get() : collect();

        $liturgical = \App\Services\LiturgicalCalendarService::getCurrentSeason();

        // Quick destinations for the command palette, mirroring what this role can see in the nave
        $quickLinks = $isCampusLife
            ? [['calendar.index', 'Master Calendar', 'Approved activities across campus'], ['guidelines.index', 'Policy & Guidelines', 'Chapel directives']]
            : array_filter([
                ['dashboard', 'Dashboard', 'Your overview'],
                ['requests.create', 'New proposal', 'Submit an activity for clearance'],
                ['requests.index', 'Proposals & Requests', 'Review and track proposals'],
                ['calendar.index', 'Master Calendar', 'Sanctuary & venue bookings'],
                ['worship.index', 'Call to Services', 'Orders of service & slides'],
                ['hymns.index', 'Methodist Hymnal', 'UMH · MHB · Shona'],
                ['vault.index', 'Records Vault', 'Constitutions, minutes & reports'],
                $isOfficeUser ? ['clubs.index', 'Fellowships Registry', 'Charters & executives'] : null,
                $isOfficeUser ? ['users.index', 'Staff & User Registry', 'Accounts & roles'] : null,
            ]);
    @endphp

    <div class="flex flex-col h-full overflow-hidden">
    {{-- The altar-cloth strip: today's liturgical colour runs across the top of the workspace --}}
    <div class="season-strip shrink-0" style="--season: {{ $liturgical['hex'] }}" aria-hidden="true"></div>

    <div class="flex flex-1 min-h-0 overflow-hidden">

        {{-- ═══════════════ SIDEBAR ═══════════════ --}}
        <div id="sidebarOverlay" class="hidden fixed inset-0 z-40 bg-ink/40 backdrop-blur-[2px] lg:hidden" onclick="closeSidebar()" aria-hidden="true"></div>

        <aside id="sidebarDrawer"
               class="sidebar-shell fixed lg:relative inset-y-0 left-0 z-50 flex flex-col w-[16.5rem] shrink-0 overflow-hidden"
               aria-label="Site navigation">

            {{-- Seal & name --}}
            <div class="flex items-center gap-3 px-5 h-16 shrink-0 border-b border-slate-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0">
                    <span class="relative w-9 h-9 shrink-0 rounded-xl grid place-items-center bg-brand shadow-xs">
                        <x-umc-logo class="w-[1.1rem] h-[1.1rem] transition-transform duration-500 group-hover:scale-110" />
                    </span>
                    <span class="min-w-0 leading-tight">
                        <span class="block font-display text-[16px] text-slate-900 leading-tight truncate" style="font-variation-settings: 'SOFT' 60, 'opsz' 30;">Kwang-Lim Chapel</span>
                        <span class="block text-[11.5px] font-medium text-slate-500 truncate">Africa University</span>
                    </span>
                </a>
                <button id="sidebarCloseBtn" onclick="closeSidebar()" class="ml-auto lg:hidden topbar-action -mr-2 shrink-0" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto pt-4 pb-4 px-3 space-y-5" aria-label="Primary" data-nave-scroll>
                <div class="space-y-0.5">
                    <x-sidebar-link route="dashboard" icon="home" label="Dashboard" :active="request()->routeIs('dashboard')" />
                    <x-sidebar-link route="inbox.index" icon="inbox" label="Inbox" :active="request()->routeIs('inbox.*')" :badge="$unreadInboxCount ?? 0" />
                    <x-sidebar-link route="calendar.index" icon="calendar" label="Master Calendar" :active="request()->routeIs('calendar.*')" />
                </div>

                @if(!$isCampusLife)
                <div>
                    <p class="sidebar-section-label">Ministry</p>
                    <div class="space-y-0.5">
                        <x-sidebar-link route="requests.index" icon="requests" label="Proposals & Requests" :active="request()->routeIs('requests.*')" />
                        <x-sidebar-link route="worship.index" icon="worship" label="Call to Services" :active="request()->routeIs('worship.*')" />
                        <x-sidebar-link route="equipment.index" icon="equipment" label="Sound & Equipment" :active="request()->routeIs('equipment.*')" />
                        <x-sidebar-link route="members.index" icon="members" label="Members & Attendance" :active="request()->routeIs('members.*')" />
                    </div>
                </div>

                <div>
                    <p class="sidebar-section-label">Resources</p>
                    <div class="space-y-0.5">
                        <x-sidebar-link route="hymns.index" icon="hymns" label="Methodist Hymnal" :active="request()->routeIs('hymns.*')" />
                        <x-sidebar-link route="vault.index" icon="vault" label="Records Vault" :active="request()->routeIs('vault.*')" />
                        <x-sidebar-link route="reports.index" icon="reports" label="Activity Reports" :active="request()->routeIs('reports.*')" />
                        <x-sidebar-link route="handover.index" icon="handover" label="Leadership Handover" :active="request()->routeIs('handover.*')" />
                        <x-sidebar-link route="guidelines.index" icon="guidelines" label="Policy & Guidelines" :active="request()->routeIs('guidelines.*')" />
                    </div>
                </div>

                @if($isOfficeUser)
                <div>
                    <p class="sidebar-section-label">Administration</p>
                    <div class="space-y-0.5">
                        <x-sidebar-link route="clubs.index" icon="clubs" label="Fellowships Registry" :active="request()->routeIs('clubs.*')" />
                        <x-sidebar-link route="users.index" icon="users" label="Staff & User Registry" :active="request()->routeIs('users.*')" />
                        <x-sidebar-link route="notifications.index" icon="notifications" label="Dispatch Centre" :active="request()->routeIs('notifications.*')" />
                        <x-sidebar-link route="audit.index" icon="audit" label="System Audit Log" :active="request()->routeIs('audit.*')" />
                    </div>
                </div>
                @endif

                @else
                <div>
                    <p class="sidebar-section-label">Resources</p>
                    <div class="space-y-0.5">
                        <x-sidebar-link route="guidelines.index" icon="guidelines" label="Policy & Guidelines" :active="request()->routeIs('guidelines.*')" />
                    </div>
                </div>
                @endif
            </nav>

            {{-- Season + account --}}
            <div class="p-3 space-y-1 border-t border-slate-100">
                <button type="button" onclick="showLiturgicalInfo()" class="w-full flex items-center gap-3 px-2.5 py-2 rounded-xl hover:bg-slate-100 transition text-left group" aria-label="About the current liturgical season: {{ $liturgical['name'] }}">
                    <span class="w-8 h-8 shrink-0" data-dial="mini" data-tone="paper"></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-[12.5px] font-semibold text-slate-800 leading-tight truncate">{{ $liturgical['name'] }}</span>
                        <span class="block text-[11.5px] text-slate-500 leading-tight truncate mt-0.5" data-dial-mini-meta>{{ $liturgical['color'] }}</span>
                    </span>
                </button>

                @auth
                <div class="relative">
                    <button id="personaButton" type="button" onclick="togglePersonaDropdown(event)" aria-haspopup="true" aria-expanded="false" aria-controls="personaDropdown"
                            class="w-full flex items-center gap-3 px-2.5 py-2 rounded-xl hover:bg-slate-100 transition text-left" aria-label="Account menu for {{ $userName }}">
                        <span class="w-8 h-8 rounded-full grid place-items-center text-[13px] font-bold text-brand-dark bg-brand-soft ring-1 ring-brand/15 shrink-0">{{ $userInitial }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[12.5px] font-semibold text-slate-900 truncate">{{ $shortName }}</span>
                            <span class="block text-[11.5px] text-slate-500 truncate">{{ $userRole }}</span>
                        </span>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                    </button>

                    <div id="personaDropdown" class="hidden absolute bottom-full left-0 right-0 mb-2 bg-white text-slate-800 rounded-2xl shadow-lg ring-1 ring-black/5 overflow-hidden z-50 text-[13px] modal-panel">
                        <div class="p-3.5 border-b border-slate-100">
                            <div class="font-semibold text-slate-900 truncate">{{ $userName }}</div>
                            <div class="text-[12px] text-slate-500 truncate">{{ $user->email }}</div>
                            <span class="badge badge-brand mt-2">{{ $userRole }}</span>
                        </div>
                        <div class="p-1.5 space-y-0.5">
                            <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 font-medium transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Profile &amp; Password</span>
                                @if($user->mustChangePassword())
                                    <span class="ml-auto w-2 h-2 rounded-full bg-brand shrink-0"></span>
                                @endif
                            </a>
                            <a href="{{ route('guidelines.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 font-medium transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Chapel Policy</span>
                            </a>
                            <button type="button" onclick="openAccessibilityModal()" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 font-medium transition cursor-pointer">
                                <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z"/></svg>
                                <span>Accessibility &amp; Language</span>
                            </button>
                        </div>
                        <div class="p-1.5 border-t border-slate-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-brand-soft text-brand font-semibold transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </aside>

        {{-- ═══════════════ MAIN ═══════════════ --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <header class="topbar sticky top-0 z-30 flex items-center gap-3 px-4 sm:px-6 lg:px-8 h-16 shrink-0">
                <button onclick="openSidebar()" class="lg:hidden topbar-action -ml-2" aria-label="Open navigation menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>

                <div class="min-w-0 flex items-center gap-2 text-[14px] lg:w-56 xl:w-64 shrink-0 lg:shrink">
                    <span class="topbar-title font-semibold text-slate-900 truncate">@yield('title', 'Dashboard')</span>
                </div>

                <button type="button" onclick="openCommandPalette()" class="topbar-search hidden md:flex mx-auto" aria-label="Search the portal (Ctrl+K)">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                    <span class="flex-1 text-left truncate">Search proposals, fellowships, hymns…</span>
                    <span class="kbd">Ctrl K</span>
                </button>

                <div class="ml-auto md:ml-0 flex items-center gap-1">
                    <span class="hidden xl:inline-flex items-center gap-2 mr-2 px-3 h-9 rounded-full bg-slate-100 text-[12.5px] font-semibold text-slate-600 whitespace-nowrap" title="{{ $liturgical['name'] }}">
                        <span class="w-2 h-2 rounded-full" style="background: {{ $liturgical['hex'] }}"></span>
                        {{ now()->format('D, j M') }}
                    </span>

                    <button type="button" onclick="openCommandPalette()" class="topbar-action md:hidden" title="Quick Search (Ctrl+K)" aria-label="Quick search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                    </button>

                    <a href="{{ route('inbox.index') }}" class="topbar-action" title="Inbox" aria-label="Inbox{{ ($unreadInboxCount ?? 0) ? ' (' . $unreadInboxCount . ' unread)' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        @if($unreadInboxCount ?? 0)
                            <span class="absolute top-1 right-1 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-brand text-white text-[10px] font-bold leading-[1.1rem] text-center ring-2 ring-white">{{ ($unreadInboxCount > 9) ? '9+' : $unreadInboxCount }}</span>
                        @endif
                    </a>

                    <div class="relative">
                        <button id="notificationButton" type="button" onclick="toggleNotificationDrawer(event)" aria-haspopup="true" aria-expanded="false" class="topbar-action" aria-label="Chapel notices ({{ $activeAnnouncements->count() }})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if($activeAnnouncements->isNotEmpty())
                                <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-brand ring-2 ring-white"></span>
                            @endif
                        </button>
                        <div id="notificationDrawer" class="hidden absolute right-0 mt-2 w-[22rem] max-w-[calc(100vw-2rem)] bg-white text-slate-800 rounded-2xl shadow-lg ring-1 ring-black/5 overflow-hidden z-50 text-[13px] modal-panel">
                            <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                                <span class="font-bold text-[14px] text-slate-900">Chapel notices</span>
                                <span class="badge">Latest {{ $activeAnnouncements->count() }}</span>
                            </div>
                            <div class="p-1.5 max-h-80 overflow-y-auto">
                                @forelse($activeAnnouncements as $ann)
                                    <a href="{{ $ann->is_guideline ? route('guidelines.index') : route('calendar.index') }}" class="block px-3 py-2.5 hover:bg-slate-50 rounded-xl transition group">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="font-semibold text-slate-900 group-hover:text-brand transition truncate">{{ $ann->title }}</div>
                                            @if($ann->is_pinned)
                                                <span class="badge badge-brand shrink-0">Policy</span>
                                            @endif
                                        </div>
                                        <p class="text-[12.5px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ Str::limit($ann->message, 95) }}</p>
                                    </a>
                                @empty
                                    <div class="empty-state py-8">
                                        <p class="empty-state-text">No active notices.</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="px-4 py-2.5 border-t border-slate-100 flex items-center justify-between bg-slate-50">
                                @if($isOfficeUser)
                                    <a href="{{ route('notifications.index') }}" class="card-link">Dispatch Centre</a>
                                @else
                                    <a href="{{ route('guidelines.index') }}" class="card-link">View guidelines</a>
                                @endif
                                <button type="button" onclick="document.getElementById('notificationDrawer').classList.add('hidden')" class="text-[12px] font-semibold text-slate-500 hover:text-slate-800 px-2 py-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">Close</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="openAccessibilityModal()" class="topbar-action hidden sm:inline-flex" aria-label="Accessibility preferences">
                        <svg class="w-[1.15rem] h-[1.15rem]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z"/></svg>
                    </button>

                    <a href="{{ route('profile.index') }}" class="lg:hidden ml-1 w-9 h-9 rounded-full grid place-items-center text-[13px] font-bold text-brand-dark bg-brand-soft ring-1 ring-brand/15 shrink-0" aria-label="Your profile">{{ $userInitial }}</a>
                </div>
            </header>

            <main id="main-content" tabindex="-1" class="flex-1 overflow-y-auto focus:outline-none pb-16">
                @if(session('success') || session('warning') || session('error') || session('info') || ($errors->any() && !request()->routeIs('clubs.register', 'profile.index')))
                <div class="max-w-[84rem] mx-auto px-4 sm:px-6 lg:px-10 pt-6 space-y-2" data-no-reveal>
                    @if(session('success'))
                        <div class="flash-success" role="alert">
                            <svg class="w-4 h-4 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-[13px] font-medium">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="flash-warning" role="alert">
                            <svg class="w-4 h-4 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="text-[13px] font-medium">{{ session('warning') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="flash-error" role="alert">
                            <svg class="w-4 h-4 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-[13px] font-medium">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="flash-info" role="alert">
                            <svg class="w-4 h-4 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-[13px] font-medium">{{ session('info') }}</span>
                        </div>
                    @endif
                    @if($errors->any() && !request()->routeIs('clubs.register', 'profile.index'))
                        <div class="flash-error" role="alert">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="text-[13px]">
                                <p class="font-semibold">Please correct {{ $errors->count() === 1 ? 'this issue' : 'these issues' }}:</p>
                                <ul class="list-disc pl-4 mt-1 space-y-0.5">
                                    @foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    </div>

    {{-- ═══════════════ GLOBAL MODALS ═══════════════ --}}

    {{-- Command palette --}}
    <div id="commandPaletteModal" role="dialog" aria-modal="true" aria-label="Quick search" class="hidden fixed inset-0 z-50 flex items-start justify-center pt-[12vh] bg-ink/50 backdrop-blur-sm p-4 modal-backdrop" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-lg max-w-xl w-full ring-1 ring-black/5 overflow-hidden modal-panel" onclick="event.stopPropagation()">
            <div class="px-4 py-3.5 border-b border-slate-100 flex items-center gap-3">
                <svg class="w-[1.1rem] h-[1.1rem] text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                <input type="search" id="commandSearchInput" aria-label="Search the portal" autocomplete="off" placeholder="Search proposals, fellowships, events, hymns…" oninput="handleCommandSearch(this.value)" onkeydown="paletteKeys(event)" class="flex-1 bg-transparent text-[15px] text-slate-900 placeholder-slate-400 focus:outline-none !shadow-none !border-0">
                <button type="button" onclick="document.getElementById('commandPaletteModal').classList.add('hidden')" aria-label="Close" class="kbd kbd-light cursor-pointer">Esc</button>
            </div>
            <div id="commandSearchResults" aria-live="polite" class="p-2 max-h-[22rem] overflow-y-auto text-xs"></div>
            <template id="paletteQuickLinks">
                <p class="px-3 pt-2 pb-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Jump to</p>
                @foreach($quickLinks as [$r, $label, $hint])
                    <a href="{{ route($r) }}" class="palette-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 focus:bg-slate-50 focus:outline-none transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold/70 shrink-0"></span>
                        <span class="font-semibold text-[13px] text-slate-900">{{ $label }}</span>
                        <span class="text-slate-400 text-[11.5px] truncate">{{ $hint }}</span>
                    </a>
                @endforeach
            </template>
            <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50/70 flex items-center gap-4 text-[11px] text-slate-400">
                <span class="inline-flex items-center gap-1.5"><span class="kbd kbd-light">↑</span><span class="kbd kbd-light">↓</span> to move</span>
                <span class="inline-flex items-center gap-1.5"><span class="kbd kbd-light">Enter</span> to open</span>
            </div>
        </div>
    </div>

    {{-- Accessibility --}}
    <div id="accessibilityModal" role="dialog" aria-modal="true" aria-labelledby="a11yModalTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-3xl shadow-lg max-w-md w-full ring-1 ring-black/5 overflow-hidden modal-panel" onclick="event.stopPropagation()">
            <div class="px-6 pt-6 pb-4 flex items-start justify-between">
                <div>
                    <p class="eyebrow">Preferences</p>
                    <h3 id="a11yModalTitle" class="font-display text-xl text-slate-900">Accessibility &amp; language</h3>
                    <p class="text-xs text-slate-500 mt-1">Zvirongwa zvekuwanikwa nemutauro</p>
                </div>
                <button type="button" onclick="document.getElementById('accessibilityModal').classList.add('hidden')" class="topbar-action -mr-2 -mt-1" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 pb-6 space-y-3">
                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 ring-1 ring-slate-200/70">
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">High contrast</div>
                        <div class="text-[11.5px] text-slate-500">Pure black with high-contrast crimson</div>
                    </div>
                    <button type="button" id="a11yHighContrastBtn" aria-pressed="false" onclick="toggleHighContrast()" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer">Off</button>
                </div>
                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 ring-1 ring-slate-200/70">
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">Larger text</div>
                        <div class="text-[11.5px] text-slate-500">Scale reading size by 12%</div>
                    </div>
                    <button type="button" id="a11yLargeTextBtn" aria-pressed="false" onclick="toggleLargeText()" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300 cursor-pointer">Off</button>
                </div>
                <div class="pt-1">
                    <p class="field-label">Display language · Mutauro</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" id="langBtnEn" onclick="setLanguage('en')" class="py-2 px-3 rounded-xl text-xs font-bold border border-brand bg-brand text-white cursor-pointer">English</button>
                        <button type="button" id="langBtnSn" onclick="setLanguage('sn')" class="py-2 px-3 rounded-xl text-xs font-bold border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 cursor-pointer">ChiShona</button>
                    </div>
                </div>
                <div class="pt-4 mt-1 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" onclick="resetAccessibility()" class="btn btn-ghost btn-sm">Reset</button>
                    <button type="button" onclick="document.getElementById('accessibilityModal').classList.add('hidden')" class="btn btn-ink btn-sm">Done</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Liturgical season --}}
    <div id="liturgicalModal" role="dialog" aria-modal="true" aria-labelledby="liturgicalTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-3xl shadow-lg max-w-md w-full ring-1 ring-black/5 overflow-hidden modal-panel">
            <div class="px-7 pt-7 pb-6 text-white relative overflow-hidden grain" style="background: linear-gradient(160deg, {{ $liturgical['hex'] }}, #15121A 120%)">
                <div class="relative">
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-white/70 mb-2">The Christian year · now</p>
                    <h3 id="liturgicalTitle" class="text-[1.75rem] leading-tight font-display">{{ $liturgical['name'] }}</h3>
                    <p class="text-sm mt-1 text-white/75">{{ $liturgical['color'] }}</p>
                </div>
            </div>
            <div class="p-7 space-y-4">
                <blockquote class="font-display italic text-[17px] leading-snug text-slate-800">{{ $liturgical['verse'] }}</blockquote>
                <div class="rule-ornament text-[10px]">✦</div>
                <p class="text-[13px] text-slate-600 leading-relaxed">{{ $liturgical['parament'] }}</p>
                <p class="text-[13px] text-slate-800 font-medium leading-relaxed">{{ $liturgical['focus'] }}</p>
                <div class="pt-2 text-right">
                    <button onclick="document.getElementById('liturgicalModal').classList.add('hidden')" class="btn btn-ink btn-sm">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.__chapelLayoutUi = true;

        // Nave drawer (mobile)
        function openSidebar() {
            document.getElementById('sidebarDrawer').classList.add('open');
            document.getElementById('sidebarOverlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.getElementById('sidebarCloseBtn')?.focus();
        }
        function closeSidebar() {
            document.getElementById('sidebarDrawer').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function showLiturgicalInfo() {
            document.getElementById('liturgicalModal').classList.remove('hidden');
        }

        // Command palette
        let lastFocusBeforeModal = null;
        function showQuickLinks() {
            const container = document.getElementById('commandSearchResults');
            container.replaceChildren(document.getElementById('paletteQuickLinks').content.cloneNode(true));
        }
        function openCommandPalette() {
            const modal = document.getElementById('commandPaletteModal');
            lastFocusBeforeModal = document.activeElement;
            modal.classList.remove('hidden');
            const input = document.getElementById('commandSearchInput');
            input.value = '';
            showQuickLinks();
            setTimeout(() => input.focus(), 30);
        }
        function paletteKeys(e) {
            const items = [...document.querySelectorAll('#commandSearchResults a')];
            if (!items.length) return;
            if (e.key === 'ArrowDown') { e.preventDefault(); items[0].focus(); }
            if (e.key === 'Enter') { e.preventDefault(); items[0].click(); }
        }
        document.getElementById('commandSearchResults').addEventListener('keydown', function (e) {
            const items = [...this.querySelectorAll('a')];
            const i = items.indexOf(document.activeElement);
            if (e.key === 'ArrowDown') { e.preventDefault(); (items[i + 1] || items[0]).focus(); }
            if (e.key === 'ArrowUp') { e.preventDefault(); i <= 0 ? document.getElementById('commandSearchInput').focus() : items[i - 1].focus(); }
        });

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

        window.addEventListener('keydown', function (e) {
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
                const drawer = document.getElementById('sidebarDrawer');
                if (drawer && drawer.classList.contains('open')) closeSidebar();
                if (restoreTo && typeof restoreTo.focus === 'function') restoreTo.focus();
            }
        });

        ['click', 'focusin'].forEach(type => document.addEventListener(type, function (e) {
            chapelDropdowns.forEach(d => {
                const panel = document.getElementById(d[0]);
                const btn = document.getElementById(d[1]);
                if (panel && !panel.classList.contains('hidden') && !panel.contains(e.target) && !(btn && btn.contains(e.target))) {
                    setDropdown(d[0], false);
                }
            });
        }));

        // Form helpers: confirm prompts and double-submit lock
        document.addEventListener('submit', function (e) {
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
        window.addEventListener('pageshow', function (e) {
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
            a.className = 'block px-3 py-2.5 rounded-xl hover:bg-slate-50 focus:bg-slate-50 focus:outline-none transition';
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2';
            const title = document.createElement('div');
            title.className = 'font-semibold text-[13px] text-slate-900 truncate';
            title.textContent = item.title || '';
            const cat = document.createElement('span');
            cat.className = 'badge badge-brand shrink-0';
            cat.textContent = item.category || '';
            row.append(title, cat);
            const sub = document.createElement('div');
            sub.className = 'text-[11.5px] text-slate-500 mt-0.5 truncate';
            sub.textContent = item.subtitle || '';
            a.append(row, sub);
            return a;
        }
        let searchTimeout = null, searchSeq = 0;
        function handleCommandSearch(val) {
            clearTimeout(searchTimeout);
            const container = document.getElementById('commandSearchResults');
            if (!val || val.length < 2) { showQuickLinks(); return; }
            const seq = ++searchSeq;
            searchTimeout = setTimeout(() => {
                fetch('/api/search?q=' + encodeURIComponent(val), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
                    .then(data => {
                        if (seq !== searchSeq) return;
                        container.replaceChildren();
                        if (!data.results || !data.results.length) {
                            container.innerHTML = '<div class="empty-state py-8"><p class="empty-state-title">Nothing found</p><p class="empty-state-text">Try a reference number, a fellowship or a hymn title.</p></div>';
                            return;
                        }
                        data.results.forEach(item => container.appendChild(buildSearchResult(item)));
                    })
                    .catch(() => {
                        if (seq !== searchSeq) return;
                        container.innerHTML = '<div class="text-rose-700 text-center py-6 text-xs">Search is unavailable. Please try again.</div>';
                    });
            }, 220);
        }

        // Accessibility
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
            const offCls = 'px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 transition bg-white text-slate-700 hover:bg-slate-100 cursor-pointer';
            const hcBtn = document.getElementById('a11yHighContrastBtn');
            if (hcBtn) { hcBtn.textContent = isHigh ? 'On' : 'Off'; hcBtn.className = isHigh ? onCls : offCls; hcBtn.setAttribute('aria-pressed', isHigh ? 'true' : 'false'); }
            const ltBtn = document.getElementById('a11yLargeTextBtn');
            if (ltBtn) { ltBtn.textContent = isLarge ? 'On' : 'Off'; ltBtn.className = isLarge ? onCls : offCls; ltBtn.setAttribute('aria-pressed', isLarge ? 'true' : 'false'); }
            const btnEn = document.getElementById('langBtnEn');
            const btnSn = document.getElementById('langBtnSn');
            if (btnEn && btnSn) {
                const activeCls = 'py-2 px-3 rounded-xl text-xs font-bold border border-brand bg-brand text-white cursor-pointer';
                const idleCls = 'py-2 px-3 rounded-xl text-xs font-bold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 cursor-pointer';
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

        (function () {
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
