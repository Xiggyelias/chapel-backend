<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Africa University Kwang-Lim Chapel - Annual Ministry Report {{ $year }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .font-cinzel { font-family: 'Cinzel', Georgia, serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; padding: 0 !important; }
            .report-card { border: none !important; box-shadow: none !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body class="p-4 sm:p-10 text-xs">

    <!-- Top Action Bar (hidden on print) -->
    <div class="no-print max-w-5xl mx-auto mb-6 flex items-center justify-between">
        <a href="{{ route('reports.budget_tracker') }}" class="text-xs text-[#DC2626] font-bold hover:underline flex items-center space-x-1">
            <span>&larr; Back to Budget Tracker</span>
        </a>
        <div class="flex items-center space-x-3">
            <a href="{{ route('exports.budget_tracker') }}" class="px-4 py-2 rounded-xl bg-white text-slate-800 text-xs font-bold border border-slate-300 hover:bg-slate-50 transition shadow-xs">
                Download CSV Data
            </a>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold transition shadow-sm">
                Print / Save PDF Report
            </button>
        </div>
    </div>

    <!-- Official Report Paper Container -->
    <div class="report-card max-w-5xl mx-auto bg-white rounded-3xl p-8 sm:p-12 shadow-xl border-2 border-slate-200/80 space-y-8">
        
        <!-- Institutional Header -->
        <div class="border-b-2 border-[#DC2626] pb-6 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-[#DC2626] border border-white/20 flex items-center justify-center p-2.5 shadow-md shrink-0">
                    <x-umc-logo class="w-full h-full" />
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#DC2626] block">
                        Office of the Chaplain
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                        Kwang-Lim Chapel Annual Ministry Report
                    </h1>
                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                        Academic Year {{ $year }}/{{ $year + 1 }} &bull; Kwang-Lim Chapel
                    </p>
                </div>
            </div>

            <div class="text-right sm:border-l border-slate-200 sm:pl-6 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Report Dossier Ref</span>
                <span class="font-mono font-bold text-xs text-[#DC2626]">AU-CHAPEL-AR-{{ $year }}</span>
                <span class="text-[10px] text-slate-400 block font-mono">Issued: {{ date('F d, Y') }}</span>
            </div>
        </div>

        <!-- Executive Narrative Summary -->
        <div class="space-y-2">
            <h2 class="font-cinzel text-sm font-bold uppercase tracking-wider text-[#DC2626]">
                Executive Chaplaincy Governance Summary
            </h2>
            <p class="text-slate-700 leading-relaxed text-justify">
                In compliance with the Africa University Chapel Manual and Student Life Regulations, this annual report consolidates ministry activities, worship celebrations, post-activity evaluations, financial allocations, and permanent institutional records across all ten official student fellowships and clubs under the Kwang-Lim Chapel spiritual umbrella. The Chaplaincy Office certifies that activities herein were conducted in accordance with university policy, maintaining the highest standards of Christian discipleship, pan-African unity, and responsible student leadership.
            </p>
        </div>

        <!-- High-Impact Key Performance Telemetry -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Total Activities Conducted</span>
                <div class="text-2xl font-black font-cinzel text-[#DC2626]">{{ $totalEvents }}</div>
                <span class="text-[10px] text-emerald-700 font-semibold">Approved & Scheduled</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Cumulative Headcount</span>
                <div class="text-2xl font-black font-cinzel text-slate-900">{{ number_format($totalHeadcount) }}+</div>
                <span class="text-[10px] text-slate-500">Student Worship Attendance</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Post-Activity Reports Filed</span>
                <div class="text-2xl font-black font-cinzel text-slate-900">{{ $totalReports }}</div>
                <span class="text-[10px] text-slate-500">Chapel Manual §7.1.2 Filings</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Financial Execution</span>
                <div class="text-2xl font-black font-cinzel text-[#DC2626]">${{ number_format($totalSpending, 2) }}</div>
                <span class="text-[10px] text-slate-500">Budgeted: ${{ number_format($totalBudget, 2) }}</span>
            </div>
        </div>

        <!-- Club Performance & Vault Sentinel Institutional Health Ledger -->
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                <h3 class="font-cinzel text-sm font-bold uppercase tracking-wider text-[#DC2626]">
                    Fellowship Performance & Institutional Memory Index
                </h3>
                <span class="text-[10px] text-slate-400 font-mono">10 Official Chaplaincy Clubs</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-600 text-[10px] uppercase font-bold tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Fellowship / Club</th>
                            <th class="py-3 px-3">Ministry Type</th>
                            <th class="py-3 px-3 text-center">Roster Size</th>
                            <th class="py-3 px-3 text-center">Activities</th>
                            <th class="py-3 px-3 text-center">Reports Filed</th>
                            <th class="py-3 px-3 text-right">Vault Sentinel Health</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($clubs as $club)
                            @php
                                $health = $club->getArchiveHealthScore();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3 font-bold text-slate-900">
                                    {{ $club->name }} ({{ $club->code }})
                                </td>
                                <td class="py-3 px-3 text-slate-600 uppercase text-[10px] font-semibold">
                                    {{ $club->type }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono">
                                    {{ $club->members_count }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-[#DC2626]">
                                    {{ $club->events_count }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono">
                                    {{ $club->reports_count }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <span class="font-mono font-bold text-slate-900">{{ $health['score'] }}%</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase ml-1" style="background-color: {{ $health['score'] >= 70 ? '#e6f4ea' : '#fef7e0' }}; color: {{ $health['score'] >= 70 ? '#137333' : '#b06000' }}">
                                        {{ $health['grade'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Official Signatures Box -->
        <div class="pt-8 border-t-2 border-slate-200 grid grid-cols-2 gap-8 text-[11px]">
            <div class="space-y-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Prepared By</span>
                <div class="border-b border-slate-300 pb-1">
                    <span class="font-bold text-slate-900 block">Chapel Secretary & Administrator</span>
                    <span class="text-slate-500">Africa University Chaplaincy Office</span>
                </div>
                <div class="text-[10px] text-slate-400 font-mono">Date: {{ date('F d, Y') }}</div>
            </div>

            <div class="space-y-4 text-right">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Approved & Confirmed</span>
                <div class="border-b border-slate-300 pb-1">
                    <span class="font-bold text-[#DC2626] block">Rev. University Chaplain</span>
                    <span class="text-slate-500">Kwang-Lim Chapel &bull; Africa University</span>
                </div>
                <div class="text-[10px] text-slate-400 font-mono">Official Seal Affixed</div>
            </div>
        </div>

    </div>

</body>
</html>
