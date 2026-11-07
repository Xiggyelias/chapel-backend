@php
    $accent = preg_match('/^#[0-9a-fA-F]{6}$/', $season['hex'] ?? '') ? $season['hex'] : '#B8892B';
    $seasonName = $season['name'] ?? 'Ordinary Time';
    $fitClass = function (string $text) {
        $lines = substr_count(trim($text), "\n") + 1;
        return match (true) {
            $lines <= 4 => 'fit-xl',
            $lines <= 6 => 'fit-lg',
            $lines <= 9 => 'fit-md',
            default => 'fit-sm',
        };
    };
    $sheetLyrics = $sheet->lyrics
        ? array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', trim($sheet->lyrics)))))
        : [];
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $sheet->theme ?: 'Chapel Worship' }} · Projection</title>
    @include('partials.head')
    @vite(['resources/css/app.css'])
    <style>
        /* Projection surface: deep warm ink, Fraunces for every word the congregation reads,
           gold hairlines for ornament, crimson only for the presenter's primary action. */
        :root {
            --accent: {{ $accent }};
            --crimson: var(--color-brand, #A51C30);
            --gold: var(--color-gold, #B8892B);
            --gold-light: var(--color-gold-light, #D9B76C);
            --ink: #110E13;
            --chalk: #FBF7EF;
            --serif: var(--font-display, 'Fraunces', Georgia, serif);
        }
        html, body { height: 100%; }
        body { margin: 0; background: var(--ink); color: var(--chalk); font-family: var(--font-sans, 'Plus Jakarta Sans', system-ui, sans-serif); overflow: hidden; }
        .serif { font-family: var(--serif); font-optical-sizing: auto; font-variation-settings: 'SOFT' 50, 'WONK' 0; }

        .stage { position: fixed; inset: 0; overflow: hidden; }
        .stage-bg { position: absolute; inset: 0; background:
            radial-gradient(70% 55% at 50% -10%, color-mix(in srgb, var(--crimson) 22%, transparent) 0%, transparent 70%),
            radial-gradient(60% 50% at 50% 115%, color-mix(in srgb, var(--accent) 16%, transparent) 0%, transparent 70%),
            linear-gradient(180deg, #18131A 0%, var(--ink) 55%, #0C0A0E 100%); }
        .vignette { position: absolute; inset: 0; pointer-events: none; box-shadow: inset 0 0 24vmin rgba(0,0,0,.7); }

        .slide { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 9vh 8vw; text-align: center; opacity: 0; visibility: hidden; transform: translateY(1.2vh);
            transition: opacity .5s ease, transform .5s ease, visibility 0s linear .5s; }
        .slide.active { opacity: 1; visibility: visible; transform: none; transition: opacity .5s ease, transform .5s ease; }
        @media (prefers-reduced-motion: reduce) { .slide, .slide.active { transition: none; transform: none; } }

        /* Small-caps kicker framed by gold hairlines */
        .kicker { display: inline-flex; align-items: center; gap: 1.1em; font-size: clamp(.72rem, 1.2vw, 1.1rem); letter-spacing: .3em; text-transform: uppercase; font-weight: 600; color: var(--gold-light); }
        .kicker::before, .kicker::after { content: ''; width: clamp(1.5rem, 3vw, 3rem); height: 1px; background: currentColor; opacity: .5; }

        /* Ornamental rule: hairline · lozenge · hairline */
        .rule { display: flex; align-items: center; gap: 1rem; width: clamp(8rem, 18vw, 16rem); margin: 3.4vh auto; color: var(--gold); }
        .rule::before, .rule::after { content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, transparent, currentColor); opacity: .7; }
        .rule::after { background: linear-gradient(90deg, currentColor, transparent); }
        .rule i { width: .42rem; height: .42rem; transform: rotate(45deg); background: currentColor; }

        .display { font-family: var(--serif); font-weight: 400; line-height: 1.06; letter-spacing: -.02em; text-wrap: balance;
            font-variation-settings: 'SOFT' 60, 'WONK' 0, 'opsz' 144; }
        .h-hero { font-size: clamp(2.6rem, 6.8vw, 7.4rem); }
        .h-big { font-size: clamp(2.1rem, 5.2vw, 5.6rem); }
        .meta { color: rgba(251,247,239,.62); font-size: clamp(.95rem, 1.6vw, 1.6rem); }

        .lyric-box { max-height: 70vh; width: 100%; display: flex; align-items: center; justify-content: center; }
        .lyric { font-family: var(--serif); font-weight: 420; white-space: pre-line; line-height: 1.34; letter-spacing: -.005em; text-wrap: balance;
            font-variation-settings: 'SOFT' 50, 'WONK' 0, 'opsz' 96; color: var(--chalk);
            text-shadow: 0 2px 24px rgba(0,0,0,.5); margin: 0; }
        .lyric.refrain { font-style: italic; color: color-mix(in srgb, var(--gold-light) 55%, var(--chalk)); }
        .fit-xl { font-size: clamp(2.1rem, 5.2vw, 5.4rem); }
        .fit-lg { font-size: clamp(1.85rem, 4.3vw, 4.5rem); }
        .fit-md { font-size: clamp(1.55rem, 3.5vw, 3.6rem); }
        .fit-sm { font-size: clamp(1.3rem, 2.8vw, 2.9rem); }

        .tag { position: absolute; left: 4vw; bottom: 5vh; display: flex; align-items: center; gap: .9rem; text-align: left; }
        .tag-num { font-weight: 700; font-size: clamp(.68rem, 1vw, .95rem); letter-spacing: .16em; padding: .4em .9em; border-radius: 999px;
            color: var(--gold-light); background: rgba(184,137,43,.08); box-shadow: inset 0 0 0 1px rgba(217,183,108,.35); }
        .tag-title { font-family: var(--serif); font-style: italic; font-size: clamp(.85rem, 1.25vw, 1.2rem); color: rgba(251,247,239,.55); }
        .verse-dots { position: absolute; right: 4vw; bottom: 5.6vh; display: flex; gap: .55rem; }
        .verse-dots span { width: .5rem; height: .5rem; border-radius: 999px; background: rgba(251,247,239,.18); }
        .verse-dots span.on { background: var(--gold-light); }

        .crest { border-radius: 1.75rem; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, var(--crimson), #5E0F1C);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.14), 0 0 0 1px rgba(217,183,108,.35), 0 24px 60px -28px rgba(165,28,48,.8); }

        .panel { background: rgba(251,247,239,.03); border: 1px solid rgba(251,247,239,.08); border-radius: 1.5rem; }
        .panel-role { color: var(--gold-light); opacity: .85; }

        .overlay { position: absolute; inset: 0; z-index: 30; display: none; align-items: center; justify-content: center; }
        .overlay.on { display: flex; }
        #blackout { background: #000; }
        #logoScreen { background: var(--ink); flex-direction: column; gap: 2.25rem; }

        .deck-progress { position: fixed; top: 0; left: 0; height: 2px; z-index: 40; background: linear-gradient(90deg, var(--gold), var(--gold-light)); box-shadow: 0 0 10px rgba(217,183,108,.45); transition: width .35s ease; }

        .chrome { position: fixed; left: 0; right: 0; z-index: 50; transition: opacity .3s ease, transform .3s ease; }
        .chrome-top { top: 0; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; background: linear-gradient(180deg, rgba(0,0,0,.6), transparent); }
        .chrome-bottom { bottom: 0; padding: .9rem 1.25rem; display: flex; gap: 1rem; justify-content: space-between; align-items: center; background: linear-gradient(0deg, rgba(0,0,0,.78), rgba(0,0,0,.3) 70%, transparent); }
        body.idle .chrome { opacity: 0; pointer-events: none; }
        body.idle { cursor: none; }
        body.audience .chrome, body.audience .deck-progress { display: none !important; }
        body.audience { cursor: none; }

        .ctl { display: inline-flex; align-items: center; gap: .45rem; padding: .5rem .85rem; border-radius: .75rem; font-size: .78rem; font-weight: 600; line-height: 1.25rem;
            color: rgba(251,247,239,.82); background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); cursor: pointer;
            transition: background-color .2s, border-color .2s, color .2s; white-space: nowrap; text-decoration: none; }
        .ctl:hover { background: rgba(255,255,255,.12); color: #fff; }
        .ctl svg { width: .95rem; height: .95rem; flex-shrink: 0; }
        .ctl.primary { color: #fff; background-color: var(--crimson); background-image: linear-gradient(180deg, rgba(255,255,255,.12), transparent 60%); border-color: #861527; }
        .ctl.primary:hover { background-color: #861527; }
        .ctl.on { color: #fff; background: rgba(184,137,43,.18); border-color: rgba(217,183,108,.6); }
        .ctl:focus-visible { outline: 2px solid var(--gold-light); outline-offset: 2px; }

        #overview { position: fixed; inset: 0; z-index: 60; background: rgba(12,10,14,.95); backdrop-filter: blur(8px); display: none; overflow-y: auto; padding: 5rem 2rem 2rem; }
        #overview.on { display: block; }
        .thumb { text-align: left; padding: .9rem 1rem; border-radius: .9rem; background: rgba(251,247,239,.04); border: 1px solid rgba(251,247,239,.08); cursor: pointer; transition: border-color .2s, background .2s; min-height: 6.2rem; display: flex; flex-direction: column; gap: .35rem; color: var(--chalk); }
        .thumb:hover { border-color: rgba(251,247,239,.28); }
        .thumb:focus-visible { outline: 2px solid var(--gold-light); outline-offset: 2px; }
        .thumb.current { border-color: var(--gold-light); background: rgba(184,137,43,.12); }
        .thumb small { font-size: .62rem; letter-spacing: .16em; text-transform: uppercase; color: rgba(217,183,108,.75); font-weight: 700; }
        .thumb span { font-family: var(--serif); font-size: .9rem; line-height: 1.35; color: rgba(251,247,239,.88); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

        #jumpBox { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 70; display: none; padding: 1rem 1.75rem; border-radius: 1rem; background: rgba(12,10,14,.9); border: 1px solid rgba(217,183,108,.6); font-family: var(--serif); font-size: 2.25rem; font-weight: 400; font-variant-numeric: lining-nums tabular-nums; color: var(--chalk); }
        #jumpBox.on { display: block; }
    </style>
</head>
<body>
<div class="stage" id="stage">
    <div class="stage-bg grain" aria-hidden="true"></div>
    <div class="vignette" aria-hidden="true"></div>

    {{-- Holding slide: shown while the congregation gathers --}}
    <section class="slide" data-kind="Welcome" data-label="{{ $sheet->theme ?: 'Welcome' }}">
        <div class="crest w-[clamp(4rem,7.5vw,6.5rem)] h-[clamp(4rem,7.5vw,6.5rem)] p-4 mb-10">
            <x-umc-logo class="w-full h-full" />
        </div>
        <p class="kicker">Africa University · Kwang-Lim Chapel</p>
        <h1 class="display h-hero mt-6">Welcome to worship</h1>
        <div class="rule" aria-hidden="true"><i></i></div>
        <p class="meta serif italic">Please silence your phones as we prepare our hearts.</p>
        <p class="serif mt-10 tabular-nums" style="font-size: clamp(2rem, 4vw, 4rem); font-weight: 300; color: rgba(251,247,239,.78); font-variant-numeric: lining-nums tabular-nums" data-clock></p>
    </section>

    {{-- Theme --}}
    <section class="slide" data-kind="Theme" data-label="{{ $sheet->theme ?: 'Worship & Fellowship' }}">
        <p class="kicker">{{ $sheet->service_type }} · {{ $sheet->service_date->format('l, j F Y') }}</p>
        <h1 class="display h-hero mt-7 max-w-[18ch]">{{ $sheet->theme ?: 'Worship & Fellowship' }}</h1>
        <div class="rule" aria-hidden="true"><i></i></div>
        @if($sheet->preacher)
            <p class="meta uppercase tracking-[.28em] text-[clamp(.7rem,1.05vw,1rem)] font-semibold">Preacher of the Word</p>
            <p class="serif mt-2" style="font-size: clamp(1.7rem, 3.1vw, 3.1rem)">{{ $sheet->preacher }}</p>
        @endif
        <p class="tag"><span class="tag-num">{{ strtoupper($seasonName) }}</span></p>
    </section>

    {{-- Scripture --}}
    <section class="slide" data-kind="Scripture" data-label="{{ $sheet->scripture ?: 'Scripture reading' }}">
        <p class="kicker">The Word of God</p>
        <h2 class="display h-big mt-7">{{ $sheet->scripture ?: 'Scripture to be announced' }}</h2>
        <div class="rule" aria-hidden="true"><i></i></div>
        <p class="serif italic meta max-w-[40ch]" style="font-size: clamp(1.2rem, 2.1vw, 2.1rem); color: rgba(251,247,239,.75)">“Your word is a lamp to my feet and a light to my path.”</p>
        <p class="meta mt-3 text-[clamp(.75rem,1.1vw,1rem)] uppercase tracking-[.2em]">Psalm 119:105</p>
        @if($sheet->bible_reader)
            <p class="tag"><span class="tag-num">READER</span><span class="tag-title">{{ $sheet->bible_reader }}</span></p>
        @endif
    </section>

    {{-- Ministers --}}
    <section class="slide" data-kind="Ministers" data-label="Ministers of the service">
        <p class="kicker">Serving among us today</p>
        <div class="rule" aria-hidden="true"><i></i></div>
        <div class="panel grid grid-cols-2 gap-x-[5vw] gap-y-[4.5vh] text-left px-[4.5vw] py-[5.5vh] w-full max-w-[min(1100px,86vw)]">
            @foreach([
                ['Preacher', $sheet->preacher ?: 'To be announced'],
                ['Liturgist', $sheet->liturgist ?: 'Chaplaincy Team'],
                ['Bible Reader', $sheet->bible_reader ?: 'Student Representative'],
                ['Choir · Praise', $sheet->choir ?: ($sheet->praise_worship_team ?: 'Praise & Worship Team')],
            ] as [$role, $name])
                <div>
                    <p class="panel-role uppercase tracking-[.24em] text-[clamp(.65rem,.95vw,.9rem)] font-semibold">{{ $role }}</p>
                    <p class="serif mt-1.5" style="font-size: clamp(1.4rem, 2.6vw, 2.7rem); line-height: 1.15">{{ $name }}</p>
                </div>
            @endforeach
        </div>
        @if($sheet->sign_interpreter_required)
            <p class="meta mt-7">Sign-language interpretation by <strong class="serif font-normal italic" style="color: var(--chalk)">{{ $sheet->sign_interpreter_name ?: 'our interpreter' }}</strong></p>
        @endif
    </section>

    {{-- Hymns: title card, then each verse with its refrain sung after it --}}
    @foreach($hymnSlides as $hIdx => $hymn)
        @php
            $verses = array_values(array_filter($hymn['stanzas'], fn ($s) => ! $s['refrain']));
            $refrain = collect($hymn['stanzas'])->firstWhere('refrain', true);
            $sequence = [];
            if ($refrain && ($hymn['stanzas'][0]['refrain'] ?? false)) {
                $sequence[] = ['refrain' => true, 'text' => $refrain['text'], 'verse' => null];
            }
            foreach ($verses as $vIdx => $verse) {
                $sequence[] = ['refrain' => false, 'text' => $verse['text'], 'verse' => $vIdx + 1];
                if ($refrain) {
                    $sequence[] = ['refrain' => true, 'text' => $refrain['text'], 'verse' => $vIdx + 1];
                }
            }
            $tagLabel = $hymn['label'] ?? 'Hymn ' . ($hIdx + 1);
        @endphp

        <section class="slide" data-kind="Hymn {{ $hIdx + 1 }}" data-label="{{ $tagLabel }} · {{ $hymn['title'] }}">
            <p class="kicker">Let us sing</p>
            @if($hymn['label'])
                <p class="serif mt-7" style="font-size: clamp(3.2rem, 9.5vw, 9.5rem); font-weight: 300; line-height: 1; font-variant-numeric: lining-nums tabular-nums; font-variation-settings: 'SOFT' 30, 'opsz' 144; color: var(--gold-light)">{{ preg_replace('/^\D+/', '', $hymn['label']) }}</p>
            @endif
            <h2 class="display h-big mt-5 max-w-[20ch]">{{ $hymn['title'] }}</h2>
            <div class="rule" aria-hidden="true"><i></i></div>
            <p class="meta serif italic">
                {{ $hymn['label'] ? str_replace(['UMH', 'MHB', 'SHONA'], ['United Methodist Hymnal', 'Methodist Hymn Book', 'Shona Hymnal'], strtok($hymn['label'], ' ')) : '' }}
                @if($hymn['author'] && $hymn['author'] !== 'Traditional') · {{ $hymn['author'] }} @endif
            </p>
            @if(empty($sequence))
                <p class="meta mt-8 text-[clamp(.75rem,1.1vw,1rem)] uppercase tracking-[.25em]">Please follow in your hymnal</p>
            @endif
        </section>

        @foreach($sequence as $sIdx => $stanza)
            <section class="slide" data-kind="{{ $tagLabel }} · {{ $stanza['refrain'] ? 'Refrain' : 'Verse ' . $stanza['verse'] }}"
                     data-label="{{ \Illuminate\Support\Str::limit(strtok($stanza['text'], "\n"), 70) }}">
                <div class="lyric-box" data-fit-box>
                    <p class="lyric {{ $stanza['refrain'] ? 'refrain' : '' }} {{ $fitClass($stanza['text']) }}" data-fit>{{ $stanza['text'] }}</p>
                </div>
                <p class="tag">
                    <span class="tag-num">{{ $tagLabel }}</span>
                    <span class="tag-title">{{ $hymn['title'] }} — {{ $stanza['refrain'] ? 'Refrain' : 'Verse ' . $stanza['verse'] . ' of ' . count($verses) }}</span>
                </p>
                @if(count($verses) > 1)
                    <div class="verse-dots" aria-hidden="true">
                        @for($d = 1; $d <= count($verses); $d++)
                            <span class="{{ $d === $stanza['verse'] ? 'on' : '' }}"></span>
                        @endfor
                    </div>
                @endif
            </section>
        @endforeach
    @endforeach

    {{-- Song lyrics typed into the service sheet --}}
    @foreach($sheetLyrics as $lIdx => $section)
        <section class="slide" data-kind="Song · {{ $lIdx + 1 }}/{{ count($sheetLyrics) }}" data-label="{{ \Illuminate\Support\Str::limit(strtok($section, "\n"), 70) }}">
            <div class="lyric-box" data-fit-box>
                <p class="lyric {{ $fitClass($section) }}" data-fit>{{ $section }}</p>
            </div>
            <p class="tag"><span class="tag-num">PRAISE</span><span class="tag-title">Part {{ $lIdx + 1 }} of {{ count($sheetLyrics) }}</span></p>
        </section>
    @endforeach

    {{-- Benediction --}}
    <section class="slide" data-kind="Benediction" data-label="The Lord bless you and keep you">
        <p class="kicker">The Benediction</p>
        <div class="rule" aria-hidden="true"><i></i></div>
        <p class="lyric fit-lg max-w-[24ch]" data-fit>The Lord bless you and keep you;
the Lord make his face to shine upon you, and be gracious to you;
the Lord lift up his countenance upon you, and give you peace.</p>
        <p class="meta mt-8 text-[clamp(.75rem,1.1vw,1rem)] uppercase tracking-[.2em]">Numbers 6:24–26</p>
        <p class="tag"><span class="tag-num">GO IN PEACE</span><span class="tag-title">to love and serve the Lord</span></p>
    </section>

    <div class="overlay" id="blackout" aria-hidden="true"></div>
    <div class="overlay" id="logoScreen" aria-hidden="true">
        <div class="crest w-[clamp(5rem,9vw,8rem)] h-[clamp(5rem,9vw,8rem)] p-5">
            <x-umc-logo class="w-full h-full" />
        </div>
        <p class="kicker">Kwang-Lim Chapel · Africa University</p>
    </div>
</div>

<div class="deck-progress" id="progress" style="width: 0"></div>

<header class="chrome chrome-top">
    <div class="flex items-center gap-3 min-w-0">
        <span class="w-2 h-2 rounded-full shrink-0" style="background: var(--accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 25%, transparent)"></span>
        <div class="leading-tight min-w-0">
            <p class="text-[10.5px] font-bold uppercase tracking-[.22em] text-[#D9B76C]">Presenter</p>
            <p class="text-[11.5px] text-white/50 truncate">{{ $seasonName }} · {{ $sheet->service_date->format('j M Y') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="hidden sm:inline-flex items-center gap-1.5 text-xs text-white/55 tabular-nums mr-1" title="Time since the service started">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="13" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4l2.5 1.5M10 2h4"/></svg>
            <span class="sr-only">Elapsed</span><span id="elapsed">00:00</span>
        </span>
        <span class="serif text-[15px] tabular-nums mr-2 text-white/90" data-clock></span>
        <button class="ctl" id="projectorBtn" onclick="Deck.openProjector()" title="Open a clean output window for the projector screen">Projector window <kbd class="kbd">P</kbd></button>
        <button class="ctl" onclick="Deck.toggleFullscreen()">Fullscreen <kbd class="kbd">F</kbd></button>
        <a class="ctl" href="{{ route('worship.show', $sheet->id) }}">Exit</a>
    </div>
</header>

<footer class="chrome chrome-bottom">
    <div class="flex items-center gap-2">
        <button class="ctl" onclick="Deck.prev()" aria-label="Previous slide">
            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            Prev
        </button>
        <button class="ctl primary" onclick="Deck.next()" aria-label="Next slide">
            Next
            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </button>
        <span class="text-xs text-white/60 tabular-nums ml-2" id="counter"></span>
    </div>
    <div class="hidden md:flex items-center gap-2.5 min-w-0 flex-1 justify-center">
        <span class="text-[10px] uppercase tracking-[.22em] text-[#D9B76C]/80 font-bold shrink-0">Up next</span>
        <span class="serif italic text-[13.5px] text-white/75 truncate max-w-[44ch]" id="upNext"></span>
    </div>
    <div class="flex items-center gap-2">
        <button class="ctl" id="overviewBtn" onclick="Deck.toggleOverview()">Overview <kbd class="kbd">G</kbd></button>
        <button class="ctl" id="logoBtn" onclick="Deck.setOverlay('logo')">Logo <kbd class="kbd">L</kbd></button>
        <button class="ctl" id="blackBtn" onclick="Deck.setOverlay('black')">Black <kbd class="kbd">B</kbd></button>
    </div>
</footer>

<div id="overview" role="dialog" aria-label="All slides">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-wrap items-end justify-between gap-3 mb-6 pb-4 border-b border-white/10">
            <div>
                <p class="text-[10.5px] font-bold uppercase tracking-[.22em] text-[#D9B76C] mb-1">{{ $sheet->theme ?: 'Chapel Worship' }}</p>
                <h2 class="serif text-[1.75rem] leading-tight">Order of service</h2>
            </div>
            <p class="text-xs text-white/50">Click a slide, or type its number and press <kbd class="kbd">Enter</kbd> · <kbd class="kbd">Esc</kbd> to close</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3" id="thumbs"></div>
    </div>
</div>
<div id="jumpBox"></div>

<script>
(function () {
    const slides = Array.from(document.querySelectorAll('.slide'));
    const isAudience = new URLSearchParams(location.search).get('mode') === 'audience';
    const channel = 'BroadcastChannel' in window ? new BroadcastChannel('chapel-slides-{{ $sheet->id }}') : null;
    const startedAt = Date.now();
    let index = 0, overlay = null, jump = '', jumpTimer = null, idleTimer = null;

    if (isAudience) document.body.classList.add('audience');

    function fit(slide) {
        slide.querySelectorAll('[data-fit]').forEach(el => {
            el.style.fontSize = '';
            const box = el.closest('[data-fit-box]') || slide;
            let size = parseFloat(getComputedStyle(el).fontSize), guard = 0;
            while ((el.scrollHeight > box.clientHeight || el.scrollWidth > box.clientWidth) && guard++ < 30 && size > 14) {
                size *= 0.94;
                el.style.fontSize = size + 'px';
            }
        });
    }

    function render() {
        slides.forEach((s, i) => s.classList.toggle('active', i === index));
        fit(slides[index]);
        document.getElementById('blackout').classList.toggle('on', overlay === 'black');
        document.getElementById('logoScreen').classList.toggle('on', overlay === 'logo');
        if (isAudience) return;
        document.getElementById('counter').textContent = `${index + 1} / ${slides.length} · ${slides[index].dataset.kind}`;
        const next = slides[index + 1];
        document.getElementById('upNext').textContent = next ? `${next.dataset.kind} — ${next.dataset.label}` : 'End of service';
        document.getElementById('progress').style.width = ((index + 1) / slides.length * 100) + '%';
        document.getElementById('blackBtn').classList.toggle('on', overlay === 'black');
        document.getElementById('logoBtn').classList.toggle('on', overlay === 'logo');
        document.querySelectorAll('.thumb').forEach((t, i) => t.classList.toggle('current', i === index));
        history.replaceState(null, '', '#' + (index + 1));
    }

    function publish() { channel && channel.postMessage({ type: 'state', index, overlay }); }
    function go(i, silent) {
        index = Math.max(0, Math.min(slides.length - 1, i));
        render();
        if (!silent) publish();
    }

    if (channel) {
        channel.onmessage = ({ data }) => {
            if (data.type === 'state') { index = data.index; overlay = data.overlay; render(); }
            if (data.type === 'hello' && !isAudience) publish();
        };
    }

    window.Deck = {
        next: () => go(index + 1),
        prev: () => go(index - 1),
        setOverlay(kind) { overlay = overlay === kind ? null : kind; render(); publish(); },
        toggleFullscreen() {
            document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen().catch(() => {});
        },
        toggleOverview(force) {
            const el = document.getElementById('overview');
            const on = typeof force === 'boolean' ? force : !el.classList.contains('on');
            el.classList.toggle('on', on);
            document.getElementById('overviewBtn').classList.toggle('on', on);
            if (on) document.querySelector('.thumb.current')?.scrollIntoView({ block: 'center' });
        },
        openProjector() {
            const url = location.pathname + '?mode=audience#' + (index + 1);
            const win = window.open(url, 'chapelProjector', 'popup,width=1280,height=720');
            if (!win) alert('Allow pop-ups for this site to open the projector window.');
            document.getElementById('projectorBtn').classList.add('on');
        },
    };

    if (!isAudience) {
        const thumbs = document.getElementById('thumbs');
        slides.forEach((s, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'thumb';
            b.innerHTML = '<small></small><span></span>';
            b.querySelector('small').textContent = (i + 1) + ' · ' + s.dataset.kind;
            b.querySelector('span').textContent = s.dataset.label;
            b.onclick = () => { go(i); Deck.toggleOverview(false); };
            thumbs.appendChild(b);
        });
    }

    document.addEventListener('keydown', e => {
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        const k = e.key;
        if (/^\d$/.test(k)) {
            jump += k;
            const box = document.getElementById('jumpBox');
            box.textContent = 'Go to ' + jump;
            box.classList.add('on');
            clearTimeout(jumpTimer);
            jumpTimer = setTimeout(() => { jump = ''; box.classList.remove('on'); }, 2500);
            return;
        }
        if (k === 'Enter' && jump) {
            go(parseInt(jump, 10) - 1);
            jump = '';
            document.getElementById('jumpBox').classList.remove('on');
            Deck.toggleOverview(false);
            return;
        }
        if (['ArrowRight', 'ArrowDown', ' ', 'PageDown', 'Enter'].includes(k)) { e.preventDefault(); Deck.next(); }
        else if (['ArrowLeft', 'ArrowUp', 'PageUp', 'Backspace'].includes(k)) { e.preventDefault(); Deck.prev(); }
        else if (k === 'Home') go(0);
        else if (k === 'End') go(slides.length - 1);
        else if (k === 'f' || k === 'F') Deck.toggleFullscreen();
        else if (k === 'b' || k === 'B' || k === '.') Deck.setOverlay('black');
        else if (k === 'l' || k === 'L') Deck.setOverlay('logo');
        else if (!isAudience && (k === 'g' || k === 'G')) Deck.toggleOverview();
        else if (!isAudience && (k === 'p' || k === 'P')) Deck.openProjector();
        else if (k === 'Escape') { Deck.toggleOverview(false); if (overlay) Deck.setOverlay(overlay); }
    });

    document.getElementById('stage').addEventListener('click', e => {
        if (isAudience) return;
        e.clientX < window.innerWidth / 3 ? Deck.prev() : Deck.next();
    });

    function wake() {
        document.body.classList.remove('idle');
        clearTimeout(idleTimer);
        idleTimer = setTimeout(() => {
            if (document.fullscreenElement) document.body.classList.add('idle');
        }, 3000);
    }
    ['mousemove', 'keydown', 'touchstart'].forEach(ev => document.addEventListener(ev, wake, { passive: true }));
    document.addEventListener('fullscreenchange', wake);

    function tick() {
        const now = new Date();
        document.querySelectorAll('[data-clock]').forEach(el => el.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
        const secs = Math.floor((Date.now() - startedAt) / 1000);
        const el = document.getElementById('elapsed');
        if (el) el.textContent = String(Math.floor(secs / 60)).padStart(2, '0') + ':' + String(secs % 60).padStart(2, '0');
    }
    setInterval(tick, 1000);
    tick();

    window.addEventListener('resize', () => fit(slides[index]));
    const fromHash = parseInt(location.hash.slice(1), 10);
    go(Number.isFinite(fromHash) ? fromHash - 1 : 0, true);
    if (isAudience && channel) channel.postMessage({ type: 'hello' });
})();
</script>
</body>
</html>
