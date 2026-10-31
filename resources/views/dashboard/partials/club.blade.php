{{-- Club President / Executive: run the fellowship, propose activities, report back, keep the vault complete --}}
@php $club = $dash['club']; @endphp

@if(!$club)
    <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 text-sm text-amber-900">
        Your account is not linked to a fellowship yet. Please ask the Chapel Secretary to assign you to your club.
    </div>
@else
<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:px-5 flex flex-col sm:flex-row sm:items-center gap-3 shadow-2xs">
        <div class="w-11 h-11 rounded-xl bg-red-50 text-[#DC2626] font-extrabold text-sm flex items-center justify-center shrink-0 border border-red-100" aria-hidden="true">
            {{ \Illuminate\Support\Str::limit($club->code, 4, '') }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-slate-900">{{ $club->name }}</p>
            <p class="text-xs text-slate-500">{{ $club->meeting_schedule ?: 'Meeting schedule not set' }} &bull; {{ $club->venue ?: 'Venue not set' }}</p>
        </div>
        <nav class="flex flex-wrap gap-2" aria-label="Club quick actions">
            <a href="{{ route('members.qr_kiosk') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800">Open check-in kiosk</a>
            <a href="{{ route('members.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800">Take attendance</a>
            <a href="{{ route('vault.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800">Upload record</a>
            <a href="{{ route('equipment.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800">Borrow equipment</a>
        </nav>
    </div>

    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" title="Your fellowship's to-do list" />

            <x-dash.panel title="Our proposals" :link="route('requests.index')">
                @php
                    $badges = [
                        'submitted' => 'bg-sky-100 text-sky-800',
                        'under_review' => 'bg-purple-100 text-purple-800',
                        'needs_changes' => 'bg-amber-100 text-amber-800',
                        'approved' => 'bg-emerald-100 text-emerald-800',
                        'declined' => 'bg-rose-100 text-rose-800',
                    ];
                @endphp
                @forelse($dash['myRequests'] as $r)
                    <div class="flex items-center gap-3 py-2.5 first:pt-0 last:pb-0 border-b last:border-0 border-slate-100">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('requests.show', $r->id) }}" class="text-sm font-semibold text-slate-900 hover:text-[#DC2626] truncate block">{{ $r->title }}</a>
                            <p class="text-xs text-slate-500">{{ $r->event_date->format('D, M j') }} &bull; {{ $r->venue?->name ?? $r->venue_other }}</p>
                        </div>
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $badges[$r->status] ?? 'bg-slate-100 text-slate-700' }}">{{ ucwords(str_replace('_', ' ', $r->status)) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">You haven't submitted any proposals yet. Activities need 14 days' notice.</p>
                @endforelse
                <a href="{{ route('requests.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold">+ New proposal</a>
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Coming up for us" :link="route('calendar.index', ['club_id' => $club->id])">
                @forelse($dash['upcoming'] as $event)
                    <x-dash.event-row :event="$event" />
                @empty
                    <p class="text-sm text-slate-500">No approved activities scheduled yet.</p>
                @endforelse
            </x-dash.panel>

            @php $health = $dash['health']; @endphp
            <x-dash.panel title="Records vault health" :link="route('vault.index')">
                <div class="flex items-center gap-3">
                    <div class="text-3xl font-extrabold" style="color: {{ $health['color'] }}">{{ $health['score'] }}%</div>
                    <div class="text-xs text-slate-600">{{ $health['grade'] }}</div>
                </div>
                <ul class="mt-3 space-y-1 text-xs">
                    @foreach($health['completed'] as $item)
                        <li class="text-emerald-800">✓ {{ $item }}</li>
                    @endforeach
                    @foreach($health['missing'] as $item)
                        <li class="text-slate-500">○ {{ $item }}</li>
                    @endforeach
                </ul>
            </x-dash.panel>

            <x-dash.panel title="Meeting attendance" :link="route('members.index')">
                @php $peak = max(1, (int) $dash['lastMeetingAttendance']->max('present')); @endphp
                @if($dash['lastMeetingAttendance']->isEmpty())
                    <p class="text-sm text-slate-500">No attendance recorded yet. Use the kiosk or roster to start tracking.</p>
                @else
                    <div class="flex items-end gap-2 h-28" role="img" aria-label="Attendance for the last {{ $dash['lastMeetingAttendance']->count() }} meetings">
                        @foreach($dash['lastMeetingAttendance'] as $row)
                            <div class="flex-1 flex flex-col items-center justify-end h-full gap-1">
                                <span class="text-[10px] font-bold text-slate-700">{{ $row->present }}</span>
                                <div class="w-full rounded-t bg-[#DC2626]/80" style="height: {{ max(6, round($row->present / $peak * 80)) }}px"></div>
                                <span class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($row->meeting_date)->format('M j') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-dash.panel>

            @isset($dash['services'])
                <x-dash.panel title="Chapel Committee: upcoming worship" :link="route('worship.index')">
                    <x-dash.services :services="$dash['services']" />
                </x-dash.panel>
            @endisset
        </div>
    </div>
</div>
@endif
