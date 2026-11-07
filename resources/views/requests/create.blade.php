@extends('layouts.app')

@section('title', 'Submit Activity Request')

@section('content')
<div class="page-shell max-w-5xl space-y-6">

    <!-- Breadcrumb & Title -->
    <header class="page-header">
        <div class="min-w-0">
            <nav class="eyebrow" aria-label="Breadcrumb">
                <a href="{{ route('requests.index') }}" class="hover:underline underline-offset-4">Requests</a>
                <span class="text-slate-300" aria-hidden="true">/</span>
                <span class="text-slate-500">New Proposal</span>
            </nav>
            <h1 class="page-title">Chapel Activity &amp; Resource Request Form</h1>
            <p class="page-lede">One unified institutional form for events, venue bookings, poster authorizations, equipment, and off-campus outings.</p>
        </div>
    </header>

    <!-- 14-Day Notice Banner -->
    <div id="fourteenDayNotice" class="relative overflow-hidden flex items-start gap-3 rounded-[0.875rem] bg-white px-4 py-3.5 text-[13px] text-slate-700 shadow-sm ring-1 ring-black/5 transition-all duration-300 before:absolute before:inset-y-0 before:left-0 before:w-[3px] before:bg-gold">
        <svg class="w-5 h-5 text-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="leading-relaxed">
            <p class="font-semibold text-slate-900">Chapel Policy (§4.1)</p>
            <p class="mt-0.5">Activity requests must be decided at least 14 days before the event. Please select a date with at least 14 days notice to ensure timely review and compliance.</p>
        </div>
    </div>

    <!-- Live Clash Alert Box with Conflict & Capacity Resolver -->
    <div id="liveClashAlert" role="alert" class="hidden relative overflow-hidden flex flex-col gap-3 rounded-[0.875rem] bg-white p-4 sm:p-5 text-[13px] text-rose-900 shadow-sm ring-1 ring-rose-200 animate-fade-in before:absolute before:inset-y-0 before:left-0 before:w-[3px] before:bg-rose-500">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3.75m0 3.75h.008M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div class="space-y-1 leading-relaxed">
                <p class="font-semibold text-rose-900">Venue / calendar clash detected</p>
                <p id="liveClashText" class="text-rose-800"></p>
            </div>
        </div>

        <!-- Suggested Alternatives -->
        <div id="smartSuggestionsContainer" class="hidden rounded-xl bg-slate-50 p-4 ring-1 ring-black/5 space-y-2.5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-slate-500">Available Alternative Venues &amp; Slots</span>
                <span class="badge badge-gold">1-Click Auto-Fix</span>
            </div>
            <div id="suggestionsList" class="space-y-2 text-xs"></div>
        </div>
    </div>

    <!-- Main Request Form -->
    <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data" class="card" id="requestForm">
        @csrf

        <div class="divide-y divide-slate-100">

            <!-- Sponsor & category -->
            <section class="grid gap-5 p-5 sm:p-7 lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-10">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">I</p>
                    <h2 class="card-title mt-2">Sponsor &amp; category</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Who is proposing, and what kind of request this is.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="club_id" class="field-label">
                            Sponsoring Club / Fellowship <span class="text-brand">*</span>
                        </label>
                        @if(auth()->user()->isOffice())
                            <select name="club_id" id="club_id" required class="field">
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" @selected((string) old('club_id') === (string) $club->id)>{{ $club->name }} ({{ $club->code }})</option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="club_id" value="{{ auth()->user()->club_id }}">
                            <div class="field flex items-center gap-2 bg-slate-50 font-semibold text-slate-800">
                                <span class="w-2 h-2 rounded-full bg-brand shrink-0" aria-hidden="true"></span>
                                <span class="truncate">{{ auth()->user()->club?->name ?? 'Chapel Club' }} ({{ auth()->user()->club?->code ?? 'AU' }})</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="category" class="field-label">
                            Request Category <span class="text-brand">*</span>
                        </label>
                        <select name="category" id="category" required class="field">
                            <option value="event" @selected(old('category') === 'event')>Campus Event / Fellowship Activity</option>
                            <option value="venue" @selected(old('category') === 'venue')>Venue Booking Only</option>
                            <option value="poster" @selected(old('category') === 'poster')>Poster & Media Clearance</option>
                            <option value="equipment" @selected(old('category') === 'equipment')>Chapel Equipment Loan</option>
                            <option value="outing" @selected(old('category') === 'outing')>Off-Campus Outing / Mission Trip (Requires VC Go-Ahead)</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- The activity -->
            <section class="grid gap-5 p-5 sm:p-7 lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-10">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">II</p>
                    <h2 class="card-title mt-2">The activity</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Name it, and tell the Chaplaincy why it matters.</p>
                </div>
                <div class="space-y-4">
                    <!-- Title -->
                    <div>
                        <label for="req_title" class="field-label">
                            Activity Title <span class="text-brand">*</span>
                        </label>
                        <input type="text" id="req_title" name="title" value="{{ old('title') }}" required placeholder="e.g. Annual Praise Night, Leadership Retreat, All-Night Vigil" class="field">
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="req_purpose" class="field-label">
                            Purpose & Detailed Description <span class="text-brand">*</span>
                        </label>
                        <textarea id="req_purpose" name="purpose" rows="4" required placeholder="Explain the spiritual, fellowship, or community purpose of this activity..." class="field">{{ old('purpose') }}</textarea>
                    </div>
                </div>
            </section>

            <!-- Date and Times Row with 14-Day Calculator -->
            <section class="grid gap-5 p-5 sm:p-7 lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-10">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">III</p>
                    <h2 class="card-title mt-2">Date &amp; time</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Notice is counted live against the 14-day rule.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="event_date" class="field-label">
                            Proposed Event Date <span class="text-brand">*</span>
                        </label>
                        <input type="date" name="event_date" id="event_date" min="{{ date('Y-m-d') }}" value="{{ old('event_date', date('Y-m-d', strtotime('+15 days'))) }}" required class="field">
                        <div id="dateNoticeSpan" class="text-[11px] text-emerald-700 font-semibold mt-1.5" aria-live="polite">15 days notice (Compliant)</div>
                    </div>

                    <div>
                        <label for="start_time" class="field-label">
                            Start Time <span class="text-brand">*</span>
                        </label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '17:00') }}" required class="field">
                    </div>

                    <div>
                        <label for="end_time" class="field-label">
                            End Time <span class="text-brand">*</span>
                        </label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '19:00') }}" required class="field">
                    </div>
                </div>
            </section>

            <!-- Venue Selection Row -->
            <section class="grid gap-5 p-5 sm:p-7 lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-10">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">IV</p>
                    <h2 class="card-title mt-2">Venue</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Chapel venues are checked against the Master Calendar.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="venue_id" class="field-label">
                            Chapel / University Venue
                        </label>
                        <select name="venue_id" id="venue_id" class="field">
                            <option value="">-- Select Chapel Venue --</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}" data-capacity="{{ $venue->capacity }}" @selected((string) old('venue_id') === (string) $venue->id)>
                                    {{ $venue->name }} (Cap: {{ $venue->capacity }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="venue_other" class="field-label">
                            Other Venue / Off-Campus Location
                        </label>
                        <input type="text" name="venue_other" id="venue_other" value="{{ old('venue_other') }}" placeholder="e.g. Honde Valley, Student Union Hall" class="field">
                    </div>
                </div>
            </section>

            <!-- Attendance, Budget, Attachment Row -->
            <section class="grid gap-5 p-5 sm:p-7 lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-10">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">V</p>
                    <h2 class="card-title mt-2">Logistics</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Numbers, budget and any supporting document.</p>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="req_expected_attendance" class="field-label">
                                Expected Attendance
                            </label>
                            <input type="number" id="req_expected_attendance" name="expected_attendance" value="{{ old('expected_attendance', 50) }}" min="1" class="field tabular-nums">
                        </div>

                        <div>
                            <label for="req_budget" class="field-label">
                                Estimated Budget (USD)
                            </label>
                            <input type="number" id="req_budget" step="0.01" name="budget" value="{{ old('budget', '0.00') }}" min="0" class="field tabular-nums">
                        </div>
                    </div>

                    <div>
                        <label for="req_attachment" class="field-label">
                            Attachment (Draft Poster / Budget)
                        </label>
                        <input type="file" id="req_attachment" name="attachment" class="field py-2 text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-[12px] file:font-semibold file:text-slate-800 hover:file:bg-slate-200">
                    </div>
                </div>
            </section>

            <!-- Accessibility: Sign-Language Interpreter Option -->
            <section class="grid gap-5 p-5 sm:p-7 lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-10">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">VI</p>
                    <h2 class="card-title mt-2">Accessibility</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Chapel Accessibility &amp; Special Needs.</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-black/5 space-y-3 self-start">
                    <div class="flex items-start gap-2.5">
                        <input type="checkbox" id="sign_interpreter_required" name="sign_interpreter_required" value="1" onchange="document.getElementById('sign_req_name').classList.toggle('hidden', !this.checked)" class="mt-0.5 rounded">
                        <label for="sign_interpreter_required" class="text-[13px] font-semibold text-slate-800 cursor-pointer">Request Sign-Language Interpreter (Chapel Accessibility & Special Needs)</label>
                    </div>
                    <div id="sign_req_name" class="hidden pt-1">
                        <label for="req_sign_interpreter_name" class="field-label">Interpreter Contact / Notes (Optional)</label>
                        <input type="text" id="req_sign_interpreter_name" name="sign_interpreter_name" placeholder="e.g. Sister Chipo or AU Special Needs Ministry volunteer" class="field">
                    </div>
                </div>
            </section>

        </div>

        <!-- Submission Footer -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-b-[var(--radius-card)] border-t border-slate-100 bg-slate-50/70 px-5 py-4 sm:px-7">
            <p class="flex items-start gap-2 text-[11.5px] text-slate-500 leading-relaxed">
                <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                Your request will be logged in the permanent audit trail with your verified email signature.
            </p>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('requests.index') }}" class="btn btn-ghost flex-1 sm:flex-none">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                    Submit Proposal
                </button>
            </div>
        </div>

    </form>

