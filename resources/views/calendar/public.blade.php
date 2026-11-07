<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#15121A">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Public Calendar Feed | Kwang-Lim Chapel</title>

    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-paper text-slate-800 antialiased">

    {{-- ═══ Header band ═══ --}}
    <header class="relative grain overflow-hidden text-white on-dark"
            style="background: radial-gradient(110% 120% at 0% 0%, rgb(165 28 48 / .28), transparent 55%), radial-gradient(80% 90% at 100% 100%, rgb(184 137 43 / .12), transparent 65%), linear-gradient(180deg, #17131A, var(--color-ink));">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            {{-- Masthead --}}
            <div class="flex items-center justify-between gap-4 py-5">
                <a href="{{ route('calendar.public') }}" class="flex items-center gap-3 min-w-0">
                    <span class="relative w-10 h-10 shrink-0 rounded-full grid place-items-center"
                          style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px #17131A, 0 0 0 4px rgb(217 183 108 / .45);">
                        <x-umc-logo class="w-[1.15rem] h-[1.15rem]" />
                    </span>
                    <span class="min-w-0 leading-tight">
                        <span class="block text-[9.5px] font-bold uppercase tracking-[0.24em] text-gold-light/90">Chaplaincy Office</span>
                        <span class="block font-display text-[16px] text-white truncate">Kwang-Lim Chapel</span>
                    </span>
                </a>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('calendar.feed_ics') }}" download="kwang-lim-chapel.ics" title="Subscribe in Google Calendar, Outlook, or Apple Calendar" class="btn btn-glass btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="hidden sm:inline">Subscribe (.ics)</span>
                        <span class="sm:hidden">.ics</span>
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-gold btn-sm">
                        Sign In
                    </a>
                </div>
            </div>

            {{-- Title --}}
            <div class="pt-8 pb-10 sm:pt-12 sm:pb-14 border-t border-white/[0.07]">
                <p class="eyebrow eyebrow-gold">Master Calendar</p>
                <h1 class="font-display text-[2.1rem] sm:text-[3rem] leading-[1.04] tracking-[-0.03em] text-white max-w-3xl" style="font-weight: 400 !important; --wght: 400; --soft: 100;" data-ink>
                    Kwang-Lim Chapel Master Calendar
                </h1>
                <p class="mt-4 max-w-2xl text-[14px] leading-relaxed text-white/60">
                    Official read-only feed synchronized with Chaplaincy Office records.
                </p>
                <p class="mt-5 inline-flex items-center gap-2 text-[11.5px] font-semibold text-white/45 tabular-nums">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold-light breathe" aria-hidden="true"></span>
                    Current Time: {{ now()->format('D, M d, Y H:i') }} (CAT)
                </p>
            </div>
        </div>
        <div class="h-px" style="background: linear-gradient(90deg, transparent, var(--color-gold) 30%, var(--color-gold-light) 50%, var(--color-gold) 70%, transparent); opacity: .55" aria-hidden="true"></div>
    </header>

    {{-- ═══ Activities ═══ --}}
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10 w-full flex-1">

        <div class="flex items-end justify-between gap-4 mb-5">
            <div>
                <p class="card-kicker">Upcoming</p>
                <h2 class="font-display text-[1.5rem] leading-tight text-slate-900 tracking-[-0.02em]">Approved University Spiritual &amp; Ministry Activities</h2>
            </div>
        </div>

        <div class="card overflow-hidden divide-y divide-slate-100">
            @forelse($events as $event)
                <div class="px-4 py-5 sm:px-6 hover:bg-slate-50/60 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        {{-- Date tile --}}
                        <div class="w-14 shrink-0 text-center rounded-xl bg-white ring-1 ring-slate-200 shadow-2xs overflow-hidden">
                            <div class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-white bg-brand py-0.5">{{ $event->event_date->format('M') }}</div>
                            <div class="font-display text-[1.55rem] text-slate-900 leading-none pt-1.5 tabular-nums">{{ $event->event_date->format('d') }}</div>
                            <div class="text-[9.5px] font-semibold uppercase tracking-wider text-slate-400 pb-1.5">{{ $event->event_date->format('D') }}</div>
                        </div>

                        <div class="min-w-0 space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge badge-dot" style="color: {{ $event->club?->color_hex ?? '#A51C30' }}">
                                    <span class="text-slate-700">{{ $event->club?->name }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 text-[12px] font-semibold text-slate-500 tabular-nums">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ substr($event->start_time, 0, 5) }} – {{ substr($event->end_time, 0, 5) }}
                                </span>
                            </div>
                            <h3 class="font-display text-[1.125rem] leading-snug text-slate-900">{{ $event->title }}</h3>
                            <div class="text-[12.5px] text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Venue: <strong class="font-semibold text-slate-700">{{ $event->venue ? $event->venue?->name : $event->venue_name }}</strong></span>
                                </span>
                                @if($event->expected_attendance)
                                    <span class="tabular-nums">Expected: <strong class="font-semibold text-slate-700">{{ $event->expected_attendance }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 pl-[4.5rem] sm:pl-0">
                        <span class="badge badge-emerald badge-dot">Approved &bull; Cleared</span>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state-mark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="empty-state-title">No Upcoming Activities Scheduled</p>
                    <p class="empty-state-text">All newly approved club requests and chapel convocations appear automatically on this calendar.</p>
                </div>
            @endforelse
        </div>

    </main>

    {{-- ═══ Footer ═══ --}}
    <footer class="border-t border-slate-200/70 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center">
            <div class="rule-ornament text-[9px] max-w-[12rem] mx-auto mb-4" aria-hidden="true">&#9670;</div>
            <p class="font-display text-[14px] text-slate-700">Chaplaincy Office &bull; Kwang-Lim Chapel</p>
            <p class="text-[11px] text-slate-400 mt-1">&copy; {{ date('Y') }} Kwang-Lim Chapel Center. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
