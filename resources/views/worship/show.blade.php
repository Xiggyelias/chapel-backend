@extends('layouts.app')

@section('title', 'Service Sheet: ' . $sheet->service_date->format('M d, Y'))

@section('content')
@php
    $hymnsText = $sheet->hymns ?: 'UMH 57: O For a Thousand Tongues to Sing; UMH 368: My Hope Is Built; Shona Hymn 11';
    $hymnList = array_values(array_filter(array_map('trim', explode(';', $hymnsText))));
@endphp
<div class="page-shell max-w-5xl space-y-6">

    <x-page-header class="md:flex-col md:items-start" :eyebrow="'Worship Planning · ' . $sheet->service_type" :title="$sheet->service_date->format('l, F j, Y')"
                   :description="$sheet->service_type . ' · Kwang-Lim Chapel Sanctuary'">
        <a href="{{ route('worship.index') }}" class="btn btn-ghost">
            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12l7.5-7.5M3 12h18"/></svg>
            Worship Planning
        </a>
        <button type="button" onclick="dispatchWorshipDutyWhatsApp()" class="btn btn-secondary">
            <svg class="text-emerald-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.86-1.537c1.7 1.011 3.684 1.595 5.803 1.595 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/></svg>
            WhatsApp Duty Call
        </button>
        <a href="{{ route('worship.bulletin', $sheet->id) }}" target="_blank" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Bulletin (PDF)
        </a>
        @if(auth()->user()->isOffice())
            <a href="{{ route('worship.edit', $sheet->id) }}" class="btn btn-ink">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Sheet
            </a>
        @endif
        <a href="{{ route('worship.slides', $sheet->id) }}" target="_blank" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Projector Slides <span class="kbd ml-0.5">F</span>
        </a>
    </x-page-header>

    {{-- Worship Service Policy 4.3: milestone compliance --}}
    <section class="card">
        <div class="card-header flex-wrap">
            <div class="min-w-0">
                <p class="card-kicker">Worship Service Policy (§4.3)</p>
                <h2 class="card-title mt-0.5">Compliance Tracker</h2>
            </div>
            <span class="text-[12px] text-slate-500 tabular-nums">Service: <strong class="font-semibold text-slate-700">{{ $sheet->service_date->toDateString() }}</strong></span>
        </div>
        <ul class="card-body grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
            @foreach($milestones as $key => $m)
                <li class="flex items-start gap-3">
                    @if($m['satisfied'])
                        <span class="mt-0.5 w-6 h-6 rounded-full grid place-items-center shrink-0 bg-emerald-50 text-emerald-600 ring-1 ring-emerald-600/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="sr-only">Satisfied:</span>
                        </span>
                    @else
                        <span class="mt-0.5 w-6 h-6 rounded-full grid place-items-center shrink-0 bg-amber-50 text-amber-600 ring-1 ring-amber-600/25">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v6m0 4h.01"/></svg>
                            <span class="sr-only">Outstanding:</span>
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="text-[13px] font-semibold {{ $m['satisfied'] ? 'text-slate-900' : 'text-amber-900' }}">{{ $m['label'] }}</p>
                        <p class="text-[11.5px] text-slate-500 mt-0.5 tabular-nums">Target Deadline: {{ $m['deadline']->format('D, M d') }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- The service sheet itself --}}
    <article class="card overflow-hidden">
        <header class="relative grain bg-ink text-white px-6 py-8 sm:px-10 sm:py-10 text-center overflow-hidden">
            <div aria-hidden="true" class="absolute inset-0 pointer-events-none" style="background: radial-gradient(70% 90% at 50% 0%, rgb(165 28 48 / 0.28), transparent 70%);"></div>
            <div class="relative">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.24em] text-gold-light">Liturgy & Order of Worship</p>
                <h2 class="mt-3 font-display text-[1.75rem] sm:text-[2.375rem] leading-[1.1] text-white text-balance">&ldquo;{{ $sheet->theme ?: 'Worship Service' }}&rdquo;</h2>
                <div class="rule-ornament text-[9px] max-w-[14rem] mx-auto my-4" aria-hidden="true">&#9670;</div>
                <p class="text-[13px] text-white/60">Scripture Reading: <strong class="font-display font-normal italic text-[15px] text-white">{{ $sheet->scripture ?: 'To be announced' }}</strong></p>
            </div>
        </header>

        <div class="divide-y divide-slate-100">
            {{-- Ministers & liturgical team --}}
            <section class="px-6 py-6 sm:px-10">
                <h3 class="card-kicker mb-4">Ministers of Worship</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-5">
                    @foreach([
                        ['Preacher', $sheet->preacher ?: 'University Chaplain'],
                        ['Liturgist', $sheet->liturgist ?: 'Assistant Chaplain'],
                        ['Bible Reader', $sheet->bible_reader ?: 'Student Representative'],
                        ['Choir', $sheet->choir ?: 'Praise the Lord Choir'],
                        ['Praise & Worship Team', $sheet->praise_worship_team ?: 'Chapel Worship Band'],
                    ] as [$role, $name])
                        <div class="border-l-2 border-gold-light/60 pl-3">
                            <dt class="text-[11px] font-semibold uppercase tracking-[0.1em] text-slate-400">{{ $role }}</dt>
                            <dd class="mt-0.5 text-[14px] font-semibold text-slate-900">{{ $name }}</dd>
                        </div>
                    @endforeach
                    <div class="border-l-2 border-gold-light/60 pl-3">
                        <dt class="text-[11px] font-semibold uppercase tracking-[0.1em] text-slate-400">Media & Sound Contact</dt>
                        <dd class="mt-0.5 text-[14px] font-semibold text-slate-900 break-words">{{ $sheet->media_contact ?: 'media@africau.edu' }}</dd>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3 border-l-2 {{ $sheet->sign_interpreter_required ? 'border-emerald-500/60' : 'border-slate-200' }} pl-3">
                        <dt class="text-[11px] font-semibold uppercase tracking-[0.1em] text-slate-400">Sign-Language Interpreter</dt>
                        @if($sheet->sign_interpreter_required)
                            <dd class="mt-1"><span class="badge badge-emerald badge-dot">{{ $sheet->sign_interpreter_name ?: 'Chaplaincy Interpreter Confirmed' }}</span></dd>
                        @else
                            <dd class="mt-0.5 text-[13px] text-slate-400 italic">Not requested</dd>
                        @endif
                    </div>
                </dl>
            </section>

            {{-- Hymns --}}
            <section class="px-6 py-6 sm:px-10">
                <h3 class="card-kicker mb-3">Hymns of Praise & Dedication</h3>
                <ol class="space-y-2">
                    @foreach($hymnList as $hymnLine)
                        <li class="flex items-baseline gap-3">
                            <span class="font-display text-[13px] text-gold tabular-nums w-5 shrink-0" aria-hidden="true">{{ $loop->iteration }}.</span>
                            <span class="font-display text-[16px] leading-snug text-slate-900">{{ $hymnLine }}</span>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- Hymn & worship lyrics --}}
            @if($sheet->lyrics)
                <section class="px-6 py-6 sm:px-10">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h3 class="card-kicker">Hymn & Praise Lyrics</h3>
                        <button type="button" onclick="navigator.clipboard.writeText({{ json_encode($sheet->lyrics) }}); alert('Lyrics copied to clipboard!');" class="btn btn-ghost btn-sm text-brand">
                            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copy for Media Desk
                        </button>
                    </div>
                    <div class="hymn-lyrics-block max-h-96 overflow-y-auto rounded-xl bg-paper/70 ring-1 ring-slate-200/70 px-5 py-4">{{ $sheet->lyrics }}</div>
                </section>
            @endif

            {{-- Ushers --}}
            @if($sheet->ushers)
                <section class="px-6 py-6 sm:px-10">
                    <h3 class="card-kicker mb-2">Ushering Squad</h3>
                    <p class="text-[14px] font-semibold text-slate-900">{{ $sheet->ushers }}</p>
                </section>
            @endif

            {{-- Call to services --}}
            <section class="px-6 py-6 sm:px-10">
                <h3 class="card-kicker mb-2">Call to Services</h3>
                <p class="text-[14px] text-slate-700 whitespace-pre-line leading-relaxed max-w-prose">{{ $sheet->order_of_service_notes ?: 'Standard Methodist liturgical sequence with Call to Worship, Invocation, Scripture, Anthem, Sermon, Offertory, and Benediction.' }}</p>
            </section>
        </div>
    </article>

