@extends('layouts.app')

@section('title', 'Master Calendar')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] mb-1">
                <span>Schedule &amp; Bookings</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Master Calendar
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Centralized schedule of approved fellowship activities, worship services, and venue bookings
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- iCal .ics Feed for Outlook & Google Calendar -->
            <a href="{{ route('calendar.feed_ics') }}" download="kwang-lim-chapel.ics" title="Subscribe or import into Outlook, Google Calendar or Apple Calendar" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-800 text-xs font-semibold transition border border-slate-200 shadow-2xs active:scale-95">
                <svg class="w-4 h-4 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Subscribe (.ics)</span>
            </a>

            <!-- Shareable Feed Button for Campus Life & SRC -->
            <a href="{{ route('calendar.public') }}" target="_blank" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition border border-slate-200 active:scale-95">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Public View</span>
            </a>

            @if(!auth()->user() || !auth()->user()->isCampusLifeSrc())
                <a href="{{ route('requests.create') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold transition shadow-xs active:scale-95">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Request Booking</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Liturgical Season & Altar Paraments Banner -->
    @php
        $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
    @endphp
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl text-white flex items-center justify-center font-bold text-sm shrink-0" style="background-color: {{ $season['hex'] ?? '#15803D' }}" aria-hidden="true">
                ✝
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-bold text-slate-900 text-xs">{{ $season['title'] }}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold border {{ $season['badge_class'] ?? 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">{{ $season['color'] ?? 'Ordinary Time' }}</span>
                </div>
                <p class="text-slate-500 text-[11px] mt-0.5">{{ $season['verse'] }}</p>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('liturgicalModal').classList.remove('hidden')" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs shrink-0 self-start sm:self-auto transition">
            Liturgical Guide &rarr;
        </button>
    </div>

    <!-- Clash Warning Banner (if any overlapping clashes exist in current month) -->
    @if(!empty($clashDates))
        <div class="p-4 sm:p-5 rounded-2xl border border-amber-300 bg-amber-50 text-amber-900 text-xs flex items-start space-x-3 card-elevation">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <span class="font-bold uppercase tracking-wider">SCHEDULE CLASH ALERT:</span>
                <p class="mt-0.5 leading-relaxed">The system has identified overlapping bookings on the same venue during this month. Conflicting entries are marked with a red warning badge.</p>
            </div>
        </div>
    @endif

    <!-- Controls Bar: Month Navigation & Filter & View Toggle -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl card-elevation border border-slate-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        
        <!-- Month Navigation -->
        <div class="flex items-center space-x-2.5">
            @php
                $prevMonth = $startDate->copy()->subMonth();
                $nextMonth = $startDate->copy()->addMonth();
            @endphp
            <a href="{{ route('calendar.index', array_merge(request()->all(), ['month' => $prevMonth->month, 'year' => $prevMonth->year])) }}" aria-label="Previous month" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 transition active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h3 class="text-base sm:text-lg font-bold font-cinzel text-slate-900 tracking-wide">
                {{ $startDate->format('F Y') }}
            </h3>
            <a href="{{ route('calendar.index', array_merge(request()->all(), ['month' => $nextMonth->month, 'year' => $nextMonth->year])) }}" aria-label="Next month" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 transition active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('calendar.index', array_merge(request()->all(), ['month' => now()->month, 'year' => now()->year])) }}" class="text-[11px] font-bold uppercase text-[#DC2626] bg-[#DC2626]/10 hover:bg-[#DC2626]/20 px-3 py-1.5 rounded-xl transition border border-[#DC2626]/20 active:scale-95">
                Today
            </a>
        </div>

        <!-- Filter Dropdowns -->
        <form method="GET" action="{{ route('calendar.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="view" value="{{ $viewMode }}">

            <label for="calClubFilter" class="sr-only">Filter by club</label>
            <select id="calClubFilter" name="club_id" onchange="this.form.submit()" class="text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50">
                <option value="">All Clubs</option>
                @foreach($clubs as $club)
                    <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>{{ $club->name }} ({{ $club->code }})</option>
                @endforeach
            </select>

            <label for="calVenueFilter" class="sr-only">Filter by venue</label>
            <select id="calVenueFilter" name="venue_id" onchange="this.form.submit()" class="text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50">
                <option value="">All Venues</option>
                @foreach($venues as $venue)
                    <option value="{{ $venue->id }}" {{ request('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                @endforeach
            </select>
        </form>

        <!-- View Mode Buttons (Month / List) -->
        <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl">
            <a href="{{ route('calendar.index', array_merge(request()->all(), ['view' => 'month'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $viewMode === 'month' ? 'bg-white shadow-xs text-[#DC2626]' : 'text-slate-600 hover:text-slate-900' }}">
                Month Grid
            </a>
            <a href="{{ route('calendar.index', array_merge(request()->all(), ['view' => 'list'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $viewMode === 'list' ? 'bg-white shadow-xs text-[#DC2626]' : 'text-slate-600 hover:text-slate-900' }}">
                Agenda List
            </a>
        </div>

    </div>

    <!-- Calendar View: Month Grid -->
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

        <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-x-auto">
          <div class="min-w-[640px]">
            <!-- Day of Week Headers -->
            <div class="grid grid-cols-7 bg-[#DC2626] text-white text-center text-xs font-bold py-3 border-b border-white/10 tracking-wider">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>

            <!-- Month Days Grid -->
            <div class="grid grid-cols-7 auto-rows-fr divide-x divide-y divide-slate-200/80 text-xs min-h-[580px]">
                @while($currentDay <= $lastDayOfGrid)
                    @php
                        $dayStr = $currentDay->toDateString();
                        $isCurrentMonth = $currentDay->month == $month;
                        $isToday = $currentDay->isToday();
                        $dayEvents = $eventsByDate[$dayStr] ?? [];
                        $hasClashOnDay = isset($clashDates[$dayStr]);
                    @endphp

                    <div class="p-2 sm:p-2.5 min-h-[115px] flex flex-col justify-between transition {{ $isCurrentMonth ? 'bg-white' : 'bg-slate-50/60 text-slate-400' }} {{ $isToday ? 'ring-2 ring-inset ring-[#DC2626] bg-red-50/30' : '' }}">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-bold text-xs {{ $isToday ? 'bg-[#DC2626] text-white w-6 h-6 rounded-full flex items-center justify-center font-cinzel shadow-xs' : ($isCurrentMonth ? 'text-slate-800' : 'text-slate-400') }}">
                                {{ $currentDay->day }}
                            </span>
                            @if($hasClashOnDay)
                                <span class="text-[9px] bg-rose-100 text-rose-800 font-bold px-1.5 py-0.2 rounded-full uppercase" title="Schedule clash on this day">Clash</span>
                            @endif
                        </div>

                        <!-- Events for this day -->
                        <div class="space-y-1.5 overflow-y-auto max-h-[85px] flex-1">
                            @foreach($dayEvents as $item)
                                @php
                                    $evClubColor = $item->club?->color_hex ?: '#DC2626';
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
                                <button type="button" data-event="{{ json_encode($evPayload) }}" onclick="openEventModal(this)" class="w-full text-left cursor-pointer p-1.5 rounded-lg text-[10px] font-semibold text-white truncate shadow-xs transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-slate-900 flex items-center space-x-1" style="background-color: {{ $evClubColor }}">
                                    @if(isset($item->is_clashing) && $item->is_clashing)
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-300 animate-ping" aria-hidden="true"></span>
                                        <span class="sr-only">Clash:</span>
                                    @endif
                                    <span class="truncate">{{ substr($item->start_time, 0, 5) }} {{ $item->title }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @php $currentDay->addDay(); @endphp
                @endwhile
            </div>
          </div>
        </div>

    @else
        <!-- Agenda List View -->
        <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-hidden">
            <div class="divide-y divide-slate-100">
                @forelse($events as $event)
                    <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start space-x-3.5">
                            <!-- Date Box -->
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 text-center flex flex-col justify-center shrink-0">
                                <span class="text-[10px] uppercase font-bold text-slate-500">{{ $event->event_date->format('M') }}</span>
                                <span class="text-lg font-black text-[#DC2626] leading-none">{{ $event->event_date->format('d') }}</span>
                                <span class="text-[9px] text-slate-400 font-medium">{{ $event->event_date->format('D') }}</span>
                            </div>

                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded text-white" style="background-color: {{ $event->club?->color_hex ?: '#DC2626' }}">
                                        {{ $event->club?->name ?? 'Chaplaincy Office' }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-500 font-mono">
                                        ⏰ {{ substr($event->start_time, 0, 5) }} - {{ substr($event->end_time, 0, 5) }}
                                    </span>
                                    @if(isset($event->is_clashing) && $event->is_clashing)
                                        <span class="text-[10px] bg-rose-100 text-rose-800 font-bold px-1.5 py-0.5 rounded">Clash Alert</span>
                                    @endif
                                </div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $event->title }}</h4>
                                <div class="text-xs text-slate-500 flex items-center space-x-3">
                                    <span>📍 {{ $event->venue?->name ?? ($event->venue_name ?: 'Venue to be confirmed') }}</span>
                                    @if($event->expected_attendance)
                                        <span>&bull; Exp. {{ $event->expected_attendance }} attendees</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @php
                            $canOpenProposal = $event->request_id && auth()->user() && !auth()->user()->isCampusLifeSrc()
                                && (auth()->user()->isOffice() || auth()->user()->club_id === $event->club_id);
                        @endphp
                        @if($canOpenProposal)
                            <a href="{{ route('requests.show', $event->request_id) }}" class="text-xs font-bold text-[#DC2626] hover:underline self-end sm:self-center">
                                View Linked Proposal &rarr;
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="p-10 text-center text-xs text-slate-500">
                        No events scheduled for the selected month and filters.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>

<!-- Liturgical Guide Modal -->
<div id="liturgicalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4" role="dialog" aria-modal="true" aria-labelledby="liturgicalModalTitle" onclick="if (event.target === this) this.classList.add('hidden')">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden card-elevation">
        <div class="p-5 text-white flex items-center justify-between" style="background-color: {{ $season['hex'] ?? '#15803D' }}">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider block opacity-90">{{ $season['name'] ?? 'Liturgical Season' }}</span>
                <h3 id="liturgicalModalTitle" class="text-base font-bold mt-0.5">{{ $season['title'] }}</h3>
            </div>
            <button type="button" onclick="document.getElementById('liturgicalModal').classList.add('hidden')" class="text-white/80 hover:text-white p-1" aria-label="Close liturgical guide">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-3.5 text-xs">
            <div>
                <span class="text-slate-600 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Liturgical Colour</span>
                <p class="font-bold text-slate-900">{{ $season['color'] ?? '—' }}</p>
            </div>
            <div>
                <span class="text-slate-600 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Altar Paraments</span>
                <p class="text-slate-700 leading-relaxed">{{ $season['parament'] ?? '—' }}</p>
            </div>
            <div>
                <span class="text-slate-600 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Ministry Focus</span>
                <p class="text-slate-700 leading-relaxed">{{ $season['focus'] ?? '—' }}</p>
            </div>
            <p class="text-slate-600 italic bg-slate-50 p-3 rounded-2xl border border-slate-200/80 leading-relaxed">{{ $season['verse'] }}</p>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div id="calendarEventModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle" onclick="if (event.target === this) this.classList.add('hidden')">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden card-elevation">
        <div id="modalHeader" class="p-5 text-white flex items-center justify-between" style="background-color: #DC2626">
            <div>
                <span id="modalClub" class="text-[10px] font-bold uppercase tracking-wider block opacity-90 font-cinzel"></span>
                <h3 id="modalTitle" class="text-base font-bold font-cinzel mt-0.5"></h3>
            </div>
            <button type="button" onclick="document.getElementById('calendarEventModal').classList.add('hidden')" class="text-white/80 hover:text-white p-1" aria-label="Close event details">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-3.5 text-xs">
            <div>
                <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Date & Time</span>
                <p id="modalTime" class="font-bold text-slate-900"></p>
            </div>
            <div>
                <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Venue</span>
                <p id="modalVenue" class="font-bold text-slate-900"></p>
            </div>
            <div>
                <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Expected Attendance</span>
                <p id="modalAttendance" class="font-bold text-slate-900"></p>
            </div>
            <div>
                <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Description</span>
                <p id="modalDesc" class="text-slate-700 bg-slate-50 p-3 rounded-2xl border border-slate-200/80 leading-relaxed"></p>
            </div>
        </div>
        <div class="bg-slate-50 px-6 py-3.5 border-t border-slate-100 text-right">
            <button type="button" onclick="document.getElementById('calendarEventModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition">
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
        document.getElementById('modalHeader').style.backgroundColor = /^#[0-9a-fA-F]{3,8}$/.test(ev.color || '') ? ev.color : '#DC2626';
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
