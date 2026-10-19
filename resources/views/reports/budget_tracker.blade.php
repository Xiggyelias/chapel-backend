@extends('layouts.app')

@section('title', 'Chapel Ministry Budget Tracker')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                <a href="{{ route('reports.index') }}" class="hover:underline font-bold">Activity Reports</a>
                <span>&bull;</span>
                <span class="text-slate-400 font-bold">Financial Governance</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold font-cinzel text-slate-900 tracking-tight">
                Ministry Budget Tracker & Variance Ledger
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Automatically reconciles approved activity proposals against post-activity expenditure reported under Chapel Manual §7.1.2
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('exports.budget_tracker') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold transition shadow-xs border border-slate-300 active:scale-95">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export to Excel</span>
            </a>
            <a href="{{ route('reports.annual_report') }}" target="_blank" class="inline-flex items-center space-x-1.5 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold transition shadow-sm active:scale-95">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Annual Ministry Report</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-5 card-elevation border border-slate-200/80 space-y-1 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-[#DC2626]"></div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block font-cinzel">Total Approved Budgets</span>
            <div class="text-3xl font-extrabold font-cinzel text-slate-900">${{ number_format($totalApprovedBudget, 2) }}</div>
            <p class="text-[11px] text-slate-500">Allocated across approved activity proposals</p>
        </div>

        <div class="bg-white rounded-3xl p-5 card-elevation border border-slate-200/80 space-y-1 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-slate-700"></div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block font-cinzel">Actual Reported Spending</span>
            <div class="text-3xl font-extrabold font-cinzel text-slate-900">${{ number_format($totalActualSpending, 2) }}</div>
            <p class="text-[11px] text-slate-500">Receipts & vouchers filed in post-activity reports</p>
        </div>

        <div class="bg-white rounded-3xl p-5 card-elevation border border-slate-200/80 space-y-1 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 {{ $variance >= 0 ? 'bg-emerald-600' : 'bg-rose-600' }}"></div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block font-cinzel">Net Budget Variance</span>
            <div class="text-3xl font-extrabold font-cinzel {{ $variance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $variance >= 0 ? '+' : '' }}${{ number_format($variance, 2) }}
            </div>
            <p class="text-[11px] {{ $variance >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                {{ $variance >= 0 ? 'Under-budget surplus' : 'Budget overrun requiring clearance' }}
            </p>
        </div>
    </div>

    <!-- Club Filter (for Office) -->
    @if(auth()->user()->isOffice())
        <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-5">
            <form method="GET" action="{{ route('reports.budget_tracker') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center space-x-2.5 w-full sm:w-auto">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap">Filter by Club:</label>
                    <select name="club_id" onchange="this.form.submit()" class="text-xs px-3.5 py-2 rounded-xl border border-slate-300 w-full sm:w-72 bg-slate-50/50">
                        <option value="">All Fellowships & Clubs</option>
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}" {{ $clubId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
                @if($clubId)
                    <a href="{{ route('reports.budget_tracker') }}" class="text-xs text-[#DC2626] font-bold hover:underline">
                        Clear Filter
                    </a>
                @endif
            </form>
        </div>
    @endif

    <!-- Reconciliation Table -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">
                Approved Activities vs Reported Spending
            </h3>
            <span class="text-xs text-slate-500 font-mono font-semibold">{{ $budgetItems->total() }} Approved Activities</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 uppercase tracking-wider text-[10px] border-b border-slate-200 font-cinzel font-bold">
                    <tr>
                        <th class="py-4 px-4">Ref & Date</th>
                        <th class="py-4 px-4">Club / Fellowship</th>
                        <th class="py-4 px-4">Activity Title</th>
                        <th class="py-4 px-4 text-right">Approved Budget</th>
                        <th class="py-4 px-4 text-right">Actual Spending</th>
                        <th class="py-4 px-4 text-right">Variance</th>
                        <th class="py-4 px-4 text-center">Report Filing</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($budgetItems as $item)
                        @php
                            $actual = $item->report ? $item->report->spending : 0;
                            $diff = $item->budget - $actual;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-4 px-4 font-mono">
                                <a href="{{ route('requests.show', $item->id) }}" class="text-[#DC2626] font-bold hover:underline block">
                                    {{ $item->reference_no }}
                                </a>
                                <span class="text-[10px] text-slate-400">{{ $item->event_date->format('M d, Y') }}</span>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-900">
                                {{ $item->club->name ?? 'Chapel Ministry' }}
                            </td>
                            <td class="py-4 px-4 font-medium text-slate-900 max-w-xs">
                                {{ $item->title }}
                            </td>
                            <td class="py-4 px-4 text-right font-mono font-bold text-slate-900">
                                ${{ number_format($item->budget, 2) }}
                            </td>
                            <td class="py-4 px-4 text-right font-mono font-bold text-slate-700">
                                @if($item->report)
                                    ${{ number_format($actual, 2) }}
                                @else
                                    <span class="text-amber-600 font-normal italic">Pending</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right font-mono font-bold {{ $diff >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                @if($item->report)
                                    {{ $diff >= 0 ? '+' : '' }}${{ number_format($diff, 2) }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($item->report)
                                    <a href="{{ route('reports.show', $item->report->id) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition">
                                        Report #{{ $item->report->id }} &bull; {{ $item->report->attendance }} Attendees
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Awaiting Report
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Approved Budgets on Record</h4>
                                    <p class="text-xs text-slate-400">Approved activity proposals with allocated budgets will automatically reconcile here against filed receipts.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($budgetItems->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $budgetItems->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
