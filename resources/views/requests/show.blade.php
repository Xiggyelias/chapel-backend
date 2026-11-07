@extends('layouts.app')

@section('title', 'Request ' . $requestItem->reference_no)

@section('content')
@php
    // Presentation only — how the workflow reads on this page.
    $statusMeta = [
        'submitted'     => ['label' => 'Submitted',              'badge' => ''],
        'under_review'  => ['label' => 'Under Review',           'badge' => 'badge-sky'],
        'needs_changes' => ['label' => 'Needs Changes',          'badge' => 'badge-amber'],
        'approved'      => ['label' => 'Approved & On Calendar', 'badge' => 'badge-emerald'],
        'declined'      => ['label' => 'Declined',               'badge' => 'badge-rose'],
    ];
    $status = $statusMeta[$requestItem->status] ?? $statusMeta['submitted'];
    $isApproved = $requestItem->status === 'approved';
    $isDeclined = $requestItem->status === 'declined';
    $showVc = $requestItem->vc_go_ahead_needed || $requestItem->category === 'outing';
    $vcReceived = $requestItem->vc_go_ahead_status === 'received';

    // Workflow steps: done | current | warn | danger | upcoming | skipped
    $steps = [
        [
            'label' => 'Submitted',
            'meta'  => $requestItem->created_at ? $requestItem->created_at->format('M d, Y') : 'Received',
            'state' => 'done',
        ],
        [
            'label' => 'Chaplaincy review',
            'meta'  => match ($requestItem->status) {
                'submitted' => 'Awaiting review',
                'under_review' => 'In progress',
                'needs_changes' => 'Returned for changes',
                default => 'Complete',
            },
            'state' => match ($requestItem->status) {
                'submitted', 'under_review' => 'current',
                'needs_changes' => 'warn',
                default => 'done',
            },
        ],
    ];
    if ($showVc) {
        $steps[] = [
            'label' => 'VC go-ahead',
            'meta'  => $vcReceived ? 'Clearance recorded' : 'Pending clearance',
            'state' => $vcReceived ? 'done' : ($isDeclined ? 'skipped' : 'upcoming'),
        ];
    }
    $steps[] = [
        'label' => 'Decision',
        'meta'  => $isApproved ? 'Approved' . ($requestItem->decided_at ? ' · ' . $requestItem->decided_at->format('M d') : '')
                 : ($isDeclined ? 'Declined' . ($requestItem->decided_at ? ' · ' . $requestItem->decided_at->format('M d') : '') : 'Pending'),
        'state' => $isApproved ? 'done' : ($isDeclined ? 'danger' : 'upcoming'),
    ];
    $steps[] = [
        'label' => 'On Master Calendar',
        'meta'  => $isApproved ? $requestItem->event_date->format('M d, Y') : ($isDeclined ? 'Not scheduled' : 'After approval'),
        'state' => $isApproved ? 'done' : ($isDeclined ? 'skipped' : 'upcoming'),
    ];
    $nodeClass = [
        'done'     => 'bg-ink text-gold-light',
        'current'  => 'bg-white text-brand ring-2 ring-brand',
        'warn'     => 'bg-amber-50 text-amber-700 ring-2 ring-amber-500',
        'danger'   => 'bg-rose-50 text-rose-700 ring-2 ring-rose-500',
        'upcoming' => 'bg-white text-slate-300 ring-1 ring-slate-300',
        'skipped'  => 'bg-slate-50 text-slate-300 border border-dashed border-slate-300',
    ];
    $stateText = [
        'done' => 'Complete', 'current' => 'Current step', 'warn' => 'Needs attention',
        'danger' => 'Declined', 'upcoming' => 'Upcoming', 'skipped' => 'Skipped',
    ];
    $venueName = $requestItem->venue ? $requestItem->venue?->name : ($requestItem->venue_other ?: 'TBA');
@endphp

