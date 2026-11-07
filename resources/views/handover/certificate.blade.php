<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Handover Certificate - {{ $handover->club?->name }} ({{ $handover->academic_year }}) - Africa University Chaplaincy</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page { size: A4 portrait; margin: 9mm; }

        .cert-desk {
            background:
                radial-gradient(70% 50% at 50% 0%, rgb(217 183 108 / .14), transparent 70%),
                var(--color-vellum);
        }

        /* The sheet: warm laid paper, a gold double rule, a crimson hairline */
        .cert-outer {
            position: relative;
            max-width: 820px;
            margin: 0 auto;
            padding: 22px;
            background:
                radial-gradient(120% 80% at 50% 0%, #FFFDF8, #FBF7EE 60%, #F6F0E2);
            box-shadow: 0 1px 2px rgb(28 24 21 / .06), 0 30px 60px -25px rgb(28 24 21 / .35);
            color: var(--color-slate-900);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .cert-frame {
            position: relative;
            border: 1.5px solid var(--color-gold);
            padding: 6px;
        }
        .cert-frame-inner {
            position: relative;
            border: 0.75px solid rgb(184 137 43 / .7);
            outline: 0.75px solid rgb(220 38 38 / .35);
            outline-offset: -7px;
            padding: 44px 52px 30px;
            overflow: hidden;
        }

        /* Corner lozenges sit on the gold rule */
        .cert-corner {
            position: absolute;
            width: 11px;
            height: 11px;
            background: var(--color-gold);
            transform: rotate(45deg);
            z-index: 2;
        }
        .cert-tl { top: -6px; left: -6px; }
        .cert-tr { top: -6px; right: -6px; }
        .cert-bl { bottom: -6px; left: -6px; }
        .cert-br { bottom: -6px; right: -6px; }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -46%);
            height: 74%;
            width: auto;
            opacity: 0.045;
            pointer-events: none;
            z-index: 0;
            color: var(--color-brand);
        }

        .cert-serif { font-family: var(--font-display); font-optical-sizing: auto; }
        .cert-title { font-variation-settings: 'SOFT' 100, 'opsz' 144; letter-spacing: -0.02em; }
        .cert-name { font-variation-settings: 'SOFT' 100, 'WONK' 1, 'opsz' 144; letter-spacing: -0.025em; }
        .cert-caps { font-family: var(--font-sans); font-size: 9.5px; font-weight: 700; letter-spacing: .24em; text-transform: uppercase; }

        .gold-rule { height: 1px; background: linear-gradient(90deg, transparent, var(--color-gold) 20%, var(--color-gold) 80%, transparent); }

        .cert-seal {
            width: 84px;
            height: 84px;
            border-radius: 9999px;
            display: grid;
            place-items: center;
            position: relative;
            background: radial-gradient(circle at 38% 30%, #EF4444, var(--color-brand-deep) 75%);
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / .18), 0 0 0 3px #FBF7EE, 0 0 0 4px var(--color-gold), 0 8px 18px -8px rgb(94 15 28 / .6);
        }
        .cert-seal::after {
            content: '';
            position: absolute;
            inset: 6px;
            border-radius: 9999px;
            border: 0.75px dashed rgb(241 213 143 / .6);
        }

        @media print {
            html, body { background: #fff !important; }
            .cert-desk { background: #fff !important; padding: 0 !important; min-height: 0 !important; }
            .no-print { display: none !important; }
            .cert-outer { box-shadow: none !important; max-width: none !important; width: 100% !important; margin: 0 !important; padding: 0 !important; background: #fff !important; break-inside: avoid; page-break-inside: avoid; }
            .cert-frame-inner { padding: 34px 40px 22px !important; }
        }
    </style>
</head>
<body class="bg-vellum">
<div class="cert-desk min-h-screen px-4 py-6 sm:py-10">

    {{-- Toolbar --}}
    <div class="no-print max-w-[820px] mx-auto mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('handover.show', $handover->id) }}" class="btn btn-ghost -ml-3">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.5 19.5L3 12l7.5-7.5M3 12h18"/></svg>
            Back to Handover Dossier
        </a>
        <div class="flex items-center gap-3">
            <span class="text-[12px] text-slate-500 tabular-nums">Academic Year: {{ $handover->academic_year }}</span>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Official Certificate
            </button>
        </div>
    </div>

    {{-- The certificate --}}
    <article class="cert-outer text-center" aria-label="Certificate of Leadership Stewardship">
        <div class="cert-frame">
            <span class="cert-corner cert-tl" aria-hidden="true"></span>
            <span class="cert-corner cert-tr" aria-hidden="true"></span>
            <span class="cert-corner cert-bl" aria-hidden="true"></span>
            <span class="cert-corner cert-br" aria-hidden="true"></span>

            <div class="cert-frame-inner">
                <x-umc-logo variant="monochrome" class="watermark" aria-hidden="true" />

                <div class="relative z-10">

                    {{-- Institution --}}
                    <header>
                        <x-umc-logo variant="official" class="h-[68px] w-auto mx-auto" />
                        <p class="cert-caps text-brand mt-4">Africa University &bull; Office of the University Chaplain</p>
                        <p class="cert-serif italic text-[14px] text-slate-600 mt-1.5">Kwang-Lim Chapel Fellowship &amp; Ministry Governance</p>
                    </header>

                    <div class="flex items-center justify-center gap-3 text-gold mt-6" aria-hidden="true">
                        <span class="gold-rule w-28"></span><span class="text-[8px]">&#9670;</span><span class="gold-rule w-28"></span>
                    </div>

                    {{-- Title --}}
                    <h1 class="cert-serif cert-title mt-5 text-[30px] sm:text-[34px] leading-[1.12] font-normal text-slate-900 max-w-[34rem] mx-auto">
                        Certificate of Leadership Stewardship &amp; Institutional Transition
                    </h1>
                    <p class="mt-3 font-mono text-[9.5px] tracking-[0.2em] text-slate-400">
                        ATTESTATION REF: AU-CHAPEL-HDV-{{ $handover->id }}-{{ str_replace('/', '-', $handover->academic_year) }}
                    </p>

                    {{-- Attestation --}}
                    <div class="cert-serif mt-7 max-w-[36rem] mx-auto text-[15px] leading-[1.7] text-slate-700">
                        <p>
                            This is to solemnly attest that under the regulations of the Africa University Chaplaincy Office and the Kwang-Lim Chapel Fellowship Manual, the formal leadership transition for:
                        </p>

                        <p class="cert-name mt-5 text-[38px] sm:text-[42px] leading-[1.08] text-brand">
                            {{ $handover->club?->name }}
                        </p>
                        <div class="gold-rule w-72 max-w-full mx-auto mt-3" aria-hidden="true"></div>

                        <p class="mt-5 text-[14.5px]">
                            has been fully and faithfully executed for the <strong class="font-semibold text-slate-900">Academic Year {{ $handover->academic_year }}</strong>.
                            Outgoing Executive <strong class="font-semibold text-slate-900">{{ $handover->outgoing_president_name }}</strong> has formally surrendered institutional archives, permanent minutes, equipment registers, and financial accountabilities.
                        </p>
                    </div>

                    {{-- Particulars --}}
                    <dl class="mt-7 max-w-[34rem] mx-auto grid grid-cols-2 border-y border-gold/40 text-left">
                        <div class="py-3.5 pr-5 border-r border-gold/30">
                            <dt class="cert-caps text-slate-400 !text-[8.5px]">Vault Sentinel Record Audit</dt>
                            <dd class="cert-serif mt-1 text-[16px] text-slate-900">100% Institutional Compliance</dd>
                            <dd class="mt-0.5 text-[10.5px] text-slate-500">Grade A &bull; Verified by Chaplaincy Office</dd>
                        </div>
                        <div class="py-3.5 pl-5">
                            <dt class="cert-caps text-slate-400 !text-[8.5px]">Conferred Executive Authority</dt>
                            <dd class="cert-serif mt-1 text-[16px] text-brand">{{ $handover->incoming_president_name }}</dd>
                            <dd class="mt-0.5 text-[10.5px] text-slate-500">President &amp; Chapel Committee Member</dd>
                        </div>
                    </dl>

                    <blockquote class="cert-serif italic mt-6 max-w-[32rem] mx-auto text-[13.5px] leading-relaxed text-slate-600">
                        "Whatever you do, work at it with all your heart, as working for the Lord, not for human masters." &mdash; Colossians 3:23
                    </blockquote>

                    {{-- Signatures & seal --}}
                    <div class="mt-9 grid grid-cols-[1fr_auto_1fr] items-end gap-8">
                        <div class="text-center">
                            <div class="cert-serif italic text-[17px] text-slate-800 pb-1.5 border-b border-slate-400/70">
                                {{ $handover->outgoing_president_name }}
                            </div>
                            <span class="cert-caps text-slate-500 !text-[8.5px] block mt-2">Outgoing President</span>
                            <span class="text-[10.5px] text-slate-500">{{ $handover->club?->name }}</span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="cert-seal" role="img" aria-label="Chapel Seal Affixed">
                                <div class="relative z-10 flex flex-col items-center text-gold-bright">
                                    <x-umc-logo variant="monochrome" class="h-6 w-auto" aria-hidden="true" />
                                    <span class="mt-1 font-sans text-[6.5px] font-bold uppercase tracking-[0.18em] leading-[1.35] text-center text-gold-bright/90">Chapel<br>Seal<br>Affixed</span>
                                </div>
                            </div>
                            <span class="mt-2.5 text-[9.5px] text-slate-400 tabular-nums">{{ date('M d, Y') }}</span>
                        </div>

                        <div class="text-center">
                            <div class="cert-serif italic text-[17px] text-brand pb-1.5 border-b border-slate-400/70">
                                {{ $handover->completer ? $handover->completer?->name : 'Rev. Supt. Eunice Marima' }}
                            </div>
                            <span class="cert-caps text-slate-500 !text-[8.5px] block mt-2">University Chaplain</span>
                            <span class="text-[10.5px] text-slate-500">Africa University</span>
                        </div>
                    </div>

                    <footer class="mt-8 pt-3 border-t border-gold/30 flex items-center justify-between text-[9.5px] text-slate-400">
                        <span>Kwang-Lim Chapel &bull; Ministry Records</span>
                        <span class="tracking-[0.18em]">VERIFIED OFFICIAL DOCUMENT</span>
                    </footer>

                </div>
            </div>
        </div>
    </article>

</div>
</body>
</html>
