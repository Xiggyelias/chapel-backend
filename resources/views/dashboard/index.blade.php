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

<div class="page-shell space-y-6">

    {{-- ═══ Welcome + the season we are in ═══ --}}
    <section class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_23rem] gap-4 sm:gap-5">
        <div class="card relative overflow-hidden p-6 sm:p-7">
            <div class="absolute inset-y-0 right-0 w-1/2 pointer-events-none hidden md:block" style="background: radial-gradient(70% 90% at 100% 0%, var(--color-brand-soft), transparent 70%);" aria-hidden="true"></div>
            <svg class="absolute -right-6 -bottom-10 w-48 h-48 text-brand/[0.05] pointer-events-none hidden md:block" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10.5 2h3v6h6v3h-6v11h-3V11h-6V8h6z"/></svg>
            <div class="relative">
                <p class="text-[13px] font-semibold text-slate-500">{{ now()->format('l, j F Y') }}</p>
                <h1 class="mt-1.5 font-display text-slate-900 text-[2rem] sm:text-[2.5rem] leading-[1.08] tracking-[-0.025em]"
                    style="--wght: 500; --soft: 60;" data-ink>
                    {{ $greeting }}, <span class="text-brand">{{ $firstName }}</span>
                </h1>
                <p class="mt-2 flex flex-wrap items-center gap-2 text-[13.5px] font-semibold text-slate-700">
                    <span class="badge badge-brand">{{ $user->getRoleDisplayName() }}</span>
                    @if($user->club)<span class="badge">{{ $user->club->name }}</span>@endif
                </p>
                <p class="mt-3 text-[15px] text-slate-600 max-w-xl leading-relaxed">{{ $focusText }}</p>

                <div class="mt-5 flex flex-wrap items-center gap-2">
                    @foreach($actions as [$url, $label, $isPrimary])
                        <a href="{{ $url }}" class="btn {{ $isPrimary ? 'btn-primary' : 'btn-secondary' }}">
                            {{ $label }}
                            @if($isPrimary)<x-icon name="arrow-right" />@endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="button" onclick="showLiturgicalInfo()" data-season-progress
                class="card text-left overflow-hidden group cursor-pointer flex flex-col"
                style="--tint: {{ $liturgical['hex'] }};">
            <span class="block h-1.5 shrink-0" style="background: var(--tint)" aria-hidden="true"></span>
            <span class="p-5 flex-1 flex flex-col">
                <span class="flex items-start justify-between gap-4">
                    <span class="min-w-0">
                        <span class="block text-[12.5px] font-semibold text-slate-500">The Christian year</span>
                        <span class="mt-1 block font-display text-[1.45rem] leading-tight text-slate-900">{{ $liturgical['name'] }}</span>
                        <span class="mt-1 block text-[12.5px] text-slate-500" data-season-meta>{{ $liturgical['color'] }}</span>
                    </span>
                    <span class="w-12 h-12 shrink-0 transition-transform duration-700 group-hover:rotate-12" data-dial="mini" data-tone="paper"></span>
                </span>
                <span class="mt-4 block h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <span class="block h-full rounded-full transition-[width] duration-[1600ms] ease-[cubic-bezier(.16,1,.3,1)]" style="width: 0; background: var(--tint);" data-season-bar></span>
                </span>
                <span class="mt-4 block font-display italic text-[14.5px] leading-snug text-slate-600 line-clamp-3">{{ $liturgical['verse'] }}</span>
                <span class="mt-auto pt-3 inline-flex items-center gap-1 text-[12.5px] font-semibold text-brand">About this season <x-icon name="chevron-right" class="w-3.5 h-3.5" /></span>
            </span>
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
                    <h2 class="card-title">Directives &amp; guidelines</h2>
                </div>
                <a href="{{ route('guidelines.index') }}" class="card-link">Policy manual</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2">
                @foreach($announcements as $ann)
                    <article class="p-5 border-slate-100 {{ $loop->odd ? 'md:border-r' : '' }} {{ $loop->remaining > ($loop->odd ? 1 : 0) ? 'border-b' : '' }}">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-[14.5px] font-semibold text-slate-900 leading-snug">{{ $ann->title }}</h3>
                            @if($ann->is_guideline)
                                <span class="badge badge-brand shrink-0">Guideline</span>
                            @endif
                        </div>
                        <p class="mt-1.5 text-[14px] text-slate-600 leading-relaxed">{{ $ann->message }}</p>
                        <p class="mt-2.5 text-[12px] text-slate-500 font-medium">
                            {{ optional($ann->sender)->name ?? 'Chaplaincy Office' }} · {{ optional($ann->published_date)->format('j M Y') ?? '' }}
                        </p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
