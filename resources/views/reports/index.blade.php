@extends('layouts.app')

@section('title', 'Post-Activity Reports')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                <span>Kwang-Lim Chapel</span>
                <span>&bull;</span>
                <span class="text-slate-400 font-bold">Accountability &amp; Evaluation</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Post-Activity Reports & Accountability
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Official post-event evaluation: attendance stats, spiritual outcomes, financial expenditure, and event documentation
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('reports.budget_tracker') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold transition shadow-xs border border-slate-300 active:scale-95">
                <svg class="w-4 h-4 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Budget Tracker &amp; Variance</span>
            </a>

            @if(!auth()->user()->isCampusLifeSrc())
                <a href="{{ route('reports.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm self-start sm:self-auto active:scale-95">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Submit Activity Report</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-hidden divide-y divide-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">Submitted Club Reports</h3>
            <span class="text-xs text-slate-500 font-semibold">{{ $reports->total() }} Reports Filed</span>
        </div>

        @forelse($reports as $rep)
            <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded text-white" style="background-color: {{ $rep->club->color_hex ?? '#DC2626' }}">
                            {{ $rep->club->name }} ({{ $rep->club->code }})
                        </span>
                        @if($rep->status === 'reviewed')
                            <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full">Reviewed by Chaplaincy</span>
                        @else
                            <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full">Submitted</span>
                        @endif
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 font-cinzel">
                        <a href="{{ route('reports.show', $rep->id) }}" class="hover:text-[#DC2626] hover:underline transition">
                            {{ $rep->title }}
                        </a>
                    </h4>

                    <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span>👥 Attendance: <strong class="text-slate-900 font-mono">{{ $rep->attendance }} participants</strong></span>
                        <span>💰 Total Expenditure: <strong class="text-slate-900 font-mono">${{ number_format($rep->spending, 2) }}</strong></span>
                        <span>Submitted by: {{ $rep->submitter->name ?? 'Club Executive' }} &bull; {{ $rep->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('reports.show', $rep->id) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-[#DC2626] hover:text-white text-slate-700 text-xs font-bold transition active:scale-95">
                        <span>Read Report</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-slate-500 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#DC2626] flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">No Activity Reports Filed Yet</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">File post-event summaries, attendances, and financial expenditure reconciliations.</p>
                @if(!auth()->user()->isCampusLifeSrc())
                    <div class="pt-1">
                        <a href="{{ route('reports.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs">
                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Submit Activity Report</span>
                        </a>
                    </div>
                @endif
            </div>
        @endforelse
    </div>

</div>
@endsection
