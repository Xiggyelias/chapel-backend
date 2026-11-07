<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FFFFFF">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Public Calendar Feed | Kwang-Lim Chapel</title>

    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
    $byDay = $events->groupBy(fn ($e) => $e->event_date->toDateString());
@endphp
<body class="workspace min-h-full flex flex-col bg-paper text-slate-800 antialiased">
    <div class="season-strip" style="--season: {{ $season['hex'] }}" aria-hidden="true"></div>

    {{-- ═══ Masthead ═══ --}}
    <header class="bg-white border-b border-slate-200/80">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('calendar.public') }}" class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 shrink-0 rounded-xl grid place-items-center bg-brand shadow-xs">
                    <x-umc-logo class="w-[1.1rem] h-[1.1rem]" />
                </span>
                <span class="min-w-0 leading-tight">
                    <span class="block font-display text-[16px] text-slate-900 truncate">Kwang-Lim Chapel</span>
                    <span class="block text-[11.5px] font-medium text-slate-500 truncate">Chaplaincy Office · Africa University</span>
                </span>
            </a>
            <a href="{{ route('login') }}" class="btn btn-secondary btn-sm shrink-0">Sign In</a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-10 w-full flex-1 space-y-8">

        {{-- ═══ Title + subscribe ═══ --}}
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)] items-start">
            <div>
                <p class="eyebrow">Master Calendar</p>
                <h1 class="font-display text-[2rem] sm:text-[2.6rem] leading-[1.08] tracking-[-0.025em] text-slate-900" data-ink>
                    Kwang-Lim Chapel Master Calendar
                </h1>
                <p class="mt-3 text-[15px] leading-relaxed text-slate-600 max-w-md">
                    Every approved worship service and fellowship activity, kept up to date by the Chaplaincy Office. Tap an activity to add it to your phone.
                </p>
                <p class="mt-4 inline-flex items-center gap-2 text-[13px] font-semibold text-slate-500 tabular-nums">
                    <span class="w-2 h-2 rounded-full" style="background: {{ $season['hex'] }}" aria-hidden="true"></span>
                    {{ now()->format('l, j F Y · H:i') }} (CAT) · {{ $season['name'] }}
                </p>
            </div>

            <div class="card p-5 sm:p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="icon-tile" style="--tile: var(--color-brand)"><x-icon name="phone" /></span>
                    <div>
                        <h2 class="card-title">Get every activity on your phone</h2>
                        <p class="text-[13px] text-slate-500">Subscribe once. It updates itself.</p>
                    </div>
                </div>
                <x-calendar-subscribe compact />
            </div>
        </section>

        {{-- ═══ Activities ═══ --}}
        <section>
            <div class="flex items-end justify-between gap-4 mb-3">
                <div>
                    <p class="card-kicker">Upcoming</p>
                    <h2 class="text-[1.2rem] font-bold leading-tight text-slate-900">Approved University Spiritual &amp; Ministry Activities</h2>
                </div>
                <span class="hidden sm:inline badge tabular-nums">{{ $events->count() }} {{ Str::plural('activity', $events->count()) }}</span>
            </div>

            <div class="card overflow-hidden">
                @forelse($byDay as $date => $dayEvents)
                    @php $d = \Carbon\Carbon::parse($date); @endphp
                    <h3 class="sticky top-0 z-[1] flex items-center gap-2 px-4 sm:px-5 py-2 bg-slate-50/95 backdrop-blur border-b border-slate-100 text-[13px] font-bold {{ $d->isToday() ? 'text-brand' : 'text-slate-700' }}">
                        {{ $d->format('l, j F Y') }}
                        @if($d->isToday())<span class="badge badge-brand">Today</span>@elseif($d->isTomorrow())<span class="badge">Tomorrow</span>@endif
                    </h3>
                    <ul class="divide-y divide-slate-100 border-b border-slate-100 last:border-b-0">
                        @foreach($dayEvents as $event)
                            <li>
                                <button type="button" data-event="{{ \App\Support\CalendarLinks::payload($event) }}" onclick="openEvent(this)"
                                        class="w-full text-left flex items-start gap-3.5 px-4 sm:px-5 py-4 hover:bg-slate-50 transition-colors cursor-pointer">
                                    <span class="w-12 shrink-0 pt-0.5 text-[13.5px] font-bold text-slate-800 tabular-nums">{{ substr($event->start_time, 0, 5) }}<span class="block text-[12px] font-medium text-slate-400">{{ substr($event->end_time, 0, 5) }}</span></span>
                                    <span class="w-1 self-stretch rounded-full shrink-0" style="background: {{ $event->club?->color_hex ?: '#DC2626' }}" aria-hidden="true"></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-[15px] font-semibold text-slate-900">{{ $event->title }}</span>
                                        <span class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[13px] text-slate-500">
                                            <span class="inline-flex items-center gap-1"><x-icon name="pin" class="w-3.5 h-3.5 text-slate-400" />Venue: <strong class="font-semibold text-slate-700">{{ $event->venue ? $event->venue?->name : $event->venue_name }}</strong></span>
                                            <span>{{ $event->club?->name }}</span>
                                            @if($event->expected_attendance)<span class="tabular-nums">Expected: <strong class="font-semibold text-slate-700">{{ $event->expected_attendance }}</strong></span>@endif
                                        </span>
                                        <span class="mt-2 inline-flex badge badge-emerald badge-dot">Approved &bull; Cleared</span>
                                    </span>
                                    <span class="hidden sm:inline-flex btn btn-secondary btn-sm shrink-0 self-center pointer-events-none"><x-icon name="calendar-plus" /> Add to calendar</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-mark"><x-icon name="calendar" class="w-6 h-6" /></div>
                        <p class="empty-state-title">No Upcoming Activities Scheduled</p>
                        <p class="empty-state-text">All newly approved club requests and chapel convocations appear automatically on this calendar.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200/80 bg-white py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-center">
            <p class="text-[13px] font-semibold text-slate-700">Chaplaincy Office &bull; Kwang-Lim Chapel</p>
            <p class="text-[12px] text-slate-500">&copy; {{ date('Y') }} Kwang-Lim Chapel Center. All rights reserved.</p>
        </div>
    </footer>

    @include('calendar.partials.event-drawer')
</body>
</html>
