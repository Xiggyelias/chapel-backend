@extends('layouts.app')

@section('title', 'Handover: ' . $handover->club?->name)

@section('content')
@php
    // Presentation only: map the handover status onto the five protocol steps
    $s = $handover->status;
    $steps = [
        ['label' => 'Initiated',   'state' => 'done'],
        ['label' => 'Checklist',   'state' => in_array($s, ['checklist_completed', 'folders_verified', 'meeting_scheduled', 'completed']) ? 'done' : 'current'],
        ['label' => 'Vault Check', 'state' => in_array($s, ['folders_verified', 'meeting_scheduled', 'completed']) ? 'done' : ($s === 'checklist_completed' ? 'current' : 'upcoming')],
        ['label' => 'Meeting',     'state' => in_array($s, ['meeting_scheduled', 'completed']) ? 'done' : ($s === 'folders_verified' ? 'current' : 'upcoming')],
        ['label' => 'Transfer',    'state' => $s === 'completed' ? 'done' : ($s === 'meeting_scheduled' ? 'current' : 'upcoming')],
    ];
    $doneCount = collect($steps)->where('state', 'done')->count();
    $stateLabel = ['done' => 'complete', 'current' => 'in progress', 'upcoming' => 'not started'];
@endphp
<div class="page-shell max-w-5xl space-y-6">

    <x-page-header eyebrow="Leadership Handover / {{ $handover->club?->code }}" title="{{ $handover->club?->name }} Transition"
                   description="Academic Year {{ $handover->academic_year }} • {{ $handover->outgoing_president_name }} to {{ $handover->incoming_president_name }}">
        <a href="{{ route('handover.index') }}" class="btn btn-ghost">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.5 19.5L3 12l7.5-7.5M3 12h18"/></svg>
            Leadership Handover
        </a>
        <a href="{{ route('handover.certificate', $handover->id) }}" target="_blank" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Handover Certificate
        </a>
    </x-page-header>

    {{-- ═══ The stepper ═══ --}}
    <section class="card overflow-hidden" aria-labelledby="stepperTitle">
        <div class="card-header">
            <div>
                <p class="card-kicker">Transition progress</p>
                <h2 id="stepperTitle" class="card-title mt-0.5">{{ $doneCount }} of 5 steps complete</h2>
            </div>
            <span class="badge badge-dot capitalize {{ $handover->status === 'completed' ? 'badge-emerald' : 'badge-amber' }}">{{ str_replace('_', ' ', $handover->status) }}</span>
        </div>
        <div class="px-3 sm:px-6 pt-7 pb-6">
            <ol class="grid grid-cols-5">
                @foreach($steps as $i => $step)
                    <li class="relative flex flex-col items-center text-center" @if($step['state'] === 'current') aria-current="step" @endif>
                        {{-- connector to the next node --}}
                        @if(!$loop->last)
                            <span class="absolute top-[1.125rem] left-1/2 w-full h-px {{ $steps[$i + 1]['state'] === 'upcoming' ? 'bg-slate-200' : 'bg-gold-light' }}" aria-hidden="true"></span>
                        @endif

                        @if($step['state'] === 'done')
                            <span class="relative z-10 grid place-items-center w-9 h-9 rounded-full text-[#2A1E05]"
                                  style="background: linear-gradient(180deg, var(--color-gold-bright), var(--color-gold-light)); box-shadow: inset 0 0 0 1px rgb(184 137 43 / .6), 0 6px 14px -8px rgb(184 137 43 / .8);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        @elseif($step['state'] === 'current')
                            <span class="relative z-10 grid place-items-center w-9 h-9 rounded-full bg-white text-brand font-display text-[15px] ring-2 ring-brand breathe" style="--breathe: 220 38 38">{{ $i + 1 }}</span>
                        @else
                            <span class="relative z-10 grid place-items-center w-9 h-9 rounded-full bg-white text-slate-400 font-display text-[15px] ring-1 ring-slate-200">{{ $i + 1 }}</span>
                        @endif

                        <span class="mt-3 text-[12px] sm:text-[12.5px] font-semibold leading-tight {{ $step['state'] === 'upcoming' ? 'text-slate-400' : 'text-slate-900' }}">
                            <span class="hidden sm:inline">{{ $i + 1 }}. </span>{{ $step['label'] }}
                        </span>
                        <span class="sr-only">({{ $stateLabel[$step['state']] }})</span>
                        @if($step['state'] === 'current')
                            <span class="hidden sm:block mt-1 text-[12.5px] font-semibold text-brand" aria-hidden="true">In progress</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ═══ Step 2 · Outgoing checklist ═══ --}}
    <section class="card" aria-labelledby="step2Title">
        <div class="card-header items-start">
            <div class="flex items-start gap-4 min-w-0">
                <span class="font-display text-[1.6rem] leading-none text-gold tabular-nums pt-0.5" aria-hidden="true">02</span>
                <div class="min-w-0">
                    <p class="card-kicker">Step 2</p>
                    <h2 id="step2Title" class="card-title mt-0.5">Outgoing Executive Handover Checklist &amp; Notes</h2>
                </div>
            </div>
            @if($handover->checklist_running_activities)
                <span class="badge badge-emerald badge-dot shrink-0">Completed</span>
            @endif
        </div>

        <div class="card-body">
            @if($handover->checklist_running_activities)
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <dt class="text-[12.5px] font-semibold text-slate-500">Currently Running Activities &amp; Projects</dt>
                        <dd class="mt-1.5 text-[13.5px] text-slate-800 whitespace-pre-line leading-relaxed">{{ $handover->checklist_running_activities }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12.5px] font-semibold text-slate-500">Pending Logistics &amp; Liabilities</dt>
                        <dd class="mt-1.5 text-[13.5px] text-slate-800 whitespace-pre-line leading-relaxed">{{ $handover->checklist_pending_items }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12.5px] font-semibold text-slate-500">Key Institutional Contacts &amp; Advisors</dt>
                        <dd class="mt-1.5 text-[13.5px] text-slate-800 whitespace-pre-line leading-relaxed">{{ $handover->checklist_key_contacts }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12.5px] font-semibold text-slate-500">Yearly Calendar Highlights &amp; Traditions</dt>
                        <dd class="mt-1.5 text-[13.5px] text-slate-800 whitespace-pre-line leading-relaxed">{{ $handover->checklist_yearly_calendar }}</dd>
                    </div>
                </dl>
            @else
                <form method="POST" action="{{ route('handover.checklist', $handover->id) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="checklist_running_activities" class="field-label">What is currently running? <span class="text-brand">*</span></label>
                            <textarea id="checklist_running_activities" name="checklist_running_activities" rows="3" required placeholder="Describe active weekly fellowships, ongoing campaigns, or projects..." class="field"></textarea>
                        </div>
                        <div>
                            <label for="checklist_pending_items" class="field-label">Pending items or outstanding matters <span class="text-brand">*</span></label>
                            <textarea id="checklist_pending_items" name="checklist_pending_items" rows="3" required placeholder="Any pending approvals, unpaid invoices, or borrowed equipment..." class="field"></textarea>
                        </div>
                        <div>
                            <label for="checklist_key_contacts" class="field-label">Key contacts &amp; faculty advisors <span class="text-brand">*</span></label>
                            <textarea id="checklist_key_contacts" name="checklist_key_contacts" rows="3" required placeholder="Chaplaincy contacts, patrons, senior committee leads..." class="field"></textarea>
                        </div>
                        <div>
                            <label for="checklist_yearly_calendar" class="field-label">Yearly calendar traditions &amp; recurrent events <span class="text-brand">*</span></label>
                            <textarea id="checklist_yearly_calendar" name="checklist_yearly_calendar" rows="3" required placeholder="Annual retreat dates, joint fellowships, missionary outreach..." class="field"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Save Handover Checklist</button>
                    </div>
                </form>
            @endif
        </div>
    </section>

    {{-- ═══ Step 3 · Vault verification ═══ --}}
    @php
        $vaultHealth = $handover->club->getArchiveHealthScore();
    @endphp
    <section class="card" aria-labelledby="step3Title">
        <div class="card-header items-start">
            <div class="flex items-start gap-4 min-w-0">
                <span class="font-display text-[1.6rem] leading-none text-gold tabular-nums pt-0.5" aria-hidden="true">03</span>
                <div class="min-w-0">
                    <p class="card-kicker">Step 3</p>
                    <h2 id="step3Title" class="card-title mt-0.5">Vault Records Completeness Verification</h2>
                </div>
            </div>
            @if($handover->folders_verified)
                <span class="badge badge-emerald badge-dot shrink-0">Verified Complete</span>
            @endif
        </div>

        <div class="card-body grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_17rem] gap-6 items-start">
            <div class="space-y-3">
                <p class="text-[13.5px] text-slate-700 leading-relaxed">
                    The Chaplaincy Office must verify that all 9 required Vault folders contain the latest documents (Constitution, Executive roster, Minutes, Financials, Reports).
                </p>
                <p class="text-[12px] text-slate-500">
                    Current active records in {{ $handover->club?->name }} Vault: <strong class="text-slate-900 font-semibold tabular-nums">{{ $vaultCount }} files</strong>
                </p>
                @if(auth()->user()->isOffice() && !$handover->folders_verified && $handover->checklist_running_activities)
                    <form method="POST" action="{{ route('handover.verify_folders', $handover->id) }}" class="pt-1">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Confirm Vault Complete
                        </button>
                    </form>
                @endif
            </div>

            <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200/70 p-4">
                <p class="text-[12.5px] font-semibold text-slate-500">Vault Sentinel Health Score</p>
                <p class="mt-2 flex items-baseline gap-2">
                    <span class="stat-value text-[2rem] text-slate-900">{{ $vaultHealth['score'] }}</span><span class="font-display text-slate-400">%</span>
                    <span class="ml-auto badge badge-gold"><span class="sr-only">{{ $vaultHealth['score'] }}% &bull; </span>{{ $vaultHealth['grade'] }}</span>
                </p>
                <div class="progress mt-3" aria-hidden="true"><span style="width: {{ max(0, min(100, $vaultHealth['score'])) }}%"></span></div>
                <a href="{{ route('vault.index', ['club_id' => $handover->club?->id]) }}" target="_blank" class="card-link mt-3">Inspect Club Vault</a>
            </div>
        </div>
    </section>

    {{-- ═══ Step 4 · Joint meeting ═══ --}}
    <section class="card" aria-labelledby="step4Title">
        <div class="card-header items-start">
            <div class="flex items-start gap-4 min-w-0">
                <span class="font-display text-[1.6rem] leading-none text-gold tabular-nums pt-0.5" aria-hidden="true">04</span>
                <div class="min-w-0">
                    <p class="card-kicker">Step 4</p>
                    <h2 id="step4Title" class="card-title mt-0.5">Formal Joint Handover Meeting</h2>
                </div>
            </div>
            @if($handover->meeting_date)
                <span class="badge badge-emerald badge-dot shrink-0">Meeting Recorded</span>
            @endif
        </div>

        <div class="card-body">
            @if($handover->meeting_date)
                <dl class="grid grid-cols-1 sm:grid-cols-[12rem_minmax(0,1fr)] gap-x-8 gap-y-4">
                    <div>
                        <dt class="text-[12.5px] font-semibold text-slate-500">Meeting Date:</dt>
                        <dd class="mt-1.5 font-display text-[1.05rem] text-slate-900">{{ $handover->meeting_date->format('l, F j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12.5px] font-semibold text-slate-500">Meeting Minutes &amp; Notes:</dt>
                        <dd class="mt-1.5 text-[13.5px] text-slate-700 whitespace-pre-line leading-relaxed">{{ $handover->meeting_notes }}</dd>
                    </div>
                </dl>
            @elseif(auth()->user()->isOffice() && $handover->folders_verified)
                <form method="POST" action="{{ route('handover.meeting', $handover->id) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-[12rem_minmax(0,1fr)] gap-4">
                        <div>
                            <label for="handover_meeting_date" class="field-label">Meeting date <span class="text-brand">*</span></label>
                            <input type="date" id="handover_meeting_date" name="meeting_date" required value="{{ date('Y-m-d') }}" class="field">
                        </div>
                        <div>
                            <label for="handover_meeting_notes" class="field-label">Meeting summary &amp; pastoral counsel notes <span class="text-brand">*</span></label>
                            <textarea id="handover_meeting_notes" name="meeting_notes" rows="3" required placeholder="Record attendees, pastoral counsel given, and transition agreements..." class="field"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Record Handover Meeting</button>
                    </div>
                </form>
            @else
                <p class="flex items-center gap-2.5 text-[13px] text-slate-500 italic">
                    <svg class="w-4 h-4 text-slate-400 not-italic shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Awaiting previous steps before scheduling official meeting.
                </p>
            @endif
        </div>
    </section>

    {{-- ═══ Step 5 · Transfer of access ═══ --}}
    @if(auth()->user()->isOffice() && $handover->meeting_date && $handover->status !== 'completed')
        <section class="card card-ink grain overflow-hidden on-dark" aria-labelledby="step5Title"
                 style="background-image: radial-gradient(90% 120% at 100% 0%, rgb(220 38 38 / .35), transparent 60%), radial-gradient(60% 80% at 0% 100%, rgb(217 183 108 / .12), transparent 70%);">
            <div class="relative p-6 sm:p-8 flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div class="flex items-start gap-4 min-w-0">
                    <span class="font-display text-[1.6rem] leading-none text-gold-light tabular-nums pt-0.5" aria-hidden="true">05</span>
                    <div class="min-w-0">
                        <p class="text-[12.5px] font-semibold text-gold-light/80">Step 5</p>
                        <h2 id="step5Title" class="mt-1 font-display text-[1.4rem] leading-tight text-white">Executive Credential &amp; Email Access Transfer</h2>
                        <p class="mt-3 max-w-2xl text-[13.5px] leading-relaxed text-white/65">
                            Clicking the button below will officially transfer club executive permissions to <strong class="font-semibold text-white">{{ $handover->incoming_president_name }}</strong> ({{ $handover->incoming_president_email }}), bind the official email <strong class="font-semibold text-white">{{ $handover->club?->official_email }}</strong> to the new term, and archive previous leadership into the permanent university history.
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('handover.transfer', $handover->id) }}" onsubmit="return confirm('Confirm transfer of leadership access to {{ $handover->incoming_president_name }}?');" class="shrink-0">
                    @csrf
                    <button type="submit" class="btn btn-gold btn-lg">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Complete Handover &amp; Transfer Access
                    </button>
                </form>
            </div>
        </section>
    @elseif($handover->status === 'completed')
        <section class="card overflow-hidden" aria-labelledby="sealedTitle">
            <div class="h-[3px]" style="background: linear-gradient(90deg, var(--color-gold-light), var(--color-gold), var(--color-gold-light));" aria-hidden="true"></div>
            <div class="p-6 sm:p-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex items-start gap-4 min-w-0">
                    <span class="grid place-items-center w-11 h-11 rounded-full shrink-0 text-[#2A1E05]"
                          style="background: linear-gradient(180deg, var(--color-gold-bright), var(--color-gold-light)); box-shadow: inset 0 0 0 1px rgb(184 137 43 / .6), 0 0 0 4px var(--color-gold-soft);" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[12.5px] font-semibold text-gold">HANDOVER FULLY EXECUTED &amp; SEALED</p>
                        <p class="mt-1.5 text-[13.5px] leading-relaxed text-slate-600">Official executive authority conferred upon <strong class="font-semibold text-slate-900">{{ $handover->incoming_president_name }}</strong>. All records archived in permanent history.</p>
                    </div>
                </div>
                <a href="{{ route('handover.certificate', $handover->id) }}" target="_blank" class="btn btn-primary shrink-0 self-start sm:self-auto">
                    View &amp; Print Official Certificate
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </section>
    @endif

</div>
@endsection
