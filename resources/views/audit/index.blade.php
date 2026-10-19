@extends('layouts.app')

@section('title', 'Institutional Audit Trail & Governance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 animate-fade-in">

    <!-- Top Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
                <span>&bull;</span>
                <span class="text-slate-400 font-bold">Institutional Governance</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold font-cinzel text-slate-900 tracking-tight mt-1">
                Security & Activity Audit Ledger
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Immutable, cryptographic-stamped activity records capturing every proposal change, approval decision, VC concurrence, and document transaction.
            </p>
        </div>

        <!-- Filter Action Pills -->
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>Audit Sentinel Active</span>
            </span>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Recorded Transactions</span>
            <div class="text-2xl font-black font-cinzel text-slate-900">{{ number_format($totalLogsCount) }}</div>
            <span class="text-[11px] text-slate-500">All actions logged permanently</span>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Chaplaincy Decisions Audited</span>
            <div class="text-2xl font-black font-cinzel text-[#DC2626]">{{ number_format($approvalActionsCount) }}</div>
            <span class="text-[11px] text-red-700">Approvals, declines, changes requested</span>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Proposals Submitted</span>
            <div class="text-2xl font-black font-cinzel text-indigo-900">{{ number_format($submissionActionsCount) }}</div>
            <span class="text-[11px] text-indigo-700">Student leader filings</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('audit.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ empty($actionFilter) ? 'bg-[#DC2626] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All Events
            </a>
            <a href="{{ route('audit.index', ['action' => 'approve']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $actionFilter === 'approve' ? 'bg-[#DC2626] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Approvals Only
            </a>
            <a href="{{ route('audit.index', ['action' => 'submit']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $actionFilter === 'submit' ? 'bg-[#DC2626] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Submissions
            </a>
            <a href="{{ route('audit.index', ['action' => 'vc_go_ahead']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $actionFilter === 'vc_go_ahead' ? 'bg-[#DC2626] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                VC Clearances
            </a>
        </div>
        <div class="text-xs text-slate-500 font-mono">
            Immutable SQLite / MySQL DB Ledger
        </div>
    </div>

    <!-- Audit Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Timestamp (CAT)</th>
                        <th class="py-3 px-4">Authorized User</th>
                        <th class="py-3 px-4">Action Taken</th>
                        <th class="py-3 px-4">Related Dossier</th>
                        <th class="py-3 px-4">Status Transition</th>
                        <th class="py-3 px-4">Operational Remarks</th>
                        <th class="py-3 px-4 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                {{ $log->created_at->timezone('Africa/Harare')->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $log->user_name ?: ($log->user?->name ?: 'System Automatic') }}</div>
                                @if($log->user)
                                    <div class="text-[10px] text-slate-400">{{ $log->user->getRoleDisplayName() }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider
                                    {{ str_contains($log->action, 'approve') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-600/20' : '' }}
                                    {{ str_contains($log->action, 'decline') ? 'bg-rose-50 text-rose-800 ring-1 ring-rose-600/20' : '' }}
                                    {{ str_contains($log->action, 'submit') ? 'bg-indigo-50 text-indigo-800 ring-1 ring-indigo-600/20' : '' }}
                                    {{ str_contains($log->action, 'vc') ? 'bg-purple-50 text-purple-800 ring-1 ring-purple-600/20' : '' }}
                                    {{ !str_contains($log->action, 'approve') && !str_contains($log->action, 'decline') && !str_contains($log->action, 'submit') && !str_contains($log->action, 'vc') ? 'bg-slate-100 text-slate-700' : '' }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($log->request)
                                    <a href="{{ route('requests.show', $log->request->id) }}" class="font-semibold text-[#DC2626] hover:underline">
                                        {{ $log->request->title }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">Ref: {{ $log->request->reference_no }} &bull; {{ $log->request->club?->name }}</div>
                                @else
                                    <span class="text-slate-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($log->old_status || $log->new_status)
                                    <span class="text-slate-400">{{ $log->old_status ?: 'start' }}</span>
                                    <span class="text-slate-300 mx-1">&rarr;</span>
                                    <span class="font-bold text-slate-800">{{ $log->new_status }}</span>
                                @else
                                    <span class="text-slate-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate" title="{{ $log->notes }}">
                                {{ $log->notes ?: 'Standard operational execution' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-[11px] text-slate-400">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                No audit events logged under the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
