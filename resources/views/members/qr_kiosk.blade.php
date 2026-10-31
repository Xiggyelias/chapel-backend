<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $club->name }} - QR Check-In Kiosk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0F172A; }
        .font-cinzel { font-family: 'Cinzel', Georgia, serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen text-slate-100 p-4 sm:p-8 flex flex-col items-center justify-between">

    <!-- Top Header -->
    <div class="max-w-2xl w-full flex items-center justify-between pb-4 border-b border-white/10">
        <a href="{{ route('members.index', ['club_id' => $club->id]) }}" class="text-xs text-rose-200 hover:underline flex items-center space-x-1 font-semibold">
            <span>&larr; Back to Member Roster</span>
        </a>
        <div class="flex items-center space-x-3">
            <a href="{{ route('members.scanner', $club->id) }}" class="px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold text-white border border-white/20 transition flex items-center space-x-1.5 shadow-xs">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Launch Camera Scanner</span>
            </a>
            <span class="text-xs font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-3 py-1 rounded-full font-bold">
                Live Attendance Active
            </span>
        </div>
    </div>

    <!-- Center Kiosk Card -->
    <div class="max-w-md w-full bg-white text-slate-900 rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-6">
        
        <!-- Card Banner -->
        <div class="bg-gradient-to-br from-[#DC2626] via-[#B91C1C] to-[#991B1B] text-white p-6 text-center border-b border-white/10">
            <span class="text-[10px] font-mono uppercase tracking-widest text-rose-200 font-bold block mb-1">
                Africa University Chaplaincy
            </span>
            <h1 class="text-xl sm:text-2xl font-bold font-cinzel text-white leading-tight">
                {{ $club->name }}
            </h1>
            <p class="text-xs text-red-100 mt-1">Fellowship Meeting Check-In &bull; {{ date('l, M d, Y') }}</p>
        </div>

        <div class="p-6 text-center space-y-5">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">Scan with Phone Camera</span>
                <a href="{{ route('members.self_checkin', $token) }}" target="_blank" rel="noopener" title="Open the student check-in page" class="inline-block p-4 bg-slate-50 border-2 border-dashed border-[#DC2626]/40 rounded-3xl hover:scale-105 transition group cursor-pointer shadow-inner">
                    <x-qr-code :value="route('members.self_checkin', $token)" size="240" label="Attendance check-in QR code" class="mx-auto" />
                </a>
            </div>

            <div class="space-y-1">
                <span class="text-[11px] font-mono text-slate-400 block font-bold">MEETING TOKEN: {{ $token }}</span>
                <p class="text-xs text-slate-500">
                    Students scan with their default camera to automatically mark attendance. Or <a href="{{ route('members.self_checkin', $token) }}" target="_blank" class="text-[#DC2626] font-bold underline">click to simulate student check-in</a>.
                </p>
            </div>

            <!-- Checked In Headcount -->
            <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center justify-between text-xs">
                <span class="font-bold text-emerald-900">Total Checked In Today:</span>
                <span class="font-mono font-black text-emerald-800 text-base" id="headcountBadge">{{ $attendeesToday->count() }} attendees</span>
            </div>

        </div>

        <!-- Recent Checked In Stream -->
        <div class="bg-slate-50 border-t border-slate-200 p-4 max-h-48 overflow-y-auto space-y-1.5 text-xs text-left">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Live Check-in Feed</span>
            @forelse($attendeesToday as $att)
                <div class="flex items-center justify-between py-1.5 border-b border-slate-200/60 last:border-0">
                    <span class="font-semibold text-slate-900">{{ $att->member?->full_name }}</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $att->updated_at->timezone('Africa/Harare')->format('H:i') }} CAT</span>
                </div>
            @empty
                <div class="text-center py-3 text-slate-400 text-xs italic">Awaiting first attendee scan...</div>
            @endforelse
        </div>

    </div>

    <div class="text-xs text-slate-400 text-center">
        Kwang-Lim Chapel &bull; Valley of Hope
    </div>

</body>
</html>
