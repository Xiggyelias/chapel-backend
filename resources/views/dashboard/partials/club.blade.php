{{-- Club President / Executive: run the fellowship, propose activities, report back, keep the vault complete --}}
@php $club = $dash['club']; @endphp

@if(!$club)
    <div class="flash-warning" role="status">
        <svg class="w-5 h-5 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3.75m0 3.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-[13px] leading-relaxed">Your account is not linked to a fellowship yet. Please ask the Chapel Secretary to assign you to your club.</p>
    </div>
@else
<div class="space-y-6">
    {{-- Fellowship identity --}}
    <section class="card p-4 sm:px-5 flex flex-col md:flex-row md:items-center gap-4">
        <div class="flex items-center gap-3.5 min-w-0 flex-1">
            <div class="relative w-12 h-12 rounded-full grid place-items-center shrink-0 bg-white font-display text-[11px] tracking-wide text-brand"
                 style="box-shadow: inset 0 0 0 1px rgb(165 28 48 / .18), 0 0 0 3px #fff, 0 0 0 4px rgb(184 137 43 / .45), 0 6px 14px -8px rgb(165 28 48 / .5);" aria-hidden="true">
                {{ \Illuminate\Support\Str::limit($club->code, 4, '') }}
            </div>
            <div class="min-w-0">
                <p class="card-kicker">Your fellowship</p>
                <p class="font-display text-[1.15rem] leading-tight text-slate-900 truncate">{{ $club->name }}</p>
                <p class="mt-0.5 text-[12px] text-slate-500 truncate">{{ $club->meeting_schedule ?: 'Meeting schedule not set' }} · {{ $club->venue ?: 'Venue not set' }}</p>
            </div>
        </div>
        <nav class="flex flex-wrap gap-2" aria-label="Club quick actions">
            <a href="{{ route('members.qr_kiosk') }}" class="btn btn-secondary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h2m4 0v2m-4 4h4m-6-2v2"/></svg>
                Open check-in kiosk
            </a>
            <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Take attendance
            </a>
            <a href="{{ route('vault.index') }}" class="btn btn-secondary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload record
            </a>
            <a href="{{ route('equipment.index') }}" class="btn btn-secondary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Borrow equipment
            </a>
        </nav>
    </section>

    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" title="Your fellowship's to-do list" />

            <x-dash.panel title="Our proposals" :link="route('requests.index')">
                @php
                    $badges = [
                        'submitted' => 'badge-sky',
                        'under_review' => 'badge-gold',
                        'needs_changes' => 'badge-amber',
                        'approved' => 'badge-emerald',
                        'declined' => 'badge-rose',
                    ];
                @endphp
                @forelse($dash['myRequests'] as $r)
                    <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0 border-b last:border-0 border-slate-100">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('requests.show', $r->id) }}" class="text-[13.5px] font-semibold text-slate-900 hover:text-brand truncate block transition-colors">{{ $r->title }}</a>
                            <p class="mt-0.5 text-[12px] text-slate-500">{{ $r->event_date->format('D, M j') }} · {{ $r->venue?->name ?? $r->venue_other }}</p>
                        </div>
                        <span class="shrink-0 badge badge-dot {{ $badges[$r->status] ?? '' }}">{{ ucwords(str_replace('_', ' ', $r->status)) }}</span>
                    </div>
                @empty
                    <p class="text-[13px] text-slate-500">You haven't submitted any proposals yet. Activities need 14 days' notice.</p>
                @endforelse
                <a href="{{ route('requests.create') }}" class="btn btn-primary btn-sm mt-5">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New proposal
                </a>
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Coming up for us" :link="route('calendar.index', ['club_id' => $club->id])">
                @forelse($dash['upcoming'] as $event)
                    <x-dash.event-row :event="$event" />
                @empty
                    <p class="text-[13px] text-slate-500">No approved activities scheduled yet.</p>
                @endforelse
            </x-dash.panel>

            @php $health = $dash['health']; @endphp
            <x-dash.panel title="Records vault health" :link="route('vault.index')">
                <div class="flex items-end justify-between gap-4">
                    <div class="flex items-baseline gap-0.5" style="color: {{ $health['color'] }}">
                        <span class="stat-value text-[2.6rem]">{{ $health['score'] }}</span><span class="font-display text-[1.3rem] opacity-70">%</span>
                    </div>
                    <span class="mb-1 text-[12px] font-semibold text-slate-600 text-right">{{ $health['grade'] }}</span>
                </div>
                <div class="mt-3 progress" role="progressbar" aria-valuenow="{{ $health['score'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Records vault health">
                    <span style="width: {{ $health['score'] }}%; background: {{ $health['color'] }}"></span>
                </div>
                <ul class="mt-4 space-y-1.5 text-[12.5px]">
                    @foreach($health['completed'] as $item)
                        <li class="flex items-start gap-2 text-slate-700">
                            <svg class="w-4 h-4 mt-px shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span><span class="sr-only">Completed: </span>{{ $item }}</span>
                        </li>
                    @endforeach
                    @foreach($health['missing'] as $item)
                        <li class="flex items-start gap-2 text-slate-500">
                            <svg class="w-4 h-4 mt-px shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="7" stroke-width="1.75"/></svg>
                            <span><span class="sr-only">Missing: </span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-dash.panel>

            <x-dash.panel title="Meeting attendance" :link="route('members.index')">
                @php $peak = max(1, (int) $dash['lastMeetingAttendance']->max('present')); @endphp
                @if($dash['lastMeetingAttendance']->isEmpty())
                    <p class="text-[13px] text-slate-500">No attendance recorded yet. Use the kiosk or roster to start tracking.</p>
                @else
                    <div class="flex items-end gap-2.5 h-32 border-b border-slate-200" role="img" aria-label="Attendance for the last {{ $dash['lastMeetingAttendance']->count() }} meetings">
                        @foreach($dash['lastMeetingAttendance'] as $row)
                            <div class="flex-1 flex flex-col items-center justify-end h-full gap-1.5">
                                <span class="font-display text-[13px] text-slate-800 tabular-nums">{{ $row->present }}</span>
                                <div class="w-full max-w-[2.5rem] rounded-t-md {{ $loop->last ? 'bg-brand' : 'bg-gold-light/70' }}" style="height: {{ max(6, round($row->present / $peak * 80)) }}px"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-2.5 mt-1.5" aria-hidden="true">
                        @foreach($dash['lastMeetingAttendance'] as $row)
                            <span class="flex-1 text-center text-[10.5px] text-slate-500 tabular-nums">{{ \Carbon\Carbon::parse($row->meeting_date)->format('M j') }}</span>
                        @endforeach
                    </div>
                @endif
            </x-dash.panel>

            @isset($dash['services'])
                {{-- Inline panel so the full committee title can wrap instead of truncating --}}
                <section class="card">
                    <div class="card-header items-start">
                        <h2 class="card-title min-w-0">Chapel Committee: upcoming worship</h2>
                        <a href="{{ route('worship.index') }}" class="card-link shrink-0 mt-1">View all</a>
                    </div>
                    <div class="card-body">
                        <x-dash.services :services="$dash['services']" />
                    </div>
                </section>
            @endisset
        </div>
    </div>
</div>
@endif
