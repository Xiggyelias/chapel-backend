@extends('layouts.app')

@section('title', 'Chaplaincy Notification & Dispatch Center')

@section('content')
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Africa University Chaplaincy · Automated Dispatch Engine" title="Chaplaincy Notification & Dispatch Center"
                   description="Real-time monitoring of automated policy alerts, 14-day advance notice triggers, 3-day preacher sermon reminders, 2-day media tech notices, and gatekeeper security passes.">
        @if(auth()->user()->isOffice())
            <form method="POST" action="{{ route('notifications.trigger_reminders') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Trigger Daily Policy Checks
                </button>
            </form>
        @endif
    </x-page-header>

    {{-- Active channels --}}
    <section aria-label="Dispatch channels" class="card overflow-hidden grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-px !bg-slate-100">
        @php
            $channels = [
                ['Chaplaincy Email (SMTP)', 'chaplaincy@africau.edu', 'Connected &bull; Active', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
                ['WhatsApp Dispatcher', '1-Click Direct URL', 'Zero Cost &bull; Instant', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>'],
                ['PWA Push & Bells', 'Service Worker v1.2', 'Browser Telemetry Active', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>'],
                ['Phone & Calendar Sync', 'Google / Apple / Outlook', 'Live Schedule Updates', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
            ];
        @endphp
        @foreach($channels as [$label, $value, $status, $icon])
            <div class="bg-white p-5 flex items-start gap-3.5">
                <span class="w-10 h-10 rounded-xl grid place-items-center shrink-0 text-slate-600 bg-slate-50 ring-1 ring-slate-200/70">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $icon !!}</svg>
                </span>
                <div class="min-w-0">
                    <span class="card-kicker block">{{ $label }}</span>
                    <span class="block text-[13px] font-semibold text-slate-900 mt-0.5 truncate">{{ $value }}</span>
                    <span class="mt-1 inline-flex items-center gap-1.5 text-[12.5px] font-medium text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>{!! $status !!}
                    </span>
                </div>
            </div>
        @endforeach
    </section>

    {{-- Telemetry columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- 14-day advance rule alerts --}}
        <section class="card">
            <div class="card-header">
                <div class="min-w-0">
                    <p class="card-kicker">Proposals</p>
                    <h2 class="card-title mt-0.5">14-Day Advance Alerts</h2>
                </div>
                <span class="badge badge-rose badge-dot tabular-nums shrink-0">{{ $urgentRequests->count() }} Pending</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($urgentRequests as $req)
                    @php
                        $daysLeft = Carbon\Carbon::today()->diffInDays($req->event_date, false);
                    @endphp
                    <div class="px-5 py-4 space-y-1.5 hover:bg-slate-50/60 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-[13px] font-semibold text-slate-900 truncate">{{ $req->title }}</span>
                            <span class="shrink-0 tabular-nums text-[12px] font-semibold {{ $daysLeft < 14 ? 'badge badge-rose' : 'text-slate-500' }}">
                                {{ $daysLeft }}d left
                            </span>
                        </div>
                        <div class="text-[12px] text-slate-500 flex items-center justify-between gap-3">
                            <span class="truncate">{{ $req->club?->name }}</span>
                            <span class="tabular-nums shrink-0">{{ $req->event_date->format('M d') }}</span>
                        </div>
                        <div class="pt-0.5 flex items-center justify-between gap-3">
                            <span class="text-[12px] text-slate-400 font-mono">Ref: {{ $req->reference_no }}</span>
                            <a href="{{ route('requests.show', $req->id) }}" class="card-link">Review Request</a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state !py-10">
                        <div class="empty-state-mark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </div>
                        <p class="empty-state-text">No pending requests with urgent 14-day rule constraints.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Worship preparation deadlines --}}
        <section class="card">
            <div class="card-header">
                <div class="min-w-0">
                    <p class="card-kicker">Worship</p>
                    <h2 class="card-title mt-0.5">Liturgical Deadlines</h2>
                </div>
                <span class="badge badge-gold shrink-0">Upcoming Services</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($upcomingServices as $service)
                    @php
                        $daysToService = Carbon\Carbon::today()->diffInDays($service->service_date, false);
                    @endphp
                    <div class="px-5 py-4 space-y-2 hover:bg-slate-50/60 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-[13px] font-semibold text-slate-900">{{ $service->service_type }}</span>
                            <span class="text-[12.5px] text-slate-500 shrink-0 tabular-nums">{{ $service->service_date->format('l, M d') }}</span>
                        </div>
                        <div class="font-display italic text-[13px] text-slate-600 truncate">
                            Theme: "{{ $service->theme ?: 'Worship Assembly' }}"
                        </div>
                        <div class="flex flex-wrap gap-1.5 pt-0.5">
                            <span class="badge {{ $service->preacher_submitted_materials ? 'badge-emerald' : 'badge-amber' }}">
                                Preacher Notes: {{ $service->preacher_submitted_materials ? 'Submitted' : 'Pending (3d)' }}
                            </span>
                            <span class="badge {{ $service->media_notified ? 'badge-emerald' : '' }}">
                                Media Desk: {{ $service->media_notified ? 'Notified' : 'Pending (2d)' }}
                            </span>
                        </div>
                        <div class="pt-0.5 flex items-center justify-between gap-3 text-[12px]">
                            <a href="{{ route('worship.slides', $service->id) }}" target="_blank" class="font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                                Projector Slides
                            </a>
                            <a href="{{ route('worship.show', $service->id) }}" class="card-link">Manage Service</a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state !py-10">
                        <div class="empty-state-mark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <p class="empty-state-text">No upcoming worship service sheets scheduled.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Outstanding post-activity reports (§7.1.2) --}}
        <section class="card">
            <div class="card-header">
                <div class="min-w-0">
                    <p class="card-kicker">Accountability</p>
                    <h2 class="card-title mt-0.5">§7.1.2 Report Filings</h2>
                </div>
                <span class="badge shrink-0">Post-Event</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($pendingReports as $ev)
                    <div class="px-5 py-4 space-y-1.5 hover:bg-slate-50/60 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-[13px] font-semibold text-slate-900 truncate">{{ $ev->title }}</span>
                            <span class="text-[12.5px] text-slate-500 tabular-nums shrink-0">{{ $ev->event_date->format('M d') }}</span>
                        </div>
                        <div class="text-[12px] text-slate-500">
                            Sponsoring: <span class="font-semibold text-slate-800">{{ $ev->club?->name }}</span>
                        </div>
                        <div class="pt-0.5 flex items-center justify-between gap-3 text-[12.5px]">
                            <span class="text-rose-700 font-semibold">Report Due Within 7 Days</span>
                            <a href="{{ route('reports.create', ['event_id' => $ev->id]) }}" class="card-link">File Report</a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state !py-10">
                        <div class="empty-state-mark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="empty-state-text">All recent fellowship activities have filed reports in compliance with §7.1.2.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>

    {{-- Recent dispatched notifications --}}
    <section class="card overflow-hidden">
        <div class="card-header">
            <div class="min-w-0">
                <p class="card-kicker">Ledger</p>
                <h3 class="card-title mt-0.5">Automated System Dispatch Ledger</h3>
                <p class="text-[12px] text-slate-500 mt-0.5">Cryptographic log of all simulated emails, SMS alerts, and clearance dispatches.</p>
            </div>
            <span class="badge badge-emerald badge-dot shrink-0">Ledger Active</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($recentDispatches as $log)
                <div class="px-5 py-4 hover:bg-slate-50/60 transition-colors flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex items-start gap-3.5 min-w-0">
                        <span class="w-8 h-8 rounded-full grid place-items-center shrink-0 text-gold"
                              style="background: radial-gradient(circle at 50% 35%, #fff, var(--color-gold-soft)); box-shadow: inset 0 0 0 1px rgb(184 137 43 / .25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </span>
                        <div class="space-y-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[13px] font-semibold text-slate-900">{{ $log->user_name ?: ($log->user?->name ?: 'Automated Sentinel') }}</span>
                                <span class="badge">{{ $log->action }}</span>
                            </div>
                            <p class="text-[12.5px] text-slate-600 max-w-2xl leading-relaxed">
                                {{ $log->notes ?: 'Automated policy notification processed.' }}
                            </p>
                        </div>
                    </div>
                    <div class="sm:text-right text-[12px] text-slate-400 font-mono shrink-0 pl-[2.875rem] sm:pl-0">
                        <div>{{ $log->created_at->timezone('Africa/Harare')->format('Y-m-d H:i:s') }} CAT</div>
                        <div>IP: {{ $log->ip_address ?: '127.0.0.1' }}</div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state-mark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <p class="empty-state-text">No dispatches logged in the sentinel database yet.</p>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection
