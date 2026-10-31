@extends('layouts.app')

@section('title', 'Submit Activity Request')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Breadcrumb & Title -->
    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('requests.index') }}" class="hover:underline text-[#DC2626] font-bold">Requests</a>
            <span>/</span>
            <span class="text-slate-700">New Proposal</span>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
            Chapel Activity & Resource Request Form
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
            One unified institutional form for events, venue bookings, poster authorizations, equipment, and off-campus outings.
        </p>
    </div>

    <!-- 14-Day Notice Banner -->
    <div id="fourteenDayNotice" class="p-4 sm:p-5 rounded-2xl border border-blue-200 bg-blue-50/60 text-blue-900 text-xs flex items-start space-x-3 transition-all duration-300 shadow-2xs">
        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="space-y-0.5">
            <span class="font-bold uppercase tracking-wider">Chapel Policy (§4.1):</span>
            <p class="leading-relaxed">Activity requests must be decided at least 14 days before the event. Please select a date with at least 14 days notice to ensure timely review and compliance.</p>
        </div>
    </div>

    <!-- Live Clash Alert Box with Conflict & Capacity Resolver -->
    <div id="liveClashAlert" class="hidden p-5 rounded-2xl border border-rose-300 bg-rose-50 text-rose-900 text-xs flex flex-col space-y-3 animate-fade-in shadow-2xs">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="space-y-1">
                <span class="font-bold text-rose-800 uppercase tracking-wide">VENUE / CALENDAR CLASH DETECTED:</span>
                <p id="liveClashText" class="text-rose-700"></p>
            </div>
        </div>

        <!-- Suggested Alternatives -->
        <div id="smartSuggestionsContainer" class="hidden bg-white/95 p-4 rounded-xl border border-rose-200 space-y-2.5">
            <div class="flex items-center space-x-1.5 text-xs font-bold text-[#DC2626] uppercase tracking-wide">
                <span>Available Alternative Venues &amp; Slots</span>
                <span class="text-[9px] bg-amber-100 text-amber-900 px-1.5 py-0.5 rounded font-mono">1-Click Auto-Fix</span>
            </div>
            <div id="suggestionsList" class="space-y-2 text-xs"></div>
        </div>
    </div>

    <!-- Main Request Form -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-8">
        <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data" class="space-y-6" id="requestForm">
            @csrf

            <!-- Club & Category Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Sponsoring Club / Fellowship <span class="text-rose-600">*</span>
                    </label>
                    @if(auth()->user()->isOffice())
                        <select name="club_id" id="club_id" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" @selected((string) old('club_id') === (string) $club->id)>{{ $club->name }} ({{ $club->code }})</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="club_id" value="{{ auth()->user()->club_id }}">
                        <div class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 font-bold text-[#DC2626] flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                            <span>{{ auth()->user()->club->name ?? 'Chapel Club' }} ({{ auth()->user()->club->code ?? 'AU' }})</span>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Request Category <span class="text-rose-600">*</span>
                    </label>
                    <select name="category" id="category" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                        <option value="event" @selected(old('category') === 'event')>Campus Event / Fellowship Activity</option>
                        <option value="venue" @selected(old('category') === 'venue')>Venue Booking Only</option>
                        <option value="poster" @selected(old('category') === 'poster')>Poster & Media Clearance</option>
                        <option value="equipment" @selected(old('category') === 'equipment')>Chapel Equipment Loan</option>
                        <option value="outing" @selected(old('category') === 'outing')>Off-Campus Outing / Mission Trip (Requires VC Go-Ahead)</option>
                    </select>
                </div>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Activity Title <span class="text-rose-600">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Annual Praise Night, Leadership Retreat, All-Night Vigil" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
            </div>

            <!-- Purpose -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Purpose & Detailed Description <span class="text-rose-600">*</span>
                </label>
                <textarea name="purpose" rows="3" required placeholder="Explain the spiritual, fellowship, or community purpose of this activity..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">{{ old('purpose') }}</textarea>
            </div>

            <!-- Date and Times Row with 14-Day Calculator -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Proposed Event Date <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" name="event_date" id="event_date" min="{{ date('Y-m-d') }}" value="{{ old('event_date', date('Y-m-d', strtotime('+15 days'))) }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-white transition">
                    <div id="dateNoticeSpan" class="text-[10px] text-emerald-700 font-semibold mt-1">15 days notice (Compliant)</div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Start Time <span class="text-rose-600">*</span>
                    </label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '17:00') }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        End Time <span class="text-rose-600">*</span>
                    </label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '19:00') }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-white transition">
                </div>
            </div>

            <!-- Venue Selection Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Chapel / University Venue
                    </label>
                    <select name="venue_id" id="venue_id" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                        <option value="">-- Select Chapel Venue --</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}" data-capacity="{{ $venue->capacity }}" @selected((string) old('venue_id') === (string) $venue->id)>
                                {{ $venue->name }} (Cap: {{ $venue->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Other Venue / Off-Campus Location
                    </label>
                    <input type="text" name="venue_other" id="venue_other" value="{{ old('venue_other') }}" placeholder="e.g. Honde Valley, Student Union Hall" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                </div>
            </div>

            <!-- Attendance, Budget, Attachment Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Expected Attendance
                    </label>
                    <input type="number" name="expected_attendance" value="{{ old('expected_attendance', 50) }}" min="1" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Estimated Budget (USD)
                    </label>
                    <input type="number" step="0.01" name="budget" value="{{ old('budget', '0.00') }}" min="0" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Attachment (Draft Poster / Budget)
                    </label>
                    <input type="file" name="attachment" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 bg-white">
                </div>
            </div>

            <!-- Accessibility: Sign-Language Interpreter Option -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <div class="flex items-center space-x-2.5">
                    <input type="checkbox" id="sign_interpreter_required" name="sign_interpreter_required" value="1" onchange="document.getElementById('sign_req_name').classList.toggle('hidden', !this.checked)" class="rounded text-[#DC2626] focus:ring-[#DC2626]">
                    <label for="sign_interpreter_required" class="text-xs font-bold text-slate-800">Request Sign-Language Interpreter (Chapel Accessibility & Special Needs)</label>
                </div>
                <div id="sign_req_name" class="hidden pt-2">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Interpreter Contact / Notes (Optional)</label>
                    <input type="text" name="sign_interpreter_name" placeholder="e.g. Sister Chipo or AU Special Needs Ministry volunteer" class="w-full text-xs p-2.5 rounded-xl border border-slate-300 bg-white">
                </div>
            </div>

            <!-- Submission Footer -->
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-[11px] text-slate-500">
                    Your request will be logged in the permanent audit trail with your verified email signature.
                </span>
                <div class="flex items-center space-x-2.5 w-full sm:w-auto">
                    <a href="{{ route('requests.index') }}" class="w-1/2 sm:w-auto text-center px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition active:scale-95">
                        Cancel
                    </a>
                    <button type="submit" class="w-1/2 sm:w-auto px-6 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm active:scale-95 cursor-pointer">
                        Submit Proposal
                    </button>
                </div>
            </div>

        </form>
    </div>

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
                noticeDiv.className = 'p-4 sm:p-5 rounded-2xl border border-rose-300 bg-rose-50 text-rose-900 text-xs flex items-start space-x-3 transition-all duration-300 card-elevation';
                noticeSpan.className = 'text-[10px] text-rose-700 font-bold mt-1';
                noticeSpan.innerText = `${diffDays} days notice (< 14 days! Late Submission flag will be recorded per Chapel Manual §4.1)`;
            } else {
                noticeDiv.className = 'p-4 sm:p-5 rounded-2xl border border-blue-200 bg-blue-50/60 text-blue-900 text-xs flex items-start space-x-3 transition-all duration-300 card-elevation';
                noticeSpan.className = 'text-[10px] text-emerald-700 font-semibold mt-1';
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
                                btn.className = 'p-3 rounded-xl bg-slate-50 border border-slate-200 hover:border-amber-400 hover:bg-amber-50/50 flex items-center justify-between transition cursor-pointer';
                                btn.innerHTML = `
                                    <div class="text-xs text-slate-800">💡 <strong>${s.message}</strong></div>
                                    <button type="button" class="ml-2 px-3 py-1.5 rounded-lg bg-[#DC2626] hover:bg-[#B91C1C] text-white text-[10px] font-bold shrink-0 shadow-xs">
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
