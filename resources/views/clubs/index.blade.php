@extends('layouts.app')

@section('title', 'Fellowship Registry & Charter Directory')

@section('content')
@php
    $filters = [
        'all'      => ['All fellowships', $activeCount + $pendingCount + $archivedCount],
        'active'   => ['Active & chartered', $activeCount],
        'pending'  => ['Pending review', $pendingCount],
        'archived' => ['Archived', $archivedCount],
    ];
@endphp
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Fellowships & Charters" title="Fellowship Registry & Charter Directory"
                   description="Official register of student fellowships, ministry organizations, and pending charter applications recognized under the Kwang-Lim Chapel.">
        <a href="{{ route('exports.clubs') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
        <a href="{{ route('clubs.register') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/></svg>
            Register Fellowship
        </a>
    </x-page-header>

    {{-- Registry at a glance --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <a href="{{ route('clubs.index', ['status' => 'active']) }}" class="group dash-stat-card focus:outline-none focus-visible:ring-2 focus-visible:ring-brand" style="--tone: #059669">
            <div class="flex items-center justify-between gap-2">
                <span class="flex items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    Active Fellowships
                </span>
                <svg class="w-4 h-4 text-slate-300 shrink-0 -translate-x-1 opacity-0 transition duration-300 group-hover:translate-x-0 group-hover:opacity-100 group-hover:text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 17L17 7M17 7H8M17 7v9"/></svg>
            </div>
            <div class="mt-4 stat-value text-[2.6rem] text-slate-900">{{ $activeCount }}</div>
            <div class="mt-2 text-[11.5px] text-slate-500">Officially chartered fellowships</div>
        </a>

        <a href="{{ route('clubs.index', ['status' => 'pending']) }}" class="group dash-stat-card focus:outline-none focus-visible:ring-2 focus-visible:ring-brand {{ $pendingCount > 0 ? 'ring-1 ring-amber-500/25' : '' }}" style="--tone: {{ $pendingCount > 0 ? '#D97706' : 'var(--color-slate-300)' }}">
            <div class="flex items-center justify-between gap-2">
                <span class="flex items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span class="w-1.5 h-1.5 rounded-full {{ $pendingCount > 0 ? 'bg-amber-500' : 'bg-slate-300' }}" aria-hidden="true"></span>
                    Pending Charters
                </span>
                @if($pendingCount > 0)
                    <span class="badge badge-amber">Needs review</span>
                @endif
            </div>
            <div class="mt-4 stat-value text-[2.6rem] {{ $pendingCount > 0 ? 'text-amber-800' : 'text-slate-900' }}">{{ $pendingCount }}</div>
            <div class="mt-2 text-[11.5px] text-slate-500">Awaiting Chaplaincy Office review &amp; constitution verification</div>
        </a>

        <a href="{{ route('clubs.index', ['status' => 'archived']) }}" class="group dash-stat-card focus:outline-none focus-visible:ring-2 focus-visible:ring-brand" style="--tone: var(--color-gold)">
            <div class="flex items-center justify-between gap-2">
                <span class="flex items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold" aria-hidden="true"></span>
                    Archived Records
                </span>
                <svg class="w-4 h-4 text-slate-300 shrink-0 -translate-x-1 opacity-0 transition duration-300 group-hover:translate-x-0 group-hover:opacity-100 group-hover:text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 17L17 7M17 7H8M17 7v9"/></svg>
            </div>
            <div class="mt-4 stat-value text-[2.6rem] text-slate-900">{{ $archivedCount }}</div>
            <div class="mt-2 text-[11.5px] text-slate-500">Permanent institutional records retained per policy</div>
        </a>
    </div>

    {{-- Registry --}}
    <section class="card overflow-hidden" aria-labelledby="registry-heading">
        <div class="card-header flex-col items-stretch sm:flex-row sm:items-center">
            <div class="min-w-0">
                <p class="card-kicker">The register</p>
                <h2 id="registry-heading" class="card-title mt-0.5">Chartered &amp; pending fellowships</h2>
            </div>
            <div class="overflow-x-auto -mx-1 px-1">
                <nav class="segmented" aria-label="Filter by charter status">
                    @foreach($filters as $key => [$label, $count])
                        <a href="{{ route('clubs.index', ['status' => $key]) }}"
                           class="whitespace-nowrap {{ $statusFilter === $key ? 'is-active' : '' }}"
                           @if($statusFilter === $key) aria-current="page" @endif>
                            {{ $label }} <span class="tabular-nums text-slate-400 font-medium ml-0.5">{{ $count }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="table-wrap">
            <table class="table min-w-[66rem]">
                <thead>
                    <tr>
                        <th scope="col">Fellowship</th>
                        <th scope="col" class="w-[12.5rem]">Leadership &amp; contact</th>
                        <th scope="col" class="w-[11.5rem]">Gathering</th>
                        <th scope="col" class="w-[8.5rem]">Records</th>
                        <th scope="col" class="w-[8.5rem]">Status</th>
                        <th scope="col" class="w-[10.5rem] text-right"><span class="sr-only">Office actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clubs as $club)
                        <tr class="align-top">
                            <td class="!align-top !py-4">
                                <div class="flex items-start gap-3">
                                    <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0 ring-[3px] ring-white shadow-[0_0_0_1px_rgb(28_24_21/0.12)]" style="background-color: {{ $club->color_hex ?? '#A51C30' }}" aria-hidden="true"></span>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                            <h3 class="font-display text-[15px] leading-snug text-slate-900">{{ $club->name }}</h3>
                                            <span class="font-mono text-[10.5px] font-semibold text-slate-500 tracking-wide">{{ $club->code }}</span>
                                        </div>
                                        <p class="mt-0.5 text-[11px] font-semibold uppercase tracking-[0.1em] text-gold">{{ ucfirst($club->type) }}</p>
                                        <p class="mt-1.5 text-[12.5px] text-slate-600 leading-relaxed max-w-md line-clamp-2">{{ $club->description }}</p>
                                        <div class="mt-2.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11.5px] font-semibold">
                                            <a href="{{ route('vault.index', ['club_id' => $club->id]) }}" class="inline-flex items-center gap-1 text-slate-500 hover:text-brand transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                Club Vault
                                            </a>
                                            <a href="{{ route('members.index', ['club_id' => $club->id]) }}" class="inline-flex items-center gap-1 text-slate-500 hover:text-brand transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Members
                                            </a>
                                            <a href="{{ route('requests.index', ['club_id' => $club->id]) }}" class="inline-flex items-center gap-1 text-slate-500 hover:text-brand transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                Requests
                                            </a>
                                            @if($club->constitution_path)
                                                <a href="{{ route('clubs.constitution', $club->id) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-brand hover:text-brand-dark transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    Constitution (PDF)
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="!align-top !py-4">
                                <p class="text-[10.5px] font-semibold uppercase tracking-[0.1em] text-slate-400">President</p>
                                <p class="mt-0.5 font-semibold text-slate-800">{{ $club->current_president ?: ($club->founding_president_name ?: 'Vacant') }}</p>
                                <p class="mt-1.5 font-mono text-[11px] text-slate-500 break-words [overflow-wrap:anywhere]">{{ $club->official_email }}</p>
                            </td>

                            <td class="!align-top !py-4">
                                <p class="text-[10.5px] font-semibold uppercase tracking-[0.1em] text-slate-400">Schedule</p>
                                <p class="mt-0.5 text-slate-700">{{ $club->meeting_schedule ?: 'Not set' }}</p>
                                <p class="mt-1.5 text-[10.5px] font-semibold uppercase tracking-[0.1em] text-slate-400">Venue</p>
                                <p class="mt-0.5 text-slate-700">{{ $club->venue ?: 'Not set' }}</p>
                            </td>

                            <td class="!align-top !py-4">
                                <dl class="space-y-1 text-[12px]">
                                    @foreach([['Members', $club->members_count ?? 0], ['Proposals', $club->requests_count ?? 0], ['Vault Docs', $club->documents_count ?? 0]] as [$recLabel, $recValue])
                                        <div class="flex items-baseline justify-between gap-3">
                                            <dt class="text-slate-500">{{ $recLabel }}</dt>
                                            <dd class="font-display text-[14px] text-slate-900 tabular-nums">{{ $recValue }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </td>

                            <td class="!align-top !py-4">
                                @if($club->status === 'active')
                                    <span class="badge badge-emerald badge-dot">Chartered</span>
                                @elseif($club->status === 'pending_approval')
                                    <span class="badge badge-amber badge-dot">Pending charter</span>
                                @else
                                    <span class="badge">Archived</span>
                                @endif
                            </td>

                            <td class="!align-top !py-4 text-right">
                                @if(auth()->user()->isOffice())
                                    @if($club->status === 'pending_approval')
                                        <form method="POST" action="{{ route('clubs.approve', $club->id) }}" data-confirm="Officially charter and activate {{ $club->name }} under Chaplaincy oversight? Executive account will be provisioned.">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Approve &amp; Charter
                                            </button>
                                        </form>
                                    @elseif($club->status === 'active')
                                        <form method="POST" action="{{ route('clubs.archive', $club->id) }}" data-confirm="Archive fellowship records for {{ $club->name }}? Historical records will be retained in the Vault.">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-ghost hover:!text-rose-700">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                Archive
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!p-0">
                                <div class="empty-state">
                                    <div class="empty-state-mark" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <p class="empty-state-title">No fellowships found</p>
                                    <p class="empty-state-text">No fellowship records match the selected filter criteria.</p>
                                    <a href="{{ route('clubs.register') }}" class="card-link mt-4">Submit new charter application</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clubs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $clubs->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
