<!-- CLUB EXECUTIVE / PRESIDENT FRIENDLY WORKSPACE -->
<div class="space-y-6">

    @php
        $myClub = $roleData['myClub'] ?? auth()->user()->club;
        $clubColor = $myClub->color_hex ?? '#DC2626';
        if ($clubColor === '#DC2626' || $clubColor === '#B91C1C' || $clubColor === '#B91C1C') {
            $clubColor = '#DC2626';
        }
    @endphp

    <!-- Fellowship Context Strip -->
    @if($myClub)
        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 px-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] font-extrabold text-sm flex items-center justify-center shrink-0 border border-red-100">
                    {{ substr($myClub->code ?? 'AU', 0, 3) }}
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-900">{{ $myClub->name }}</div>
                    <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-2.5 gap-y-1 mt-0.5">
                        <span>🗓️ {{ $myClub->meeting_schedule ?? 'Weekly Fellowship' }}</span>
                        <span class="text-slate-300">&bull;</span>
                        <span>📍 {{ $myClub->venue ?? 'Main Sanctuary' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                    Charter Active
                </span>
            </div>
        </div>
    @endif

    <!-- 4 Clean Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Registered Members -->
        <a href="{{ route('members.index', ['club_id' => $myClub->id ?? null]) }}" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:border-[#DC2626]/40 hover:shadow-xs transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-slate-500">Roster</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight group-hover:text-[#DC2626] transition-colors">
                {{ $roleData['my_members_count'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Registered Members</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Students enrolled in roll</p>
        </a>

        <!-- 2. Proposals Under Review -->
        <a href="{{ route('requests.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:border-slate-300 hover:shadow-xs transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-slate-500">Review</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['my_pending_proposals'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Pending Requests</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Awaiting Chaplaincy decision</p>
        </a>

        <!-- 3. Approved Activities -->
        <a href="{{ route('calendar.index') }}" class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-slate-600">Calendar</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['my_approved_proposals'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Approved Events</div>
            <p class="text-[11px] text-slate-500 mt-0.5">On official master calendar</p>
        </a>

        <!-- 4. Sound & Equipment -->
        <a href="{{ route('equipment.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:border-slate-300 hover:shadow-xs transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                </div>
                <span class="text-xs font-semibold text-slate-500">Logistics</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ count($roleData['myEquipment'] ?? []) }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Equipment Loans</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Microphones &amp; sound desks</p>
        </a>
    </div>

    <!-- Upcoming Meeting Highlight -->
    @if(isset($roleData['myNextEvent']) && $roleData['myNextEvent'])
        @php $nextEv = $roleData['myNextEvent']; @endphp
        <div class="p-6 rounded-2xl bg-red-50/70 border border-red-200/80 shadow-2xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-[#DC2626]">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626] animate-pulse"></span>
                    <span>Upcoming Fellowship Gathering</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900">{{ $nextEv->title }}</h3>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600">
                    <span>🗓️ <strong class="text-slate-900">{{ \Carbon\Carbon::parse($nextEv->event_date)->format('l, F j, Y') }}</strong></span>
                    <span>⏰ <strong class="text-slate-900">{{ $nextEv->start_time }} - {{ $nextEv->end_time }}</strong></span>
                    <span>📍 <strong class="text-slate-900">{{ $nextEv->venue->name ?? 'Chapel Hall' }}</strong></span>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <a href="{{ route('members.qr_kiosk', ['clubId' => $myClub->id ?? null]) }}" class="px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm active:scale-95">
                    Launch Check-in
                </a>
            </div>
        </div>
    @endif

    <!-- Event Proposals Section (Plain & Human) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Our Event Requests
                </h3>
                <p class="text-xs text-slate-500">Track approvals and clearance passes for upcoming dates</p>
            </div>
            <a href="{{ route('requests.create') }}" class="inline-flex items-center space-x-1 text-xs font-bold text-[#DC2626] hover:underline">
                <span>+ Request New Date</span>
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($roleData['myRequests'] ?? [] as $req)
                <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-bold text-slate-500">{{ $req->reference_no }}</span>
                            
                            @if($req->status === 'approved')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Approved &amp; Scheduled &check;
                                </span>
                            @elseif($req->status === 'needs_changes')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Action Needed: Update Request
                                </span>
                            @elseif($req->status === 'under_review')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Under Chaplaincy Review
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    Submitted
                                </span>
                            @endif
                        </div>

                        <h4 class="text-sm font-bold text-slate-900">
                            {{ $req->title }}
                        </h4>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                            <span>🗓️ <strong class="text-slate-700">{{ \Carbon\Carbon::parse($req->event_date)->format('M d, Y') }}</strong></span>
                            <span>⏰ <strong class="text-slate-700">{{ $req->start_time }} - {{ $req->end_time }}</strong></span>
                            <span>📍 <strong class="text-slate-700">{{ $req->venue->name ?? 'Chapel' }}</strong></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        @if($req->status === 'approved')
                            <a href="{{ route('requests.clearance_pass', $req->id) }}" target="_blank" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-semibold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Security Pass</span>
                            </a>
                        @endif

                        <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition">
                            <span>Details &rarr;</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-500">
                    <p>No proposals submitted yet for this semester.</p>
                    <a href="{{ route('requests.create') }}" class="inline-flex items-center space-x-1 text-[#DC2626] font-bold mt-2 hover:underline">
                        <span>Submit your first event proposal &rarr;</span>
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Members & Equipment Status Widgets -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Members List Shortcut -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Fellowship Members
                    </h3>
                    <p class="text-xs text-slate-500">Active student roll</p>
                </div>
                <a href="{{ route('members.index', ['club_id' => $myClub->id ?? null]) }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                    View All &rarr;
                </a>
            </div>

            <div class="space-y-2">
                @forelse($roleData['myMembers'] ?? [] as $m)
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-900">{{ $m->full_name }}</span>
                            <span class="text-slate-500 text-[11px] block">{{ $m->student_id }} &bull; {{ $m->role_in_club ?? 'Member' }}</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Active</span>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-slate-500">
                        <span>No members enrolled yet. Use the QR Kiosk to register students.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sound Equipment Requests Shortcut -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Sound &amp; Microphones
                    </h3>
                    <p class="text-xs text-slate-500">Chapel gear borrowed for meetings</p>
                </div>
                <a href="{{ route('equipment.index') }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                    Borrow Gear &rarr;
                </a>
            </div>

            <div class="space-y-2">
                @forelse($roleData['myEquipment'] ?? [] as $eq)
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <span class="font-bold text-slate-900">{{ $eq->item_name }} (x{{ $eq->quantity }})</span>
                            <span class="text-[10px] text-slate-500 block">Needed: {{ $eq->needed_date ? $eq->needed_date->format('M d, Y') : 'TBD' }}</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase {{ $eq->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $eq->status }}
                        </span>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-slate-500">
                        <span>No equipment requests submitted.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
