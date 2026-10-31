@extends('layouts.app')

@section('title', 'Request ' . $requestItem->reference_no)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Breadcrumb & Status Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('requests.index') }}" class="hover:underline text-[#DC2626] font-bold">Requests</a>
                <span>/</span>
                <span class="font-mono text-slate-700">{{ $requestItem->reference_no }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    {{ $requestItem->title }}
                </h2>
            </div>
        </div>

        <!-- Status Badge & Action -->
        <div class="flex flex-wrap items-center gap-2">
            @if($requestItem->isEditableByClub() && ($user->isOffice() || $user->club_id === $requestItem->club_id))
                <a href="{{ route('requests.edit', $requestItem->id) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold {{ $requestItem->status === 'needs_changes' ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }} shadow-xs transition active:scale-95">
                    {{ $requestItem->status === 'needs_changes' ? 'Revise & Resubmit' : 'Edit Proposal' }}
                </a>
            @endif
            @if($requestItem->status === 'approved')
                <button type="button" onclick="dispatchWhatsApp()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs flex items-center space-x-1.5 transition active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.86-1.537c1.7 1.011 3.684 1.595 5.803 1.595 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/></svg>
                    <span>WhatsApp Pass</span>
                </button>
                <a href="{{ route('requests.clearance_pass', $requestItem->id) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#DC2626] hover:bg-[#B91C1C] text-white shadow-xs flex items-center space-x-1.5 transition active:scale-95">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Clearance Pass (QR)</span>
                </a>
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>Approved & On Calendar</span>
                </span>
            @elseif($requestItem->status === 'under_review')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-300 flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
                    <span>Under Review</span>
                </span>
            @elseif($requestItem->status === 'needs_changes')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                    <span>Needs Changes</span>
                </span>
            @elseif($requestItem->status === 'declined')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    <span>Declined</span>
                </span>
            @else
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300 flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span>Submitted</span>
                </span>
            @endif
        </div>
    </div>

    <!-- 14-Day Rule Notice Alert -->
    @if($requestItem->is_late_submission)
        <div class="p-4 sm:p-5 rounded-2xl border border-rose-300 bg-rose-50 text-rose-900 text-xs flex items-start space-x-3 shadow-2xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <span class="font-bold uppercase tracking-wide">LATE SUBMISSION NOTICE (Chapel Manual §4.1 Flag):</span>
                <p class="mt-1 leading-relaxed">This request was submitted with less than 14 days advance notice prior to the activity date. The Chaplaincy Office reviews late submissions on exceptional grounds only.</p>
            </div>
        </div>
    @else
        <div class="p-3.5 sm:p-4 rounded-2xl border border-emerald-200 bg-emerald-50/70 text-emerald-900 text-xs flex items-center space-x-2.5">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium">14-Day Advance Notice Rule Satisfied: Formally submitted in accordance with Chapel regulations.</span>
        </div>
    @endif

    <!-- Venue Clash Alert (If Detected) -->
    @if($clashes->isNotEmpty())
        <div class="p-4 sm:p-5 rounded-2xl border border-amber-300 bg-amber-50 text-amber-900 text-xs flex items-start space-x-3 card-elevation">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="space-y-1.5">
                <span class="font-bold uppercase tracking-wide">POTENTIAL VENUE SCHEDULE CLASH DETECTED:</span>
                <p>The selected venue has overlapping activity bookings on the Master Calendar:</p>
                <ul class="list-disc list-inside space-y-0.5 font-semibold text-amber-950">
                    @foreach($clashes as $clash)
                        <li>{{ $clash->title }} ({{ $clash->club?->name }}) &bull; {{ substr($clash->start_time, 0, 5) }} - {{ substr($clash->end_time, 0, 5) }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Request Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Main Information & VC Section -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#DC2626]">Activity Dossier</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white shadow-sm" style="background-color: {{ $requestItem->club?->color_hex ?? '#DC2626' }}">
                        {{ $requestItem->club?->name }} ({{ $requestItem->club?->code }})
                    </span>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <strong class="text-slate-500 uppercase tracking-wider text-[10px] block mb-1">Purpose & Objectives</strong>
                        <p class="text-slate-800 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                            {{ $requestItem->purpose }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-1">
                        <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-200/60">
                            <strong class="text-slate-500 uppercase tracking-wider text-[10px] block">Event Date</strong>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $requestItem->event_date->format('M d, Y') }}</span>
                        </div>
                        <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-200/60">
                            <strong class="text-slate-500 uppercase tracking-wider text-[10px] block">Time Schedule</strong>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block font-mono">{{ substr($requestItem->start_time, 0, 5) }} - {{ substr($requestItem->end_time, 0, 5) }}</span>
                        </div>
                        <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-200/60">
                            <strong class="text-slate-500 uppercase tracking-wider text-[10px] block">Venue</strong>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $requestItem->venue ? $requestItem->venue?->name : ($requestItem->venue_other ?: 'TBA') }}</span>
                        </div>
                        <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-200/60">
                            <strong class="text-slate-500 uppercase tracking-wider text-[10px] block">Expected Attendance</strong>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $requestItem->expected_attendance ?: 'N/A' }} attendees</span>
                        </div>
                        <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-200/60">
                            <strong class="text-slate-500 uppercase tracking-wider text-[10px] block">Estimated Budget</strong>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block font-mono">${{ number_format($requestItem->budget, 2) }}</span>
                        </div>
                        <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-200/60">
                            <strong class="text-slate-500 uppercase tracking-wider text-[10px] block">Submitted By</strong>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $requestItem->requester?->name }}</span>
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <strong class="text-slate-500 uppercase tracking-wider text-[10px] block">Accessibility / Sign Interpreter</strong>
                            @if($requestItem->sign_interpreter_required)
                                <span class="font-bold text-emerald-800 text-xs flex items-center space-x-1.5 mt-0.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Requested: {{ $requestItem->sign_interpreter_name ?: 'Chaplaincy Sign Language Interpreter' }}</span>
                                </span>
                            @else
                                <span class="text-slate-600 text-xs mt-0.5 block">Standard Accessibility Arrangement</span>
                            @endif
                        </div>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-600">AU Inclusion §2.4</span>
                    </div>

                    @if($requestItem->attachment_path)
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Supporting Attachment:</span>
                            <a href="{{ route('requests.attachment', $requestItem->id) }}" target="_blank" rel="noopener" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-[#DC2626] hover:text-white text-[#DC2626] font-bold transition shadow-sm active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>{{ $requestItem->attachment_name ?: 'Download Document' }}</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Vice-Chancellor Go-Ahead Clearance Section -->
            @if($requestItem->vc_go_ahead_needed || $requestItem->category === 'outing')
                <div class="bg-white rounded-3xl card-elevation border border-purple-200 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-purple-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-purple-900 font-cinzel">
                                Vice-Chancellor Go-Ahead Clearance (Outing / Mission)
                            </h3>
                        </div>
                        @if($requestItem->vc_go_ahead_status === 'received')
                            <span class="text-[11px] font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full border border-emerald-200">Clearance Recorded</span>
                        @else
                            <span class="text-[11px] font-bold bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full border border-amber-200">Pending Clearance</span>
                        @endif
                    </div>

                    @if($requestItem->vc_go_ahead_status === 'received')
                        <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 text-xs space-y-2">
                            <strong class="text-purple-950 font-bold block">Recorded Go-Ahead Note:</strong>
                            <p class="text-purple-900 leading-relaxed">{{ $requestItem->vc_go_ahead_note }}</p>
                            @if($requestItem->vc_go_ahead_attachment)
                                <div class="pt-2">
                                    <a href="{{ route('requests.vc_attachment', $requestItem->id) }}" target="_blank" rel="noopener" class="text-purple-700 font-bold underline text-[11px] hover:text-purple-900">
                                        View Attached VC Clearance Memo &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Per Chapel Manual §4.1, off-campus outings, mission trips, and major gatherings require the Vice-Chancellor's official clearance memo before final approval by the Chaplain.
                        </p>
                        @if($user->canApproveRequests())
                            <form method="POST" action="{{ route('requests.vc_go_ahead', $requestItem->id) }}" enctype="multipart/form-data" class="space-y-3 pt-2">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Clearance Memo Reference / Note</label>
                                    <textarea name="vc_go_ahead_note" rows="2" required placeholder="e.g. VC Memo VC/CH/2026/088 approved by Prof. Vice-Chancellor on 14 Oct 2026." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Upload Clearance Memo / Email PDF</label>
                                    <input type="file" name="vc_attachment" class="text-xs">
                                </div>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold transition shadow-sm active:scale-95">
                                    Record VC Clearance
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            @endif

            <!-- Decision Reason (If Declined or Needs Changes) -->
            @if($requestItem->decision_reason)
                <div class="bg-white rounded-3xl card-elevation border {{ $requestItem->status === 'declined' ? 'border-rose-200' : 'border-amber-200' }} p-6 space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $requestItem->status === 'declined' ? 'text-rose-800' : 'text-amber-800' }} font-cinzel">
                        {{ $requestItem->status === 'declined' ? 'Official Decline Justification' : 'Chaplaincy Feedback / Required Changes' }}
                    </span>
                    <p class="text-xs text-slate-800 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        {{ $requestItem->decision_reason }}
                    </p>
                    @if($requestItem->decidedBy)
                        <div class="text-[10px] text-slate-500 pt-1 font-mono">
                            Recorded by {{ $requestItem->decidedBy?->name }} &bull; {{ $requestItem->decided_at?->format('M d, Y H:i') }}
                        </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- Right 1 Col: Approver Action Panel & Audit Trail -->
        <div class="space-y-6">

            <!-- Approver Action Box (Chaplain / Assistant) -->
            @if($user->canApproveRequests() && in_array($requestItem->status, ['submitted', 'under_review', 'needs_changes']))
                <div class="bg-white rounded-3xl card-elevation border-2 border-[#DC2626] p-6 space-y-4">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626]"></span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 font-cinzel">Chaplaincy Decision Panel</h3>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Review against Master Calendar, 14-day rule, and university policy. Approving immediately schedules the activity on the Master Calendar.
                    </p>

                    <div class="space-y-2.5 pt-1">
                        <!-- Approve Form -->
                        @if($requestItem->status === 'needs_changes')
                        <p class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 rounded-xl p-2.5">Returned to the club for changes. It can be approved once the club resubmits.</p>
                        @else
                        <form method="POST" action="{{ route('requests.approve', $requestItem->id) }}" class="space-y-2">
                            @csrf
                            @if($clashes->isNotEmpty())
                                <label class="flex items-start gap-2 text-[11px] text-rose-800 bg-rose-50 border border-rose-200 rounded-xl p-2.5 cursor-pointer">
                                    <input type="checkbox" name="override_clash" value="1" class="mt-0.5 rounded text-[#DC2626] focus:ring-[#DC2626]">
                                    <span>This venue is already booked at this time. Tick to double-book deliberately.</span>
                                </label>
                            @endif
                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm flex items-center justify-center space-x-1.5 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Approve & Schedule Event</span>
                            </button>
                        </form>
                        @endif

                        <!-- Needs Changes Toggle -->
                        <button type="button" onclick="document.getElementById('needsChangesModal').classList.toggle('hidden')" class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-sm active:scale-95">
                            Return for Changes
                        </button>

                        <!-- Decline Toggle -->
                        <button type="button" onclick="document.getElementById('declineModal').classList.toggle('hidden')" class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm active:scale-95">
                            Decline Request
                        </button>
                    </div>
                </div>
            @endif

            <!-- Audit Trail & Change History -->
            <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">Audit Log & History</h3>
                        <p class="text-[11px] text-slate-500">Tamper-evident compliance trail</p>
                    </div>
                    <a href="{{ route('audit') }}" class="text-[11px] font-bold text-[#DC2626] hover:underline">Full Ledger &rarr;</a>
                </div>
                <div class="p-5 space-y-4">
                    @forelse($requestItem->auditLogs as $log)
                        <div class="relative pl-5 border-l-2 border-slate-200 space-y-1">
                            <span class="absolute -left-1.5 top-1 w-2.5 h-2.5 rounded-full bg-[#DC2626]"></span>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold text-slate-900 uppercase">{{ str_replace('_', ' ', $log->action) }}</span>
                                <span class="text-slate-400 font-mono">{{ $log->created_at->format('M d, H:i') }}</span>
                            </div>
                            <div class="text-xs text-slate-600">{{ $log->notes }}</div>
                            <div class="text-[10px] text-slate-400">By {{ $log->user_name }}</div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-slate-400">No previous audit logs.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Needs Changes Modal -->