</div>

<!-- Real-time 14-day rule and Clash Detection Javascript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('event_date');
        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');
        const venueSelect = document.getElementById('venue_id');
        const noticeDiv = document.getElementById('fourteenDayNotice');
        const noticeSpan = document.getElementById('dateNoticeSpan');
        const clashAlert = document.getElementById('liveClashAlert');
        const clashText = document.getElementById('liveClashText');

        function evaluate14DayRule() {
            if (!dateInput.value) return;
            const today = new Date();
            today.setHours(0,0,0,0);
            const target = new Date(dateInput.value);
            const diffTime = target - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays < 14) {
                noticeDiv.className = 'relative overflow-hidden flex items-start gap-3 rounded-[0.875rem] bg-rose-50/60 px-4 py-3.5 text-[13px] text-rose-900 shadow-sm ring-1 ring-rose-200 transition-all duration-300 before:absolute before:inset-y-0 before:left-0 before:w-[3px] before:bg-rose-500 [&_svg]:text-rose-600 [&_p:first-child]:text-rose-900';
                noticeSpan.className = 'text-[11px] text-rose-700 font-semibold mt-1.5';
                noticeSpan.innerText = `${diffDays} days notice (< 14 days! Late Submission flag will be recorded per Chapel Manual §4.1)`;
            } else {
                noticeDiv.className = 'relative overflow-hidden flex items-start gap-3 rounded-[0.875rem] bg-white px-4 py-3.5 text-[13px] text-slate-700 shadow-sm ring-1 ring-black/5 transition-all duration-300 before:absolute before:inset-y-0 before:left-0 before:w-[3px] before:bg-gold';
                noticeSpan.className = 'text-[11px] text-emerald-700 font-semibold mt-1.5';
                noticeSpan.innerText = `${diffDays} days notice (Complies with 14-day advance rule)`;
            }
        }

        const suggestionsBox = document.getElementById('smartSuggestionsContainer');
        const suggestionsList = document.getElementById('suggestionsList');

        window.applySmartSuggestion = function(venueId, start, end) {
            if (venueId) venueSelect.value = venueId;
            if (start) startInput.value = start;
            if (end) endInput.value = end;
            checkClash();
        };

        function checkClash() {
            const venueId = venueSelect.value;
            const date = dateInput.value;
            const start = startInput.value;
            const end = endInput.value;
            const attendance = document.querySelector('input[name="expected_attendance"]')?.value || 50;

            if (!venueId || !date || !start || !end) {
                clashAlert.classList.add('hidden');
                return;
            }

            fetch(`/api/requests/check-clash?venue_id=${venueId}&event_date=${date}&start_time=${start}&end_time=${end}&expected_attendance=${attendance}`)
                .then(r => r.json())
                .then(data => {
                    if (data.has_clash) {
                        const conflict = data.clashes[0];
                        clashText.innerHTML = `This venue is already booked on <strong>${date}</strong> between <strong>${conflict.start_time.substr(0,5)} - ${conflict.end_time.substr(0,5)}</strong> for "<strong>${conflict.title}</strong>" (${conflict.club}).`;
                        clashAlert.classList.remove('hidden');

                        if (data.suggestions && data.suggestions.length > 0) {
                            suggestionsList.innerHTML = '';
                            data.suggestions.forEach(s => {
                                const btn = document.createElement('div');
                                btn.className = 'flex items-center justify-between gap-3 rounded-xl bg-white px-3.5 py-3 ring-1 ring-black/5 shadow-xs transition hover:ring-gold/50 cursor-pointer';
                                btn.innerHTML = `
                                    <div class="flex items-start gap-2 text-[12.5px] text-slate-800"><svg class="w-4 h-4 mt-px shrink-0 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/></svg><strong class="font-semibold">${s.message}</strong></div>
                                    <button type="button" class="btn btn-primary btn-sm shrink-0">
                                        Apply
                                    </button>
                                `;
                                btn.onclick = () => applySmartSuggestion(s.venue_id, s.start_time, s.end_time);
                                suggestionsList.appendChild(btn);
                            });
                            suggestionsBox.classList.remove('hidden');
                        } else {
                            suggestionsBox.classList.add('hidden');
                        }
                    } else {
                        clashAlert.classList.add('hidden');
                        suggestionsBox.classList.add('hidden');
                    }
                })
                .catch(() => {});
        }

        dateInput.addEventListener('change', () => { evaluate14DayRule(); checkClash(); });
        startInput.addEventListener('change', checkClash);
        endInput.addEventListener('change', checkClash);
        venueSelect.addEventListener('change', checkClash);

        evaluate14DayRule();
        checkClash();
    });
</script>
@endsection
