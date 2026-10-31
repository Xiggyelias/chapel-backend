@extends('layouts.app')

@section('title', 'Chaplaincy Notification & Dispatch Center')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 animate-fade-in">

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0B0F17] via-[#1A0B12] to-[#DC2626] p-8 text-white shadow-2xl border-t-2 border-[#DC2626]">
        <div class="absolute -right-12 -bottom-16 w-80 h-80 rounded-full bg-[#DC2626]/20 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold tracking-widest text-rose-200 uppercase mb-2">
                    <span>Africa University Chaplaincy</span>
                    <span>&bull;</span>
                    <span>Automated Dispatch Engine</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black font-cinzel tracking-tight text-white">
                    Chaplaincy Notification & Dispatch Center
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
                    Real-time monitoring of automated policy alerts, 14-day advance notice triggers, 3-day preacher sermon reminders, 2-day media tech notices, and gatekeeper security passes.
                </p>
            </div>
            @if(auth()->user()->isOffice())
                <div class="flex items-center space-x-3 shrink-0">
                    <form method="POST" action="{{ route('notifications.trigger_reminders') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center space-x-2 px-5 py-3 rounded-2xl bg-white hover:bg-slate-100 text-[#DC2626] text-xs font-black shadow-lg hover:shadow-xl transition transform active:scale-95 border border-white/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Trigger Daily Policy Checks</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Active Channel Status Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Chaplaincy Email (SMTP)</span>
                <span class="text-xs font-bold text-slate-900">chaplaincy@africau.edu</span>
                <span class="text-[10px] text-emerald-600 font-semibold block">Connected &bull; Active</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.86-1.537c1.7 1.011 3.684 1.595 5.803 1.595 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">WhatsApp Dispatcher</span>
                <span class="text-xs font-bold text-slate-900">1-Click Direct URL</span>
                <span class="text-[10px] text-green-600 font-semibold block">Zero Cost &bull; Instant</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">PWA Push & Bells</span>
                <span class="text-xs font-bold text-slate-900">Service Worker v1.2</span>
                <span class="text-[10px] text-purple-600 font-semibold block">Browser Telemetry Active</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Calendar iCal (.ics)</span>
                <span class="text-xs font-bold text-slate-900">RFC 5545 Sync</span>
                <span class="text-[10px] text-blue-600 font-semibold block">Google / Outlook Feed</span>
            </div>
        </div>
    </div>

    <!-- Telemetry Sections Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1: 14-Day Advance Rule Urgent Alerts -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-pulse"></span>
                    <h2 class="text-xs font-bold font-cinzel text-slate-900 uppercase tracking-wider">14-Day Advance Alerts</h2>
                </div>
                <span class="text-[10px] bg-rose-50 text-rose-700 px-2 py-0.5 rounded-full font-bold font-mono">{{ $urgentRequests->count() }} Pending</span>
            </div>

            <div class="space-y-3">
                @forelse($urgentRequests as $req)
                    @php
                        $daysLeft = Carbon\Carbon::today()->diffInDays($req->event_date, false);
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5 hover:border-[#DC2626] transition">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-bold text-slate-900 truncate">{{ $req->title }}</span>
                            <span class="font-mono text-[10px] font-bold {{ $daysLeft < 14 ? 'text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded' : 'text-slate-500' }}">
                                {{ $daysLeft }}d left
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 flex items-center justify-between">
                            <span>{{ $req->club?->name }}</span>
                            <span class="font-mono">{{ $req->event_date->format('M d') }}</span>
                        </div>
                        <div class="pt-1 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-mono">Ref: {{ $req->reference_no }}</span>
                            <a href="{{ route('requests.show', $req->id) }}" class="text-[10px] font-bold text-[#DC2626] hover:underline">
                                Review Request &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs italic">
                        No pending requests with urgent 14-day rule constraints.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column 2: Worship Preparation Deadlines -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h2 class="text-xs font-bold font-cinzel text-slate-900 uppercase tracking-wider">Liturgical Deadlines</h2>
                </div>
                <span class="text-[10px] bg-amber-50 text-amber-800 px-2 py-0.5 rounded-full font-bold font-mono">Upcoming Services</span>
            </div>

            <div class="space-y-3">
                @forelse($upcomingServices as $service)
                    @php
                        $daysToService = Carbon\Carbon::today()->diffInDays($service->service_date, false);
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 hover:border-[#DC2626] transition">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-900">{{ $service->service_type }}</span>
                            <span class="font-mono text-[10px] text-slate-500">{{ $service->service_date->format('l, M d') }}</span>
                        </div>
                        <div class="text-[11px] text-slate-600 truncate">
                            Theme: "{{ $service->theme ?: 'Worship Assembly' }}"
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 pt-1 text-[10px]">
                            <div class="p-1.5 rounded-lg border {{ $service->preacher_submitted_materials ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                                Preacher Notes: {{ $service->preacher_submitted_materials ? 'Submitted' : 'Pending (3d)' }}
                            </div>
                            <div class="p-1.5 rounded-lg border {{ $service->media_notified ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-100 border-slate-200 text-slate-600' }}">
                                Media Desk: {{ $service->media_notified ? 'Notified' : 'Pending (2d)' }}
                            </div>
                        </div>
                        <div class="pt-1 flex items-center justify-between text-[11px]">
                            <a href="{{ route('worship.slides', $service->id) }}" target="_blank" class="text-[10px] font-bold text-slate-600 hover:text-slate-900">
                                Projector Slides
                            </a>
                            <a href="{{ route('worship.show', $service->id) }}" class="text-[10px] font-bold text-[#DC2626] hover:underline">
                                Manage Service &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs italic">
                        No upcoming worship service sheets scheduled.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column 3: Outstanding Post-Activity Reports (§7.1.2) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <h2 class="text-xs font-bold font-cinzel text-slate-900 uppercase tracking-wider">§7.1.2 Report Filings</h2>
                </div>
                <span class="text-[10px] bg-indigo-50 text-indigo-800 px-2 py-0.5 rounded-full font-bold font-mono">Post-Event</span>
            </div>

            <div class="space-y-3">
                @forelse($pendingReports as $ev)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5 hover:border-indigo-500 transition">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-900">
                            <span class="truncate">{{ $ev->title }}</span>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $ev->event_date->format('M d') }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Sponsoring: <span class="font-semibold text-slate-800">{{ $ev->club?->name }}</span>
                        </div>
                        <div class="pt-1 flex items-center justify-between text-[10px]">
                            <span class="text-rose-600 font-semibold">Report Due Within 7 Days</span>
                            <a href="{{ route('reports.create', ['event_id' => $ev->id]) }}" class="font-bold text-[#DC2626] hover:underline">
                                File Report &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs italic">
                        All recent fellowship activities have filed reports in compliance with §7.1.2.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Recent Dispatched Notifications Stream -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/60">
            <div>
                <h3 class="text-xs font-bold text-[#DC2626] uppercase tracking-wider font-cinzel">
                    Automated System Dispatch Ledger
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Cryptographic log of all simulated emails, SMS alerts, and clearance dispatches.</p>
            </div>
            <span class="text-xs font-mono text-slate-400">Ledger Active</span>
        </div>

        <div class="divide-y divide-slate-100 text-xs">
            @forelse($recentDispatches as $log)
                <div class="p-4 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-mono text-xs font-bold shrink-0 mt-0.5">
                            ⚡
                        </div>
                        <div class="space-y-0.5">
                            <div class="font-bold text-slate-900">
                                {{ $log->user_name ?: ($log->user?->name ?: 'Automated Sentinel') }}
                                <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                    {{ $log->action }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-600 max-w-2xl leading-relaxed">
                                {{ $log->notes ?: 'Automated policy notification processed.' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right text-[10px] text-slate-400 font-mono shrink-0">
                        <div>{{ $log->created_at->timezone('Africa/Harare')->format('Y-m-d H:i:s') }} CAT</div>
                        <div>IP: {{ $log->ip_address ?: '127.0.0.1' }}</div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs italic">
                    No dispatches logged in the sentinel database yet.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
