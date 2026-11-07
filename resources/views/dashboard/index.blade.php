@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $liturgical = \App\Services\LiturgicalCalendarService::getCurrentSeason();
    $greeting = now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening');
    // Greet by given name, skipping honorifics ("Rev. Supt. Eunice Marima" → "Eunice")
    $honorifics = ['rev', 'revd', 'supt', 'dr', 'prof', 'mr', 'mrs', 'ms', 'miss', 'pastor', 'bishop', 'fr', 'sr', 'br', 'hon', 'eng'];
    $firstName = collect(preg_split('/\s+/', trim($user->name)))
        ->first(fn ($part) => !in_array(strtolower(rtrim($part, '.')), $honorifics, true)) ?? $user->name;
    $firstName = rtrim($firstName, '.,');

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
@endphp

<div class="page-shell space-y-8">

    {{-- ═══ Greeting + the season we are in ═══ --}}
    <section class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_25rem] gap-6 xl:gap-10 items-end">
        <div class="min-w-0">
            <p class="eyebrow">{{ now()->format('l · j F Y') }}</p>
            <h1 class="font-display text-slate-900 text-[2.25rem] sm:text-[3rem] leading-[1.02] tracking-[-0.03em]"
                style="font-weight: 400 !important; --wght: 400; --soft: 100; font-variation-settings: 'SOFT' 100, 'opsz' 144;" data-ink>
                {{ $greeting }}, <em class="italic text-brand" style="--soft: 100">{{ $firstName }}.</em>
            </h1>
            <p class="mt-3 text-[13px] font-semibold text-slate-600">
                {{ $user->getRoleDisplayName() }}@if($user->club)<span class="text-slate-300 mx-2">/</span>{{ $user->club->name }}@endif
            </p>
            <p class="mt-1 text-sm text-slate-500 max-w-xl leading-relaxed">{{ $focusText }}</p>

            <div class="mt-6 flex flex-wrap items-center gap-2">
                @foreach($actions as [$url, $label, $isPrimary])
                    <a href="{{ $url }}" class="btn {{ $isPrimary ? 'btn-primary' : 'btn-secondary' }}">
                        {{ $label }}
                        @if($isPrimary)
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <button type="button" onclick="showLiturgicalInfo()" data-season-progress
                class="card card-ink grain text-left p-5 overflow-hidden group cursor-pointer focus-visible:outline-gold-light"
                style="--tint: {{ $liturgical['hex'] }}; background-image: radial-gradient(120% 90% at 100% 0%, color-mix(in srgb, var(--tint) 45%, transparent), transparent 60%);">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/50">The Christian year</p>
                    <p class="mt-1.5 font-display text-[1.4rem] leading-tight text-white">{{ $liturgical['name'] }}</p>
                    <p class="mt-0.5 text-[11.5px] text-white/50" data-season-meta>{{ $liturgical['color'] }}</p>
                </div>
                <span class="w-12 h-12 shrink-0 -mr-1 -mt-1 transition-transform duration-700 group-hover:rotate-12" data-dial="mini"></span>
            </div>
            <div class="mt-4 h-[3px] rounded-full bg-white/10 overflow-hidden">
                <span class="block h-full rounded-full transition-[width] duration-[1600ms] ease-[cubic-bezier(.16,1,.3,1)]" style="width: 0; background: linear-gradient(90deg, color-mix(in srgb, var(--tint) 70%, white), #F1D58F);" data-season-bar></span>
            </div>
            <p class="mt-4 font-display italic text-[13.5px] leading-snug text-white/75 line-clamp-2">{{ $liturgical['verse'] }}</p>
        </button>
    </section>

    {{-- ═══ Role dashboard ═══ --}}
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

    {{-- ═══ Chapel directives ═══ --}}
    @if(isset($announcements) && $announcements->count() > 0)
        <section class="card">
            <div class="card-header">
                <div>
                    <p class="card-kicker">From the Chaplaincy</p>
                    <h2 class="card-title mt-0.5">Directives &amp; guidelines</h2>
                </div>
                <a href="{{ route('guidelines.index') }}" class="card-link">Policy manual</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2">
                @foreach($announcements as $ann)
                    <article class="p-5 border-slate-100 {{ $loop->odd ? 'md:border-r' : '' }} {{ $loop->remaining > ($loop->odd ? 1 : 0) ? 'border-b' : '' }}">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-[13.5px] font-semibold text-slate-900 leading-snug">{{ $ann->title }}</h3>
                            @if($ann->is_guideline)
                                <span class="badge badge-brand shrink-0">Guideline</span>
                            @endif
                        </div>
                        <p class="mt-1.5 text-[13px] text-slate-600 leading-relaxed">{{ $ann->message }}</p>
                        <p class="mt-2.5 text-[11px] text-slate-400 font-medium">
                            {{ optional($ann->sender)->name ?? 'Chaplaincy Office' }} · {{ optional($ann->published_date)->format('j M Y') ?? '' }}
                        </p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
