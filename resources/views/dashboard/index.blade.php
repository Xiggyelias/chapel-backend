@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $liturgical = \App\Services\LiturgicalCalendarService::getCurrentSeason();
    $greeting = now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = \Illuminate\Support\Str::of($user->name)->explode(' ')->first();

    $primary   = 'inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-semibold shadow-xs transition';
    $secondary = 'inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs transition';

    $actions = match (true) {
        $user->isChaplain()        => [[route('requests.index', ['status' => 'submitted']), 'Review proposals', true], [route('worship.create'), 'Plan a service', false]],
        $user->isChapelAssistant() => [[route('requests.index', ['status' => 'submitted']), 'Review proposals', true], [route('equipment.index'), 'Equipment desk', false], [route('worship.create'), 'Plan a service', false]],
        $user->isChapelSecretary() => [[route('clubs.register'), 'Charter a fellowship', true], [route('users.index'), 'Manage accounts', false], [route('handover.index'), 'Handovers', false]],
        $user->isClubLeader()      => [[route('requests.create'), 'New proposal', true], [route('reports.create'), 'File a report', false], [route('members.qr_kiosk'), 'Check-in kiosk', false]],
        default                    => [[route('calendar.index'), 'Master calendar', true], [route('calendar.public'), 'Public calendar', false]],
    };

    $focusText = match (true) {
        $user->isChaplain()        => 'Decisions, VC clearances and worship readiness awaiting your seal.',
        $user->isChapelAssistant() => "Today's operations: reviews, venues, equipment and liturgy.",
        $user->isChapelSecretary() => "Charters, accounts and the health of every fellowship's records.",
        $user->isClubLeader()      => 'Everything your fellowship owes the Chaplaincy, and what is coming up.',
        default                    => 'Approved chapel activities that affect campus life.',
    };

    $roleIcon = match (true) {
        $user->isChaplain()        => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        $user->isChapelAssistant() => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        $user->isChapelSecretary() => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        $user->isClubLeader()      => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        default                    => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    };
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- ═══ HERO GREETING CARD ═══ --}}
    <div class="relative overflow-hidden rounded-2xl bg-ink text-white p-6 sm:p-8">
        {{-- Background decorative pattern --}}
        <div class="absolute inset-0 opacity-[0.04]" aria-hidden="true" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

        {{-- Crimson-gold accent line --}}
        <div class="absolute bottom-0 left-0 right-0 h-0.5" style="background: linear-gradient(90deg, transparent, {{ $liturgical['hex'] }} 30%, var(--color-gold) 50%, {{ $liturgical['hex'] }} 70%, transparent)"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-start gap-4">
                {{-- Role icon --}}
                <div class="w-12 h-12 rounded-xl bg-brand/20 ring-1 ring-brand/30 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-brand" style="color: #E8798A" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $roleIcon }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-gold opacity-90">{{ now()->format('l, F j') }}</p>
                    <h1 class="text-2xl sm:text-3xl font-display font-semibold text-white leading-tight">
                        {{ $greeting }}, {{ $firstName }}
                    </h1>
                    <p class="text-sm text-white/50 mt-0.5">
                        {{ $user->getRoleDisplayName() }}@if($user->club) &nbsp;&bull;&nbsp; {{ $user->club->name }}@endif
                    </p>
                    <p class="text-xs text-white/40 mt-1 max-w-lg leading-relaxed">{{ $focusText }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @foreach($actions as $i => [$url, $label, $isPrimary])
                    <a href="{{ $url }}" class="{{ $isPrimary ? 'inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-semibold transition shadow-sm' : 'inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/8 hover:bg-white/14 text-white text-xs font-semibold border border-white/12 transition' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        {{-- Liturgical season strip --}}
        <div class="relative mt-5 pt-4 border-t border-white/8 flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full shrink-0 ring-2 ring-white/10" style="background: {{ $liturgical['hex'] }}"></span>
            <div class="text-xs text-white/50 leading-tight">
                <span class="font-semibold text-white/70">{{ $liturgical['name'] }}</span>
                <span class="mx-1.5">&bull;</span>
                <span class="italic">{{ $liturgical['verse'] }}</span>
            </div>
            <button type="button" onclick="showLiturgicalInfo()" class="ml-auto text-xs text-white/30 hover:text-white/70 font-medium transition shrink-0">Learn more</button>
        </div>
    </div>

    {{-- ═══ ROLE DASHBOARD ═══ --}}
    @if($user->isChaplain())
        @include('dashboard.partials.chaplain')
    @elseif($user->isChapelAssistant())
        @include('dashboard.partials.assistant')
    @elseif($user->isChapelSecretary())
        @include('dashboard.partials.secretary')
    @elseif($user->isClubLeader())
        @include('dashboard.partials.club')
    @else
        @include('dashboard.partials.campus_life')
    @endif

    {{-- ═══ CHAPEL DIRECTIVES ═══ --}}
    @if(isset($announcements) && $announcements->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-brand"></span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Chapel Directives &amp; Guidelines</h3>
                </div>
                <a href="{{ route('guidelines.index') }}" class="text-xs font-semibold text-brand hover:underline">Policy Manual &rarr;</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($announcements as $ann)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">{{ $ann->title }}</span>
                            @if($ann->is_guideline)
                                <span class="text-[9px] bg-red-100 text-brand px-1.5 py-0.5 rounded font-bold uppercase">Guideline</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $ann->message }}</p>
                        <div class="text-[10px] text-slate-400 font-medium">
                            Issued by {{ optional($ann->sender)->name ?? 'Chaplaincy Office' }} &bull; {{ optional($ann->published_date)->format('M d, Y') ?? '' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
