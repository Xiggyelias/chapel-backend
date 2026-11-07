@extends('layouts.app')

@section('title', 'Master Calendar')

@section('content')
@php
    $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
    $viewer = auth()->user();
    $isObserver = $viewer && $viewer->isCampusLifeSrc();

    // Everything the event panel needs, embedded on each clickable activity
    $payload = function ($ev) use ($viewer, $isObserver) {
        $canOpenProposal = $ev->request_id && $viewer && !$isObserver
            && ($viewer->isOffice() || $viewer->club_id === $ev->club_id);

        return \App\Support\CalendarLinks::payload($ev, $canOpenProposal ? route('requests.show', $ev->request_id) : null);
    };

    $eventsByDate = $events->groupBy(fn ($ev) => $ev->event_date->toDateString());
    $prevMonth = $startDate->copy()->subMonth();
    $nextMonth = $startDate->copy()->addMonth();
    $nav = fn ($extra) => route('calendar.index', array_merge(request()->only(['club_id', 'venue_id', 'view']), $extra));
    $isThisMonth = $startDate->isSameMonth(now());
    $filtered = request()->filled('club_id') || request()->filled('venue_id');
@endphp

<div class="page-shell space-y-5">

    <x-page-header eyebrow="Schedule & Bookings" title="Master Calendar"
                   description="Every approved fellowship activity, worship service and venue booking in one place. Tap any activity to add it to your own calendar.">
        <button type="button" onclick="openSubscribe()" class="btn btn-soft">
            <x-icon name="phone" />
            Add to my phone
        </button>
        <a href="{{ route('calendar.public') }}" target="_blank" rel="noopener" class="btn btn-secondary">
            <x-icon name="external" />
            Public view
        </a>
        @unless($isObserver)
            <a href="{{ route('requests.create') }}" class="btn btn-primary">
                <x-icon name="plus" stroke="2" />
                Request Booking
            </a>
        @endunless
    </x-page-header>

    {{-- One-time nudge: most people want the calendar on their phone --}}
    <section id="subscribeNudge" class="card hidden p-4 sm:px-5 flex-col sm:flex-row sm:items-center gap-4" data-no-reveal>
        <span class="icon-tile" style="--tile: var(--color-brand)"><x-icon name="calendar-plus" /></span>
        <div class="min-w-0 flex-1">
            <p class="text-[14.5px] font-semibold text-slate-900">Never miss a chapel activity</p>
            <p class="text-[13.5px] text-slate-600">Put this calendar on your phone in one tap. It keeps itself up to date, with no files to download.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="openSubscribe()" class="btn btn-primary btn-sm">Set it up</button>
            <button type="button" onclick="dismissNudge()" class="btn btn-ghost btn-sm">Not now</button>
        </div>
    </section>

    @if(!empty($clashDates))
        <div class="flash-warning" role="alert">
            <x-icon name="alert" class="w-5 h-5 shrink-0" />
            <div class="text-[14px]">
                <p class="font-semibold">Schedule clash this month</p>
                <p class="mt-0.5 text-amber-800">Two bookings overlap in the same venue on {{ count($clashDates) === 1 ? 'one day' : count($clashDates) . ' days' }}. Clashing activities are marked in red.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 2xl:grid-cols-[minmax(0,1fr)_19rem] gap-5 items-start">

        {{-- ═══ Calendar ═══ --}}
        <section class="card overflow-hidden min-w-0" aria-label="Calendar for {{ $startDate->format('F Y') }}">

            {{-- Toolbar --}}
            <div class="flex flex-col gap-3 p-3 sm:p-4 border-b border-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-1.5">
                    <a href="{{ $nav(['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" aria-label="Previous month" class="btn btn-secondary btn-sm btn-icon"><x-icon name="chevron-left" class="w-4 h-4" /></a>
                    <a href="{{ $nav(['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" aria-label="Next month" class="btn btn-secondary btn-sm btn-icon"><x-icon name="chevron-right" class="w-4 h-4" /></a>
                    <h2 class="ml-2 text-[1.25rem] font-bold text-slate-900 tracking-[-0.02em] whitespace-nowrap">
                        {{ $startDate->format('F') }} <span class="text-slate-400 font-semibold">{{ $startDate->format('Y') }}</span>
                    </h2>
                    @unless($isThisMonth)
                        <a href="{{ $nav(['month' => now()->month, 'year' => now()->year]) }}" class="btn btn-ghost btn-sm text-brand ml-1">Today</a>
                    @endunless
                </div>

                    <nav class="segmented" aria-label="Calendar view">
                        <a href="{{ $nav(['view' => 'month', 'month' => $month, 'year' => $year]) }}" @if($viewMode === 'month') aria-current="page" @endif>
                            <x-icon name="grid" class="w-4 h-4" /> Month Grid
                        </a>
                        <a href="{{ $nav(['view' => 'list', 'month' => $month, 'year' => $year]) }}" @if($viewMode === 'list') aria-current="page" @endif>
                            <x-icon name="list" class="w-4 h-4" /> Agenda List
                        </a>
                    </nav>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" action="{{ route('calendar.index') }}" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="view" value="{{ $viewMode }}">

                        <label for="calClubFilter" class="sr-only">Filter by fellowship</label>
                        <select id="calClubFilter" name="club_id" onchange="this.form.submit()" class="field !w-auto !py-1.5 !text-[13px] max-w-[12rem]">
                            <option value="">All fellowships</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                            @endforeach
                        </select>

                        <label for="calVenueFilter" class="sr-only">Filter by venue</label>
                        <select id="calVenueFilter" name="venue_id" onchange="this.form.submit()" class="field !w-auto !py-1.5 !text-[13px] max-w-[12rem]">
                            <option value="">All venues</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}" {{ request('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                            @endforeach
                        </select>
                        @if($filtered)
                            <a href="{{ route('calendar.index', ['month' => $month, 'year' => $year, 'view' => $viewMode]) }}" class="btn btn-ghost btn-sm">Clear</a>
                        @endif
                        <noscript><button type="submit" class="btn btn-secondary btn-sm">Apply</button></noscript>
                    </form>


                </div>
            </div>

            @if($viewMode === 'month')
                @php
                    $cursor = $startDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $lastDayOfGrid = $endDate->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                @endphp

                {{-- Weekday header --}}
                <div class="cal-grid border-b border-slate-100 bg-slate-50 text-[12px] font-semibold text-slate-500">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
                        <div class="px-2 sm:px-3 py-2 text-center sm:text-left {{ $dow === 'Sun' ? 'text-brand' : '' }}">{{ $dow }}</div>
                    @endforeach
                </div>

                <div class="cal-grid">
                    @while($cursor <= $lastDayOfGrid)
                        @php
                            $dayStr = $cursor->toDateString();
                            $dayEvents = $eventsByDate[$dayStr] ?? collect();
                            $inMonth = $cursor->month == $month;
                        @endphp
                        <div class="cal-day {{ $inMonth ? '' : 'is-outside' }} {{ $cursor->isToday() ? 'is-today' : '' }} !min-h-[3.5rem] sm:!min-h-[7.5rem]">
                            <div class="flex items-center justify-between">
                                @if($dayEvents->isNotEmpty())
                                    <a href="#day-{{ $dayStr }}" class="cal-num sm:pointer-events-none" aria-label="{{ $cursor->format('l j F') }}: {{ $dayEvents->count() }} {{ Str::plural('activity', $dayEvents->count()) }}">{{ $cursor->day }}</a>
                                @else
                                    <span class="cal-num">@if($cursor->isToday())<span class="sr-only">Today, </span>@endif{{ $cursor->day }}</span>
                                @endif
                                @if(isset($clashDates[$dayStr]))
                                    <span class="hidden sm:inline-flex badge badge-rose !text-[11px] !px-1.5 !leading-4" title="Two bookings overlap on this day">Clash</span>
                                @endif
                            </div>

                            {{-- Phones: coloured dots; larger screens: full activity chips --}}
                            @if($dayEvents->isNotEmpty())
                                <div class="flex flex-wrap justify-center gap-1 sm:hidden" aria-hidden="true">
                                    @foreach($dayEvents->take(4) as $item)
                                        <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $item->club?->color_hex ?: '#DC2626' }}"></span>
                                    @endforeach
                                </div>
                                <div class="hidden sm:flex flex-col gap-1 min-w-0">
                                    @foreach($dayEvents->take(3) as $item)
                                        <button type="button" data-event="{{ $payload($item) }}" onclick="openEvent(this)" class="cal-chip"
                                                style="--c: {{ $item->club?->color_hex ?: '#DC2626' }}" title="{{ $item->title }}">
                                            @if($item->is_clashing ?? false)
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0" aria-hidden="true"></span><span class="sr-only">Clash:</span>
                                            @endif
                                            <span class="min-w-0">
                                                <span class="cal-chip-time block">{{ substr($item->start_time, 0, 5) }}</span>
                                                <span class="block line-clamp-2 break-words">{{ $item->title }}</span>
                                            </span>
                                        </button>
                                    @endforeach
                                    @if($dayEvents->count() > 3)
                                        <a href="{{ $nav(['view' => 'list', 'month' => $month, 'year' => $year]) }}#day-{{ $dayStr }}" class="px-1.5 text-[12px] font-semibold text-slate-500 hover:text-brand">+{{ $dayEvents->count() - 3 }} more</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @php $cursor->addDay(); @endphp
                    @endwhile
                </div>
            @endif

            {{-- Agenda: the list view, and on phones the readable companion to the dot calendar --}}
            <div class="{{ $viewMode === 'month' ? 'sm:hidden border-t border-slate-100' : '' }}">
                @forelse($eventsByDate as $date => $dayEvents)
                    @php $d = \Carbon\Carbon::parse($date); @endphp
                    <div id="day-{{ $date }}" class="scroll-mt-20">
                        <h3 class="sticky top-0 z-[1] flex items-center gap-2 px-4 sm:px-5 py-2 bg-slate-50/95 backdrop-blur border-b border-slate-100 text-[13px] font-bold {{ $d->isToday() ? 'text-brand' : 'text-slate-700' }}">
                            {{ $d->format('l, j F') }}
                            @if($d->isToday())<span class="badge badge-brand">Today</span>@elseif($d->isTomorrow())<span class="badge">Tomorrow</span>@endif
                        </h3>
                        <ul class="divide-y divide-slate-100">
                            @foreach($dayEvents as $event)
                                <li>
                                    <button type="button" data-event="{{ $payload($event) }}" onclick="openEvent(this)"
                                            class="w-full text-left flex items-start gap-3.5 px-4 sm:px-5 py-3.5 hover:bg-slate-50 transition-colors cursor-pointer">
                                        <span class="w-12 shrink-0 pt-0.5 text-[13px] font-bold text-slate-800 tabular-nums">{{ substr($event->start_time, 0, 5) }}<span class="block text-[11.5px] font-medium text-slate-400">{{ substr($event->end_time, 0, 5) }}</span></span>
                                        <span class="w-1 self-stretch rounded-full shrink-0" style="background: {{ $event->club?->color_hex ?: '#DC2626' }}" aria-hidden="true"></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="flex flex-wrap items-center gap-2">
                                                <span class="text-[14.5px] font-semibold text-slate-900">{{ $event->title }}</span>
                                                @if($event->is_clashing ?? false)<span class="badge badge-rose badge-dot">Clash</span>@endif
                                            </span>
                                            <span class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[13px] text-slate-500">
                                                <span class="inline-flex items-center gap-1"><x-icon name="pin" class="w-3.5 h-3.5 text-slate-400" />{{ $event->venue?->name ?? ($event->venue_name ?: 'Venue to be confirmed') }}</span>
                                                <span>{{ $event->club?->name ?? 'Chaplaincy Office' }}</span>
                                                @if($event->expected_attendance)<span class="tabular-nums">{{ $event->expected_attendance }} expected</span>@endif
                                            </span>
                                        </span>
                                        <span class="hidden sm:inline-flex btn btn-secondary btn-sm shrink-0 self-center pointer-events-none"><x-icon name="calendar-plus" /> Add</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-mark"><x-icon name="calendar" class="w-6 h-6" /></div>
                        <p class="empty-state-title">A quiet month</p>
                        <p class="empty-state-text">No events scheduled for the selected month and filters.</p>
                        @if($filtered)
                            <a href="{{ route('calendar.index', ['month' => $month, 'year' => $year, 'view' => $viewMode]) }}" class="btn btn-secondary btn-sm mt-4">Show all fellowships &amp; venues</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ═══ Side rail ═══ --}}
        <aside class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-1 items-start">
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Coming up</h2>
                    <span class="text-[12.5px] text-slate-500">From today</span>
                </div>
                <ul class="p-2">
                    @forelse($upcoming as $event)
                        <li>
                            <button type="button" data-event="{{ $payload($event) }}" onclick="openEvent(this)" class="w-full text-left flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="date-leaf !w-11">
                                    <span class="date-leaf-m" style="background: {{ $event->club?->color_hex ?: '#DC2626' }}">{{ $event->event_date->format('M') }}</span>
                                    <span class="date-leaf-d !text-[1.1rem] !pb-1.5">{{ $event->event_date->format('j') }}</span>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-[13.5px] font-semibold text-slate-900 truncate">{{ $event->title }}</span>
                                    <span class="block text-[12.5px] text-slate-500 truncate">{{ $event->event_date->isToday() ? 'Today' : ($event->event_date->isTomorrow() ? 'Tomorrow' : $event->event_date->format('D')) }} · {{ substr($event->start_time, 0, 5) }} · {{ $event->venue?->name ?? ($event->venue_name ?: 'TBC') }}</span>
                                </span>
                            </button>
                        </li>
                    @empty
                        <li class="px-3 py-6 text-center text-[13.5px] text-slate-500">Nothing scheduled yet.</li>
                    @endforelse
                </ul>
            </section>

            <section class="card overflow-hidden">
                <div class="p-5 flex items-start gap-3.5">
                    <span class="icon-tile" style="--tile: var(--color-brand)"><x-icon name="phone" /></span>
                    <div class="min-w-0">
                        <h2 class="card-title">On your phone</h2>
                        <p class="mt-1 text-[13.5px] text-slate-600 leading-relaxed">Subscribe once and every approved activity shows up in Google, Apple or Outlook calendar automatically.</p>
                        <button type="button" onclick="openSubscribe()" class="btn btn-primary btn-sm mt-3">Add to my phone</button>
                    </div>
                </div>
            </section>

            <button type="button" onclick="showLiturgicalInfo()" class="card w-full text-left overflow-hidden cursor-pointer group">
                <span class="block h-1.5" style="background: {{ $season['hex'] ?? '#15803D' }}" aria-hidden="true"></span>
                <span class="block p-5">
                    <span class="block text-[12.5px] font-semibold text-slate-500">Liturgical season</span>
                    <span class="mt-1 block text-[15px] font-bold text-slate-900">{{ $season['title'] }}</span>
                    <span class="mt-0.5 inline-flex items-center gap-1.5 text-[12.5px] text-slate-600"><span class="w-2 h-2 rounded-full" style="background: {{ $season['hex'] ?? '#15803D' }}"></span>{{ $season['color'] ?? 'Ordinary Time' }}</span>
                    <span class="mt-3 block font-display italic text-[14px] leading-snug text-slate-600 line-clamp-3">{{ $season['verse'] }}</span>
                    <span class="mt-3 inline-flex items-center gap-1 text-[12.5px] font-semibold text-brand">Liturgical Guide <x-icon name="chevron-right" class="w-3.5 h-3.5" /></span>
                </span>
            </button>
        </aside>
    </div>
