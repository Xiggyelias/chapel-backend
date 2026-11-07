<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Official Chapel Clearance Pass - {{ $requestItem->reference_no }}</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page { size: A5 portrait; margin: 10mm; }
        @media print {
            .no-print { display: none !important; }
            html, body { background: #ffffff !important; }
            body { padding: 0 !important; min-height: 0 !important; display: block !important; }
            .pass { box-shadow: none !important; margin: 0 auto; break-inside: avoid; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-paper p-4 sm:p-8 flex flex-col items-center justify-center min-h-screen text-slate-900">

    <div class="no-print max-w-md w-full mb-5 flex flex-wrap items-center justify-between gap-2">
        <a href="{{ route('requests.show', $requestItem->id) }}" class="btn btn-ghost btn-sm -ml-2">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to Proposal
        </a>
        <div class="flex items-center gap-2">
            <button type="button" onclick="dispatchWhatsApp()" class="btn btn-secondary btn-sm">
                <svg class="text-emerald-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.86-1.537c1.7 1.011 3.684 1.595 5.803 1.595 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/></svg>
                WhatsApp
            </button>
            <button type="button" onclick="copyMemo()" class="btn btn-secondary btn-sm">
                Copy Memo
            </button>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                PDF / Print
            </button>
        </div>
    </div>

    <!-- Official Gatekeeper Clearance Badge -->
    <main class="pass max-w-md w-full bg-white rounded-[1.375rem] shadow-lg ring-1 ring-black/5 overflow-hidden">

        <!-- Badge Header -->
        <div class="relative grain bg-ink text-white px-6 pt-7 pb-6 text-center"
             style="background-image: radial-gradient(120% 90% at 50% 0%, rgb(165 28 48 / 0.55), transparent 65%);">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-brand p-2 mb-3 ring-1 ring-white/15 shadow-md">
                <x-umc-logo class="w-full h-full" />
            </div>
            <div class="text-[10.5px] uppercase tracking-[0.22em] text-gold-light font-bold">Kwang-Lim Chapel</div>
            <h1 class="font-display text-[1.5rem] leading-tight text-white mt-1.5">Official Activity Clearance Pass</h1>
            <div class="mx-auto mt-3 flex max-w-[12rem] items-center gap-2 text-gold-light/70" aria-hidden="true">
                <span class="h-px flex-1 bg-current opacity-50"></span><span class="text-[7px]">◆</span><span class="h-px flex-1 bg-current opacity-50"></span>
            </div>
            <div class="text-[10.5px] text-white/55 mt-3">Kwang-Lim Chapel &middot; Campus Gatekeeper Authority</div>
        </div>

        <!-- Clearance Status Banner -->
        <div class="bg-emerald-700 text-white px-4 py-2.5 text-center text-[11px] font-bold uppercase tracking-[0.14em] flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M5 13l4 4L19 7"/></svg>
            <span>Officially Approved &amp; Cleared</span>
        </div>

        <div class="p-6 sm:p-7 space-y-6 text-[13px]">

            <!-- Reference & Club -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 block">Pass Reference</span>
                    <span class="mt-0.5 block font-semibold text-[15px] text-brand tabular-nums tracking-tight">{{ $requestItem->reference_no }}</span>
                </div>
                <div class="text-right min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 block">Authorized Club</span>
                    <span class="mt-1 inline-flex items-center gap-1.5 text-[12.5px] font-semibold text-slate-800">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $requestItem->club?->color_hex ?? '#A51C30' }}" aria-hidden="true"></span>
                        {{ $requestItem->club?->name }}
                    </span>
                </div>
            </div>

            <!-- Activity Title -->
            <div class="border-t border-slate-100 pt-5">
                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 block">Approved Activity</span>
                <div class="font-display text-[1.25rem] text-slate-900 mt-1 leading-snug">{{ $requestItem->title }}</div>
            </div>

            <!-- Venue & Time -->
            <dl class="grid grid-cols-2 gap-x-4 gap-y-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-black/5">
                <div>
                    <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">Date</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900">{{ $requestItem->event_date->format('l, M d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">Permitted Hours</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 tabular-nums">{{ substr($requestItem->start_time, 0, 5) }} - {{ substr($requestItem->end_time, 0, 5) }}</dd>
                </div>
                <div class="col-span-2 border-t border-slate-200/80 pt-3.5">
                    <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">Cleared Venue</dt>
                    <dd class="mt-0.5 flex items-center gap-1.5 font-semibold text-brand">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        {{ $requestItem->venue ? $requestItem->venue?->name : $requestItem->venue_other }}
                    </dd>
                    <dd class="text-[11px] text-slate-500 mt-1">Approved Attendance Limit: {{ $requestItem->expected_attendance ?: 'Standard' }} persons</dd>
                </div>
            </dl>

            <!-- Scannable Verification QR Code Representation -->
            <div class="text-center space-y-2.5">
                <a href="{{ $verifyUrl }}" target="_blank" rel="noopener" title="Click to test Gatekeeper Verification Screen" class="inline-block p-3 bg-white rounded-2xl shadow-sm ring-1 ring-black/10 transition hover:ring-brand/40 hover:shadow-md">
                    <x-qr-code :value="$verifyUrl" size="160" label="Gatekeeper verification QR code for {{ $requestItem->reference_no }}" />
                </a>
                <div class="text-[10.5px] text-slate-600 font-semibold uppercase tracking-[0.14em] tabular-nums">
                    VERIFY CODE: <span class="font-mono normal-case tracking-normal">{{ $verifyToken }}</span>
                </div>
                <div class="text-[11px] text-slate-400">
                    Scan by Security / Campus Life or <a href="{{ $verifyUrl }}" target="_blank" rel="noopener" class="text-brand font-semibold underline underline-offset-2">click to simulate gate scan</a>
                </div>
            </div>

            <!-- Official Approver Signature Box -->
            <div class="pt-5 border-t border-slate-100 flex items-end justify-between gap-4 text-[12px]">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 block">Approved By Authority</span>
                    <span class="mt-1 block font-display text-[15px] text-slate-900 italic">{{ $requestItem->decidedBy ? $requestItem->decidedBy?->name : 'University Chaplaincy Office' }}</span>
                    <span class="text-[11px] text-slate-500 block tabular-nums">{{ $requestItem->decided_at ? $requestItem->decided_at->format('M d, Y H:i') : now()->format('M d, Y') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 block">Security Curfew</span>
                    <span class="mt-1 block font-semibold text-amber-800">Strictly 22:00 CAT</span>
                </div>
            </div>

        </div>

        <div class="bg-slate-50 px-4 py-3 text-center text-[10.5px] tracking-[0.08em] text-slate-400 border-t border-slate-100">
            Kwang-Lim Chapel &middot; Valley of Hope
        </div>

    </main>

    <!-- Live Toast Feedback -->
    <div id="copyToast" role="status" class="hidden fixed bottom-6 bg-ink text-white px-4 py-2.5 rounded-2xl shadow-lg text-[12px] font-semibold ring-1 ring-white/10 flex items-center gap-2 animate-fade-in z-50">
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>Clearance memo copied to clipboard!</span>
    </div>

    <script>
        function getMemoText() {
            const pass = {{ Js::from([
                'title' => $requestItem->title,
                'club' => $requestItem->club?->name,
                'venue' => $requestItem->venue ? $requestItem->venue?->name : $requestItem->venue_other,
                'date' => $requestItem->event_date->format('l, M d, Y'),
                'hours' => substr($requestItem->start_time, 0, 5) . ' - ' . substr($requestItem->end_time, 0, 5),
                'reference' => $requestItem->reference_no,
                'verifyUrl' => $verifyUrl,
            ]) }};
            return `🏛️ *KWANG-LIM CHAPEL CLEARANCE PASS*\n\n📋 *Activity:* ${pass.title}\n👥 *Sponsoring Club:* ${pass.club}\n📍 *Venue:* ${pass.venue}\n📅 *Date:* ${pass.date}\n⏰ *Hours:* ${pass.hours} CAT\n🛡️ *Reference:* ${pass.reference}\n\n✅ *Gatekeeper Verification Link:*\n${pass.verifyUrl}\n\n⚠️ *Directives:* Sound & amplification curfew strictly 22:00 CAT. Facility access authorized by Chaplaincy Office.`;
        }

        function dispatchWhatsApp() {
            const memo = getMemoText();
            const url = `https://api.whatsapp.com/send?text=${encodeURIComponent(memo)}`;
            window.open(url, '_blank');
        }

        function copyMemo() {
            const memo = getMemoText();
            navigator.clipboard.writeText(memo).then(() => {
                const toast = document.getElementById('copyToast');
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 2500);
            }).catch(() => {
                alert('Copied to clipboard!');
            });
        }
    </script>

</body>
</html>
