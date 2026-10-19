<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#DC2626">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <title>Public Calendar Feed | Kwang-Lim Chapel</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-[#DC2626] selection:text-white">

    <!-- Top Header -->
    <header class="bg-[#DC2626] text-white border-b border-white/10 shadow-sm py-4">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center p-1.5 shadow-xs backdrop-blur-md">
                    <x-umc-logo class="w-full h-full" />
                </div>
                <div>
                    <span class="text-xs uppercase tracking-widest text-rose-200 font-bold">Chaplaincy Office</span>
                    <h1 class="text-base sm:text-lg font-bold text-white tracking-tight">Kwang-Lim Chapel Master Calendar</h1>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('calendar.feed_ics') }}" download="kwang-lim-chapel.ics" title="Subscribe in Google Calendar, Outlook, or Apple Calendar" class="text-xs bg-white/10 hover:bg-white/20 text-white font-bold px-3.5 py-1.5 rounded-full border border-white/20 transition flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Subscribe (.ics)</span>
                </a>
                <a href="{{ route('login') }}" class="text-xs bg-white text-[#DC2626] font-bold px-4 py-1.5 rounded-full uppercase tracking-wider shadow-xs hover:bg-slate-100 active:scale-95">
                    Sign In
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6 w-full flex-1 space-y-6">
        
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
            <div>
                <h2 class="text-base font-bold text-slate-900">Approved University Spiritual & Ministry Activities</h2>
                <p class="text-xs text-slate-500 mt-0.5">Official read-only feed synchronized with Chaplaincy Office records.</p>
            </div>
            <div class="text-xs text-slate-500 font-mono font-semibold">
                Current Time: {{ now()->format('D, M d, Y H:i') }} (CAT)
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden divide-y divide-slate-100 shadow-xs">
            @forelse($events as $event)
                <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start space-x-4">
                        <!-- Date Badge -->
                        <div class="w-14 h-14 rounded-2xl bg-[#DC2626] text-white text-center flex flex-col justify-center shrink-0 shadow-xs">
                            <span class="text-[10px] uppercase font-bold text-rose-200">{{ $event->event_date->format('M') }}</span>
                            <span class="text-xl font-bold text-white leading-none">{{ $event->event_date->format('d') }}</span>
                            <span class="text-[9px] text-rose-200 font-medium">{{ $event->event_date->format('D') }}</span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded text-white" style="background-color: {{ $event->club->color_hex ?? '#DC2626' }}">
                                    {{ $event->club->name }}
                                </span>
                                <span class="text-xs font-bold text-slate-600 font-mono">
                                    ⏰ {{ substr($event->start_time, 0, 5) }} - {{ substr($event->end_time, 0, 5) }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">{{ $event->title }}</h3>
                            <div class="text-xs text-slate-500">
                                📍 Venue: <strong>{{ $event->venue ? $event->venue->name : $event->venue_name }}</strong>
                                @if($event->expected_attendance)
                                    &bull; Expected: <strong>{{ $event->expected_attendance }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center space-x-2">
                        <span class="text-xs px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold uppercase tracking-wider">
                            Approved &bull; Cleared
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-500 space-y-2">
                    <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div class="font-bold text-slate-700">No Upcoming Activities Scheduled</div>
                    <p class="text-xs text-slate-400">All newly approved club requests and chapel convocations appear automatically on this calendar.</p>
                </div>
            @endforelse
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <p>Chaplaincy Office &bull; Kwang-Lim Chapel</p>
        <p class="text-[11px] text-slate-400 mt-1">&copy; {{ date('Y') }} Kwang-Lim Chapel Center. All rights reserved.</p>
    </footer>

</body>
</html>