</div>

@include('calendar.partials.event-drawer')

{{-- ═══ Subscribe dialog ═══ --}}
<div id="subscribeModal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-ink/40 backdrop-blur-[2px] sm:p-4 modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="subscribeTitle" onclick="if (event.target === this) closeSubscribe()">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl shadow-lg w-full sm:max-w-2xl max-h-[92vh] overflow-y-auto modal-panel">
        <div class="px-6 pt-6 pb-2 flex items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Subscribe to the chapel calendar</p>
                <h3 id="subscribeTitle" class="text-[1.35rem] font-bold text-slate-900 leading-tight">Put the chapel calendar on your phone</h3>
            </div>
            <button type="button" onclick="closeSubscribe()" class="topbar-action -mr-2 -mt-1 shrink-0" aria-label="Close"><x-icon name="x" /></button>
        </div>
        <div class="px-6 pb-6">
            <x-calendar-subscribe />
        </div>
    </div>
</div>

<script>
    (function () {
        let lastFocus = null;
        const $ = (id) => document.getElementById(id);
        const safeGet = (k) => { try { return localStorage.getItem(k); } catch (e) { return null; } };
        const safeSet = (k, v) => { try { localStorage.setItem(k, v); } catch (e) {} };

        window.openSubscribe = function () {
            lastFocus = document.activeElement;
            $('subscribeModal').classList.remove('hidden');
            dismissNudge();
        };
        window.closeSubscribe = function () {
            $('subscribeModal').classList.add('hidden');
            if (lastFocus) lastFocus.focus();
        };
        window.dismissNudge = function () {
            safeSet('chapel_cal_nudge', 'done');
            $('subscribeNudge').classList.add('hidden');
            $('subscribeNudge').classList.remove('flex');
        };
        if (safeGet('chapel_cal_nudge') !== 'done') {
            $('subscribeNudge').classList.remove('hidden');
            $('subscribeNudge').classList.add('flex');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            if (!$('subscribeModal').classList.contains('hidden')) closeSubscribe();
        });
    })();
</script>
@endsection
