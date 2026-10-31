@extends('layouts.app')

@section('title', 'Chaplaincy Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    @if($user->mustChangePassword())
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
            <div class="flex items-start sm:items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-amber-950">Security Action Required: Update Initial Default Password</h3>
                    <p class="text-[11px] text-amber-800 mt-0.5">You are signed in with a temporary password issued by the Chaplaincy Office. Please set your own private password.</p>
                </div>
            </div>
            <a href="{{ route('profile.index') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-amber-900 hover:bg-black text-white text-xs font-semibold shrink-0 transition active:scale-95 shadow-xs">
                <span>Change Password</span>
                <span>&rarr;</span>
            </a>
        </div>
    @endif

    <!-- Clean Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Welcome, {{ $user->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $user->getRoleDisplayName() }} @if($user->club) &bull; {{ $user->club->name }} @endif &bull; Kwang-Lim Chapel
            </p>
        </div>

        <!-- Quick Primary Actions (Clean, minimal, 1-2 buttons max) -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            @if($user->isClubExecutive() || $user->isClubPresident())
                <a href="{{ route('requests.create') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>New Proposal</span>
                </a>
                <a href="{{ route('members.qr_kiosk', ['clubId' => $user->club_id]) }}" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs transition active:scale-95">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>QR Kiosk</span>
                </a>
            @elseif($user->isChaplain())
                <a href="{{ route('requests.index') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Review Queue</span>
                </a>
                <a href="{{ route('worship.index') }}" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs transition active:scale-95">
                    <span>Sunday Liturgy</span>
                </a>
            @elseif($user->isChapelSecretary())
                <a href="{{ route('clubs.register') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Charter Fellowship</span>
                </a>
                <a href="{{ route('users.index') }}" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs transition active:scale-95">
                    <span>Staff & Users</span>
                </a>
            @elseif($user->isChapelAssistant())
                <a href="{{ route('calendar.index') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Venue Radar</span>
                </a>
                <a href="{{ route('equipment.index') }}" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs transition active:scale-95">
                    <span>Sound and Equipment</span>
                </a>
            @else
                <a href="{{ route('calendar.index') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Master Calendar</span>
                </a>
            @endif
        </div>
    </div>

    <!-- ROLE-TAILORED POWER STRUCTURE VIEWPORT -->
    @if($user->isChaplain())
        @include('dashboard.partials.chaplain')
    @elseif($user->isChapelAssistant())
        @include('dashboard.partials.assistant')
    @elseif($user->isChapelSecretary())
        @include('dashboard.partials.secretary')
    @elseif($user->isClubExecutive() || $user->isClubPresident())
        @include('dashboard.partials.club')
    @else
        @include('dashboard.partials.campus_life')
    @endif

    <!-- Chapel Directives & Guidelines Footer -->
    @if(isset($announcements) && $announcements->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                        Chapel Directives &amp; Guidelines
                    </h3>
                </div>
                <a href="{{ route('guidelines.index') }}" class="text-xs font-semibold text-[#DC2626] hover:underline">
                    Policy Manual &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($announcements as $ann)
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">{{ $ann->title }}</span>
                            @if($ann->is_guideline)
                                <span class="text-[9px] bg-red-100 text-[#DC2626] px-1.5 py-0.5 rounded font-bold uppercase">Guideline</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $ann->message }}</p>
                        <div class="text-[10px] text-slate-400 font-medium pt-1">
                            Issued by {{ optional($ann->sender)->name ?? 'Chaplaincy Office' }} &bull; {{ optional($ann->published_date)->format('M d, Y') ?? '' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
