@extends('layouts.app')

@section('title', 'Chapel Ministry Budget Tracker')

@section('content')
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Financial governance"
                   title="Ministry Budget Tracker & Variance Ledger"
                   description="Automatically reconciles approved activity proposals against post-activity expenditure reported under Chapel Manual §7.1.2.">
        <a href="{{ route('reports.index') }}" class="btn btn-ghost">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Activity Reports
        </a>
        <a href="{{ route('exports.budget_tracker') }}" class="btn btn-secondary">
            <svg class="text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export to Excel
        </a>
        <a href="{{ route('reports.annual_report') }}" target="_blank" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Annual Ministry Report
        </a>
    </x-page-header>

    {{-- Summary --}}
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="dash-stat-card" style="--tone: var(--color-gold)">
            <dt class="text-[12.5px] font-semibold text-slate-500">Total Approved Budgets</dt>
            <dd class="mt-4 flex items-baseline gap-0.5 text-slate-900">
                <span class="font-display text-[1.4rem] text-slate-400">$</span><span class="stat-value text-[2.4rem]">{{ number_format($totalApprovedBudget, 2) }}</span>
            </dd>
            <p class="mt-2 text-[12.5px] text-slate-500">Allocated across approved activity proposals</p>
        </div>

        <div class="dash-stat-card" style="--tone: var(--color-slate-600)">
            <dt class="text-[12.5px] font-semibold text-slate-500">Actual Reported Spending</dt>
            <dd class="mt-4 flex items-baseline gap-0.5 text-slate-900">
                <span class="font-display text-[1.4rem] text-slate-400">$</span><span class="stat-value text-[2.4rem]">{{ number_format($totalActualSpending, 2) }}</span>
            </dd>
            <p class="mt-2 text-[12.5px] text-slate-500">Receipts &amp; vouchers filed in post-activity reports</p>
        </div>

        <div class="dash-stat-card" style="--tone: {{ $variance >= 0 ? '#059669' : '#E11D48' }}">
            <dt class="text-[12.5px] font-semibold text-slate-500">Net Budget Variance</dt>
            <dd class="mt-4 flex items-baseline gap-0.5 {{ $variance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                <span class="font-display text-[1.4rem] opacity-60">{{ $variance >= 0 ? '+' : '−' }}$</span><span class="stat-value text-[2.4rem]">{{ number_format(abs($variance), 2) }}</span>
            </dd>
            <p class="mt-2 text-[12.5px] font-semibold {{ $variance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $variance >= 0 ? 'Under-budget surplus' : 'Budget overrun requiring clearance' }}
            </p>
        </div>
    </dl>

    {{-- Reconciliation --}}
    <section class="card overflow-hidden">
        <div class="card-header flex-wrap">
            <div class="min-w-0">
                <p class="card-kicker"><span class="tabular-nums">{{ $budgetItems->total() }}</span> Approved Activities</p>
                <h2 class="card-title mt-0.5">Approved activities vs reported spending</h2>
            </div>

            @if(auth()->user()->isOffice())
                <form method="GET" action="{{ route('reports.budget_tracker') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="budget_club_filter" class="text-[12px] font-semibold text-slate-600 whitespace-nowrap">Filter by Club:</label>
                    <select id="budget_club_filter" name="club_id" onchange="this.form.submit()" class="field py-1.5 w-full sm:w-64">
                        <option value="">All Fellowships & Clubs</option>
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}" {{ $clubId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                    @if($clubId)
                        <a href="{{ route('reports.budget_tracker') }}" class="btn btn-ghost btn-sm shrink-0">
                            Clear Filter
                        </a>
                    @endif
                </form>
            @endif
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="!pl-5">Ref &amp; Date</th>
                        <th scope="col">Club / Fellowship</th>
                        <th scope="col">Activity Title</th>
                        <th scope="col" class="text-right">Approved Budget</th>
                        <th scope="col" class="text-right">Actual Spending</th>
                        <th scope="col" class="text-right">Variance</th>
                        <th scope="col" class="text-center !pr-5">Report Filing</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgetItems as $item)
                        @php
                            $actual = $item->report ? $item->report->spending : 0;
                            $diff = $item->budget - $actual;
                        @endphp
                        <tr>
                            <td class="!pl-5 whitespace-nowrap">
                                <a href="{{ route('requests.show', $item->id) }}" class="font-semibold text-brand hover:text-brand-dark tabular-nums block">
                                    {{ $item->reference_no }}
                                </a>
                                <span class="text-[12.5px] text-slate-500">{{ $item->event_date->format('M d, Y') }}</span>
                            </td>
                            <td class="text-slate-700">
                                {{ $item->club?->name ?? 'Chapel Ministry' }}
                            </td>
                            <td class="font-semibold text-slate-900 max-w-xs">
                                {{ $item->title }}
                            </td>
                            <td class="text-right tabular-nums font-semibold text-slate-900 whitespace-nowrap">
                                ${{ number_format($item->budget, 2) }}
                            </td>
                            <td class="text-right tabular-nums text-slate-700 whitespace-nowrap">
                                @if($item->report)
                                    ${{ number_format($actual, 2) }}
                                @else
                                    <span class="text-amber-700 italic">Pending</span>
                                @endif
                            </td>
                            <td class="text-right tabular-nums font-semibold whitespace-nowrap {{ $diff >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                @if($item->report)
                                    {{ $diff >= 0 ? '+' : '' }}${{ number_format($diff, 2) }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="text-center !pr-5">
                                @if($item->report)
                                    <a href="{{ route('reports.show', $item->report->id) }}" class="badge badge-emerald hover:brightness-95 transition">
                                        Report #{{ $item->report->id }} · {{ $item->report->attendance }} Attendees
                                    </a>
                                @else
                                    <span class="badge badge-amber badge-dot">Awaiting Report</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-mark" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="empty-state-title">No approved budgets on record</p>
                                    <p class="empty-state-text">Approved activity proposals with allocated budgets will automatically reconcile here against filed receipts.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($budgetItems->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $budgetItems->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
