@extends('layouts.app')

@section('title', 'Post-Activity Reports')

@section('content')
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Accountability & evaluation"
                   title="Post-Activity Reports & Accountability"
                   description="Official post-event evaluation: attendance stats, spiritual outcomes, financial expenditure, and event documentation.">
        <a href="{{ route('reports.budget_tracker') }}" class="btn btn-secondary">
            <svg class="text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Budget Tracker &amp; Variance
        </a>

        @if(!auth()->user()->isCampusLifeSrc())
            <a href="{{ route('reports.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Submit Activity Report
            </a>
        @endif
    </x-page-header>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div class="min-w-0">
                <p class="card-kicker">Filed with the Chaplaincy</p>
                <h2 class="card-title mt-0.5">Submitted club reports</h2>
            </div>
            <span class="text-[12px] text-slate-500"><span class="font-semibold text-slate-800 tabular-nums">{{ $reports->total() }}</span> Reports Filed</span>
        </div>

        @forelse($reports as $rep)
            <article class="group flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 px-5 py-5 border-b last:border-0 border-slate-100 transition-colors hover:bg-slate-50/70">
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-[11.5px] font-semibold text-slate-700">
                            <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $rep->club?->color_hex ?? '#A51C30' }}" aria-hidden="true"></span>
                            {{ $rep->club?->name }} ({{ $rep->club?->code }})
                        </span>
                        @if($rep->status === 'reviewed')
                            <span class="badge badge-emerald badge-dot">Reviewed by Chaplaincy</span>
                        @else
                            <span class="badge badge-sky badge-dot">Submitted</span>
                        @endif
                    </div>

                    <h3 class="font-display text-[1.0625rem] leading-snug text-slate-900">
                        <a href="{{ route('reports.show', $rep->id) }}" class="hover:text-brand transition-colors">
                            {{ $rep->title }}
                        </a>
                    </h3>

                    <dl class="flex flex-wrap items-center gap-x-5 gap-y-1 text-[12px] text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <dt>Attendance:</dt>
                            <dd class="font-semibold text-slate-800 tabular-nums">{{ $rep->attendance }} participants</dd>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <dt>Total Expenditure:</dt>
                            <dd class="font-semibold text-slate-800 tabular-nums">${{ number_format($rep->spending, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="inline">Submitted by:</dt>
                            <dd class="inline">{{ $rep->submitter?->name ?? 'Club Executive' }} · {{ $rep->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <a href="{{ route('reports.show', $rep->id) }}" class="btn btn-secondary btn-sm self-start sm:self-auto shrink-0">
                    Read Report
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </article>
        @empty
            <div class="empty-state">
                <div class="empty-state-mark" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="empty-state-title">No activity reports filed yet</p>
                <p class="empty-state-text">File post-event summaries, attendances, and financial expenditure reconciliations.</p>
                @if(!auth()->user()->isCampusLifeSrc())
                    <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm mt-5">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Submit Activity Report
                    </a>
                @endif
            </div>
        @endforelse
    </section>

    @if($reports->hasPages())
        <div>{{ $reports->links() }}</div>
    @endif

</div>
@endsection
