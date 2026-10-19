<!-- CHAPLAIN EXECUTIVE & LITURGICAL DASHBOARD -->
<div class="space-y-6">

    <!-- Priority Executive Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- VC Concurrence Clearances -->
        <a href="{{ route('requests.index', ['filter' => 'vc']) }}" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:border-[#DC2626]/40 hover:shadow-xs transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                @if(($roleData['pending_vc_clearance'] ?? 0) > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-[#DC2626]">
                        Action Required
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                        Up to date
                    </span>
                @endif
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight group-hover:text-[#DC2626] transition-colors">
                {{ $roleData['pending_vc_clearance'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">VC Concurrence Needed</div>
            <p class="text-[11px] text-slate-500 mt-0.5">VIP &amp; external speaker clearances</p>
        </a>

        <!-- High-Impact Convocations -->
        <a href="{{ route('requests.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:border-slate-300 hover:shadow-xs transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-[10px] font-medium text-slate-500">Major Events</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['high_impact_reviews'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">High-Impact Assemblies</div>
            <p class="text-[11px] text-slate-500 mt-0.5">300+ attendee revivals &amp; services</p>
        </a>

        <!-- Handover Certificates -->
        <a href="{{ route('handover.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:border-slate-300 hover:shadow-xs transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-[10px] font-medium text-slate-500">Governance</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['pending_handovers_count'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Handover Attestations</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Certificates awaiting signature</p>
        </a>

        <!-- Policy Waivers (14-Day Rule) -->
        <a href="{{ route('requests.index', ['filter' => 'late']) }}" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:border-red-300 hover:shadow-xs transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                @if(($roleData['late_waiver_count'] ?? 0) > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                        Late Notice
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                        None
                    </span>
                @endif
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['late_waiver_count'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Policy Waiver Requests</div>
            <p class="text-[11px] text-slate-500 mt-0.5">14-Day rule exemption review</p>
        </a>
    </div>

    <!-- Senior Liturgical Oversight: Sunday Convocational Service -->
    @if(isset($roleData['next_sunday_service']) && $roleData['next_sunday_service'])
        @php $sunday = $roleData['next_sunday_service']; @endphp
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div class="space-y-1.5">
                    <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626]">
                        <span>Sunday Convocational Liturgy</span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-slate-600 font-medium">{{ \Carbon\Carbon::parse($sunday->service_date)->format('l, F j, Y') }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                        {{ $sunday->theme ?? 'Walking in Divine Wisdom and Integrity' }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 pt-1">
                        <span>Preacher: <strong class="text-slate-900">{{ $sunday->preacher_name ?? 'Rev. Dr. Maudy Muchanyereyi' }}</strong></span>
                        <span class="text-slate-300">&bull;</span>
                        <span>Scripture: <strong class="font-mono text-slate-900">{{ $sunday->scripture_reading ?? 'Proverbs 3:1-12' }}</strong></span>
                        @if($sunday->sign_interpreter_name)
                            <span class="text-slate-300">&bull;</span>
                            <span class="inline-flex items-center space-x-1 text-emerald-700 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Interpreter: {{ $sunday->sign_interpreter_name }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('worship.show', $sunday->id) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Order of Service</span>
                    </a>
                    <a href="{{ route('worship.bulletin', $sunday->id) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold transition shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print Bulletin</span>
                    </a>
                    <a href="{{ route('worship.slides', $sunday->id) }}" target="_blank" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Projector Slides</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Executive Requests Awaiting Chaplaincy Action -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">
                    Executive Review Queue &bull; VC Clearances &amp; High-Impact Proposals
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Proposals requiring the University Chaplain's signature or Vice-Chancellor endorsement</p>
            </div>
            <a href="{{ route('requests.index') }}" class="text-xs font-semibold text-[#DC2626] hover:underline">
                View All Proposals &rarr;
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($roleData['executiveRequests'] ?? [] as $req)
                <div class="p-5 hover:bg-slate-50/80 transition flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-bold text-slate-500">{{ $req->reference_no }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                            <span class="text-xs font-bold text-slate-900">{{ $req->club->name ?? 'Chaplaincy' }}</span>
                            
                            @if($req->vc_go_ahead_needed)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    VC Clearance Required
                                </span>
                            @endif

                            @if($req->is_late_submission)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Late Notice Waiver
                                </span>
                            @endif

                            @if($req->expected_attendance >= 300)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $req->expected_attendance }}+ Attendees
                                </span>
                            @endif
                        </div>

                        <h4 class="text-sm font-bold text-slate-900">
                            {{ $req->title }}
                        </h4>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                            <span>Date: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($req->event_date)->format('M d, Y') }}</strong></span>
                            <span>Time: <strong class="text-slate-700">{{ $req->start_time }} - {{ $req->end_time }}</strong></span>
                            <span>Venue: <strong class="text-slate-700">{{ $req->venue->name ?? 'TBA' }}</strong></span>
                            <span>Submitted: <strong class="text-slate-700">{{ $req->created_at->diffForHumans() }}</strong></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold transition shadow-xs active:scale-95">
                            <span>Review &amp; Endorse</span>
                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-500">
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>No pending high-level VC clearances or late waiver requests. All executive items cleared.</span>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Leadership Handover Certificates Awaiting Chaplain's Signature -->
    @if(isset($roleData['pendingHandovers']) && $roleData['pendingHandovers']->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Executive Handover Attestations &bull; Official Chaplaincy Seal
                    </h3>
                    <p class="text-xs text-slate-500">Verify club leadership transitions and issue official University Certificates of Stewardship</p>
                </div>
                <a href="{{ route('handover.index') }}" class="text-xs font-semibold text-[#DC2626] hover:underline">
                    View Handover Registry &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($roleData['pendingHandovers'] as $handover)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-900">{{ $handover->club->name ?? 'Fellowship' }}</span>
                            <div class="text-[11px] text-slate-600">
                                <span>Outgoing: <strong>{{ $handover->outgoing_president_name }}</strong></span>
                                <span class="mx-1">&rarr;</span>
                                <span>Incoming: <strong>{{ $handover->incoming_president_name }}</strong></span>
                            </div>
                            <div class="text-[10px] text-slate-400">Academic Year: {{ $handover->academic_year }}</div>
                        </div>

                        <a href="{{ route('handover.certificate', $handover->id) }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold transition shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Sign &amp; Attest</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
