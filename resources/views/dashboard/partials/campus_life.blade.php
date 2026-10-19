<!-- CAMPUS LIFE & SRC OBSERVER DASHBOARD -->
<div class="space-y-6">

    <!-- Information Header -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 text-slate-800 text-xs flex items-center justify-between shadow-2xs">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span><strong>Campus Life &amp; SRC Observer Mode:</strong> Real-time read-only access to approved chapel events and master calendar feeds.</span>
        </div>
        <a href="{{ route('calendar.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-2xs transition">
            View Full Calendar
        </a>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Approved Events This Month -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400">Events This Month</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#DC2626] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['approved_month_count'] ?? 0 }}
            </div>
            <div class="text-[11px] text-[#DC2626] mt-1 font-semibold">Approved by Chaplaincy Office</div>
        </div>

        <!-- Active Fellowships -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400">Chartered Groups</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['total_active_clubs'] ?? 0 }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Active campus student ministries</div>
        </div>

        <!-- 1-Click iCal Feed -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400">Live Calendar Feed</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 font-mono">.ICS</span>
            </div>
            <div class="text-base font-bold text-slate-900 mt-1">
                Outlook &amp; Google Sync
            </div>
            <div class="mt-2">
                <a href="{{ route('calendar.feed_ics') }}" download class="inline-flex items-center space-x-1 text-xs font-bold text-[#DC2626] hover:underline">
                    <span>Subscribe to calendar feed &rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Today's Campus Events Radar -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Today's Approved Chapel Activities &bull; {{ \Carbon\Carbon::today()->format('l, F j, Y') }}
                </h3>
                <p class="text-xs text-slate-500">Events authorized to take place across university chapel premises today</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                {{ ($roleData['todaysCampusEvents'] ?? collect())->count() }} Active
            </span>
        </div>

        @if(isset($roleData['todaysCampusEvents']) && $roleData['todaysCampusEvents']->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($roleData['todaysCampusEvents'] as $ev)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-2">
                        <div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span class="font-mono font-bold text-[#DC2626]">{{ $ev->start_time }} - {{ $ev->end_time }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Approved</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 mt-1">{{ $ev->title }}</h4>
                            <div class="text-xs text-slate-600 mt-0.5 font-semibold">{{ $ev->club->name ?? 'Chapel' }}</div>
                        </div>
                        <div class="pt-2 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between">
                            <span>Venue: <strong>{{ $ev->venue->name ?? 'Chapel' }}</strong></span>
                            <span class="text-slate-400">{{ $ev->expected_attendance ?? 0 }} pax</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-6 text-center text-xs text-slate-500">
                <span>No chapel activities scheduled for today. Check upcoming events below.</span>
            </div>
        @endif
    </div>

    <!-- Upcoming University Events Schedule -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold font-cinzel text-slate-900 uppercase tracking-wider">
                    Upcoming University Chapel Calendar
                </h3>
                <p class="text-xs text-slate-500">Master schedule of authorized student ministry gatherings and convocations</p>
            </div>
            <a href="{{ route('calendar.index') }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                View Month Grid &rarr;
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($roleData['upcomingCampusEvents'] ?? [] as $ev)
                <div class="p-4 sm:p-5 hover:bg-slate-50/80 transition flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-mono text-xs font-bold text-[#DC2626]">{{ \Carbon\Carbon::parse($ev->event_date)->format('D, M d, Y') }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-xs font-bold text-slate-900">{{ $ev->club->name ?? 'Chapel' }}</span>
                        </div>

                        <h4 class="text-sm font-bold text-slate-900 font-cinzel">
                            {{ $ev->title }}
                        </h4>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                            <span>Time: <strong class="text-slate-700">{{ $ev->start_time }} - {{ $ev->end_time }}</strong></span>
                            <span>Venue: <strong class="text-slate-700">{{ $ev->venue->name ?? 'Main Sanctuary' }}</strong></span>
                            <span>Capacity: <strong class="text-slate-700">{{ $ev->expected_attendance ?? 0 }} attendees</strong></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <a href="{{ route('calendar.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            View on Calendar &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-500">
                    <span>No upcoming chapel events listed on the master schedule.</span>
                </div>
            @endforelse
        </div>
    </div>

</div>
