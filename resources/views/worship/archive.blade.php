@extends('layouts.app')

@section('title', 'Theme & Hymn History Archive')

@section('content')
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Worship Planning · Institutional Memory" title="Theme, Scripture & Hymn History Archive"
                   description="Search past sermons, scripture readings, and hymn frequencies in Africa University's chapel database to prevent repetition and track ministry continuity.">
        <a href="{{ route('worship.index') }}" class="btn btn-ghost">
            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12l7.5-7.5M3 12h18"/></svg>
            Back to Service Sheets
        </a>
        @if(auth()->user()->isOffice())
            <a href="{{ route('worship.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Service Sheet
            </a>
        @endif
    </x-page-header>

    {{-- Search & filter --}}
    <section class="card">
        <form method="GET" action="{{ route('worship.archive') }}" class="card-body grid grid-cols-1 sm:grid-cols-4 gap-4 items-end" role="search">
            <div class="sm:col-span-2">
                <label for="archive-search" class="field-label">Search Keyword (Theme, Hymn, Scripture, or Preacher)</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="archive-search" name="search" value="{{ $search }}" placeholder="e.g. O For a Thousand Tongues, Romans 12, Hope, Grace..." class="field pl-10">
                </div>
            </div>

            <div>
                <label for="archive-preacher" class="field-label">Preacher Filter</label>
                <select id="archive-preacher" name="preacher" class="field">
                    <option value="">All Preachers</option>
                    @foreach($preachersList as $p)
                        <option value="{{ $p }}" {{ $preacherFilter == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">Search Archive</button>
                @if($search || $preacherFilter || $yearFilter)
                    <a href="{{ route('worship.archive') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </section>

    {{-- Archive records --}}
    <section class="card overflow-hidden">
        <div class="card-header flex-wrap">
            <div class="min-w-0">
                <p class="card-kicker">Historical Worship Records</p>
                <h2 class="card-title mt-0.5"><span class="stat-value">{{ $records->total() }}</span> matches</h2>
            </div>
            <span class="text-[12px] text-slate-500">Total Chapel Archives: <strong class="font-semibold text-slate-700 tabular-nums">{{ $totalServices }}</strong> services</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date & Service</th>
                        <th>Theme</th>
                        <th>Scripture</th>
                        <th>Preacher</th>
                        <th>Hymns Sung</th>
                        <th class="text-center">Accessibility</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        <tr>
                            <td class="whitespace-nowrap">
                                <div class="font-semibold text-slate-900 tabular-nums">{{ $rec->service_date->format('M d, Y') }}</div>
                                <div class="text-[12px] text-slate-500">{{ $rec->service_type }}</div>
                            </td>
                            <td class="min-w-[12rem] max-w-xs">
                                <span class="font-display text-[14px] leading-snug text-slate-900">{{ $rec->theme ?: '—' }}</span>
                            </td>
                            <td class="text-slate-600 min-w-[9rem]">
                                {{ $rec->scripture ?: '—' }}
                            </td>
                            <td class="font-semibold text-slate-800 min-w-[9rem]">
                                {{ $rec->preacher ?: 'Chaplaincy' }}
                            </td>
                            <td class="min-w-[15rem] max-w-sm text-slate-600 leading-snug text-[12.5px]">
                                {{ $rec->hymns ?: 'Announced in service' }}
                            </td>
                            <td class="text-center">
                                @if($rec->sign_interpreter_required)
                                    <span class="badge badge-emerald" title="{{ $rec->sign_interpreter_name ?: 'Sign Language Interpreter' }}">Sign Int.</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('worship.slides', $rec->id) }}" target="_blank" title="Project Slides" class="btn btn-secondary btn-sm">Slides</a>
                                    <a href="{{ route('worship.bulletin', $rec->id) }}" target="_blank" title="Print Bulletin" class="btn btn-ghost btn-sm">PDF / Print</a>
                                    <a href="{{ route('worship.show', $rec->id) }}" title="View Sheet" class="btn btn-ghost btn-sm text-brand">Details</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-mark" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <p class="empty-state-title">Nothing in the archive matches</p>
                                    <p class="empty-state-text">No historical service sheets matched your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $records->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
