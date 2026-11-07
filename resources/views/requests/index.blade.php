@extends('layouts.app')

@section('title', 'Requests & Approvals')

@section('content')
@php
    // Presentation only: how each status reads and which status filters are offered.
    $statusMeta = [
        'submitted'     => ['label' => 'Submitted',     'badge' => ''],
        'under_review'  => ['label' => 'Under Review',  'badge' => 'badge-sky'],
        'needs_changes' => ['label' => 'Needs Changes', 'badge' => 'badge-amber'],
        'approved'      => ['label' => 'Approved',      'badge' => 'badge-emerald'],
        'declined'      => ['label' => 'Declined',      'badge' => 'badge-rose'],
    ];
    $statusTabs = ['' => 'All'] + array_map(fn ($m) => $m['label'], $statusMeta);
    $activeStatus = is_string(request('status')) ? request('status') : '';
@endphp

<div class="page-shell space-y-6">

    <x-page-header eyebrow="Clearance & Approvals"
                   title="Event Proposals & Requests"
                   description="Proposals for campus fellowship gatherings, room bookings, equipment, and outings.">
        <a href="{{ route('requests.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Submit Proposal
        </a>
    </x-page-header>

    {{-- ═══ Filter bar ═══ --}}
    <section class="card" aria-label="Filter requests">
        <div class="flex flex-col gap-3 px-4 pt-4 sm:px-5 sm:pt-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="-mx-1 overflow-x-auto px-1 pb-1">
                <nav class="segmented" aria-label="Filter by status">
                    @foreach($statusTabs as $value => $label)
                        <a href="{{ request()->fullUrlWithQuery(['status' => $value !== '' ? $value : null, 'page' => null]) }}"
                           class="whitespace-nowrap {{ $activeStatus === (string) $value ? 'is-active' : '' }}"
                           @if($activeStatus === (string) $value) aria-current="page" @endif>
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>
            <p class="text-[12px] text-slate-500 shrink-0">
                <span class="font-semibold text-slate-800 tabular-nums">{{ $requests->total() }}</span>
                {{ \Illuminate\Support\Str::plural('proposal', $requests->total()) }}
                @if(!auth()->user()->isOffice())
                    <span class="text-slate-300 mx-1.5">/</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                        Filtered: <strong class="font-semibold text-slate-700">{{ auth()->user()->club?->name ?? 'My Club' }}</strong>
                    </span>
                @endif
            </p>
        </div>

        <form method="GET" action="{{ route('requests.index') }}"
              class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 sm:p-5 {{ auth()->user()->isOffice() ? 'lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)_minmax(0,1fr)_auto]' : 'lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)_auto]' }} lg:items-end">
            <input type="hidden" name="status" value="{{ request('status') }}">

            <!-- Search -->
            <div>
                <label for="filter_search" class="field-label">Search</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                    <input type="text" id="filter_search" name="search" value="{{ request('search') }}" placeholder="Title or Ref No..." class="field pl-9">
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="filter_category" class="field-label">Category</label>
                <select id="filter_category" name="category" class="field">
                    <option value="">All Categories</option>
                    <option value="event" {{ request('category') === 'event' ? 'selected' : '' }}>Event</option>
                    <option value="venue" {{ request('category') === 'venue' ? 'selected' : '' }}>Venue</option>
                    <option value="poster" {{ request('category') === 'poster' ? 'selected' : '' }}>Poster Approval</option>
                    <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="outing" {{ request('category') === 'outing' ? 'selected' : '' }}>Outing / Mission</option>
                </select>
            </div>

            <!-- Club Filter (Office Only) -->
            @if(auth()->user()->isOffice())
                <div>
                    <label for="filter_club" class="field-label">Club / Fellowship</label>
                    <select id="filter_club" name="club_id" class="field">
                        <option value="">All Clubs</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>{{ $club->name }} ({{ $club->code }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Buttons -->
            <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-1">
                <button type="submit" class="btn btn-ink flex-1 lg:flex-none">Filter</button>
                <a href="{{ route('requests.index') }}" class="btn btn-ghost flex-1 lg:flex-none">Reset</a>
            </div>
        </form>
    </section>

    {{-- ═══ Requests list ═══ --}}
    <section class="card overflow-hidden" aria-label="Requests">
        @if($requests->isEmpty())
            <div class="empty-state">
                <div class="empty-state-mark" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="empty-state-title">No requests found</p>
                <p class="empty-state-text">Click "Submit Proposal" to initiate a new activity proposal.</p>
            </div>
        @else
            {{-- Desktop: table --}}
            <div class="table-wrap hidden md:block">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" class="!pl-5">Ref No &amp; Club</th>
                            <th scope="col">Title &amp; Category</th>
                            <th scope="col">Activity Schedule</th>
                            <th scope="col">Venue &amp; Cap</th>
                            <th scope="col">14-Day Notice</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="!pr-5 text-right"><span class="sr-only">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                            @php $s = $statusMeta[$req->status] ?? $statusMeta['submitted']; @endphp
                            <tr>
                                <td class="!pl-5 whitespace-nowrap">
                                    <div class="font-semibold text-slate-900 tabular-nums tracking-tight">{{ $req->reference_no }}</div>
                                    <div class="mt-1 inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                                        <span class="w-2 h-2 rounded-full ring-2 ring-white shadow-xs" style="background-color: {{ $req->club?->color_hex ?? '#A51C30' }}" aria-hidden="true"></span>
                                        {{ $req->club?->code }}
                                    </div>
                                </td>

                                <td class="min-w-[14rem]">
                                    <a href="{{ route('requests.show', $req->id) }}" class="font-semibold text-slate-900 hover:text-brand transition-colors leading-snug">
                                        {{ $req->title }}
                                    </a>
                                    <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11.5px] text-slate-500">
                                        <span>{{ ucfirst($req->category) }}</span>
                                        @if($req->vc_go_ahead_needed)
                                            <span class="text-slate-300" aria-hidden="true">&middot;</span>
                                            <span class="badge badge-purple">VC Clearance</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="whitespace-nowrap">
                                    <div class="font-medium text-slate-900">{{ $req->event_date->format('M d, Y') }}</div>
                                    <div class="text-[11.5px] text-slate-500 tabular-nums">{{ substr($req->start_time, 0, 5) }} – {{ substr($req->end_time, 0, 5) }}</div>
                                </td>

                                <td>
                                    <div class="text-slate-800">
                                        {{ $req->venue ? $req->venue?->name : ($req->venue_other ?: 'To be announced') }}
                                    </div>
                                    @if($req->expected_attendance)
                                        <div class="text-[11.5px] text-slate-500">Exp. Attendance: <span class="tabular-nums">{{ $req->expected_attendance }}</span></div>
                                    @endif
                                </td>

                                <!-- 14-Day Rule Compliance -->
                                <td class="whitespace-nowrap">
                                    @if($req->is_late_submission)
                                        <span class="badge badge-rose">Late (&lt;14d)</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[11.5px] font-medium text-slate-500">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            On Time (&ge;14d)
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="whitespace-nowrap">
                                    <span class="badge badge-dot {{ $s['badge'] }}">{{ $s['label'] }}</span>
                                </td>

                                <td class="!pr-5 text-right whitespace-nowrap">
                                    <a href="{{ route('requests.show', $req->id) }}" class="btn btn-secondary btn-sm">
                                        Review
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile: stacked list --}}
            <ul class="md:hidden divide-y divide-slate-100">
                @foreach($requests as $req)
                    @php $s = $statusMeta[$req->status] ?? $statusMeta['submitted']; @endphp
                    <li>
                        <a href="{{ route('requests.show', $req->id) }}" class="block px-4 py-4 transition-colors hover:bg-slate-50/70">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 tabular-nums">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $req->club?->color_hex ?? '#A51C30' }}" aria-hidden="true"></span>
                                    {{ $req->club?->code }} &middot; {{ $req->reference_no }}
                                </span>
                                <span class="badge badge-dot {{ $s['badge'] }}">{{ $s['label'] }}</span>
                            </div>
                            <p class="mt-1.5 text-[14px] font-semibold text-slate-900 leading-snug">{{ $req->title }}</p>
                            <p class="mt-1 text-[12px] text-slate-500">
                                {{ $req->event_date->format('M d, Y') }} &middot; <span class="tabular-nums">{{ substr($req->start_time, 0, 5) }} – {{ substr($req->end_time, 0, 5) }}</span>
                                &middot; {{ $req->venue ? $req->venue?->name : ($req->venue_other ?: 'To be announced') }}
                            </p>
                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                <span class="badge">{{ ucfirst($req->category) }}</span>
                                @if($req->vc_go_ahead_needed)
                                    <span class="badge badge-purple">VC Clearance</span>
                                @endif
                                @if($req->is_late_submission)
                                    <span class="badge badge-rose">Late (&lt;14d)</span>
                                @else
                                    <span class="badge badge-emerald">On Time (&ge;14d)</span>
                                @endif
                                @if($req->expected_attendance)
                                    <span class="text-[11.5px] text-slate-500">Exp. Attendance: {{ $req->expected_attendance }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        @if($requests->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $requests->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
