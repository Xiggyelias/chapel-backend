@extends('layouts.app')

@section('title', 'Worship Planning')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                <span>Kwang-Lim Chapel</span>
                <span>&bull;</span>
                <span class="text-slate-400 font-bold">Worship Liturgy</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Worship Planning & Liturgy Sheets
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Weekly orders of worship for Sunday (9:00 a.m.) and Wednesday (8:00 a.m.) services per Worship Service Policy
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('worship.archive') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold transition shadow-xs border border-slate-300 active:scale-95">
                <svg class="w-4 h-4 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Theme & Hymn Archive</span>
            </a>

            @if(auth()->user()->isOffice())
                <a href="{{ route('worship.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm self-start sm:self-auto active:scale-95">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>New Service Sheet</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Worship Service Policy Banner -->
    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-xs space-y-2 shadow-2xs">
        <div class="flex items-center justify-between">
            <span class="font-bold text-[#DC2626] uppercase tracking-wider text-[11px] flex items-center space-x-2">
                <svg class="w-4 h-4 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Worship Service Policy &amp; Order of Service</span>
            </span>
            <span class="text-[10px] bg-[#DC2626]/10 text-[#DC2626] px-2.5 py-0.5 rounded-full font-mono font-bold border border-[#DC2626]/20">Liturgical Cycle</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-1 text-[11px]">
            <div class="bg-white/90 p-3 rounded-2xl border border-amber-100/80 card-elevation">
                <span class="font-bold text-[#DC2626] block">T-minus 3 Days</span>
                <span class="text-slate-600">Preacher submits scripture, theme & hymns</span>
            </div>
            <div class="bg-white/90 p-3 rounded-2xl border border-amber-100/80 card-elevation">
                <span class="font-bold text-[#DC2626] block">T-minus 2 Days</span>
                <span class="text-slate-600">Media receives programme & slides</span>
            </div>
            <div class="bg-white/90 p-3 rounded-2xl border border-amber-100/80 card-elevation">
                <span class="font-bold text-[#DC2626] block">T-minus 2 Days</span>
                <span class="text-slate-600">Preparatory & coordination meeting</span>
            </div>
            <div class="bg-white/90 p-3 rounded-2xl border border-amber-100/80 card-elevation">
                <span class="font-bold text-[#DC2626] block">T-minus 1 Day</span>
                <span class="text-slate-600">Praise and Worship team rehearsal</span>
            </div>
        </div>
    </div>

    <!-- Service Sheets Listing -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-hidden divide-y divide-slate-100">
        @forelse($serviceSheets as $sheet)
            <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start space-x-4">
                    <!-- Date badge -->
                    <div class="w-16 h-16 rounded-2xl bg-[#DC2626] text-white text-center flex flex-col justify-center shrink-0 border border-white/10 shadow-sm">
                        <span class="text-[10px] uppercase font-bold text-rose-200">{{ $sheet->service_date->format('M') }}</span>
                        <span class="text-2xl font-black text-white leading-none font-cinzel">{{ $sheet->service_date->format('d') }}</span>
                        <span class="text-[9px] text-rose-200 font-medium">{{ $sheet->service_date->format('D') }}</span>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold text-[#DC2626] bg-[#DC2626]/10 px-2.5 py-0.5 rounded-full border border-[#DC2626]/20">
                                {{ $sheet->service_type }}
                            </span>
                            @if($sheet->status === 'ready')
                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full">Ready for Service</span>
                            @elseif($sheet->status === 'completed')
                                <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-full">Completed</span>
                            @else
                                <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full">Draft Preparation</span>
                            @endif
                        </div>

                        <h3 class="text-base font-bold text-slate-900 font-cinzel">
                            Theme: "{{ $sheet->theme ?: 'Worship Service' }}"
                        </h3>

                        <div class="text-xs text-slate-600 flex flex-wrap items-center gap-x-4 gap-y-1">
                            <span><strong>Preacher:</strong> {{ $sheet->preacher ?: 'University Chaplain' }}</span>
                            @if($sheet->scripture)
                                <span>&bull; <strong>Scripture:</strong> {{ $sheet->scripture }}</span>
                            @endif
                            @if($sheet->liturgist)
                                <span>&bull; <strong>Liturgist:</strong> {{ $sheet->liturgist }}</span>
                            @endif
                            @if($sheet->choir)
                                <span>&bull; <strong>Choir:</strong> {{ $sheet->choir }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 shrink-0 self-end md:self-center">
                    <a href="{{ route('worship.bulletin', $sheet->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold transition flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Bulletin PDF</span>
                    </a>
                    <a href="{{ route('worship.slides', $sheet->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold transition flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-3.5 h-3.5 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Slides</span>
                    </a>
                    <a href="{{ route('worship.show', $sheet->id) }}" class="px-3.5 py-1.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold transition active:scale-95">
                        View Sheet
                    </a>
                    @if(auth()->user()->isOffice())
                        <a href="{{ route('worship.edit', $sheet->id) }}" class="p-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-500 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-slate-500 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#DC2626] flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">No Worship Service Sheets Yet</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Prepare and post upcoming Sunday or Wednesday service orders, scripture readings, and hymns.</p>
                @if(auth()->user()->isOffice())
                    <div class="pt-1">
                        <a href="{{ route('worship.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Create Service Sheet</span>
                        </a>
                    </div>
                @endif
            </div>
        @endforelse
    </div>

    @if($serviceSheets->hasPages())
        <div class="p-4 bg-white rounded-3xl border border-slate-200">
            {{ $serviceSheets->links() }}
        </div>
    @endif

</div>
@endsection
