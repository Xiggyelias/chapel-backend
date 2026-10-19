<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Handover Certificate - {{ $handover->club->name }} ({{ $handover->academic_year }}) - Africa University Chaplaincy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'EB Garamond', Georgia, serif;
            background-color: #f1f5f9;
            color: #0f172a;
            margin: 0;
            padding: 30px;
        }
        .font-cinzel { font-family: 'Cinzel', Georgia, serif; }
        .font-sans { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .cert-outer {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 12px double #DC2626;
            outline: 2px solid #0B0F17;
            outline-offset: -6px;
            padding: 48px;
            position: relative;
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .cert-corner {
            position: absolute;
            width: 32px;
            height: 32px;
            border-color: #DC2626;
        }
        .cert-tl { top: 12px; left: 12px; border-top: 3px solid #DC2626; border-left: 3px solid #DC2626; }
        .cert-tr { top: 12px; right: 12px; border-top: 3px solid #DC2626; border-right: 3px solid #DC2626; }
        .cert-bl { bottom: 12px; left: 12px; border-bottom: 3px solid #DC2626; border-left: 3px solid #DC2626; }
        .cert-br { bottom: 12px; right: 12px; border-bottom: 3px solid #DC2626; border-right: 3px solid #DC2626; }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 360px;
            height: 360px;
            opacity: 0.04;
            pointer-events: none;
            z-index: 0;
        }

        @media print {
            body { padding: 0; background: #ffffff !important; }
            .no-print { display: none !important; }
            .cert-outer { box-shadow: none !important; border-width: 8px !important; margin: 0 auto !important; width: 100% !important; padding: 32px !important; }
        }
    </style>
</head>
<body>

    <!-- Top Action Toolbar -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between font-sans text-xs">
        <a href="{{ route('handover.show', $handover->id) }}" class="text-[#DC2626] font-bold hover:underline flex items-center space-x-1">
            <span>&larr; Back to Handover Dossier</span>
        </a>
        <div class="flex items-center space-x-3">
            <span class="text-slate-500 font-mono">Academic Year: {{ $handover->academic_year }}</span>
            <button onclick="window.print()" class="px-5 py-2.5 bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold rounded-xl shadow-sm transition flex items-center space-x-2">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Official Certificate</span>
            </button>
        </div>
    </div>

    <!-- Official Certificate Frame -->
    <div class="cert-outer relative text-center">
        <!-- Corner Flourishes -->
        <div class="cert-corner cert-tl"></div>
        <div class="cert-corner cert-tr"></div>
        <div class="cert-corner cert-bl"></div>
        <div class="cert-corner cert-br"></div>

        <!-- Watermark -->
        <svg class="watermark text-[#DC2626]" viewBox="0 0 100 100" fill="currentColor">
            <rect x="45" y="10" width="10" height="80" rx="3" />
            <rect x="20" y="32" width="60" height="10" rx="3" />
        </svg>

        <div class="relative z-10 space-y-6">

            <!-- Institutional Header -->
            <div class="space-y-1">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-[#DC2626] border border-white/20 flex items-center justify-center p-2.5 shadow-md mb-2">
                    <x-umc-logo class="w-full h-full" />
                </div>
                <div class="font-cinzel text-xs font-bold uppercase tracking-widest text-[#DC2626]">
                    Africa University &bull; Office of the University Chaplain
                </div>
                <div class="font-cinzel text-sm font-semibold tracking-wider text-slate-600 uppercase">
                    Kwang-Lim Chapel Fellowship & Ministry Governance
                </div>
            </div>

            <!-- Title Decree -->
            <div class="py-2 border-y-2 border-[#DC2626]/30 my-4 space-y-1">
                <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#DC2626] tracking-wide uppercase">
                    Certificate of Leadership Stewardship & Institutional Transition
                </h1>
                <div class="text-xs font-sans text-slate-500 font-mono tracking-widest">
                    ATTESTATION REF: AU-CHAPEL-HDV-{{ $handover->id }}-{{ str_replace('/', '-', $handover->academic_year) }}
                </div>
            </div>

            <!-- Recipient & Attestation Text -->
            <div class="space-y-4 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed text-slate-800">
                <p>
                    This is to solemnly attest that under the regulations of the Africa University Chaplaincy Office and the Kwang-Lim Chapel Fellowship Manual, the formal leadership transition for:
                </p>

                <div class="font-cinzel text-2xl sm:text-3xl font-black text-[#DC2626] py-1 tracking-wider border-b border-dashed border-[#DC2626] max-w-lg mx-auto">
                    {{ $handover->club->name }}
                </div>

                <p class="text-sm sm:text-base leading-relaxed text-slate-700">
                    has been fully and faithfully executed for the <strong>Academic Year {{ $handover->academic_year }}</strong>.
                    Outgoing Executive <strong>{{ $handover->outgoing_president_name }}</strong> has formally surrendered institutional archives, permanent minutes, equipment registers, and financial accountabilities.
                </p>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs font-sans grid grid-cols-2 gap-4 text-left">
                    <div>
                        <span class="text-slate-400 uppercase font-bold text-[10px] block">Vault Sentinel Record Audit</span>
                        <span class="font-bold text-slate-900 text-sm">100% Institutional Compliance</span>
                        <span class="text-[10px] text-emerald-700 block font-semibold">Grade A &bull; Verified by Chaplaincy Office</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold text-[10px] block">Conferred Executive Authority</span>
                        <span class="font-bold text-[#DC2626] text-sm">{{ $handover->incoming_president_name }}</span>
                        <span class="text-[10px] text-slate-600 block">President & Chapel Committee Member</span>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 italic">
                    "Whatever you do, work at it with all your heart, as working for the Lord, not for human masters." &mdash; Colossians 3:23
                </p>
            </div>

            <!-- Signature & Seal Blocks -->
            <div class="pt-8 grid grid-cols-3 gap-6 text-xs font-sans text-slate-700">
                <div class="text-center space-y-2">
                    <div class="h-10 border-b border-slate-400 flex items-end justify-center font-cinzel font-bold text-sm text-slate-800 pb-1">
                        {{ $handover->outgoing_president_name }}
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Outgoing President</span>
                    <span class="text-[10px] text-slate-500">{{ $handover->club->name }}</span>
                </div>

                <div class="text-center space-y-1 flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-full border-2 border-[#DC2626] bg-red-50/40 flex items-center justify-center p-1 shadow-inner">
                        <div class="w-14 h-14 rounded-full border border-dashed border-[#DC2626] flex flex-col items-center justify-center text-[8px] font-cinzel font-bold text-[#DC2626] uppercase text-center leading-tight">
                            <span>Chapel</span>
                            <span>Seal</span>
                            <span>Affixed</span>
                        </div>
                    </div>
                    <span class="text-[9px] font-mono text-slate-400">{{ date('M d, Y') }}</span>
                </div>

                <div class="text-center space-y-2">
                    <div class="h-10 border-b border-slate-400 flex items-end justify-center font-cinzel font-bold text-sm text-[#DC2626] pb-1">
                        {{ $handover->completer ? $handover->completer->name : 'Rev. Supt. Eunice Marima' }}
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">University Chaplain</span>
                    <span class="text-[10px] text-slate-500">Africa University</span>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 text-[10px] font-sans text-slate-400 flex items-center justify-between">
                <span>Kwang-Lim Chapel &bull; Ministry Records</span>
                <span class="font-mono">VERIFIED OFFICIAL DOCUMENT</span>
            </div>

        </div>
    </div>

</body>
</html>
