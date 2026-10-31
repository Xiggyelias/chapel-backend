@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    @php
        $primary = 'inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs transition';
        $secondary = 'inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs transition';
        $actions = match (true) {
            $user->isChaplain() => [[route('requests.index', ['status' => 'submitted']), 'Review proposals'], [route('worship.create'), 'Plan a service']],
            $user->isChapelAssistant() => [[route('requests.index', ['status' => 'submitted']), 'Review proposals'], [route('equipment.index'), 'Equipment desk'], [route('worship.create'), 'Plan a service']],
            $user->isChapelSecretary() => [[route('clubs.register'), 'Charter a fellowship'], [route('users.index'), 'Manage accounts'], [route('handover.index'), 'Handovers']],
            $user->isClubLeader() => [[route('requests.create'), 'New proposal'], [route('reports.create'), 'File a report']],
            default => [[route('calendar.index'), 'Master calendar']],
        };
        $focus = match (true) {
            $user->isChaplain() => 'Decisions, VC clearances and worship readiness that need your seal.',
            $user->isChapelAssistant() => "Today's operations: reviews, venues, equipment and liturgy preparation.",
            $user->isChapelSecretary() => "Charters, accounts and the health of every fellowship's records.",
            $user->isClubLeader() => 'Everything your fellowship owes the Chaplaincy, and what is coming up.',
            default => 'Approved chapel activities that affect campus life.',
        };
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-[#DC2626]">{{ now()->format('l, F j') }}</p>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                {{ now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening') }}, {{ \Illuminate\Support\Str::of($user->name)->explode(' ')->last() }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $user->getRoleDisplayName() }}@if($user->club) &bull; {{ $user->club?->name }}@endif &mdash; {{ $focus }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @foreach($actions as $i => [$url, $label])
                <a href="{{ $url }}" class="{{ $i === 0 ? $primary : $secondary }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

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
