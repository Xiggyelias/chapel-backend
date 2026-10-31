<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gatekeeper Verification - Kwang-Lim Chapel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0F172A; }
        .font-cinzel { font-family: 'Plus Jakarta Sans', sans-serif !important; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="p-4 sm:p-8 flex flex-col items-center justify-center min-h-screen text-slate-100">

    <div class="max-w-md w-full space-y-5">
        
        <!-- Institutional Security Header -->
        <div class="text-center space-y-1.5">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#DC2626] border border-white/20 p-2 mb-1 shadow-lg shadow-black/40">
                <x-umc-logo class="w-full h-full" />
            </div>
            <div class="text-xs uppercase tracking-widest text-rose-200 font-bold">Kwang-Lim Chapel</div>
            <h1 class="text-xl font-bold text-white">Campus Security & Gatekeeper Verification</h1>
            <p class="text-xs text-slate-400">Kwang-Lim Chapel Facilities Dispatch</p>
        </div>

        @if($isValid && $requestItem)
            <!-- VALID CLEARANCE PASS CARD -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-emerald-500 text-slate-900">
                
                <!-- Status Top Banner -->
                <div class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-700 text-white p-6 text-center space-y-1.5">
                    <div class="w-14 h-14 mx-auto rounded-full bg-white/20 flex items-center justify-center border-2 border-white/50 mb-1 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="text-[10px] uppercase font-black tracking-widest text-emerald-100">Instant Cryptographic Verification</div>
                    <h2 class="text-xl font-black tracking-tight font-cinzel">OFFICIALLY AUTHORIZED</h2>
                    <div class="inline-block px-3 py-1 rounded-full bg-black/20 text-xs font-mono text-emerald-100">
                        Ref: {{ $requestItem->reference_no }}
                    </div>
                </div>

                <!-- Event Particulars -->
                <div class="p-6 space-y-4 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Activity Title</span>
                        <div class="text-base font-bold text-slate-900 mt-0.5 leading-snug">
                            {{ $requestItem->title }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Sponsoring Club</span>
                            <span class="font-bold text-slate-900 text-xs block truncate">{{ $requestItem->club?->name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Approved Venue</span>
                            <span class="font-bold text-[#DC2626] text-xs block truncate">
                                📍 {{ $requestItem->venue ? $requestItem->venue?->name : $requestItem->venue_other }}
                            </span>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-slate-200">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Permitted Date & Hours</span>
                            <span class="font-bold text-slate-900 text-xs">
                                {{ $requestItem->event_date->format('l, M d, Y') }} &bull; {{ substr($requestItem->start_time, 0, 5) }} to {{ substr($requestItem->end_time, 0, 5) }} CAT
                            </span>
                        </div>
                    </div>

                    <!-- Security Clearance Directives -->
                    <div class="p-4 bg-emerald-50/80 rounded-2xl border border-emerald-200 text-emerald-950 space-y-2">
                        <div class="font-bold uppercase tracking-wider text-[10px] text-emerald-800 flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Gatekeeper Directives & Instructions</span>
                        </div>
                        <ul class="text-[11px] list-disc list-inside space-y-1 text-emerald-900">
                            <li>Permit sanctuary & venue access for authorized club executives and attendees.</li>
                            <li>Sound & amplification curfew: <strong class="text-rose-700">Strictly 22:00 CAT</strong>.</li>
                            <li>Sound truck/instrument load-in and campus parking authorized.</li>
                        </ul>
                    </div>

                    <!-- Authorization Metadata -->
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                        <div>
                            <span>Approved by:</span>
                            <strong class="text-slate-800 block">{{ $requestItem->decidedBy ? $requestItem->decidedBy?->name : 'Chaplaincy Office' }}</strong>
                        </div>
                        <div class="text-right">
                            <span>Expected Headcount:</span>
                            <strong class="text-slate-800 block">{{ $requestItem->expected_attendance ?: 'Standard' }}</strong>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="bg-slate-50 p-3.5 text-center text-[10px] text-slate-500 border-t border-slate-200 font-mono">
                    SECURITY AUDIT TIMESTAMP: {{ now()->timezone('Africa/Harare')->format('Y-m-d H:i:s') }} CAT
                </div>

            </div>
        @else
            <!-- UNAUTHORIZED / INVALID PASS CARD -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-rose-500 text-slate-900">
                <div class="bg-gradient-to-r from-rose-700 to-rose-600 text-white p-6 text-center space-y-2">
                    <div class="w-14 h-14 mx-auto rounded-full bg-white/20 flex items-center justify-center border-2 border-white/50 mb-1 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="text-[10px] uppercase font-black tracking-widest text-rose-100">Verification Alert</div>
                    <h2 class="text-xl font-black tracking-tight font-cinzel">UNAUTHORIZED / INVALID</h2>
                    <div class="inline-block px-3 py-1 rounded-full bg-black/20 text-xs font-mono text-rose-100">
                        Scanned Code: {{ $code }}
                    </div>
                </div>

                <div class="p-6 space-y-4 text-xs">
                    <div class="p-4 bg-rose-50 rounded-2xl border border-rose-200 text-rose-950 space-y-1.5">
                        <strong class="block font-bold text-rose-900 text-xs">NO APPROVED CLEARANCE FOUND:</strong>
                        <p class="text-slate-700 leading-relaxed text-xs">
                            This clearance code is either unrecognized, has not completed formal Chaplaincy review, or was declined. Gatekeepers and campus security personnel are instructed <strong>NOT</strong> to open chapel venues or permit off-campus bus dispatch for this event.
                        </p>
                    </div>

                    <div class="text-center pt-2">
                        <a href="{{ route('calendar.public') }}" class="inline-block px-5 py-2.5 bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold rounded-xl text-xs transition shadow-sm">
                            Check Official Public Calendar &rarr;
                        </a>
                    </div>
                </div>

                <div class="bg-slate-50 p-3.5 text-center text-[10px] text-slate-500 border-t border-slate-200 font-mono">
                    Chaplaincy Hotline Ext. 2045 &bull; Campus Security Control Ext. 2222
                </div>
            </div>
        @endif

        <div class="text-center text-xs text-slate-400 pt-1">
            Kwang-Lim Chapel &bull; Valley of Hope
        </div>

    </div>

</body>
</html>