<div class="page-shell max-w-6xl space-y-6">

    {{-- ═══ Header ═══ --}}
    <header class="page-header">
        <div class="min-w-0">
            <nav class="eyebrow" aria-label="Breadcrumb">
                <a href="{{ route('requests.index') }}" class="hover:underline underline-offset-4">Requests</a>
                <span class="text-slate-300" aria-hidden="true">/</span>
                <span class="text-slate-500 tabular-nums tracking-[0.08em]">{{ $requestItem->reference_no }}</span>
            </nav>
            <h1 class="page-title">{{ $requestItem->title }}</h1>
            <p class="page-lede flex flex-wrap items-center gap-x-2 gap-y-1">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $requestItem->club?->color_hex ?? '#DC2626' }}" aria-hidden="true"></span>
                    {{ $requestItem->club?->name }}
                </span>
                <span class="text-slate-300" aria-hidden="true">&middot;</span>
                <span>{{ ucfirst($requestItem->category) }}</span>
                <span class="text-slate-300" aria-hidden="true">&middot;</span>
                <span>{{ $requestItem->event_date->format('l, M d, Y') }}</span>
            </p>
        </div>

        <!-- Status Badge & Action -->
        <div class="page-actions">
            @if($requestItem->isEditableByClub() && ($user->isOffice() || $user->club_id === $requestItem->club_id))
                <a href="{{ route('requests.edit', $requestItem->id) }}" class="btn {{ $requestItem->status === 'needs_changes' ? 'btn-primary' : 'btn-secondary' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                    {{ $requestItem->status === 'needs_changes' ? 'Revise & Resubmit' : 'Edit Proposal' }}
                </a>
            @endif
            @if($requestItem->status === 'approved')
                <button type="button" onclick="dispatchWhatsApp()" class="btn btn-secondary">
                    <svg class="text-emerald-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.86-1.537c1.7 1.011 3.684 1.595 5.803 1.595 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/></svg>
                    WhatsApp Pass
                </button>
                <a href="{{ route('requests.clearance_pass', $requestItem->id) }}" target="_blank" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Clearance Pass (QR)
                </a>
            @endif
        </div>
    </header>

    {{-- ═══ Approval workflow timeline ═══ --}}
    <section class="card" aria-labelledby="workflow-heading">
        <div class="card-header">
            <div class="min-w-0">
                <p class="card-kicker">Approval workflow</p>
                <h2 id="workflow-heading" class="card-title mt-0.5">Where this proposal stands</h2>
            </div>
            <span class="badge badge-dot {{ $status['badge'] }} !text-[12px] !px-2.5 !py-0.5">{{ $status['label'] }}</span>
        </div>
        <div class="card-body sm:px-7 sm:py-6">
            <ol class="grid gap-5 md:gap-0 md:grid-flow-col md:auto-cols-fr">
                @foreach($steps as $step)
                    @php $next = $steps[$loop->index + 1] ?? null; @endphp
                    <li class="relative flex items-start gap-3 md:flex-col md:items-center md:text-center md:gap-2.5" @if($step['state'] === 'current') aria-current="step" @endif>
                        @if($next)
                            <span aria-hidden="true"
                                  class="absolute left-4 top-9 -bottom-4 w-px md:top-4 md:bottom-auto md:left-[calc(50%+1.5rem)] md:right-[calc(-50%+1.5rem)] md:w-auto md:h-px {{ $step['state'] === 'done' && in_array($next['state'], ['done', 'current', 'warn', 'danger']) ? 'bg-gold' : 'bg-slate-200' }}"></span>
                        @endif
                        <span class="relative grid place-items-center w-8 h-8 shrink-0 rounded-full {{ $nodeClass[$step['state']] }}" aria-hidden="true">
                            @switch($step['state'])
                                @case('done')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @break
                                @case('current')
                                    <span class="w-2 h-2 rounded-full bg-brand breathe" style="--breathe: 220 38 38"></span>
                                    @break
                                @case('warn')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                    @break
                                @case('danger')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @break
                                @default
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            @endswitch
                        </span>
                        <div class="min-w-0 pt-1 md:pt-0">
                            <p class="text-[12.5px] font-semibold leading-snug {{ in_array($step['state'], ['upcoming', 'skipped']) ? 'text-slate-400' : 'text-slate-900' }}">
                                {{ $step['label'] }}<span class="sr-only"> — {{ $stateText[$step['state']] }}</span>
                            </p>
                            <p class="text-[12.5px] mt-0.5 {{ $step['state'] === 'warn' ? 'text-amber-700' : ($step['state'] === 'danger' ? 'text-rose-700' : 'text-slate-500') }}">{{ $step['meta'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <!-- 14-Day Rule Notice Alert -->
    @if($requestItem->is_late_submission)
        <div class="flash-error" role="note">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3.75m0 3.75h.008M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div class="text-[13px] leading-relaxed">
                <p class="font-semibold">Late submission notice <span class="font-normal opacity-75">(Chapel Manual §4.1 Flag)</span></p>
                <p class="mt-0.5 opacity-90">This request was submitted with less than 14 days advance notice prior to the activity date. The Chaplaincy Office reviews late submissions on exceptional grounds only.</p>
            </div>
        </div>
    @endif

    <!-- Venue Clash Alert (If Detected) -->
    @if($clashes->isNotEmpty())
        <div class="flash-warning" role="note">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5M12 13v2.5m0 2h.008"/></svg>
            <div class="text-[13px] leading-relaxed space-y-1.5">
                <p class="font-semibold">Potential venue schedule clash detected</p>
                <p class="opacity-90">The selected venue has overlapping activity bookings on the Master Calendar:</p>
                <ul class="space-y-0.5">
                    @foreach($clashes as $clash)
                        <li class="flex gap-2"><span aria-hidden="true" class="opacity-60">&mdash;</span><span><strong class="font-semibold">{{ $clash->title }}</strong> ({{ $clash->club?->name }}) &middot; <span class="tabular-nums">{{ substr($clash->start_time, 0, 5) }} – {{ substr($clash->end_time, 0, 5) }}</span></span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Request Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_21rem] gap-6 items-start">

        <!-- Main column: dossier, VC clearance, decision record -->
        <div class="space-y-6 min-w-0">

            <article class="card" aria-labelledby="dossier-heading">
                <div class="card-header">
                    <div class="min-w-0">
                        <p class="card-kicker">Activity Dossier</p>
                        <h2 id="dossier-heading" class="card-title mt-0.5">Purpose &amp; Objectives</h2>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1 text-[12.5px] font-semibold text-slate-700 ring-1 ring-black/5 min-w-0">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $requestItem->club?->color_hex ?? '#DC2626' }}" aria-hidden="true"></span>
                        <span class="truncate">{{ $requestItem->club?->name }} ({{ $requestItem->club?->code }})</span>
                    </span>
                </div>

                <div class="card-body sm:px-7 sm:py-7 space-y-7">
                    <p class="font-display text-[1.0625rem] sm:text-[1.125rem] leading-[1.7] text-slate-800 whitespace-pre-line" style="font-variation-settings: 'SOFT' 80, 'opsz' 24;">{{ $requestItem->purpose }}</p>

                    <div class="rule-ornament text-[8px]" aria-hidden="true">◆</div>

                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-6">
                        <div>
                            <dt class="text-[12.5px] font-semibold text-slate-500">Event Date</dt>
                            <dd class="mt-1 text-[14px] font-semibold text-slate-900">{{ $requestItem->event_date->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12.5px] font-semibold text-slate-500">Time Schedule</dt>
                            <dd class="mt-1 text-[14px] font-semibold text-slate-900 tabular-nums">{{ substr($requestItem->start_time, 0, 5) }} – {{ substr($requestItem->end_time, 0, 5) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12.5px] font-semibold text-slate-500">Venue</dt>
                            <dd class="mt-1 text-[14px] font-semibold text-slate-900">{{ $venueName }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12.5px] font-semibold text-slate-500">Expected Attendance</dt>
                            <dd class="mt-1 text-[14px] font-semibold text-slate-900"><span class="tabular-nums">{{ $requestItem->expected_attendance ?: 'N/A' }}</span> <span class="font-normal text-slate-500">attendees</span></dd>
                        </div>
                        <div>
                            <dt class="text-[12.5px] font-semibold text-slate-500">Estimated Budget</dt>
                            <dd class="mt-1 text-[14px] font-semibold text-slate-900 tabular-nums">${{ number_format($requestItem->budget, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12.5px] font-semibold text-slate-500">Submitted By</dt>
                            <dd class="mt-1 text-[14px] font-semibold text-slate-900">{{ $requestItem->requester?->name }}</dd>
                        </div>
                    </dl>

                    <ul class="divide-y divide-slate-100 border-y border-slate-100 text-[13px]">
                        <li class="flex items-start justify-between gap-4 py-3.5">
                            <div class="flex items-start gap-3 min-w-0">
                                @if($requestItem->is_late_submission)
                                    <svg class="w-4 h-4 mt-0.5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3.75m0 3.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div>
                                        <p class="font-semibold text-slate-900">14-Day Advance Notice</p>
                                        <p class="text-slate-500 text-[12px]">Late submission — flagged per Chapel Manual §4.1.</p>
                                    </div>
                                @else
                                    <svg class="w-4 h-4 mt-0.5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <p class="font-semibold text-slate-900">14-Day Advance Notice Rule Satisfied</p>
                                        <p class="text-slate-500 text-[12px]">Formally submitted in accordance with Chapel regulations.</p>
                                    </div>
                                @endif
                            </div>
                            <span class="badge shrink-0">§4.1</span>
                        </li>
                        <li class="flex items-start justify-between gap-4 py-3.5">
                            <div class="flex items-start gap-3 min-w-0">
                                <svg class="w-4 h-4 mt-0.5 shrink-0 {{ $requestItem->sign_interpreter_required ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.05 4.575a1.575 1.575 0 10-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 013.15 0v1.5m-3.15 0l.075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 013.15 0V15M6.9 7.575a1.575 1.575 0 10-3.15 0v8.175a6.75 6.75 0 006.75 6.75h2.018a5.25 5.25 0 003.712-1.538l1.732-1.732a5.25 5.25 0 001.538-3.712l.003-2.024a.668.668 0 01.198-.471 1.575 1.575 0 10-2.228-2.228 3.818 3.818 0 00-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0116.35 15m.002 0h-.002"/></svg>
                                <div>
                                    <p class="font-semibold text-slate-900">Accessibility / Sign Interpreter</p>
                                    @if($requestItem->sign_interpreter_required)
                                        <p class="text-emerald-800 text-[12px] font-medium">Requested: {{ $requestItem->sign_interpreter_name ?: 'Chaplaincy Sign Language Interpreter' }}</p>
                                    @else
                                        <p class="text-slate-500 text-[12px]">Standard Accessibility Arrangement</p>
                                    @endif
                                </div>
                            </div>
                            <span class="badge shrink-0">AU Inclusion §2.4</span>
                        </li>
                        @if($requestItem->attachment_path)
                            <li class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 py-3.5">
                                <div class="flex items-start gap-3">
                                    <svg class="w-4 h-4 mt-0.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                                    <p class="font-semibold text-slate-900">Supporting Attachment</p>
                                </div>
                                <a href="{{ route('requests.attachment', $requestItem->id) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm self-start sm:self-auto max-w-full">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="truncate">{{ $requestItem->attachment_name ?: 'Download Document' }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </article>

            <!-- Vice-Chancellor Go-Ahead Clearance Section -->
            @if($requestItem->vc_go_ahead_needed || $requestItem->category === 'outing')
                <section class="card" aria-labelledby="vc-heading">
                    <div class="card-header">
                        <div class="min-w-0">
                            <p class="card-kicker">Outing / Mission</p>
                            <h3 id="vc-heading" class="card-title mt-0.5">Vice-Chancellor Go-Ahead Clearance</h3>
                        </div>
                        @if($requestItem->vc_go_ahead_status === 'received')
                            <span class="badge badge-dot badge-emerald shrink-0">Clearance Recorded</span>
                        @else
                            <span class="badge badge-dot badge-amber shrink-0">Pending Clearance</span>
                        @endif
                    </div>

                    <div class="card-body sm:px-7">
                        @if($requestItem->vc_go_ahead_status === 'received')
                            <figure class="border-l-2 border-gold pl-4">
                                <figcaption class="text-[12.5px] font-semibold text-slate-500">Recorded Go-Ahead Note</figcaption>
                                <blockquote class="mt-1.5 font-display text-[15px] leading-relaxed text-slate-800">{{ $requestItem->vc_go_ahead_note }}</blockquote>
                            </figure>
                            @if($requestItem->vc_go_ahead_attachment)
                                <div class="mt-4">
                                    <a href="{{ route('requests.vc_attachment', $requestItem->id) }}" target="_blank" rel="noopener" class="card-link">
                                        View Attached VC Clearance Memo
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-[13px] text-slate-600 leading-relaxed">
                                Per Chapel Manual §4.1, off-campus outings, mission trips, and major gatherings require the Vice-Chancellor's official clearance memo before final approval by the Chaplain.
                            </p>
                            @if($user->canApproveRequests())
                                <form method="POST" action="{{ route('requests.vc_go_ahead', $requestItem->id) }}" enctype="multipart/form-data" class="mt-5 space-y-4 rounded-xl bg-slate-50 p-4 ring-1 ring-black/5">
                                    @csrf
                                    <div>
                                        <label for="vc_go_ahead_note" class="field-label">Clearance Memo Reference / Note</label>
                                        <textarea id="vc_go_ahead_note" name="vc_go_ahead_note" rows="2" required placeholder="e.g. VC Memo VC/CH/2026/088 approved by Prof. Vice-Chancellor on 14 Oct 2026." class="field"></textarea>
                                    </div>
                                    <div>
                                        <label for="vc_attachment" class="field-label">Upload Clearance Memo / Email PDF</label>
                                        <input type="file" id="vc_attachment" name="vc_attachment" class="block w-full text-[12px] text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-white file:px-3 file:py-1.5 file:text-[12px] file:font-semibold file:text-slate-800 file:shadow-xs file:ring-1 file:ring-black/10 hover:file:bg-slate-50">
                                    </div>
                                    <button type="submit" class="btn btn-ink">
                                        Record VC Clearance
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </section>
            @endif

            <!-- Decision Reason (If Declined or Needs Changes) -->
            @if($requestItem->decision_reason)
                <section class="card overflow-hidden" aria-labelledby="decision-reason-heading">
                    <span class="absolute inset-y-0 left-0 w-[3px] {{ $requestItem->status === 'declined' ? 'bg-rose-500' : ($requestItem->status === 'approved' ? 'bg-emerald-500' : 'bg-amber-500') }}" aria-hidden="true"></span>
                    <div class="card-body sm:px-7 sm:py-6">
                        <p class="card-kicker">Decision record</p>
                        <h3 id="decision-reason-heading" class="card-title mt-0.5">
                            {{ $requestItem->status === 'declined' ? 'Official Decline Justification' : 'Chaplaincy Feedback / Required Changes' }}
                        </h3>
                        <p class="mt-3 font-display text-[15px] leading-relaxed text-slate-800 whitespace-pre-line">{{ $requestItem->decision_reason }}</p>
                        @if($requestItem->decidedBy)
                            <p class="mt-4 text-[12.5px] text-slate-500">
                                Recorded by <span class="font-semibold text-slate-700">{{ $requestItem->decidedBy?->name }}</span> &middot; <span class="tabular-nums">{{ $requestItem->decided_at?->format('M d, Y H:i') }}</span>
                            </p>
                        @endif
                    </div>
                </section>
            @endif

        </div>

        <!-- Side column: Approver Action Panel & Audit Trail -->
        <aside class="space-y-6 lg:sticky lg:top-24">

            <!-- Approver Action Box (Chaplain / Assistant) -->
            @if($user->canApproveRequests() && in_array($requestItem->status, ['submitted', 'under_review', 'needs_changes']))
                <section class="card overflow-hidden" aria-labelledby="decision-heading">
                    <span class="absolute inset-x-0 top-0 h-[3px] bg-brand" aria-hidden="true"></span>
                    <div class="card-body space-y-4">
                        <div>
                            <p class="card-kicker">For the Chaplaincy</p>
                            <h3 id="decision-heading" class="card-title mt-0.5">Chaplaincy Decision Panel</h3>
                            <p class="mt-2 text-[12.5px] text-slate-500 leading-relaxed">
                                Review against Master Calendar, 14-day rule, and university policy. Approving immediately schedules the activity on the Master Calendar.
                            </p>
                        </div>

                        <div class="space-y-2.5">
                            <!-- Approve Form -->
                            @if($requestItem->status === 'needs_changes')
                            <p class="rounded-xl bg-amber-50 p-3 text-[12px] leading-relaxed text-amber-900 ring-1 ring-amber-200">Returned to the club for changes. It can be approved once the club resubmits.</p>
                            @else
                            <form method="POST" action="{{ route('requests.approve', $requestItem->id) }}" class="space-y-2.5">
                                @csrf
                                @if($clashes->isNotEmpty())
                                    <label class="flex items-start gap-2.5 rounded-xl bg-rose-50 p-3 text-[12px] leading-relaxed text-rose-900 ring-1 ring-rose-200 cursor-pointer">
                                        <input type="checkbox" name="override_clash" value="1" class="mt-0.5 rounded">
                                        <span>This venue is already booked at this time. Tick to double-book deliberately.</span>
                                    </label>
                                @endif
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Approve &amp; Schedule Event
                                </button>
                            </form>
                            @endif

                            <div class="grid grid-cols-2 gap-2">
                                <!-- Needs Changes Toggle -->
                                <button type="button" onclick="document.getElementById('needsChangesModal').classList.toggle('hidden')" class="btn btn-secondary">
                                    Return for Changes
                                </button>

                                <!-- Decline Toggle -->
                                <button type="button" onclick="document.getElementById('declineModal').classList.toggle('hidden')" class="btn btn-danger">
                                    Decline Request
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Audit Trail & Change History -->
            <section class="card" aria-labelledby="audit-heading">
                <div class="card-header">
                    <div class="min-w-0">
                        <h3 id="audit-heading" class="card-title">Audit Log &amp; History</h3>
                        <p class="text-[12.5px] text-slate-500 mt-0.5">Tamper-evident compliance trail</p>
                    </div>
                    <a href="{{ route('audit') }}" class="card-link">Full Ledger</a>
                </div>
                <div class="card-body">
                    @forelse($requestItem->auditLogs as $log)
                        <div class="relative pl-6 {{ $loop->last ? '' : 'pb-5' }}">
                            @unless($loop->last)
                                <span class="absolute left-[5px] top-3 bottom-0 w-px bg-slate-200" aria-hidden="true"></span>
                            @endunless
                            <span class="absolute left-0 top-1 w-[11px] h-[11px] rounded-full bg-white ring-2 {{ $loop->first ? 'ring-brand' : 'ring-slate-300' }}" aria-hidden="true"></span>
                            <div class="flex items-baseline justify-between gap-3">
                                <span class="text-[12px] font-semibold text-slate-900 capitalize">{{ str_replace('_', ' ', $log->action) }}</span>
                                <span class="text-[12px] text-slate-400 tabular-nums whitespace-nowrap">{{ $log->created_at->format('M d, H:i') }}</span>
                            </div>
                            <div class="mt-0.5 text-[12.5px] text-slate-600 leading-relaxed">{{ $log->notes }}</div>
                            <div class="mt-1 text-[12px] text-slate-400">By {{ $log->user_name }}</div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-[12.5px] text-slate-400">No previous audit logs.</p>
                    @endforelse
                </div>
            </section>

        </aside>

    </div>

</div>

<!-- Needs Changes Modal -->
<div id="needsChangesModal" role="dialog" aria-modal="true" aria-labelledby="needsChangesTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-lg ring-1 ring-black/5 overflow-hidden modal-panel">
        <div class="h-[3px] bg-amber-500" aria-hidden="true"></div>
        <div class="p-6">
            <p class="card-kicker">Return to the club</p>
            <h3 id="needsChangesTitle" class="font-display text-[1.25rem] text-slate-900 mt-0.5">Request Modifications from Club</h3>
            <p class="text-[12.5px] text-slate-500 mt-1.5 mb-4 leading-relaxed">Specify the adjustments required (e.g. venue change, budget revision, or program clarification):</p>
            <form method="POST" action="{{ route('requests.needs_changes', $requestItem->id) }}">
                @csrf
                <label for="needs_changes_notes" class="sr-only">Required revisions</label>
                <textarea id="needs_changes_notes" name="notes" rows="4" required placeholder="Describe required revisions..." class="field mb-4"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('needsChangesModal').classList.add('hidden')" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-ink">Submit Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Decline Modal (Mandatory Reason) -->
<div id="declineModal" role="dialog" aria-modal="true" aria-labelledby="declineTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-lg ring-1 ring-black/5 overflow-hidden modal-panel">
        <div class="h-[3px] bg-rose-500" aria-hidden="true"></div>
        <div class="p-6">
            <p class="card-kicker">Formal decision</p>
            <h3 id="declineTitle" class="font-display text-[1.25rem] text-slate-900 mt-0.5">Decline Activity Proposal</h3>
            <p class="text-[12.5px] text-slate-500 mt-1.5 mb-4 leading-relaxed">Per Chapel Manual policy, a formal reason is mandatory when declining a request:</p>
            <form method="POST" action="{{ route('requests.decline', $requestItem->id) }}">
                @csrf
                <label for="decline_reason" class="sr-only">Reason for declining</label>
                <textarea id="decline_reason" name="reason" rows="4" required placeholder="Mandatory official justification for decline..." class="field mb-4"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('declineModal').classList.add('hidden')" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function dispatchWhatsApp() {
        const pass = {{ Js::from([
            'title' => $requestItem->title,
            'club' => $requestItem->club?->name,
            'venue' => $requestItem->venue ? $requestItem->venue?->name : $requestItem->venue_other,
            'date' => $requestItem->event_date->format('l, M d, Y'),
            'hours' => substr($requestItem->start_time, 0, 5) . ' - ' . substr($requestItem->end_time, 0, 5),
            'reference' => $requestItem->reference_no,
            'verifyUrl' => $requestItem->status === 'approved' ? $requestItem->clearanceVerificationUrl() : null,
        ]) }};
        const text = `🏛️ *KWANG-LIM CHAPEL CLEARANCE PASS*\n\n📋 *Activity:* ${pass.title}\n👥 *Sponsoring Club:* ${pass.club}\n📍 *Venue:* ${pass.venue}\n📅 *Date:* ${pass.date}\n⏰ *Hours:* ${pass.hours} CAT\n🛡️ *Reference:* ${pass.reference}\n\n✅ *Gatekeeper Verification Link:*\n${pass.verifyUrl}\n\n⚠️ *Directives:* Sound curfew 22:00 CAT strict. Facility access authorized by Chaplaincy Office.`;
        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank', 'noopener');
    }
</script>
@endsection
