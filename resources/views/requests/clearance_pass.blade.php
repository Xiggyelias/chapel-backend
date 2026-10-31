<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Official Chapel Clearance Pass - {{ $requestItem->reference_no }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .font-cinzel { font-family: 'Plus Jakarta Sans', sans-serif !important; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff; padding: 0; }
        }
    </style>
</head>
<body class="p-4 sm:p-8 flex flex-col items-center justify-center min-h-screen text-slate-900 selection:bg-[#DC2626] selection:text-white">

    <div class="no-print max-w-md w-full mb-4 flex flex-wrap items-center justify-between gap-2">
        <a href="{{ route('requests.show', $requestItem->id) }}" class="text-xs text-[#DC2626] font-bold hover:underline flex items-center space-x-1">
            <span>&larr; Back to Proposal</span>
        </a>
        <div class="flex items-center space-x-2">
            <button type="button" onclick="dispatchWhatsApp()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center space-x-1.5 active:scale-95">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.86-1.537c1.7 1.011 3.684 1.595 5.803 1.595 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/></svg>
                <span>WhatsApp</span>
            </button>
            <button type="button" onclick="copyMemo()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-300 shadow-xs">
                Copy Memo
            </button>
            <button onclick="window.print()" class="px-3.5 py-2 bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold rounded-xl shadow-md transition active:scale-95">
                PDF / Print
            </button>
        </div>
    </div>

    <!-- Official Gatekeeper Clearance Badge -->
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl border-2 border-[#DC2626] overflow-hidden card-elevation">
        
        <!-- Badge Header -->
        <div class="bg-[#DC2626] text-white p-6 text-center border-b border-white/10 relative">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/10 border border-white/20 p-2 mb-2 backdrop-blur-md">
                <x-umc-logo class="w-full h-full" />
            </div>
            <div class="text-xs uppercase tracking-widest text-rose-200 font-bold">Kwang-Lim Chapel</div>
            <h1 class="text-xl font-bold text-white mt-0.5">Official Activity Clearance Pass</h1>
            <div class="text-[10px] text-rose-200/90 font-mono mt-1">Kwang-Lim Chapel &bull; Campus Gatekeeper Authority</div>
        </div>

        <!-- Clearance Status Banner -->
        <div class="bg-emerald-600 text-white px-4 py-2 text-center text-xs font-bold uppercase tracking-wider flex items-center justify-center space-x-2">
            <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Officially Approved &amp; Cleared</span>
        </div>

        <div class="p-6 space-y-5 text-xs">
            
            <!-- Reference & Club -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block font-cinzel">Pass Reference</span>
                    <span class="font-mono font-bold text-sm text-[#DC2626]">{{ $requestItem->reference_no }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block font-cinzel">Authorized Club</span>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded text-white" style="background-color: {{ $requestItem->club?->color_hex ?? '#DC2626' }}">
                        {{ $requestItem->club?->name }}
                    </span>
                </div>
            </div>

            <!-- Activity Title -->
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block font-cinzel">Approved Activity</span>
                <div class="text-base font-bold text-slate-900 mt-0.5 leading-snug font-cinzel">{{ $requestItem->title }}</div>
            </div>

            <!-- Venue & Time -->
            <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Date</span>
                    <span class="font-bold text-slate-900 text-xs">{{ $requestItem->event_date->format('l, M d, Y') }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Permitted Hours</span>
                    <span class="font-bold text-slate-900 text-xs font-mono">{{ substr($requestItem->start_time, 0, 5) }} - {{ substr($requestItem->end_time, 0, 5) }}</span>
                </div>
                <div class="col-span-2 pt-2 border-t border-slate-200/80">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Cleared Venue</span>
                    <span class="font-bold text-[#DC2626] text-xs">
                        📍 {{ $requestItem->venue ? $requestItem->venue?->name : $requestItem->venue_other }}
                    </span>
                    <div class="text-[10px] text-slate-500 mt-0.5">Approved Attendance Limit: {{ $requestItem->expected_attendance ?: 'Standard' }} persons</div>
                </div>
            </div>

            <!-- Scannable Verification QR Code Representation -->
            <div class="text-center p-5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2">
                <a href="{{ $verifyUrl }}" target="_blank" rel="noopener" title="Click to test Gatekeeper Verification Screen" class="inline-block p-2.5 bg-white rounded-2xl shadow-xs border border-slate-300 hover:border-[#DC2626] hover:scale-105 transition-all duration-200 group">
                    <x-qr-code :value="$verifyUrl" size="160" label="Gatekeeper verification QR code for {{ $requestItem->reference_no }}" />
                </a>
                <div class="font-mono text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                    VERIFY CODE: {{ $verifyToken }}
                </div>
                <div class="text-[10px] text-slate-400">
                    Scan by Security / Campus Life or <a href="{{ $verifyUrl }}" target="_blank" rel="noopener" class="text-[#DC2626] font-bold underline">click to simulate gate scan</a>
                </div>
            </div>

            <!-- Official Approver Signature Box -->
            <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-[11px]">
                <div>
                    <span class="text-[9px] font-bold uppercase text-slate-400 block font-cinzel">Approved By Authority</span>
                    <span class="font-bold text-[#DC2626]">{{ $requestItem->decidedBy ? $requestItem->decidedBy?->name : 'University Chaplaincy Office' }}</span>
                    <span class="text-[10px] text-slate-500 block font-mono">{{ $requestItem->decided_at ? $requestItem->decided_at->format('M d, Y H:i') : now()->format('M d, Y') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[9px] font-bold uppercase text-slate-400 block font-cinzel">Security Curfew</span>
                    <span class="font-bold text-amber-800">Strictly 22:00 CAT</span>
                </div>
            </div>

        </div>

        <div class="bg-slate-50 p-3.5 text-center text-[10px] text-slate-400 border-t border-slate-100">
            Kwang-Lim Chapel &bull; Valley of Hope
        </div>

    </div>

    <!-- Live Toast Feedback -->
    <div id="copyToast" class="hidden fixed bottom-6 bg-slate-900 text-white px-4 py-2.5 rounded-2xl shadow-xl text-xs font-bold border border-slate-700 flex items-center space-x-2 animate-fade-in z-50">
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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

