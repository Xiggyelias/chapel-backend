@extends('layouts.app')

@section('title', 'Requests & Approvals')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] mb-1">
                <span>Clearance & Approvals</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Event Proposals & Requests
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Proposals for campus fellowship gatherings, room bookings, equipment, and outings
            </p>
        </div>

        <a href="{{ route('requests.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold transition shadow-xs self-start sm:self-auto active:scale-95">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Submit Proposal</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('requests.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- Search -->
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or Ref No..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Status</label>
                <select name="status" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                    <option value="">All Statuses</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="needs_changes" {{ request('status') === 'needs_changes' ? 'selected' : '' }}>Needs Changes</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>Declined</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Category</label>
                <select name="category" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                    <option value="">All Categories</option>
                    <option value="event" {{ request('category') === 'event' ? 'selected' : '' }}>Event</option>
                    <option value="venue" {{ request('category') === 'venue' ? 'selected' : '' }}>Venue</option>
                    <option value="poster" {{ request('category') === 'poster' ? 'selected' : '' }}>Poster Approval</option>
                    <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="outing" {{ request('category') === 'outing' ? 'selected' : '' }}>Outing / Mission</option>
                </select>
            </div>

            <!-- Club Filter (Office Only) -->
            @if(auth()->user()->isOffice())
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Club / Fellowship</label>
                    <select name="club_id" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-slate-50/50 focus:bg-white transition">
                        <option value="">All Clubs</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>{{ $club->name }} ({{ $club->code }})</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="flex items-end">
                    <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200 w-full flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="truncate">Filtered: <strong>{{ auth()->user()->club?->name ?? 'My Club' }}</strong></span>
                    </div>
                </div>
            @endif

            <!-- Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2.5 px-3.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold transition shadow-sm active:scale-95">
                    Filter
                </button>
                <a href="{{ route('requests.index') }}" class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition text-center active:scale-95">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Requests Table / List -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-600">
                        <th class="p-4">Ref No & Club</th>
                        <th class="p-4">Title & Category</th>
                        <th class="p-4">Activity Schedule</th>
                        <th class="p-4">Venue & Cap</th>
                        <th class="p-4">14-Day Notice</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-mono font-bold text-slate-900">{{ $req->reference_no }}</div>
                                <div class="mt-1">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded text-white" style="background-color: {{ $req->club?->color_hex ?? '#DC2626' }}">
                                        {{ $req->club?->code }}
                                    </span>
                                </div>
                            </td>

                            <td class="p-4">
                                <a href="{{ route('requests.show', $req->id) }}" class="font-bold text-slate-900 hover:text-[#DC2626] hover:underline transition">
                                    {{ $req->title }}
                                </a>
                                <div class="text-[11px] text-slate-500 uppercase tracking-wide mt-1 flex items-center space-x-1.5">
                                    <span>{{ ucfirst($req->category) }}</span>
                                    @if($req->vc_go_ahead_needed)
                                        <span>&bull;</span>
                                        <span class="text-purple-700 font-bold bg-purple-50 px-1.5 py-0.2 rounded border border-purple-200">VC Clearance</span>
                                    @endif
                                </div>
                            </td>

                            <td class="p-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-900">{{ $req->event_date->format('M d, Y') }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ substr($req->start_time, 0, 5) }} - {{ substr($req->end_time, 0, 5) }}</div>
                            </td>

                            <td class="p-4">
                                <div class="text-slate-900 font-medium">
                                    {{ $req->venue ? $req->venue?->name : ($req->venue_other ?: 'To be announced') }}
                                </div>
                                @if($req->expected_attendance)
                                    <div class="text-[11px] text-slate-500">Exp. Attendance: {{ $req->expected_attendance }}</div>
                                @endif
                            </td>

                            <!-- 14-Day Rule Compliance -->
                            <td class="p-4 whitespace-nowrap">
                                @if($req->is_late_submission)
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <span>Late (&lt;14d)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span>On Time (&ge;14d)</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="p-4 whitespace-nowrap">
                                @if($req->status === 'approved')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center space-x-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        <span>Approved</span>
                                    </span>
                                @elseif($req->status === 'under_review')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 flex items-center space-x-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                                        <span>Under Review</span>
                                    </span>
                                @elseif($req->status === 'needs_changes')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 flex items-center space-x-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        <span>Needs Changes</span>
                                    </span>
                                @elseif($req->status === 'declined')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 flex items-center space-x-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        <span>Declined</span>
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 flex items-center space-x-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                        <span>Submitted</span>
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-right whitespace-nowrap">
                                <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-[#DC2626] hover:text-white text-slate-700 text-xs font-bold transition active:scale-95">
                                    <span>Review</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <svg class="w-8 h-8 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-medium text-slate-700">No requests found</p>
                                    <p class="text-xs text-slate-400">Click "Submit Proposal" to initiate a new activity proposal.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
