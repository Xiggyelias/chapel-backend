@extends('layouts.app')

@section('title', 'Worship Planning')

@section('content')
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Kwang-Lim Chapel · Worship Liturgy" title="Worship Planning & Liturgy Sheets"
                   description="Weekly orders of worship for Sunday (9:00 a.m.) and Wednesday (8:00 a.m.) services per Worship Service Policy">
        <a href="{{ route('worship.archive') }}" class="btn btn-secondary">
            <svg class="text-gold" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Theme & Hymn Archive
        </a>
        @if(auth()->user()->isOffice())
            <a href="{{ route('worship.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Service Sheet
            </a>
        @endif
    </x-page-header>

    {{-- Worship Service Policy: the preparation cycle --}}
    <section class="card overflow-hidden">
        <div class="card-header">
            <div class="min-w-0">
                <p class="card-kicker">Worship Service Policy &amp; Order of Service</p>
                <h2 class="card-title mt-0.5">The week of preparation</h2>
            </div>
            <span class="badge badge-gold shrink-0">Liturgical Cycle</span>
        </div>
        <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-slate-100">
            @foreach([
                ['T-minus 3 Days', 'Preacher submits scripture, theme & hymns'],
                ['T-minus 2 Days', 'Media receives programme & slides'],
                ['T-minus 2 Days', 'Preparatory & coordination meeting'],
                ['T-minus 1 Day', 'Praise and Worship team rehearsal'],
            ] as $i => [$when, $what])
                <li class="bg-white px-5 py-4">
                    <div class="flex items-baseline gap-3">
                        <span class="font-display text-[1.5rem] leading-none text-gold tabular-nums" aria-hidden="true">{{ $i + 1 }}</span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-brand">{{ $when }}</p>
                            <p class="mt-1 text-[13px] text-slate-600 leading-snug">{{ $what }}</p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Service sheets --}}
    <section class="card overflow-hidden">
        <div class="card-header">
            <div class="min-w-0">
                <p class="card-kicker">Orders of worship</p>
                <h2 class="card-title mt-0.5">Service sheets</h2>
            </div>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($serviceSheets as $sheet)
                <article class="p-5 sm:px-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 transition-colors hover:bg-slate-50/70">
                    <div class="flex items-start gap-4 min-w-0">
                        {{-- Date, set like a hymn board card --}}
                        <div class="w-16 shrink-0 rounded-xl bg-white text-center py-2 ring-1 ring-slate-200 shadow-xs">
                            <span class="block text-[10px] font-bold uppercase tracking-[0.14em] text-brand">{{ $sheet->service_date->format('M') }}</span>
                            <span class="block stat-value text-[1.75rem] text-slate-900 mt-1">{{ $sheet->service_date->format('d') }}</span>
                            <span class="block text-[10px] font-medium text-slate-500 mt-1">{{ $sheet->service_date->format('D') }}</span>
                        </div>

                        <div class="min-w-0 space-y-1.5">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="badge">{{ $sheet->service_type }}</span>
                                @if($sheet->status === 'ready')
                                    <span class="badge badge-emerald badge-dot">Ready for Service</span>
                                @elseif($sheet->status === 'completed')
                                    <span class="badge">Completed</span>
                                @else
                                    <span class="badge badge-sky badge-dot">Draft Preparation</span>
                                @endif
                            </div>

                            <h3 class="font-display text-[1.125rem] leading-snug text-slate-900">
                                <span class="sr-only">Theme: </span><span class="text-slate-400 text-[0.95rem] mr-1" aria-hidden="true">Theme</span>&ldquo;{{ $sheet->theme ?: 'Worship Service' }}&rdquo;
                            </h3>

                            <dl class="text-[12.5px] text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-1">
                                <div class="flex gap-1"><dt class="font-semibold text-slate-700">Preacher:</dt><dd>{{ $sheet->preacher ?: 'University Chaplain' }}</dd></div>
                                @if($sheet->scripture)
                                    <div class="flex gap-1"><dt class="font-semibold text-slate-700">Scripture:</dt><dd>{{ $sheet->scripture }}</dd></div>
                                @endif
                                @if($sheet->liturgist)
                                    <div class="flex gap-1"><dt class="font-semibold text-slate-700">Liturgist:</dt><dd>{{ $sheet->liturgist }}</dd></div>
                                @endif
                                @if($sheet->choir)
                                    <div class="flex gap-1"><dt class="font-semibold text-slate-700">Choir:</dt><dd>{{ $sheet->choir }}</dd></div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5 shrink-0 lg:justify-end sm:pl-20 lg:pl-0">
                        <a href="{{ route('worship.bulletin', $sheet->id) }}" target="_blank" class="btn btn-ghost btn-sm">
                            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Bulletin PDF
                        </a>
                        <a href="{{ route('worship.slides', $sheet->id) }}" target="_blank" class="btn btn-secondary btn-sm">
                            <svg class="text-brand" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Slides
                        </a>
                        <a href="{{ route('worship.show', $sheet->id) }}" class="btn btn-primary btn-sm">View Sheet</a>
                        @if(auth()->user()->isOffice())
                            <a href="{{ route('worship.edit', $sheet->id) }}" class="btn btn-ghost btn-sm !px-2" title="Edit service sheet">
                                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span class="sr-only">Edit service sheet</span>
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <div class="empty-state-mark" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="empty-state-title">No Worship Service Sheets Yet</h3>
                    <p class="empty-state-text">Prepare and post upcoming Sunday or Wednesday service orders, scripture readings, and hymns.</p>
                    @if(auth()->user()->isOffice())
                        <a href="{{ route('worship.create') }}" class="btn btn-primary btn-sm mt-4">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Create Service Sheet
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        @if($serviceSheets->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $serviceSheets->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
