@extends('layouts.app')

@section('title', 'Master Calendar')

@section('content')
@php
    $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
@endphp
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Schedule & Bookings" title="Master Calendar"
                   description="Centralized schedule of approved fellowship activities, worship services, and venue bookings">
        {{-- iCal .ics feed for Outlook & Google Calendar --}}
        <a href="{{ route('calendar.feed_ics') }}" download="kwang-lim-chapel.ics" title="Add chapel schedule to your phone, Google Calendar, or Outlook" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Add to Calendar
        </a>

        {{-- Shareable feed for Campus Life & SRC --}}
        <a href="{{ route('calendar.public') }}" target="_blank" class="btn btn-ghost">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Public View
        </a>

        @if(!auth()->user() || !auth()->user()->isCampusLifeSrc())
            <a href="{{ route('requests.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Request Booking
            </a>
        @endif
    </x-page-header>

    {{-- Liturgical season & altar paraments --}}
    <section class="card flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 sm:px-5">
        <div class="flex items-center gap-3.5 min-w-0">
            <span class="relative w-10 h-10 shrink-0 rounded-full grid place-items-center text-white"
                  style="background: radial-gradient(circle at 35% 30%, color-mix(in srgb, {{ $season['hex'] ?? '#15803D' }} 70%, white), {{ $season['hex'] ?? '#15803D' }}); box-shadow: 0 0 0 3px #fff, 0 0 0 4px rgb(184 137 43 / .35);" aria-hidden="true">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v18M7 8h10"/></svg>
            </span>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-display text-[15px] text-slate-900 leading-snug">{{ $season['title'] }}</span>
                    <span class="badge badge-dot" style="color: {{ $season['hex'] ?? '#15803D' }}"><span class="text-slate-700">{{ $season['color'] ?? 'Ordinary Time' }}</span></span>
                </div>
                <p class="mt-0.5 font-display italic text-[13px] text-slate-500 leading-snug">{{ $season['verse'] }}</p>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('liturgicalModal').classList.remove('hidden')" class="btn btn-secondary btn-sm shrink-0 self-start sm:self-auto">
            Liturgical Guide
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
        </button>
    </section>

    {{-- Clash warning (overlapping bookings in the current month) --}}
    @if(!empty($clashDates))
        <div class="card flex items-start gap-3 p-4 sm:px-5 border-amber-200 bg-amber-50/70 text-amber-900" role="alert">
            <span class="absolute left-0 top-4 bottom-4 w-[3px] rounded-r bg-amber-500" aria-hidden="true"></span>
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-[13px]">
                <p class="font-semibold uppercase tracking-wider text-[11.5px]">SCHEDULE CLASH ALERT:</p>
                <p class="mt-0.5 leading-relaxed text-amber-800">The system has identified overlapping bookings on the same venue during this month. Conflicting entries are marked with a red warning badge.</p>
            </div>
        </div>
    @endif

    {{-- Controls: month navigation · filters · view mode --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        {{-- Month navigation --}}
        <div class="flex items-center gap-2">
            @php
                $prevMonth = $startDate->copy()->subMonth();
                $nextMonth = $startDate->copy()->addMonth();
            @endphp
            <a href="{{ route('calendar.index', array_merge(request()->all(), ['month' => $prevMonth->month, 'year' => $prevMonth->year])) }}" aria-label="Previous month" class="btn btn-secondary btn-sm !px-2">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="{{ route('calendar.index', array_merge(request()->all(), ['month' => $nextMonth->month, 'year' => $nextMonth->year])) }}" aria-label="Next month" class="btn btn-secondary btn-sm !px-2">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="ml-2 font-display text-[1.6rem] leading-none text-slate-900 tracking-[-0.02em] whitespace-nowrap">
                {{ $startDate->format('F') }} <span class="text-slate-400">{{ $startDate->format('Y') }}</span>
            </h2>
            <a href="{{ route('calendar.index', array_merge(request()->all(), ['month' => now()->month, 'year' => now()->year])) }}" class="btn btn-ghost btn-sm ml-1 text-brand">
                Today
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            {{-- Filters --}}
            <form method="GET" action="{{ route('calendar.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="view" value="{{ $viewMode }}">

                <label for="calClubFilter" class="sr-only">Filter by club</label>
                <select id="calClubFilter" name="club_id" onchange="this.form.submit()" class="field !w-auto !py-2 !text-xs max-w-[13rem]">
                    <option value="">All Clubs</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>{{ $club->name }} ({{ $club->code }})</option>
                    @endforeach
                </select>

                <label for="calVenueFilter" class="sr-only">Filter by venue</label>
                <select id="calVenueFilter" name="venue_id" onchange="this.form.submit()" class="field !w-auto !py-2 !text-xs max-w-[13rem]">
                    <option value="">All Venues</option>
                    @foreach($venues as $venue)
                        <option value="{{ $venue->id }}" {{ request('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                    @endforeach
                </select>
            </form>

            {{-- View mode --}}
            <nav class="segmented self-start" aria-label="Calendar view">
                <a href="{{ route('calendar.index', array_merge(request()->all(), ['view' => 'month'])) }}" @if($viewMode === 'month') aria-current="page" @endif>
                    Month Grid
                </a>
                <a href="{{ route('calendar.index', array_merge(request()->all(), ['view' => 'list'])) }}" @if($viewMode === 'list') aria-current="page" @endif>
                    Agenda List
                </a>
            </nav>
        </div>
    </div>

    {{-- Month grid --}}
    @if($viewMode === 'month')
        @php
            $firstDayOfGrid = $startDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $lastDayOfGrid = $endDate->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
            $currentDay = $firstDayOfGrid->copy();

            // Group events by date string
            $eventsByDate = [];
            foreach ($events as $ev) {
                $d = $ev->event_date->toDateString();
                if (!isset($eventsByDate[$d])) $eventsByDate[$d] = [];
                $eventsByDate[$d][] = $ev;
            }
        @endphp

        <div class="card overflow-hidden">
          <div class="overflow-x-auto">
          <div class="min-w-[680px]">
            {{-- Weekday headers --}}
            <div class="grid grid-cols-7 border-b border-slate-200/70 bg-slate-50/70 text-[10.5px] font-bold uppercase tracking-[0.16em] text-slate-400">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
                    <div class="px-3 py-2.5 {{ $dow === 'Sun' ? 'text-brand/80' : '' }}">{{ $dow }}</div>
                @endforeach
            </div>

            {{-- Days --}}
            <div class="grid grid-cols-7 auto-rows-fr text-xs [&>*]:border-slate-200/60 [&>*:not(:nth-child(7n))]:border-r [&>*]:border-b">
                @while($currentDay <= $lastDayOfGrid)
                    @php
                        $dayStr = $currentDay->toDateString();
                        $isCurrentMonth = $currentDay->month == $month;
                        $isToday = $currentDay->isToday();
                        $dayEvents = $eventsByDate[$dayStr] ?? [];
                        $hasClashOnDay = isset($clashDates[$dayStr]);
                    @endphp

                    <div class="p-2 min-h-[118px] flex flex-col transition-colors {{ $isCurrentMonth ? 'bg-white hover:bg-slate-50/50' : 'bg-slate-50/60 text-slate-400' }} {{ $isToday ? '!bg-brand-soft/40' : '' }}">
                        <div class="flex items-center justify-between mb-1.5 px-0.5">
                            <span class="tabular-nums text-[12px] {{ $isToday ? 'w-6 h-6 -ml-0.5 rounded-full grid place-items-center bg-brand text-white font-semibold shadow-xs' : ($isCurrentMonth ? 'font-semibold text-slate-700' : 'font-medium text-slate-300') }}">
                                @if($isToday)<span class="sr-only">Today, </span>@endif{{ $currentDay->day }}
                            </span>
                            @if($hasClashOnDay)
                                <span class="badge badge-rose !text-[9.5px] !px-1.5 !leading-4" title="Schedule clash on this day">Clash</span>
                            @endif
                        </div>

                        {{-- Events for this day --}}
                        <div class="space-y-1 overflow-y-auto max-h-[84px] flex-1">
                            @foreach($dayEvents as $item)
                                @php
                                    $evClubColor = $item->club?->color_hex ?: '#A51C30';
                                    $evPayload = [
                                        'title' => $item->title,
                                        'club' => $item->club?->name ?? 'Chaplaincy Office',
                                        'date' => $item->event_date->format('M d, Y'),
                                        'time' => substr($item->start_time, 0, 5) . ' - ' . substr($item->end_time, 0, 5),
                                        'venue' => $item->venue?->name ?? ($item->venue_name ?: 'Venue to be confirmed'),
                                        'desc' => $item->description,
                                        'color' => $evClubColor,
                                        'attendance' => $item->expected_attendance,
                                    ];
                                @endphp
                                <button type="button" data-event="{{ json_encode($evPayload) }}" onclick="openEventModal(this)"
                                        class="w-full text-left cursor-pointer pl-2 pr-1.5 py-1 rounded-md text-[10.5px] leading-tight font-semibold text-slate-800 truncate flex items-center gap-1.5 border-l-[3px] transition hover:brightness-[0.97] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-slate-900"
                                        style="border-left-color: {{ $evClubColor }}; background-color: color-mix(in srgb, {{ $evClubColor }} 10%, white);">
                                    @if(isset($item->is_clashing) && $item->is_clashing)
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0" aria-hidden="true"></span>
                                        <span class="sr-only">Clash:</span>
                                    @endif
                                    <span class="truncate"><span class="tabular-nums font-medium text-slate-500">{{ substr($item->start_time, 0, 5) }}</span> {{ $item->title }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @php $currentDay->addDay(); @endphp
                @endwhile
            </div>
          </div>
          </div>
        </div>

    @else
        {{-- Agenda list --}}
        <div class="card overflow-hidden">
            <div class="divide-y divide-slate-100">
                @forelse($events as $event)
                    <div class="px-4 py-4 sm:px-5 hover:bg-slate-50/60 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-4 min-w-0">
                            {{-- Date tile --}}
                            <div class="w-12 shrink-0 text-center rounded-xl bg-white ring-1 ring-slate-200 shadow-2xs overflow-hidden">
                                <div class="text-[9px] font-bold uppercase tracking-[0.14em] text-white bg-brand py-0.5">{{ $event->event_date->format('M') }}</div>
                                <div class="font-display text-[1.3rem] text-slate-900 leading-none pt-1.5 tabular-nums">{{ $event->event_date->format('d') }}</div>
                                <div class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 pb-1">{{ $event->event_date->format('D') }}</div>
                            </div>

                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="badge badge-dot" style="color: {{ $event->club?->color_hex ?: '#A51C30' }}">
                                        <span class="text-slate-700">{{ $event->club?->name ?? 'Chaplaincy Office' }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[12px] font-semibold text-slate-500 tabular-nums">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ substr($event->start_time, 0, 5) }} – {{ substr($event->end_time, 0, 5) }}
                                    </span>
                                    @if(isset($event->is_clashing) && $event->is_clashing)
                                        <span class="badge badge-rose badge-dot">Clash Alert</span>
                                    @endif
                                </div>
                                <h3 class="text-[14px] font-semibold text-slate-900">{{ $event->title }}</h3>
                                <div class="text-[12px] text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $event->venue?->name ?? ($event->venue_name ?: 'Venue to be confirmed') }}
                                    </span>
                                    @if($event->expected_attendance)
                                        <span class="tabular-nums">Exp. {{ $event->expected_attendance }} attendees</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @php
                            $canOpenProposal = $event->request_id && auth()->user() && !auth()->user()->isCampusLifeSrc()
                                && (auth()->user()->isOffice() || auth()->user()->club_id === $event->club_id);
                        @endphp
                        @if($canOpenProposal)
                            <a href="{{ route('requests.show', $event->request_id) }}" class="card-link self-end sm:self-center">
                                View Linked Proposal
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-mark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="empty-state-title">A quiet month</p>
                        <p class="empty-state-text">No events scheduled for the selected month and filters.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>

{{-- Liturgical guide modal --}}
<div id="liturgicalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="liturgicalModalTitle" onclick="if (event.target === this) this.classList.add('hidden')">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full ring-1 ring-black/5 overflow-hidden modal-panel">
        <div class="relative px-7 pt-7 pb-6 text-white overflow-hidden grain flex items-start justify-between gap-4" style="background: linear-gradient(160deg, {{ $season['hex'] ?? '#15803D' }}, #15121A 120%)">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[0.22em] text-white/70 block mb-2">{{ $season['name'] ?? 'Liturgical Season' }}</span>
                <h3 id="liturgicalModalTitle" class="font-display text-[1.5rem] leading-tight">{{ $season['title'] }}</h3>
            </div>
            <button type="button" onclick="document.getElementById('liturgicalModal').classList.add('hidden')" class="-mr-2 -mt-1 p-1.5 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition shrink-0" aria-label="Close liturgical guide">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-7 space-y-4">
            <dl class="space-y-4 text-[13px]">
                <div>
                    <dt class="card-kicker mb-1">Liturgical Colour</dt>
                    <dd class="font-semibold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $season['hex'] ?? '#15803D' }}" aria-hidden="true"></span>
                        {{ $season['color'] ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="card-kicker mb-1">Altar Paraments</dt>
                    <dd class="text-slate-600 leading-relaxed">{{ $season['parament'] ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="card-kicker mb-1">Ministry Focus</dt>
                    <dd class="text-slate-800 font-medium leading-relaxed">{{ $season['focus'] ?? '—' }}</dd>
                </div>
            </dl>
            <div class="rule-ornament text-[10px]" aria-hidden="true">&#9670;</div>
            <blockquote class="font-display italic text-[16px] leading-snug text-slate-800">{{ $season['verse'] }}</blockquote>
        </div>
    </div>
</div>

{{-- Event detail modal --}}
<div id="calendarEventModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modalTitle" onclick="if (event.target === this) this.classList.add('hidden')">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full ring-1 ring-black/5 overflow-hidden modal-panel">
        <div id="modalHeader" class="relative px-7 pt-6 pb-5 text-white flex items-start justify-between gap-4 overflow-hidden grain" style="background-color: #A51C30">
            <span class="absolute inset-0 -z-[2] pointer-events-none" style="background: linear-gradient(160deg, transparent 30%, rgb(21 18 26 / .55))" aria-hidden="true"></span>
            <div class="min-w-0">
                <span id="modalClub" class="text-[10px] font-bold uppercase tracking-[0.2em] block text-white/75"></span>
                <h3 id="modalTitle" class="font-display text-[1.35rem] leading-tight mt-1.5"></h3>
            </div>
            <button type="button" onclick="document.getElementById('calendarEventModal').classList.add('hidden')" class="-mr-2 -mt-1 p-1.5 rounded-lg text-white/75 hover:text-white hover:bg-white/10 transition shrink-0" aria-label="Close event details">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-7 py-6 space-y-4 text-[13px]">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <span class="card-kicker block mb-1">Date &amp; Time</span>
                    <p id="modalTime" class="font-semibold text-slate-900 tabular-nums"></p>
                </div>
                <div>
                    <span class="card-kicker block mb-1">Venue</span>
                    <p id="modalVenue" class="font-semibold text-slate-900"></p>
                </div>
                <div>
                    <span class="card-kicker block mb-1">Expected Attendance</span>
                    <p id="modalAttendance" class="font-semibold text-slate-900 tabular-nums"></p>
                </div>
            </div>
            <div>
                <span class="card-kicker block mb-1">Description</span>
                <p id="modalDesc" class="text-slate-600 bg-slate-50 p-3.5 rounded-xl ring-1 ring-slate-200/70 leading-relaxed"></p>
            </div>
        </div>
        <div class="px-7 py-4 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="document.getElementById('calendarEventModal').classList.add('hidden')" class="btn btn-ink btn-sm">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function openEventModal(el) {
        let ev = {};
        try { ev = JSON.parse(el.dataset.event || '{}'); } catch (e) { return; }
        document.getElementById('modalTitle').textContent = ev.title || '';
        document.getElementById('modalClub').textContent = ev.club || '';
        document.getElementById('modalTime').textContent = (ev.date || '') + ' (' + (ev.time || '') + ')';
        document.getElementById('modalVenue').textContent = ev.venue || '';
        document.getElementById('modalAttendance').textContent = ev.attendance ? (ev.attendance + ' expected') : 'Not specified';
        document.getElementById('modalDesc').textContent = ev.desc || 'Official approved fellowship activity.';
        document.getElementById('modalHeader').style.backgroundColor = /^#[0-9a-fA-F]{3,8}$/.test(ev.color || '') ? ev.color : '#A51C30';
        document.getElementById('calendarEventModal').classList.remove('hidden');
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.getElementById('calendarEventModal').classList.add('hidden');
            document.getElementById('liturgicalModal').classList.add('hidden');
        }
    });
</script>
@endsection
