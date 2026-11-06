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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;1,9..144,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root { --accent: {{ $accent }}; --crimson: #A51C30; --gold: #B8892B; --ink: #0B0C10; }
        html, body { height: 100%; }
        body { margin: 0; background: var(--ink); color: #fff; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; overflow: hidden; }
        .serif { font-family: 'Fraunces', Georgia, serif; font-optical-sizing: auto; }

        .stage { position: fixed; inset: 0; overflow: hidden; }
        .stage-bg { position: absolute; inset: 0; background:
            radial-gradient(60% 50% at 15% 10%, color-mix(in srgb, var(--crimson) 38%, transparent) 0%, transparent 70%),
            radial-gradient(55% 55% at 90% 95%, color-mix(in srgb, var(--accent) 30%, transparent) 0%, transparent 70%),
            linear-gradient(160deg, #12060A 0%, #0B0C10 55%, #07080B 100%); }
        .stage-bg::after { content: ''; position: absolute; inset: 0; opacity: .05;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48'%3E%3Cpath d='M0 24h48M24 0v48' stroke='%23fff' stroke-width='.5'/%3E%3C/svg%3E"); }
        .vignette { position: absolute; inset: 0; pointer-events: none; box-shadow: inset 0 0 22vmin rgba(0,0,0,.75); }

        .slide { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 9vh 8vw; text-align: center; opacity: 0; visibility: hidden; transform: translateY(1.2vh);
            transition: opacity .45s ease, transform .45s ease, visibility 0s linear .45s; }
        .slide.active { opacity: 1; visibility: visible; transform: none; transition: opacity .45s ease, transform .45s ease; }
        @media (prefers-reduced-motion: reduce) { .slide, .slide.active { transition: none; transform: none; } }

        .eyebrow { font-size: clamp(.7rem, 1.25vw, 1.15rem); letter-spacing: .32em; text-transform: uppercase; font-weight: 700; color: color-mix(in srgb, var(--accent) 70%, #fff); }
        .rule { width: clamp(3rem, 7vw, 7rem); height: 3px; border-radius: 3px; margin: 3.2vh auto; background: linear-gradient(90deg, var(--crimson), var(--accent)); }
        .display { font-family: 'Fraunces', Georgia, serif; font-weight: 500; line-height: 1.08; letter-spacing: -.01em; text-wrap: balance; }
        .h-hero { font-size: clamp(2.4rem, 6.4vw, 7rem); }
        .h-big { font-size: clamp(2rem, 5vw, 5.4rem); }
        .meta { color: rgba(255,255,255,.62); font-size: clamp(.9rem, 1.6vw, 1.6rem); }

        .lyric-box { max-height: 70vh; width: 100%; display: flex; align-items: center; justify-content: center; }
        .lyric { font-family: 'Fraunces', Georgia, serif; font-weight: 500; white-space: pre-line; line-height: 1.32; text-wrap: balance;
            text-shadow: 0 2px 24px rgba(0,0,0,.55); margin: 0; }
        .lyric.refrain { font-style: italic; color: color-mix(in srgb, var(--accent) 35%, #fff); }
        .fit-xl { font-size: clamp(2rem, 5vw, 5.2rem); }
        .fit-lg { font-size: clamp(1.8rem, 4.2vw, 4.4rem); }
        .fit-md { font-size: clamp(1.5rem, 3.4vw, 3.5rem); }
        .fit-sm { font-size: clamp(1.25rem, 2.7vw, 2.8rem); }

        .tag { position: absolute; left: 4vw; bottom: 5vh; display: flex; align-items: center; gap: .9rem; text-align: left; }
        .tag-num { font-weight: 700; font-size: clamp(.7rem, 1.1vw, 1rem); letter-spacing: .14em; padding: .35em .8em; border-radius: 999px;
            background: color-mix(in srgb, var(--accent) 22%, transparent); border: 1px solid color-mix(in srgb, var(--accent) 50%, transparent); color: #fff; }
        .tag-title { font-size: clamp(.75rem, 1.15vw, 1.05rem); color: rgba(255,255,255,.6); }
        .verse-dots { position: absolute; right: 4vw; bottom: 5.4vh; display: flex; gap: .5rem; }
        .verse-dots span { width: .55rem; height: .55rem; border-radius: 999px; background: rgba(255,255,255,.2); }
        .verse-dots span.on { background: var(--accent); }

        .card { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.09); border-radius: 1.5rem; backdrop-filter: blur(8px); }

        .overlay { position: absolute; inset: 0; z-index: 30; display: none; align-items: center; justify-content: center; }
        .overlay.on { display: flex; }
        #blackout { background: #000; }
        #logoScreen { background: var(--ink); flex-direction: column; gap: 2rem; }

        .progress { position: fixed; top: 0; left: 0; height: 3px; z-index: 40; background: linear-gradient(90deg, var(--crimson), var(--accent)); transition: width .35s ease; }

        .chrome { position: fixed; left: 0; right: 0; z-index: 50; transition: opacity .3s ease, transform .3s ease; }
        .chrome-top { top: 0; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(180deg, rgba(0,0,0,.65), transparent); }
        .chrome-bottom { bottom: 0; padding: .9rem 1.25rem; display: flex; gap: 1rem; justify-content: space-between; align-items: center; background: linear-gradient(0deg, rgba(0,0,0,.8), rgba(0,0,0,.35) 70%, transparent); }
        body.idle .chrome { opacity: 0; pointer-events: none; }
        body.idle { cursor: none; }
        body.audience .chrome, body.audience .progress { display: none !important; }
        body.audience { cursor: none; }

        .btn { display: inline-flex; align-items: center; gap: .45rem; padding: .5rem .85rem; border-radius: .75rem; font-size: .78rem; font-weight: 600;
            color: rgba(255,255,255,.85); background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); cursor: pointer; transition: background .2s; white-space: nowrap; }
        .btn:hover { background: rgba(255,255,255,.16); color: #fff; }
        .btn.primary { background: var(--crimson); border-color: transparent; color: #fff; }
        .btn.primary:hover { background: #8a1627; }
        .btn.on { background: color-mix(in srgb, var(--accent) 35%, transparent); border-color: var(--accent); }
        .btn:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        kbd { font-family: ui-monospace, monospace; font-size: .65rem; padding: .05rem .35rem; border-radius: .3rem; background: rgba(255,255,255,.12); color: rgba(255,255,255,.7); }

        #overview { position: fixed; inset: 0; z-index: 60; background: rgba(6,7,10,.94); backdrop-filter: blur(6px); display: none; overflow-y: auto; padding: 5rem 2rem 2rem; }
        #overview.on { display: block; }
        .thumb { text-align: left; padding: .9rem 1rem; border-radius: .9rem; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08); cursor: pointer; transition: border-color .2s, background .2s; min-height: 6.2rem; display: flex; flex-direction: column; gap: .3rem; color: #fff; }
        .thumb:hover { border-color: rgba(255,255,255,.3); }
        .thumb.current { border-color: var(--accent); background: color-mix(in srgb, var(--accent) 14%, transparent); }
        .thumb small { font-size: .62rem; letter-spacing: .16em; text-transform: uppercase; color: rgba(255,255,255,.45); font-weight: 700; }
        .thumb span { font-size: .82rem; line-height: 1.35; color: rgba(255,255,255,.85); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

        #jumpBox { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 70; display: none; padding: 1rem 1.5rem; border-radius: 1rem; background: rgba(0,0,0,.85); border: 1px solid var(--accent); font-size: 2rem; font-weight: 700; font-variant-numeric: tabular-nums; }
        #jumpBox.on { display: block; }
    </style>
</head>
<body>
<div class="stage" id="stage">
    <div class="stage-bg" aria-hidden="true"></div>
    <div class="vignette" aria-hidden="true"></div>

    {{-- Holding slide: shown while the congregation gathers --}}
    <section class="slide" data-kind="Welcome" data-label="{{ $sheet->theme ?: 'Welcome' }}">
        <div class="w-[clamp(4rem,8vw,7rem)] h-[clamp(4rem,8vw,7rem)] rounded-3xl flex items-center justify-center p-4 mb-8"
             style="background: linear-gradient(135deg, var(--crimson), #6b0f1d); box-shadow: 0 20px 60px -20px var(--crimson)">
            <x-umc-logo class="w-full h-full" />
        </div>
        <p class="eyebrow">Africa University · Kwang-Lim Chapel</p>
        <h1 class="display h-hero mt-5">Welcome to worship</h1>
        <div class="rule"></div>
        <p class="meta">Please silence your phones as we prepare our hearts.</p>
        <p class="serif mt-10 tabular-nums" style="font-size: clamp(2rem, 4vw, 4rem); font-weight: 300; color: rgba(255,255,255,.8)" data-clock></p>
    </section>

    {{-- Theme --}}
    <section class="slide" data-kind="Theme" data-label="{{ $sheet->theme ?: 'Worship & Fellowship' }}">
        <p class="eyebrow">{{ $sheet->service_type }} · {{ $sheet->service_date->format('l, j F Y') }}</p>
        <h1 class="display h-hero mt-6 max-w-[18ch]">{{ $sheet->theme ?: 'Worship & Fellowship' }}</h1>
        <div class="rule"></div>
        @if($sheet->preacher)
            <p class="meta uppercase tracking-[.25em] text-[clamp(.7rem,1.1vw,1rem)]">Preacher of the Word</p>
            <p class="serif mt-2" style="font-size: clamp(1.6rem, 3vw, 3rem)">{{ $sheet->preacher }}</p>
        @endif
        <p class="tag"><span class="tag-num">{{ strtoupper($seasonName) }}</span></p>
    </section>

    {{-- Scripture --}}
    <section class="slide" data-kind="Scripture" data-label="{{ $sheet->scripture ?: 'Scripture reading' }}">
        <p class="eyebrow">The Word of God</p>
        <h2 class="display h-big mt-6">{{ $sheet->scripture ?: 'Scripture to be announced' }}</h2>
        <div class="rule"></div>
        <p class="serif italic meta max-w-[40ch]" style="font-size: clamp(1.1rem, 2vw, 2rem)">“Your word is a lamp to my feet and a light to my path.”</p>
        <p class="meta mt-2 text-[clamp(.75rem,1.1vw,1rem)]">Psalm 119:105</p>
        @if($sheet->bible_reader)
            <p class="tag"><span class="tag-num">READER</span><span class="tag-title">{{ $sheet->bible_reader }}</span></p>
        @endif
    </section>

    {{-- Ministers --}}
    <section class="slide" data-kind="Ministers" data-label="Ministers of the service">
        <p class="eyebrow">Serving among us today</p>
        <div class="rule"></div>
        <div class="card grid grid-cols-2 gap-x-[5vw] gap-y-[4vh] text-left px-[4vw] py-[5vh] w-full max-w-[min(1100px,86vw)]">
            @foreach([
                ['Preacher', $sheet->preacher ?: 'To be announced'],
                ['Liturgist', $sheet->liturgist ?: 'Chaplaincy Team'],
                ['Bible Reader', $sheet->bible_reader ?: 'Student Representative'],
                ['Choir · Praise', $sheet->choir ?: ($sheet->praise_worship_team ?: 'Praise & Worship Team')],
            ] as [$role, $name])
                <div>
                    <p class="meta uppercase tracking-[.22em] text-[clamp(.65rem,1vw,.95rem)] font-bold">{{ $role }}</p>
                    <p class="serif mt-1" style="font-size: clamp(1.3rem, 2.5vw, 2.6rem)">{{ $name }}</p>
                </div>
            @endforeach
        </div>
        @if($sheet->sign_interpreter_required)
            <p class="meta mt-6">Sign-language interpretation by <strong class="text-white">{{ $sheet->sign_interpreter_name ?: 'our interpreter' }}</strong></p>
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
            <p class="eyebrow">Let us sing</p>
            @if($hymn['label'])
                <p class="serif mt-6 tabular-nums" style="font-size: clamp(3rem, 9vw, 9rem); font-weight: 300; line-height: 1; color: color-mix(in srgb, var(--accent) 60%, #fff)">{{ preg_replace('/^\D+/', '', $hymn['label']) }}</p>
            @endif
            <h2 class="display h-big mt-4 max-w-[20ch]">{{ $hymn['title'] }}</h2>
            <div class="rule"></div>
            <p class="meta">
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
        <p class="eyebrow">The Benediction</p>
        <div class="rule"></div>
        <p class="lyric fit-lg max-w-[24ch]" data-fit>The Lord bless you and keep you;
the Lord make his face to shine upon you, and be gracious to you;
the Lord lift up his countenance upon you, and give you peace.</p>
        <p class="meta mt-8">Numbers 6:24–26</p>
        <p class="tag"><span class="tag-num">GO IN PEACE</span><span class="tag-title">to love and serve the Lord</span></p>
    </section>

    <div class="overlay" id="blackout" aria-hidden="true"></div>
    <div class="overlay" id="logoScreen" aria-hidden="true">
        <div class="w-[clamp(5rem,10vw,9rem)] h-[clamp(5rem,10vw,9rem)] rounded-3xl flex items-center justify-center p-5"
             style="background: linear-gradient(135deg, var(--crimson), #6b0f1d)">
            <x-umc-logo class="w-full h-full" />
        </div>
        <p class="eyebrow">Kwang-Lim Chapel · Africa University</p>
    </div>
</div>

<div class="progress" id="progress" style="width: 0"></div>

<header class="chrome chrome-top">
    <div class="flex items-center gap-3">
        <span class="w-2 h-2 rounded-full" style="background: var(--accent); box-shadow: 0 0 10px var(--accent)"></span>
        <div class="leading-tight">
            <p class="text-[11px] font-bold uppercase tracking-[.2em] text-white/80">Presenter</p>
            <p class="text-[11px] text-white/45">{{ $seasonName }} · {{ $sheet->service_date->format('j M Y') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs text-white/55 tabular-nums mr-2" title="Time since the service started">⏱ <span id="elapsed">00:00</span></span>
        <span class="text-sm font-semibold tabular-nums mr-3" data-clock></span>
        <button class="btn" id="projectorBtn" onclick="Deck.openProjector()" title="Open a clean output window for the projector screen">Projector window <kbd>P</kbd></button>
        <button class="btn" onclick="Deck.toggleFullscreen()">Fullscreen <kbd>F</kbd></button>
        <a class="btn" href="{{ route('worship.show', $sheet->id) }}">Exit</a>
    </div>
</header>

<footer class="chrome chrome-bottom">
    <div class="flex items-center gap-2">
        <button class="btn" onclick="Deck.prev()" aria-label="Previous slide">← Prev</button>
        <button class="btn primary" onclick="Deck.next()" aria-label="Next slide">Next →</button>
        <span class="text-xs text-white/60 tabular-nums ml-2" id="counter"></span>
    </div>
    <div class="hidden md:flex items-center gap-2 min-w-0 flex-1 justify-center">
        <span class="text-[10px] uppercase tracking-[.2em] text-white/40 font-bold shrink-0">Up next</span>
        <span class="text-xs text-white/75 truncate max-w-[40ch]" id="upNext"></span>
    </div>
    <div class="flex items-center gap-2">
        <button class="btn" id="overviewBtn" onclick="Deck.toggleOverview()">Overview <kbd>G</kbd></button>
        <button class="btn" id="logoBtn" onclick="Deck.setOverlay('logo')">Logo <kbd>L</kbd></button>
        <button class="btn" id="blackBtn" onclick="Deck.setOverlay('black')">Black <kbd>B</kbd></button>
    </div>
</footer>

<div id="overview" role="dialog" aria-label="All slides">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="serif text-2xl">Order of service</h2>
            <p class="text-xs text-white/50">Click a slide, or type its number and press <kbd>Enter</kbd> · <kbd>Esc</kbd> to close</p>
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
