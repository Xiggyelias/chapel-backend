@extends('layouts.app')

@section('title', 'Theme & Hymn History Archive')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 animate-fade-in">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                <a href="{{ route('worship.index') }}" class="hover:underline">Worship Planning</a>
                <span>&bull;</span>
                <span class="text-slate-400 font-bold">Institutional Memory</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold font-cinzel text-slate-900 tracking-tight mt-1">
                Theme, Scripture & Hymn History Archive
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Search past sermons, scripture readings, and hymn frequencies in Africa University's chapel database to prevent repetition and track ministry continuity.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('worship.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition border border-slate-300 shadow-xs">
                <span>&larr; Back to Service Sheets</span>
            </a>
            @if(auth()->user()->isOffice())
                <a href="{{ route('worship.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold transition shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>New Service Sheet</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Search & Filter Form -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5">
        <form method="GET" action="{{ route('worship.archive') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Search Keyword (Theme, Hymn, Scripture, or Preacher)</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="e.g. O For a Thousand Tongues, Romans 12, Hope, Grace..." class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Preacher Filter</label>
                <select name="preacher" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                    <option value="">All Preachers</option>
                    @foreach($preachersList as $p)
                        <option value="{{ $p }}" {{ $preacherFilter == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold text-xs shadow-sm transition">
                    Search Archive
                </button>
                @if($search || $preacherFilter || $yearFilter)
                    <a href="{{ route('worship.archive') }}" class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs border border-slate-300 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Archive Records Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/60">
            <h2 class="text-xs font-bold text-[#DC2626] uppercase tracking-wider">
                Historical Worship Records ({{ $records->total() }} matches)
            </h2>
            <span class="text-xs text-slate-500 font-mono">Total Chapel Archives: {{ $totalServices }} services</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200/80 font-bold">
                    <tr>
                        <th class="py-3 px-4">Date & Service</th>
                        <th class="py-3 px-4">Theme</th>
                        <th class="py-3 px-4">Scripture</th>
                        <th class="py-3 px-4">Preacher</th>
                        <th class="py-3 px-4">Hymns Sung</th>
                        <th class="py-3 px-4 text-center">Accessibility</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $rec->service_date->format('M d, Y') }}</div>
                                <div class="text-[10px] text-slate-500">{{ $rec->service_type }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900 max-w-xs">
                                {{ $rec->theme ?: '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono text-[11px]">
                                {{ $rec->scripture ?: '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-[#DC2626] font-semibold">
                                {{ $rec->preacher ?: 'Chaplaincy' }}
                            </td>
                            <td class="py-3.5 px-4 max-w-xs text-slate-600 leading-snug">
                                {{ $rec->hymns ?: 'Announced in service' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($rec->sign_interpreter_required)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200" title="{{ $rec->sign_interpreter_name ?: 'Sign Language Interpreter' }}">
                                        Sign Int.
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('worship.slides', $rec->id) }}" target="_blank" title="Project Slides" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-[#DC2626] text-white text-[10px] font-bold hover:bg-[#B91C1C] transition">
                                    Slides
                                </a>
                                <a href="{{ route('worship.bulletin', $rec->id) }}" target="_blank" title="Print Bulletin" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-semibold hover:bg-slate-200 transition">
                                    PDF / Print
                                </a>
                                <a href="{{ route('worship.show', $rec->id) }}" title="View Sheet" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-[#DC2626] text-[10px] font-semibold hover:bg-slate-200 transition">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                No historical service sheets matched your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/60">
                {{ $records->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