<div id="needsChangesModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-amber-300 card-elevation">
        <h3 class="text-sm font-bold text-amber-900 uppercase tracking-wide mb-2 font-cinzel">Request Modifications from Club</h3>
        <p class="text-xs text-slate-600 mb-3">Specify the adjustments required (e.g. venue change, budget revision, or program clarification):</p>
        <form method="POST" action="{{ route('requests.needs_changes', $requestItem->id) }}">
            @csrf
            <textarea name="notes" rows="4" required placeholder="Describe required revisions..." class="w-full text-xs p-3.5 rounded-2xl border border-slate-300 mb-4 focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('needsChangesModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition">Submit Notice</button>
            </div>
        </form>
    </div>
</div>

<!-- Decline Modal (Mandatory Reason) -->
<div id="declineModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-rose-300 card-elevation">
        <h3 class="text-sm font-bold text-rose-900 uppercase tracking-wide mb-2 font-cinzel">Decline Activity Proposal</h3>
        <p class="text-xs text-slate-600 mb-3">Per Chapel Manual policy, a formal reason is mandatory when declining a request:</p>
        <form method="POST" action="{{ route('requests.decline', $requestItem->id) }}">
            @csrf
            <textarea name="reason" rows="4" required placeholder="Mandatory official justification for decline..." class="w-full text-xs p-3.5 rounded-2xl border border-slate-300 mb-4 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('declineModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition">Decline Request</button>
            </div>
        </form>
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