</div>

<script>
    function dispatchWorshipDutyWhatsApp() {
        const sheet = {{ Js::from([
            'service' => $sheet->service_type . ' • ' . $sheet->service_date->format('l, F j, Y'),
            'theme' => $sheet->theme ?: 'Chapel Assembly',
            'preacher' => $sheet->preacher ?: 'University Chaplain',
            'liturgist' => $sheet->liturgist ?: 'Assistant Chaplain',
            'reader' => $sheet->bible_reader ?: 'Student Representative',
            'choir' => $sheet->choir ?: ($sheet->praise_worship_team ?: 'Praise the Lord Choir'),
            'scripture' => $sheet->scripture ?: 'As Announced',
            'hymns' => $sheet->hymns ?: 'UMH / Shona Hymnody',
        ]) }};
        const text = `⛪ *KWANG-LIM CHAPEL*\n*OFFICIAL WORSHIP LITURGY DUTY CALL*\n\n📅 *Service:* ${sheet.service}\n📖 *Theme:* "${sheet.theme}"\n🎙️ *Preacher:* ${sheet.preacher}\n✝️ *Liturgist:* ${sheet.liturgist}\n📖 *Bible Reader:* ${sheet.reader}\n🎵 *Choir / Praise Team:* ${sheet.choir}\n📜 *Scripture Lesson:* ${sheet.scripture}\n🎶 *Hymns of Praise:* ${sheet.hymns}\n\n_Ministers, liturgists, and choir members are requested to report to the vestry 30 minutes prior for vestment and altar prayer._\n\nKwang-Lim Chapel Ministry Office`;
        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
    }
</script>
@endsection
