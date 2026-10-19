@extends('layouts.app')

@section('title', 'Handover: ' . $handover->club->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('handover.index') }}" class="hover:underline text-[#DC2626] font-bold">Leadership Handover</a>
            <span>/</span>
            <span class="font-mono text-slate-700">{{ $handover->club->code }}</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold font-cinzel text-slate-900 tracking-tight">
                    {{ $handover->club->name }} Transition
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Academic Year {{ $handover->academic_year }} &bull; {{ $handover->outgoing_president_name }} to {{ $handover->incoming_president_name }}
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('handover.certificate', $handover->id) }}" target="_blank" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Handover Certificate</span>
                </a>
                <span class="text-xs font-bold px-3 py-1.5 rounded-xl uppercase
                    {{ $handover->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ str_replace('_', ' ', $handover->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- 5-Step Visual Stepper -->
    <div class="bg-white p-5 rounded-3xl card-elevation border border-slate-200/80">
        <div class="grid grid-cols-5 gap-2 text-center text-xs">
            <div class="space-y-1">
                <span class="w-8 h-8 mx-auto rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">✓</span>
                <span class="font-bold text-slate-800 block text-[11px] font-cinzel">1. Initiated</span>
            </div>
            <div class="space-y-1">
                <span class="w-8 h-8 mx-auto rounded-full {{ in_array($handover->status, ['checklist_completed', 'folders_verified', 'meeting_scheduled', 'completed']) ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white animate-pulse' }} font-bold flex items-center justify-center text-xs shadow-xs">
                    {{ in_array($handover->status, ['checklist_completed', 'folders_verified', 'meeting_scheduled', 'completed']) ? '✓' : '2' }}
                </span>
                <span class="font-bold text-slate-800 block text-[11px] font-cinzel">2. Checklist</span>
            </div>
            <div class="space-y-1">
                <span class="w-8 h-8 mx-auto rounded-full {{ in_array($handover->status, ['folders_verified', 'meeting_scheduled', 'completed']) ? 'bg-emerald-600 text-white' : ( $handover->status === 'checklist_completed' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-600' ) }} font-bold flex items-center justify-center text-xs shadow-xs">
                    {{ in_array($handover->status, ['folders_verified', 'meeting_scheduled', 'completed']) ? '✓' : '3' }}
                </span>
                <span class="font-bold text-slate-800 block text-[11px] font-cinzel">3. Vault Check</span>
            </div>
            <div class="space-y-1">
                <span class="w-8 h-8 mx-auto rounded-full {{ in_array($handover->status, ['meeting_scheduled', 'completed']) ? 'bg-emerald-600 text-white' : ( $handover->status === 'folders_verified' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-600' ) }} font-bold flex items-center justify-center text-xs shadow-xs">
                    {{ in_array($handover->status, ['meeting_scheduled', 'completed']) ? '✓' : '4' }}
                </span>
                <span class="font-bold text-slate-800 block text-[11px] font-cinzel">4. Meeting</span>
            </div>
            <div class="space-y-1">
                <span class="w-8 h-8 mx-auto rounded-full {{ $handover->status === 'completed' ? 'bg-emerald-600 text-white' : ( $handover->status === 'meeting_scheduled' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-600' ) }} font-bold flex items-center justify-center text-xs shadow-xs">
                    {{ $handover->status === 'completed' ? '✓' : '5' }}
                </span>
                <span class="font-bold text-slate-800 block text-[11px] font-cinzel">5. Transfer</span>
            </div>
        </div>
    </div>

    <!-- Step 2: Outgoing Executive Checklist Form / View -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-7 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">
                Step 2: Outgoing Executive Handover Checklist & Notes
            </h3>
            @if($handover->checklist_running_activities)
                <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full">Completed</span>
            @endif
        </div>

        @if($handover->checklist_running_activities)
            <!-- Read-only Checklist View -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-1">
                    <strong class="text-slate-500 uppercase tracking-wider text-[10px] block font-cinzel">Currently Running Activities & Projects</strong>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $handover->checklist_running_activities }}</p>
                </div>
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-1">
                    <strong class="text-slate-500 uppercase tracking-wider text-[10px] block font-cinzel">Pending Logistics & Liabilities</strong>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $handover->checklist_pending_items }}</p>
                </div>
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-1">
                    <strong class="text-slate-500 uppercase tracking-wider text-[10px] block font-cinzel">Key Institutional Contacts & Advisors</strong>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $handover->checklist_key_contacts }}</p>
                </div>
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-1">
                    <strong class="text-slate-500 uppercase tracking-wider text-[10px] block font-cinzel">Yearly Calendar Highlights & Traditions</strong>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $handover->checklist_yearly_calendar }}</p>
                </div>
            </div>
        @else
            <!-- Edit Checklist Form -->
            <form method="POST" action="{{ route('handover.checklist', $handover->id) }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">What is currently running? <span class="text-rose-600">*</span></label>
                    <textarea name="checklist_running_activities" rows="2" required placeholder="Describe active weekly fellowships, ongoing campaigns, or projects..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Pending items or outstanding matters <span class="text-rose-600">*</span></label>
                    <textarea name="checklist_pending_items" rows="2" required placeholder="Any pending approvals, unpaid invoices, or borrowed equipment..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Key contacts & faculty advisors <span class="text-rose-600">*</span></label>
                    <textarea name="checklist_key_contacts" rows="2" required placeholder="Chaplaincy contacts, patrons, senior committee leads..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Yearly calendar traditions & recurrent events <span class="text-rose-600">*</span></label>
                    <textarea name="checklist_yearly_calendar" rows="2" required placeholder="Annual retreat dates, joint fellowships, missionary outreach..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]"></textarea>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white font-bold text-xs shadow-md active:scale-95">
                    Save Handover Checklist
                </button>
            </form>
        @endif
    </div>

    <!-- Step 3: Vault Verification (Chaplaincy Office) -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-7 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">
                Step 3: Vault Records Completeness Verification
            </h3>
            @if($handover->folders_verified)
                <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full">Verified Complete</span>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
            <div class="space-y-1.5 flex-1">
                <p class="text-slate-700 leading-relaxed">
                    The Chaplaincy Office must verify that all 9 required Vault folders contain the latest documents (Constitution, Executive roster, Minutes, Financials, Reports).
                </p>
                <div class="text-[11px] text-slate-500">
                    Current active records in {{ $handover->club->name }} Vault: <strong class="text-slate-900 font-mono">{{ $vaultCount }} files</strong>
                </div>

                @php
                    $vaultHealth = $handover->club->getArchiveHealthScore();
                @endphp
                <div class="mt-3 p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block font-cinzel">Vault Sentinel Health Score</span>
                        <span class="text-xs font-bold text-[#DC2626]">{{ $vaultHealth['score'] }}% &bull; {{ $vaultHealth['grade'] }}</span>
                    </div>
                    <a href="{{ route('vault.index', ['club_id' => $handover->club->id]) }}" target="_blank" class="text-[11px] text-[#DC2626] font-bold hover:underline">
                        Inspect Club Vault &rarr;
                    </a>
                </div>
            </div>

            @if(auth()->user()->isOffice() && !$handover->folders_verified && $handover->checklist_running_activities)
                <form method="POST" action="{{ route('handover.verify_folders', $handover->id) }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white font-bold text-xs shadow-sm active:scale-95">
                        Confirm Vault Complete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Step 4: Joint Handover Meeting with Chaplaincy Office -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-7 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">
                Step 4: Formal Joint Handover Meeting
            </h3>
            @if($handover->meeting_date)
                <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full">Meeting Recorded</span>
            @endif
        </div>

        @if($handover->meeting_date)
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs space-y-1.5">
                <div><strong>Meeting Date:</strong> {{ $handover->meeting_date->format('l, F j, Y') }}</div>
                <div><strong>Meeting Minutes & Notes:</strong></div>
                <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $handover->meeting_notes }}</p>
            </div>
        @elseif(auth()->user()->isOffice() && $handover->folders_verified)
            <form method="POST" action="{{ route('handover.meeting', $handover->id) }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Meeting Date <span class="text-rose-600">*</span></label>
                    <input type="date" name="meeting_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Meeting Summary & Pastoral Counsel Notes <span class="text-rose-600">*</span></label>
                    <textarea name="meeting_notes" rows="2" required placeholder="Record attendees, pastoral counsel given, and transition agreements..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-sm active:scale-95">
                    Record Handover Meeting
                </button>
            </form>
        @else
            <p class="text-xs text-slate-500 italic">Awaiting previous steps before scheduling official meeting.</p>
        @endif
    </div>

    <!-- Step 5: Transfer of Access & Official Email (Chapel Secretary / Administrator) -->
    @if(auth()->user()->isOffice() && $handover->meeting_date && $handover->status !== 'completed')
        <div class="bg-gradient-to-br from-amber-50 to-amber-100/70 p-6 sm:p-7 rounded-3xl border-2 border-amber-300 space-y-3 card-elevation">
            <h3 class="text-xs font-bold text-amber-950 uppercase tracking-wider flex items-center space-x-2 font-cinzel">
                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                <span>Step 5: Executive Credential & Email Access Transfer</span>
            </h3>
            <p class="text-xs text-amber-900 leading-relaxed">
                Clicking the button below will officially transfer club executive permissions to <strong>{{ $handover->incoming_president_name }}</strong> ({{ $handover->incoming_president_email }}), bind the official email <strong>{{ $handover->club->official_email }}</strong> to the new term, and archive previous leadership into the permanent university history.
            </p>
            <form method="POST" action="{{ route('handover.transfer', $handover->id) }}" onsubmit="return confirm('Confirm transfer of leadership access to {{ $handover->incoming_president_name }}?');">
                @csrf
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold text-xs shadow-sm active:scale-95 cursor-pointer">
                    Complete Handover & Transfer Access
                </button>
            </form>
        </div>
    @elseif($handover->status === 'completed')
        <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 card-elevation">
            <div class="flex items-center space-x-3">
                <span class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0">✓</span>
                <div>
                    <span class="font-bold uppercase tracking-wider text-emerald-900 font-cinzel">HANDOVER FULLY EXECUTED & SEALED</span>
                    <p class="mt-0.5 text-slate-700">Official executive authority conferred upon <strong>{{ $handover->incoming_president_name }}</strong>. All records archived in permanent history.</p>
                </div>
            </div>
            <a href="{{ route('handover.certificate', $handover->id) }}" target="_blank" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold text-xs shadow-sm shrink-0 transition">
                <span>View & Print Official Certificate &rarr;</span>
            </a>
        </div>
    @endif

</div>
@endsection
