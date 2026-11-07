@extends('layouts.app')

@section('title', 'Institutional Audit Trail & Governance')

@section('content')
@php
    $auditFilters = [
        ['All Events', null, empty($actionFilter)],
        ['Approvals Only', 'approve', $actionFilter === 'approve'],
        ['Submissions', 'submit', $actionFilter === 'submit'],
        ['VC Clearances', 'vc_go_ahead', $actionFilter === 'vc_go_ahead'],
    ];
@endphp
<div class="page-shell space-y-6">

    <header class="page-header">
        <div class="min-w-0">
            <nav aria-label="Breadcrumb" class="eyebrow">
                <a href="{{ route('dashboard') }}" class="hover:underline underline-offset-4">Dashboard</a>
                <span class="text-slate-300" aria-hidden="true">/</span>
                <span class="text-slate-500">Institutional Governance</span>
            </nav>
            <h1 class="page-title">Security &amp; Activity Audit Ledger</h1>
            <p class="page-lede">Immutable, cryptographic-stamped activity records capturing every proposal change, approval decision, VC concurrence, and document transaction.</p>
        </div>
        <div class="page-actions">
            <span class="badge badge-emerald py-1 px-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 breathe" style="--breathe: 5 150 105" aria-hidden="true"></span>
                Audit Sentinel Active
            </span>
        </div>
    </header>

    {{-- Ledger at a glance --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="dash-stat-card hover:!translate-y-0" style="--tone: var(--color-gold)">
            <span class="flex items-center gap-2 text-[12.5px] font-semibold text-slate-500">
                <span class="w-1.5 h-1.5 rounded-full bg-gold" aria-hidden="true"></span>
                Total Recorded Transactions
            </span>
            <div class="mt-4 stat-value text-[2.4rem] text-slate-900">{{ number_format($totalLogsCount) }}</div>
            <div class="mt-2 text-[12.5px] text-slate-500">All actions logged permanently</div>
        </div>
        <div class="dash-stat-card hover:!translate-y-0" style="--tone: var(--color-brand)">
            <span class="flex items-center gap-2 text-[12.5px] font-semibold text-slate-500">
                <span class="w-1.5 h-1.5 rounded-full bg-brand" aria-hidden="true"></span>
                Chaplaincy Decisions Audited
            </span>
            <div class="mt-4 stat-value text-[2.4rem] text-brand-dark">{{ number_format($approvalActionsCount) }}</div>
            <div class="mt-2 text-[12.5px] text-slate-500">Approvals, declines, changes requested</div>
        </div>
        <div class="dash-stat-card hover:!translate-y-0" style="--tone: var(--color-slate-300)">
            <span class="flex items-center gap-2 text-[12.5px] font-semibold text-slate-500">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                Proposals Submitted
            </span>
            <div class="mt-4 stat-value text-[2.4rem] text-slate-900">{{ number_format($submissionActionsCount) }}</div>
            <div class="mt-2 text-[12.5px] text-slate-500">Student leader filings</div>
        </div>
    </div>

    {{-- Ledger --}}
    <section class="card overflow-hidden" aria-labelledby="ledger-heading">
        <div class="card-header flex-col items-stretch md:flex-row md:items-center">
            <div class="min-w-0">
                <p class="card-kicker font-mono !tracking-normal !normal-case">Immutable SQLite / MySQL DB Ledger</p>
                <h2 id="ledger-heading" class="card-title mt-0.5">Activity ledger</h2>
            </div>
            <div class="overflow-x-auto -mx-1 px-1">
                <nav class="segmented" aria-label="Filter by action">
                    @foreach($auditFilters as [$label, $action, $isActive])
                        <a href="{{ $action ? route('audit.index', ['action' => $action]) : route('audit.index') }}"
                           class="whitespace-nowrap {{ $isActive ? 'is-active' : '' }}"
                           @if($isActive) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="table-wrap">
            <table class="table min-w-[62rem]">
                <thead>
                    <tr>
                        <th scope="col">Timestamp (CAT)</th>
                        <th scope="col">Authorized User</th>
                        <th scope="col">Action Taken</th>
                        <th scope="col">Related Dossier</th>
                        <th scope="col">Status Transition</th>
                        <th scope="col">Operational Remarks</th>
                        <th scope="col" class="text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $actionTone = match (true) {
                                str_contains($log->action, 'decline') => 'badge-rose',
                                str_contains($log->action, 'vc')      => 'badge-gold',
                                str_contains($log->action, 'approve') => 'badge-emerald',
                                str_contains($log->action, 'submit')  => 'badge-sky',
                                default                               => '',
                            };
                        @endphp
                        <tr>
                            <td class="w-[6.5rem] font-mono text-[12.5px] leading-snug text-slate-500">
                                {{ $log->created_at->timezone('Africa/Harare')->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="min-w-[9rem]">
                                <div class="font-semibold text-slate-900">{{ $log->user_name ?: ($log->user?->name ?: 'System Automatic') }}</div>
                                @if($log->user)
                                    <div class="text-[12px] text-slate-400">{{ $log->user->getRoleDisplayName() }}</div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="badge {{ $actionTone }} font-mono !text-[11.5px]">{{ $log->action }}</span>
                            </td>
                            <td class="min-w-[13rem]">
                                @if($log->request)
                                    <a href="{{ route('requests.show', $log->request?->id) }}" class="font-semibold text-brand hover:text-brand-dark hover:underline underline-offset-2">
                                        {{ $log->request?->title }}
                                    </a>
                                    <div class="text-[12px] text-slate-400">Ref: {{ $log->request?->reference_no }} &bull; {{ $log->request?->club?->name }}</div>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-[12px]">
                                @if($log->old_status || $log->new_status)
                                    <span class="text-slate-400">{{ $log->old_status ?: 'start' }}</span>
                                    <span class="text-gold mx-1" aria-hidden="true">&rarr;</span>
                                    <span class="font-semibold text-slate-800">{{ $log->new_status }}</span>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                            <td class="text-slate-600 min-w-[12rem] max-w-[15rem]" title="{{ $log->notes }}">
                                <span class="line-clamp-2">
                                {{ $log->notes ?: 'Standard operational execution' }}</span>
                            </td>
                            <td class="text-right font-mono text-[12.5px] text-slate-400">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="!p-0">
                                <div class="empty-state">
                                    <div class="empty-state-mark" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="empty-state-title">Nothing recorded</p>
                                    <p class="empty-state-text">No audit events logged under the selected criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
