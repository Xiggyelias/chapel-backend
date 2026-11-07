<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Africa University Kwang-Lim Chapel - Annual Ministry Report {{ $year }}</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .report-sheet { box-shadow: 0 1px 2px rgb(28 24 21 / .04), 0 30px 60px -30px rgb(28 24 21 / .28); }
        .report-prose { hyphens: auto; -webkit-hyphens: auto; text-wrap: pretty; }
        .report-prose::first-letter {
            float: left;
            font-family: var(--font-display);
            font-size: 3.4em;
            line-height: .82;
            padding: .06em .05em 0 0;
            color: var(--color-brand);
            font-variation-settings: 'SOFT' 100, 'opsz' 144;
        }
        .section-numeral { font-family: var(--font-display); font-style: italic; color: var(--color-gold); }
        .ledger th { font-size: .625rem; letter-spacing: .14em; text-transform: uppercase; color: var(--color-slate-500); font-weight: 600; padding: .625rem .75rem; border-bottom: 1px solid var(--color-slate-900); }
        .ledger td { padding: .7rem .75rem; border-bottom: 1px solid rgb(28 24 21 / .08); vertical-align: baseline; }
        .ledger tbody tr:last-child td { border-bottom: 1px solid var(--color-slate-900); }

        @page { size: A4; margin: 16mm 15mm 18mm; }
        @media print {
            .no-print { display: none !important; }
            html, body { background: #ffffff !important; }
            body { padding: 0 !important; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .report-card { border: none !important; box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; max-width: none !important; }
            .ledger thead { display: table-header-group; }
            .ledger tr, .report-figures > div, .report-signatures { break-inside: avoid; }
            .report-section { break-inside: avoid-page; }
            a { color: inherit !important; text-decoration: none !important; }
        }
    </style>
</head>
<body class="bg-paper text-slate-800 px-4 py-6 sm:px-10 sm:py-10">

    {{-- Action bar (hidden on print) --}}
    <div class="no-print max-w-[54rem] mx-auto mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="{{ route('reports.budget_tracker') }}" class="btn btn-ghost self-start -ml-3">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Budget Tracker
        </a>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('exports.budget_tracker') }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download CSV Data
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Save PDF Report
            </button>
        </div>
    </div>

    {{-- The document --}}
    <article class="report-card report-sheet max-w-[54rem] mx-auto bg-white rounded-[4px] px-6 py-10 sm:px-16 sm:py-16">

        {{-- Masthead --}}
        <header class="text-center">
            <span class="mx-auto w-14 h-14 rounded-full grid place-items-center"
                  style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px #fff, 0 0 0 4px rgb(184 137 43 / .55);">
                <x-umc-logo class="w-6 h-6" />
            </span>

            <p class="mt-5 text-[10px] font-bold uppercase tracking-[0.28em] text-brand">Africa University &middot; Office of the Chaplain</p>
            <h1 class="mt-3 font-display text-[2rem] sm:text-[2.6rem] leading-[1.05] tracking-[-0.025em] text-slate-900 text-balance"
                style="font-weight: 400 !important; font-variation-settings: 'SOFT' 80, 'opsz' 144;">
                Kwang-Lim Chapel <em class="italic text-brand">Annual Ministry Report</em>
            </h1>
            <p class="mt-3 font-display italic text-[15px] text-slate-500">Academic Year {{ $year }}/{{ $year + 1 }} &middot; Kwang-Lim Chapel</p>

            <div class="rule-ornament mt-8 text-[9px]" aria-hidden="true">◆</div>

            <dl class="mt-4 flex flex-wrap justify-center gap-x-10 gap-y-2 text-[11px]">
                <div class="flex items-baseline gap-2">
                    <dt class="font-bold uppercase tracking-[0.16em] text-slate-400 text-[9.5px]">Report Dossier Ref</dt>
                    <dd class="font-semibold text-slate-800 tabular-nums tracking-wide">AU-CHAPEL-AR-{{ $year }}</dd>
                </div>
                <div class="flex items-baseline gap-2">
                    <dt class="font-bold uppercase tracking-[0.16em] text-slate-400 text-[9.5px]">Issued</dt>
                    <dd class="font-semibold text-slate-800 tabular-nums">{{ date('F d, Y') }}</dd>
                </div>
            </dl>
        </header>

        {{-- I. Executive summary --}}
        <section class="report-section mt-12">
            <h2 class="flex items-baseline gap-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-900">
                <span class="section-numeral text-[15px] normal-case tracking-normal font-normal">I.</span>
                Executive Chaplaincy Governance Summary
            </h2>
            <p class="report-prose mt-4 font-display text-[15px] leading-[1.75] text-slate-800 text-justify" style="font-variation-settings: 'SOFT' 40, 'opsz' 14;">
                In compliance with the Africa University Chapel Manual and Student Life Regulations, this annual report consolidates ministry activities, worship celebrations, post-activity evaluations, financial allocations, and permanent institutional records across all ten official student fellowships and clubs under the Kwang-Lim Chapel spiritual umbrella. The Chaplaincy Office certifies that activities herein were conducted in accordance with university policy, maintaining the highest standards of Christian discipleship, pan-African unity, and responsible student leadership.
            </p>
        </section>

        {{-- II. The year in figures --}}
        <section class="report-section mt-12">
            <h2 class="flex items-baseline gap-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-900">
                <span class="section-numeral text-[15px] normal-case tracking-normal font-normal">II.</span>
                The Year in Figures
            </h2>
            <dl class="report-figures mt-5 grid grid-cols-2 sm:grid-cols-4 border-y border-slate-900">
                <div class="px-4 py-5 border-slate-200 border-r">
                    <dt class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-slate-500 leading-snug min-h-[2.4em]">Total Activities Conducted</dt>
                    <dd class="mt-3 stat-value text-[2.5rem] text-brand">{{ $totalEvents }}</dd>
                    <dd class="mt-2 text-[10.5px] text-emerald-700 font-semibold">Approved &amp; Scheduled</dd>
                </div>
                <div class="px-4 py-5 border-slate-200 sm:border-r">
                    <dt class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-slate-500 leading-snug min-h-[2.4em]">Cumulative Headcount</dt>
                    <dd class="mt-3 flex items-baseline text-slate-900">
                        <span class="stat-value text-[2.5rem]">{{ number_format($totalHeadcount) }}</span><span class="font-display text-[1.4rem] text-gold">+</span>
                    </dd>
                    <dd class="mt-2 text-[10.5px] text-slate-500">Student Worship Attendance</dd>
                </div>
                <div class="px-4 py-5 border-slate-200 border-r border-t sm:border-t-0">
                    <dt class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-slate-500 leading-snug min-h-[2.4em]">Post-Activity Reports Filed</dt>
                    <dd class="mt-3 stat-value text-[2.5rem] text-slate-900">{{ $totalReports }}</dd>
                    <dd class="mt-2 text-[10.5px] text-slate-500">Chapel Manual §7.1.2 Filings</dd>
                </div>
                <div class="px-4 py-5 border-slate-200 border-t sm:border-t-0">
                    <dt class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-slate-500 leading-snug min-h-[2.4em]">Financial Execution</dt>
                    <dd class="mt-3 flex items-baseline text-brand">
                        <span class="font-display text-[1.3rem] opacity-60">$</span><span class="stat-value text-[2.5rem]">{{ number_format($totalSpending, 2) }}</span>
                    </dd>
                    <dd class="mt-2 text-[10.5px] text-slate-500 tabular-nums">Budgeted: ${{ number_format($totalBudget, 2) }}</dd>
                </div>
            </dl>
        </section>

        {{-- III. Fellowship ledger --}}
        <section class="mt-12">
            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                <h2 class="flex items-baseline gap-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-900">
                    <span class="section-numeral text-[15px] normal-case tracking-normal font-normal">III.</span>
                    Fellowship Performance &amp; Institutional Memory Index
                </h2>
                <span class="font-display italic text-[12.5px] text-slate-500">10 Official Chaplaincy Clubs</span>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="ledger w-full text-left text-[12px] tabular-nums">
                    <thead>
                        <tr>
                            <th scope="col" class="!pl-0">Fellowship / Club</th>
                            <th scope="col">Ministry Type</th>
                            <th scope="col" class="text-center">Roster Size</th>
                            <th scope="col" class="text-center">Activities</th>
                            <th scope="col" class="text-center">Reports Filed</th>
                            <th scope="col" class="text-right !pr-0">Vault Sentinel Health</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clubs as $club)
                            @php
                                $health = $club->getArchiveHealthScore();
                            @endphp
                            <tr>
                                <td class="!pl-0">
                                    <span class="font-display text-[14px] text-slate-900">{{ $club->name }}</span>
                                    <span class="text-slate-400 text-[11px]">({{ $club->code }})</span>
                                </td>
                                <td class="text-slate-500 capitalize text-[11.5px]">
                                    {{ $club->type }}
                                </td>
                                <td class="text-center text-slate-700">
                                    {{ $club->members_count }}
                                </td>
                                <td class="text-center font-semibold text-brand">
                                    {{ $club->events_count }}
                                </td>
                                <td class="text-center text-slate-700">
                                    {{ $club->reports_count }}
                                </td>
                                <td class="text-right !pr-0 whitespace-nowrap">
                                    <span class="block font-display text-[14px] text-slate-900">{{ $health['score'] }}%</span>
                                    <span class="mt-0.5 inline-block text-[10.5px] font-semibold {{ $health['score'] >= 70 ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $health['grade'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Attestation --}}
        <section class="report-signatures mt-16 grid grid-cols-1 sm:grid-cols-2 gap-10 sm:gap-16 text-[11.5px]">
            <div>
                <p class="text-[9.5px] font-bold uppercase tracking-[0.18em] text-slate-400">Prepared By</p>
                <div class="mt-10 border-t border-slate-400 pt-2.5">
                    <p class="font-display text-[15px] text-slate-900">Chapel Secretary &amp; Administrator</p>
                    <p class="text-slate-500">Africa University Chaplaincy Office</p>
                </div>
                <p class="mt-3 text-[10.5px] text-slate-400 tabular-nums">Date: {{ date('F d, Y') }}</p>
            </div>

            <div class="sm:text-right">
                <p class="text-[9.5px] font-bold uppercase tracking-[0.18em] text-slate-400">Approved &amp; Confirmed</p>
                <div class="mt-10 border-t border-slate-400 pt-2.5">
                    <p class="font-display text-[15px] text-brand">Rev. University Chaplain</p>
                    <p class="text-slate-500">Kwang-Lim Chapel &middot; Africa University</p>
                </div>
                <p class="mt-3 text-[10.5px] text-slate-400 italic">Official Seal Affixed</p>
            </div>
        </section>

        {{-- Colophon --}}
        <footer class="mt-16 pt-5 border-t border-slate-200 flex items-center justify-between gap-4 text-[10px] text-slate-400">
            <span class="uppercase tracking-[0.18em]">Kwang-Lim Chapel &middot; Africa University</span>
            <span class="tabular-nums">AU-CHAPEL-AR-{{ $year }}</span>
        </footer>
    </article>

</body>
</html>
