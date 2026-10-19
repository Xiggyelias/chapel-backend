<!-- CHAPEL ASSISTANT OPERATIONAL & LITURGY DASHBOARD -->
<div class="space-y-6">

    <!-- Priority Operational Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Operational Reviews -->
        <a href="{{ route('requests.index') }}" class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                @if(($roleData['operational_pending'] ?? 0) > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-[#DC2626]">
                        Action Needed
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                        Clear
                    </span>
                @endif
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight group-hover:text-[#DC2626] transition-colors">
                {{ $roleData['operational_pending'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Operational Reviews</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Proposals awaiting logistics triage</p>
        </a>

        <!-- Today's Chapel Bookings -->
        <a href="{{ route('calendar.index') }}" class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] font-medium text-slate-600">Today</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight group-hover:text-slate-800 transition-colors">
                {{ $roleData['todays_events_count'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Today's Bookings</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Live activities scheduled today</p>
        </a>

        <!-- Equipment Loans -->
        <a href="{{ route('equipment.index') }}" class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                </div>
                <span class="text-[10px] font-medium text-slate-500">Audio/Visual</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight group-hover:text-slate-800 transition-colors">
                {{ $roleData['pending_equipment_count'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Equipment Requests</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Sound &amp; microphone loans pending</p>
        </a>

        <!-- 14-Day Rule Alerts -->
        <a href="{{ route('requests.index', ['filter' => 'late']) }}" class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                @if(($roleData['late_submissions_count'] ?? 0) > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-[#DC2626]">
                        Late
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                        Zero Late
                    </span>
                @endif
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['late_submissions_count'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">14-Day Rule Alerts</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Submitted &lt;14 days in advance</p>
        </a>
    </div>

    <!-- Today's Live Venue Schedule Radar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Today's Live Venue Operations &bull; {{ \Carbon\Carbon::today()->format('l, F j, Y') }}
                </h3>
                <p class="text-xs text-slate-500">Real-time occupancy and sound management across chapel facilities</p>
            </div>
            <a href="{{ route('calendar.index') }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                Master Calendar &rarr;
            </a>
        </div>

        @if(isset($roleData['todaysSchedule']) && $roleData['todaysSchedule']->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($roleData['todaysSchedule'] as $ev)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-2">
                        <div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span class="font-mono font-bold text-[#DC2626]">{{ $ev->start_time }} - {{ $ev->end_time }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Confirmed</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 mt-1">{{ $ev->title }}</h4>
                            <div class="text-xs text-slate-600 mt-0.5 font-semibold">{{ $ev->club->name ?? 'Chapel' }}</div>
                        </div>
                        <div class="pt-2 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between">
                            <span>Venue: <strong>{{ $ev->venue->name ?? 'Main Sanctuary' }}</strong></span>
                            <span class="text-slate-400">{{ $ev->expected_attendance ?? 0 }} pax</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-6 text-center text-xs text-slate-500">
                <span>No active events booked on the chapel calendar for today. Facilities are available for quiet prayer and scheduled rehearsals.</span>
            </div>
        @endif
    </div>

    <!-- Operational Proposals Queue (Logistics & Venue Clearance) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Logistics &amp; Scheduling Review Queue
                </h3>
                <p class="text-xs text-slate-500">Review time-slots, sound requirements, and check for calendar collisions</p>
            </div>
            <a href="{{ route('requests.index') }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                View All &rarr;
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($roleData['operationalRequests'] ?? [] as $req)
                <div class="p-5 hover:bg-slate-50/80 transition flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-bold text-slate-500">{{ $req->reference_no }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                            <span class="text-xs font-bold text-slate-900">{{ $req->club->name ?? 'Fellowship' }}</span>
                            
                            @if($req->is_late_submission)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase">
                                    &lt;14 Days Late
                                </span>
                            @endif

                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                {{ ucfirst($req->category) }}
                            </span>
                        </div>

                        <h4 class="text-sm font-bold text-slate-900">
                            {{ $req->title }}
                        </h4>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                            <span>Date: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($req->event_date)->format('M d, Y') }}</strong></span>
                            <span>Time: <strong class="text-slate-700">{{ $req->start_time }} - {{ $req->end_time }}</strong></span>
                            <span>Venue: <strong class="text-slate-700">{{ $req->venue->name ?? 'TBA' }}</strong></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold transition shadow-xs active:scale-95">
                            <span>Inspect &amp; Triage</span>
                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-500">
                    <span>No proposals awaiting operational review.</span>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Liturgy Milestones & Equipment Loan Pipeline -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Weekly Liturgy Milestones -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Weekly Liturgy Milestones
                    </h3>
                    <p class="text-xs text-slate-500">Order of service deadlines, preachers &amp; slide decks</p>
                </div>
                <a href="{{ route('worship.index') }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                    Worship Center &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($roleData['upcomingServices'] ?? [] as $serv)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-[#DC2626] font-bold uppercase">{{ $serv->service_type }}</span>
                            <span class="text-slate-600 font-mono">{{ \Carbon\Carbon::parse($serv->service_date)->format('M d, Y') }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $serv->theme ?? 'Convocational Service' }}</h4>
                        <div class="text-xs text-slate-600">Preacher: <strong>{{ $serv->preacher_name ?? 'Chaplaincy Team' }}</strong></div>
                        <div class="flex items-center gap-2 pt-2">
                            <a href="{{ route('worship.edit', $serv->id) }}" class="text-[11px] font-bold text-[#DC2626] hover:underline">Edit Service Sheet &rarr;</a>
                            <span class="text-slate-300">&bull;</span>
                            <a href="{{ route('worship.slides', $serv->id) }}" target="_blank" class="text-[11px] font-bold text-slate-700 hover:underline">Launch Slides &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-slate-500">
                        <span>No upcoming service sheets registered for this week.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Equipment Loan Requests -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Sound &amp; PA Equipment Loans
                    </h3>
                    <p class="text-xs text-slate-500">Microphones, speakers, projectors &amp; cables</p>
                </div>
                <a href="{{ route('equipment.index') }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                    Equipment Manager &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($roleData['pendingEquipment'] ?? [] as $eq)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-900">{{ $eq->club->name ?? 'Fellowship' }}</span>
                            <div class="text-xs text-slate-700 font-medium">{{ $eq->equipment_list }}</div>
                            <div class="text-[10px] text-slate-500">Required: {{ $eq->event_date }}</div>
                        </div>
                        <a href="{{ route('equipment.index') }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-[#DC2626] text-white text-[11px] font-bold uppercase tracking-wider hover:bg-[#B91C1C] transition">
                            Triage
                        </a>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-slate-500">
                        <span>All sound &amp; equipment loan requests are cleared.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
