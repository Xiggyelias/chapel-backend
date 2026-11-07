<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gatekeeper Verification - Kwang-Lim Chapel</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative grain bg-nave p-4 sm:p-8 flex flex-col items-center justify-center min-h-screen text-slate-100 on-dark"
      style="background-image: radial-gradient(90% 55% at 50% 0%, rgb(220 38 38 / 0.28), transparent 70%), radial-gradient(70% 40% at 50% 100%, rgb(184 137 43 / 0.10), transparent 70%);">

    <div class="max-w-md w-full space-y-6">

        <!-- Institutional Security Header -->
        <header class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand ring-1 ring-white/15 p-2 mb-3 shadow-lg shadow-black/40">
                <x-umc-logo class="w-full h-full" />
            </div>
            <div class="text-[10.5px] uppercase tracking-[0.22em] text-gold-light font-bold">Kwang-Lim Chapel</div>
            <h1 class="font-display text-[1.5rem] leading-tight text-white mt-1.5 text-balance">Campus Security &amp; Gatekeeper Verification</h1>
            <p class="text-[12px] text-white/45 mt-1.5">Kwang-Lim Chapel Facilities Dispatch</p>
        </header>

        @if($isValid && $requestItem)
            <!-- VALID CLEARANCE PASS CARD -->
            <main class="bg-white rounded-[1.375rem] shadow-lg overflow-hidden ring-1 ring-emerald-500/40 text-slate-900">

                <!-- Status Top Banner -->
                <div class="relative bg-emerald-700 text-white px-6 pt-7 pb-6 text-center"
                     style="background-image: radial-gradient(100% 80% at 50% 0%, rgb(255 255 255 / 0.12), transparent 70%);">
                    <div class="w-14 h-14 mx-auto rounded-full bg-white/15 ring-1 ring-white/40 grid place-items-center mb-3">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="text-[10px] uppercase font-bold tracking-[0.2em] text-emerald-100/90">Instant Cryptographic Verification</div>
                    <h2 class="mt-1.5 text-[1.375rem] font-extrabold tracking-[0.04em]">OFFICIALLY AUTHORIZED</h2>
                    <div class="mt-3 inline-block px-3 py-1 rounded-full bg-black/20 text-[11.5px] font-semibold tabular-nums text-emerald-50">
                        Ref: {{ $requestItem->reference_no }}
                    </div>
                </div>

                <!-- Event Particulars -->
                <div class="p-6 sm:p-7 space-y-5 text-[13px]">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 block">Activity Title</span>
                        <div class="font-display text-[1.25rem] text-slate-900 mt-1 leading-snug">
                            {{ $requestItem->title }}
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-x-4 gap-y-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-black/5">
                        <div class="min-w-0">
                            <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">Sponsoring Club</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900 truncate">{{ $requestItem->club?->name }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">Approved Venue</dt>
                            <dd class="mt-0.5 flex items-center gap-1 font-semibold text-brand min-w-0">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                <span class="truncate">{{ $requestItem->venue ? $requestItem->venue?->name : $requestItem->venue_other }}</span>
                            </dd>
                        </div>
                        <div class="col-span-2 border-t border-slate-200/80 pt-3.5">
                            <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">Permitted Date & Hours</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">
                                {{ $requestItem->event_date->format('l, M d, Y') }} &middot; <span class="tabular-nums">{{ substr($requestItem->start_time, 0, 5) }} to {{ substr($requestItem->end_time, 0, 5) }}</span> CAT
                            </dd>
                        </div>
                    </dl>

                    <!-- Security Clearance Directives -->
                    <div class="border-l-2 border-emerald-500 pl-4 space-y-2">
                        <div class="font-bold uppercase tracking-[0.14em] text-[10px] text-emerald-800">
                            Gatekeeper Directives & Instructions
                        </div>
                        <ul class="text-[12.5px] space-y-1.5 text-slate-700">
                            <li class="flex gap-2"><span class="text-emerald-600" aria-hidden="true">&mdash;</span><span>Permit sanctuary & venue access for authorized club executives and attendees.</span></li>
                            <li class="flex gap-2"><span class="text-emerald-600" aria-hidden="true">&mdash;</span><span>Sound & amplification curfew: <strong class="text-rose-700 font-semibold">Strictly 22:00 CAT</strong>.</span></li>
                            <li class="flex gap-2"><span class="text-emerald-600" aria-hidden="true">&mdash;</span><span>Sound truck/instrument load-in and campus parking authorized.</span></li>
                        </ul>
                    </div>

                    <!-- Authorization Metadata -->
                    <div class="pt-4 border-t border-slate-100 flex items-end justify-between gap-4 text-[11.5px] text-slate-500">
                        <div>
                            <span>Approved by:</span>
                            <strong class="block font-display font-medium italic text-[14px] text-slate-900">{{ $requestItem->decidedBy ? $requestItem->decidedBy?->name : 'Chaplaincy Office' }}</strong>
                        </div>
                        <div class="text-right">
                            <span>Expected Headcount:</span>
                            <strong class="block text-[14px] font-semibold text-slate-900 tabular-nums">{{ $requestItem->expected_attendance ?: 'Standard' }}</strong>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="bg-slate-50 px-4 py-3 text-center text-[10.5px] tracking-[0.06em] text-slate-500 border-t border-slate-100 tabular-nums">
                    SECURITY AUDIT TIMESTAMP: {{ now()->timezone('Africa/Harare')->format('Y-m-d H:i:s') }} CAT
                </div>

            </main>
        @else
            <!-- UNAUTHORIZED / INVALID PASS CARD -->
            <main class="bg-white rounded-[1.375rem] shadow-lg overflow-hidden ring-1 ring-rose-500/40 text-slate-900">
                <div class="relative bg-rose-700 text-white px-6 pt-7 pb-6 text-center"
                     style="background-image: radial-gradient(100% 80% at 50% 0%, rgb(255 255 255 / 0.12), transparent 70%);">
                    <div class="w-14 h-14 mx-auto rounded-full bg-white/15 ring-1 ring-white/40 grid place-items-center mb-3">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="text-[10px] uppercase font-bold tracking-[0.2em] text-rose-100/90">Verification Alert</div>
                    <h2 class="mt-1.5 text-[1.375rem] font-extrabold tracking-[0.04em]">UNAUTHORIZED / INVALID</h2>
                    <div class="mt-3 inline-block max-w-full truncate px-3 py-1 rounded-full bg-black/20 text-[11.5px] font-semibold text-rose-50">
                        Scanned Code: {{ $code }}
                    </div>
                </div>

                <div class="p-6 sm:p-7 space-y-5 text-[13px]">
                    <div class="border-l-2 border-rose-500 pl-4 space-y-1.5">
                        <strong class="block font-bold uppercase tracking-[0.12em] text-[10.5px] text-rose-800">NO APPROVED CLEARANCE FOUND:</strong>
                        <p class="text-slate-700 leading-relaxed">
                            This clearance code is either unrecognized, has not completed formal Chaplaincy review, or was declined. Gatekeepers and campus security personnel are instructed <strong>NOT</strong> to open chapel venues or permit off-campus bus dispatch for this event.
                        </p>
                    </div>

                    <div class="text-center pt-1">
                        <a href="{{ route('calendar.public') }}" class="btn btn-primary">
                            Check Official Public Calendar
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>

                <div class="bg-slate-50 px-4 py-3 text-center text-[10.5px] tracking-[0.04em] text-slate-500 border-t border-slate-100">
                    Chaplaincy Hotline Ext. 2045 &middot; Campus Security Control Ext. 2222
                </div>
            </main>
        @endif

        <div class="text-center text-[11.5px] tracking-[0.06em] text-white/40 pt-1">
            Kwang-Lim Chapel &middot; Valley of Hope
        </div>

    </div>

</body>
</html>
