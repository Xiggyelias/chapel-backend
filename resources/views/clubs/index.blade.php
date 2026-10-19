@extends('layouts.app')

@section('title', 'Fellowship Registry & Charter Directory')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] mb-1">
                <span>Fellowships &amp; Charters</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Fellowship Registry & Charter Directory
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Official register of student fellowships, ministry organizations, and pending charter applications recognized under the Kwang-Lim Chapel.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('exports.clubs') }}" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs transition active:scale-95">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export CSV</span>
            </a>

            <a href="{{ route('clubs.register') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold transition shadow-xs active:scale-95">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Register Fellowship</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('clubs.index', ['status' => 'active']) }}" class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:border-[#DC2626]/40 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Fellowships</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-700 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 mt-2">{{ $activeCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Officially chartered fellowships</div>
        </a>

        <a href="{{ route('clubs.index', ['status' => 'pending']) }}" class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-amber-400/60 transition group {{ $pendingCount > 0 ? 'ring-2 ring-amber-400/40 bg-amber-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pending Charters</span>
                    @if($pendingCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">Needs Review</span>
                    @endif
                </div>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-700 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-cinzel text-slate-900 mt-2">{{ $pendingCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Awaiting Chaplaincy Office review & constitution verification</div>
        </a>

        <a href="{{ route('clubs.index', ['status' => 'archived']) }}" class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-slate-300 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Archived Records</span>
                <span class="p-2 rounded-xl bg-slate-100 text-slate-600 group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-cinzel text-slate-900 mt-2">{{ $archivedCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Permanent institutional records retained per policy</div>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center space-x-1.5 border-b border-slate-200 pb-2 overflow-x-auto text-xs font-semibold">
        <a href="{{ route('clubs.index', ['status' => 'all']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'all' ? 'bg-[#DC2626] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            All Fellowships ({{ $activeCount + $pendingCount + $archivedCount }})
        </a>
        <a href="{{ route('clubs.index', ['status' => 'active']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'active' ? 'bg-[#DC2626] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Active & Chartered ({{ $activeCount }})
        </a>
        <a href="{{ route('clubs.index', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'pending' ? 'bg-[#DC2626] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Pending Review ({{ $pendingCount }})
        </a>
        <a href="{{ route('clubs.index', ['status' => 'archived']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'archived' ? 'bg-[#DC2626] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Archived ({{ $archivedCount }})
        </a>
    </div>

    <!-- Fellowship Registry List -->
    <div class="space-y-4">
        @forelse($clubs as $club)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex items-start space-x-3.5">
                        <span class="w-4 h-4 rounded-full mt-1 shrink-0 ring-2 ring-white shadow-sm" style="background-color: {{ $club->color_hex ?? '#DC2626' }}"></span>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900 font-cinzel">{{ $club->name }}</h3>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">{{ $club->code }}</span>
                                
                                @if($club->status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        &bull; Chartered
                                    </span>
                                @elseif($club->status === 'pending_approval')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        &bull; Pending Charter
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-300">
                                        Archived
                                    </span>
                                @endif

                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider bg-rose-50 text-[#DC2626] border border-rose-100">
                                    {{ ucfirst($club->type) }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 mt-1 max-w-2xl line-clamp-2">
                                {{ $club->description }}
                            </p>

                            <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500 mt-2.5">
                                <span class="flex items-center space-x-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Schedule: <strong class="text-slate-700">{{ $club->meeting_schedule }}</strong></span>
                                </span>
                                <span class="flex items-center space-x-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <span>Venue: <strong class="text-slate-700">{{ $club->venue }}</strong></span>
                                </span>
                                <span class="flex items-center space-x-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                                    <span class="font-mono text-slate-700">{{ $club->official_email }}</span>
                                </span>
                                <span class="flex items-center space-x-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>President: <strong class="text-slate-800">{{ $club->current_president ?: $club->founding_president_name }}</strong></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Telemetry Stats -->
                    <div class="flex items-center space-x-3 shrink-0 self-start sm:self-auto bg-slate-50 px-3 py-2 rounded-xl border border-slate-200/60 text-xs">
                        <div class="text-center px-1">
                            <span class="block font-bold text-slate-800 font-cinzel">{{ $club->members_count ?? 0 }}</span>
                            <span class="text-[9px] uppercase font-bold text-slate-400">Members</span>
                        </div>
                        <div class="h-6 w-px bg-slate-200"></div>
                        <div class="text-center px-1">
                            <span class="block font-bold text-slate-800 font-cinzel">{{ $club->requests_count ?? 0 }}</span>
                            <span class="text-[9px] uppercase font-bold text-slate-400">Proposals</span>
                        </div>
                        <div class="h-6 w-px bg-slate-200"></div>
                        <div class="text-center px-1">
                            <span class="block font-bold text-slate-800 font-cinzel">{{ $club->documents_count ?? 0 }}</span>
                            <span class="text-[9px] uppercase font-bold text-slate-400">Vault Docs</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('vault.index', ['club_id' => $club->id]) }}" class="inline-flex items-center space-x-1 text-slate-600 hover:text-[#DC2626] font-semibold bg-slate-50 hover:bg-slate-100 px-2.5 py-1.5 rounded-lg border border-slate-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            <span>Club Vault</span>
                        </a>

                        <a href="{{ route('members.index', ['club_id' => $club->id]) }}" class="inline-flex items-center space-x-1 text-slate-600 hover:text-[#DC2626] font-semibold bg-slate-50 hover:bg-slate-100 px-2.5 py-1.5 rounded-lg border border-slate-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Members</span>
                        </a>

                        <a href="{{ route('requests.index', ['club' => $club->id]) }}" class="inline-flex items-center space-x-1 text-slate-600 hover:text-[#DC2626] font-semibold bg-slate-50 hover:bg-slate-100 px-2.5 py-1.5 rounded-lg border border-slate-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Requests</span>
                        </a>

                        @if($club->constitution_path)
                            <a href="{{ asset('storage/' . $club->constitution_path) }}" target="_blank" class="inline-flex items-center space-x-1 text-[#DC2626] hover:text-[#B91C1C] font-semibold bg-rose-50 hover:bg-rose-100 px-2.5 py-1.5 rounded-lg border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Constitution (PDF)</span>
                            </a>
                        @endif
                    </div>

                    <!-- Office Management Actions -->
                    @if(auth()->user()->isOffice())
                        <div class="flex items-center space-x-2">
                            @if($club->status === 'pending_approval')
                                <form method="POST" action="{{ route('clubs.approve', $club->id) }}" onsubmit="return confirm('Officially charter and activate {{ addslashes($club->name) }} under Chaplaincy oversight? Executive account will be provisioned.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition active:scale-95">
                                        <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>Approve &amp; Charter</span>
                                    </button>
                                </form>
                            @elseif($club->status === 'active')
                                <form method="POST" action="{{ route('clubs.archive', $club->id) }}" onsubmit="return confirm('Archive fellowship records for {{ addslashes($club->name) }}? Historical records will be retained in the Vault.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center space-x-1 text-slate-400 hover:text-rose-700 font-semibold px-2 py-1 rounded text-xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Archive</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-sm space-y-3">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h4 class="text-base font-bold font-cinzel text-slate-800">No Fellowships Found</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    No fellowship records match the selected filter criteria.
                </p>
                <a href="{{ route('clubs.register') }}" class="inline-flex items-center space-x-1 text-xs font-bold text-[#DC2626] hover:underline pt-2">
                    <span>Submit new charter application &rarr;</span>
                </a>
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $clubs->links() }}
    </div>

</div>
@endsection
