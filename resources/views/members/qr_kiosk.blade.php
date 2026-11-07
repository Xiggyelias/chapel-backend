<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#15121A">
    <title>{{ $club->name }} - QR Check-In Kiosk</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .kiosk-light {
            background:
                radial-gradient(60% 50% at 78% 40%, rgb(217 183 108 / .10), transparent 70%),
                radial-gradient(50% 45% at 10% 0%, rgb(165 28 48 / .16), transparent 70%);
        }
        .kiosk-title { font-variation-settings: 'SOFT' 100, 'opsz' 144; }
    </style>
</head>
<body class="min-h-full bg-ink text-white antialiased">
<div class="relative min-h-screen grain on-dark flex flex-col overflow-hidden">
    <div class="kiosk-light absolute inset-0 pointer-events-none" aria-hidden="true"></div>

    {{-- Top bar --}}
    <header class="relative z-10 w-full max-w-6xl mx-auto px-5 sm:px-8 pt-5 sm:pt-7 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('members.index', ['club_id' => $club->id]) }}" class="inline-flex items-center gap-2 text-[12.5px] font-semibold text-white/60 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.5 19.5L3 12l7.5-7.5M3 12h18"/></svg>
            Back to Member Roster
        </a>
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11.5px] font-semibold text-emerald-200 bg-emerald-400/10 ring-1 ring-emerald-300/25">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 breathe" style="--breathe: 52 211 153" aria-hidden="true"></span>
                Live Attendance Active
            </span>
            <a href="{{ route('members.scanner', $club->id) }}" class="btn btn-glass btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Launch Camera Scanner
            </a>
        </div>
    </header>

    {{-- Kiosk --}}
    <main class="relative z-10 flex-1 w-full max-w-6xl mx-auto px-5 sm:px-8 py-10 lg:py-14 grid lg:grid-cols-[minmax(0,1fr)_auto] gap-10 lg:gap-16 items-center">

        {{-- Welcome + headcount --}}
        <div class="min-w-0 text-center lg:text-left order-2 lg:order-1" data-reveal>
            <p class="text-[11px] sm:text-[12px] font-bold uppercase tracking-[0.26em] text-gold-light/90">Africa University Chaplaincy</p>
            <h1 class="kiosk-title mt-4 font-display text-[2.6rem] sm:text-[3.6rem] xl:text-[4.5rem] leading-[1.02] tracking-[-0.03em] text-white text-balance" data-ink>
                {{ $club->name }}
            </h1>
            <p class="mt-4 text-[15px] sm:text-[17px] text-white/60">
                Fellowship Meeting Check-In <span class="text-gold-light/60 mx-1.5" aria-hidden="true">&#9670;</span> {{ date('l, M d, Y') }}
            </p>

            <div class="mt-8 lg:mt-10 h-px w-24 mx-auto lg:mx-0 bg-gradient-to-r from-gold-light/70 to-transparent" aria-hidden="true"></div>

            <div class="mt-8 lg:mt-10 flex flex-col sm:flex-row items-center lg:items-end justify-center lg:justify-start gap-3 sm:gap-6">
                <p class="text-[12px] font-bold uppercase tracking-[0.2em] text-white/45 sm:pb-3 sm:order-2">Total checked in today</p>
                <p class="font-display text-white leading-none sm:order-1" id="headcountBadge"><span class="text-[4.5rem] sm:text-[6rem] tabular-nums tracking-[-0.04em]">{{ $attendeesToday->count() }}</span> <span class="ml-1 text-[1.25rem] sm:text-[1.5rem] text-white/50 italic">attendees</span></p>
            </div>

            <p class="mt-8 max-w-lg mx-auto lg:mx-0 text-[14px] leading-relaxed text-white/55">
                Students scan with their default camera to automatically mark attendance. Or
                <a href="{{ route('members.self_checkin', $token) }}" target="_blank" class="text-gold-light underline decoration-gold-light/40 underline-offset-4 hover:decoration-gold-light">click to simulate student check-in</a>.
            </p>
        </div>

        {{-- QR plate --}}
        <div class="order-1 lg:order-2 flex flex-col items-center" data-reveal>
            <p class="mb-4 text-[12px] sm:text-[13px] font-bold uppercase tracking-[0.24em] text-white/70">Scan with phone camera</p>
            <a href="{{ route('members.self_checkin', $token) }}" target="_blank" rel="noopener" title="Open the student check-in page"
               class="group relative block rounded-[1.75rem] bg-white p-5 sm:p-6 ring-1 ring-gold-light/40 transition duration-300 hover:-translate-y-0.5 focus-visible:outline-gold-light"
               style="box-shadow: 0 0 0 6px rgb(255 255 255 / .04), 0 0 0 7px rgb(217 183 108 / .22), 0 40px 80px -30px rgb(0 0 0 / .8), 0 0 90px -20px rgb(217 183 108 / .25);">
                <x-qr-code :value="route('members.self_checkin', $token)" size="240" label="Attendance check-in QR code" class="mx-auto w-[240px] sm:w-[280px] [&_svg]:w-full [&_svg]:h-auto" />
            </a>
            <p class="mt-5 text-[11.5px] font-mono tracking-[0.14em] text-white/40">MEETING TOKEN: <span class="text-white/70">{{ $token }}</span></p>
        </div>
    </main>

    {{-- Live feed --}}
    <section class="relative z-10 w-full max-w-6xl mx-auto px-5 sm:px-8 pb-6" aria-labelledby="kioskFeedTitle">
        <div class="rounded-2xl bg-white/[0.035] ring-1 ring-white/10 backdrop-blur-sm">
            <div class="px-5 py-3.5 flex items-center justify-between gap-3 border-b border-white/[0.07]">
                <h2 id="kioskFeedTitle" class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/50">Live Check-in Feed</h2>
                <span class="text-[11px] text-white/35">Times in CAT</span>
            </div>
            <div class="max-h-48 overflow-y-auto px-5 py-2 grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8">
                @forelse($attendeesToday as $att)
                    <div class="flex items-center justify-between gap-3 py-2.5 border-b border-white/[0.06]">
                        <span class="text-[15px] font-display text-white/90 truncate">{{ $att->member?->full_name }}</span>
                        <span class="text-[11px] text-white/40 tabular-nums shrink-0">{{ $att->updated_at->timezone('Africa/Harare')->format('H:i') }} CAT</span>
                    </div>
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 text-center py-5 font-display italic text-[15px] text-white/40">Awaiting first attendee scan...</div>
                @endforelse
            </div>
        </div>
    </section>

    <footer class="relative z-10 pb-6 text-center text-[11.5px] tracking-[0.08em] text-white/35">
        Kwang-Lim Chapel <span class="text-gold-light/50 mx-1.5" aria-hidden="true">&#9670;</span> Valley of Hope
    </footer>
</div>
</body>
</html>
