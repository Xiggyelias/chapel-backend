@extends('layouts.app')

@section('title', 'Hymnary')

@section('content')
@php
    $hymnalMeta = [
        'UMH'   => ['label' => 'The United Methodist Hymnal', 'short' => 'UMH', 'range' => '1–734',  'color' => 'brand',  'dot' => '#DC2626'],
        'MHB'   => ['label' => 'Methodist Hymn Book',         'short' => 'MHB', 'range' => '1–984',  'color' => 'sky',    'dot' => '#0369A1'],
        'SHONA' => ['label' => 'Shona Methodist Hymnal',      'short' => 'SHO', 'range' => '1–311',  'color' => 'amber',  'dot' => '#B45309'],
    ];
    $totalWithLyrics = $totalHymns - $missingByHymnal->sum();
    $pctComplete = $totalHymns ? round(100 * $totalWithLyrics / $totalHymns) : 0;
@endphp

<div class="page-shell space-y-6">

    <x-page-header eyebrow="Kwang-Lim Chapel · Africa University" title="Hymnary"
                   description="Three hymnals · {{ number_format($totalWithLyrics) }} of {{ number_format($totalHymns) }} hymn texts entered. Hymns without text may be sung from the printed copies in the chapel.">
        @if(!auth()->user()?->isCampusLifeSrc())
            <a href="{{ route('worship.index') }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Worship sheets
            </a>
        @endif
        @if(auth()->user()?->isOffice())
            <a href="{{ route('worship.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New service sheet
            </a>
        @endif
    </x-page-header>

    {{-- ═══ The three volumes ═══ --}}
    <section class="space-y-4" aria-label="Hymnals">
        <nav class="segmented max-w-full overflow-x-auto" aria-label="Choose a hymnal">
            <a href="{{ route('hymns.index') }}" @if(!$hymnal) aria-current="page" @endif class="whitespace-nowrap">All hymnals</a>
            @foreach($hymnalMeta as $code => $meta)
                <a href="{{ route('hymns.index', ['hymnal' => $code]) }}" @if($hymnal === $code) aria-current="page" @endif class="whitespace-nowrap inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background: {{ $meta['dot'] }}" aria-hidden="true"></span>
                    {{ $meta['short'] }} <span class="font-medium opacity-60 tabular-nums">{{ $meta['range'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($hymnalMeta as $code => $meta)
                @php
                    $total  = \App\Models\Hymn::where('hymnal_code', $code)->count();
                    $haveL  = \App\Models\Hymn::where('hymnal_code', $code)->where('lyrics_status', 'available')->count();
                    $pct    = $total ? round(100 * $haveL / $total) : 0;
                    $isOpen = $hymnal === $code;
                @endphp
                <a href="{{ route('hymns.index', ['hymnal' => $code]) }}"
                   class="card group relative flex flex-col overflow-hidden p-5 pl-6 transition hover:shadow-md {{ $isOpen ? 'ring-1 ring-gold/50' : '' }}"
                   @if($isOpen) aria-current="true" @endif>
                    {{-- the spine of the volume --}}
                    <span class="absolute left-0 inset-y-0 w-[5px]" style="background: linear-gradient(180deg, {{ $meta['dot'] }}, color-mix(in srgb, {{ $meta['dot'] }} 70%, black));" aria-hidden="true"></span>
                    <span class="absolute left-[5px] inset-y-0 w-px bg-gold-light/60" aria-hidden="true"></span>
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="card-kicker">{{ $meta['short'] }} · Nos. {{ $meta['range'] }}</p>
                            <h2 class="mt-1 font-display text-[1.15rem] leading-snug text-slate-900 group-hover:text-brand transition-colors">{{ $meta['label'] }}</h2>
                        </div>
                        <span class="stat-value text-[1.6rem] text-slate-900 shrink-0">{{ $pct }}%</span>
                    </div>
                    <div class="mt-auto pt-4">
                        <div class="progress"><span style="width: {{ $pct }}%"></span></div>
                        <p class="mt-2 text-[12.5px] text-slate-500 tabular-nums">{{ number_format($haveL) }} / {{ number_format($total) }} texts entered</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ═══ Search & filter ═══ --}}
    <section class="card">
        <form method="GET" action="{{ route('hymns.index') }}" id="hymnSearchForm"
              class="card-body grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">

            {{-- Search --}}
            <div class="sm:col-span-5">
                <label for="hymnSearch" class="field-label">Search</label>
                <div class="relative">
                    <input type="text" id="hymnSearch" name="search" value="{{ $search }}"
                           placeholder="Hymn number, title or lyric fragment…"
                           class="field !pl-10">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Hymnal --}}
            <div class="sm:col-span-3">
                <label for="hymnalFilter" class="field-label">Hymnal</label>
                <select id="hymnalFilter" name="hymnal" onchange="this.form.submit()" class="field">
                    <option value="">All collections ({{ $totalHymns }})</option>
                    @foreach($hymnalMeta as $code => $meta)
                        <option value="{{ $code }}" {{ $hymnal === $code ? 'selected' : '' }}>{{ $meta['short'] }} — {{ $meta['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Lyrics status --}}
            <div class="sm:col-span-2">
                <label for="lyricsFilter" class="field-label">Lyrics</label>
                <select id="lyricsFilter" name="lyrics" onchange="this.form.submit()" class="field">
                    <option value="">Any status</option>
                    <option value="available" @selected($lyricsFilter === 'available')>Have text</option>
                    <option value="missing"   @selected($lyricsFilter === 'missing')>Missing ({{ $missingByHymnal->sum() }})</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    Search
                </button>
                @if($search || $hymnal || $lyricsFilter)
                    <a href="{{ route('hymns.index') }}" class="btn btn-ghost !px-2.5" title="Clear" aria-label="Clear filters">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>

        <div class="flex items-center justify-between gap-3 px-5 py-3 border-t border-slate-100 text-[12px] text-slate-500">
            <p>
                Showing <span class="font-semibold text-slate-800 tabular-nums">{{ number_format($hymns->total()) }}</span> entries
                @if($search) for <span class="font-display italic text-slate-800">&ldquo;{{ $search }}&rdquo;</span>@endif
            </p>
            <p class="tabular-nums text-slate-400">{{ $hymns->currentPage() }} / {{ $hymns->lastPage() }} pages</p>
        </div>
    </section>

    {{-- ═══ Hymns ═══ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($hymns as $hymn)
            @php
                $badgeClasses = match($hymn->hymnal_code) {
                    'MHB'   => 'bg-sky-100 text-sky-800',
                    'SHONA' => 'bg-amber-100 text-amber-800',
                    default => 'bg-red-50 text-brand',
                };
                $hymnPayload = [
                    'id'     => $hymn->id,
                    'badge'  => trim(($hymn->hymnal_code ?? '') . ' ' . $hymn->number),
                    'title'  => $hymn->title,
                    'author' => $hymn->author ?: 'Traditional',
                    'tune'   => $hymn->tune,
                    'lyrics' => $hymn->hasLyrics() ? $hymn->lyrics : '',
                    'updateUrl' => auth()->user()?->isOffice() ? route('hymns.update', $hymn->id) : '',
                    'source' => $hymn->lyrics_source,
                ];
                $dot = $hymnalMeta[$hymn->hymnal_code]['dot'] ?? '#DC2626';
            @endphp
            <article class="card group flex flex-col hover:shadow-md" data-hymn="{{ json_encode($hymnPayload) }}">

                {{-- Number · title · meta --}}
                <div class="flex items-start gap-4 px-5 pt-5 pb-4">
                    <div class="w-16 shrink-0 text-center">
                        <p class="text-[12.5px] font-semibold text-slate-500 inline-flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full" style="background: {{ $dot }}" aria-hidden="true"></span>{{ $hymn->hymnal_code }}
                        </p>
                        <p class="font-display text-[2rem] leading-none text-slate-900 tabular-nums tracking-[-0.03em] mt-1" style="font-variation-settings: 'SOFT' 30, 'opsz' 72;">{{ $hymn->number }}</p>
                    </div>
                    <div class="min-w-0 flex-1 border-l border-slate-100 pl-4">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-display text-[1.125rem] leading-snug text-slate-900 group-hover:text-brand transition-colors">
                                {{ $hymn->title }}
                            </h3>
                            @if($hymn->tune)
                                <span class="shrink-0 mt-0.5 text-[12.5px] font-semibold text-gold">{{ $hymn->tune }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-[12px] text-slate-500">{{ $hymn->author ?: 'Traditional' }}</p>
                        @if($hymn->scripture_ref)
                            <p class="mt-1 font-display italic text-[12.5px] leading-snug text-slate-500 line-clamp-2" title="{{ $hymn->scripture_ref }}">{{ $hymn->scripture_ref }}</p>
                        @endif
                        @if($hymn->category)
                            <span class="mt-2 inline-block text-[12px] font-semibold text-slate-500">{{ $hymn->category }}</span>
                        @endif
                    </div>
                </div>

                {{-- Lyrics preview --}}
                <div class="px-5 pb-5 flex-1">
                    @if($hymn->hasLyrics())
                        <div class="relative pl-4 font-display text-[14px] text-slate-700 leading-[1.75] whitespace-pre-line line-clamp-4" style="font-variation-settings: 'SOFT' 80, 'opsz' 14;">
                            <span class="absolute left-0 top-1 bottom-1 w-px bg-gold-light" aria-hidden="true"></span>{{ $hymn->lyrics }}</div>
                    @else
                        <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-slate-50 ring-1 ring-slate-200/70 text-[12.5px] text-slate-500">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span>Lyrics not yet entered — please sing from the printed hymnal{{ $canEdit ? '.' : '.' }}</span>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="px-4 py-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if($canEdit)
                        <button type="button" onclick="openHymnEditor(this.closest('[data-hymn]'))"
                                class="btn btn-sm {{ $hymn->hasLyrics() ? 'btn-ghost' : 'btn-gold' }}">
                            {{ $hymn->hasLyrics() ? 'Edit text' : 'Enter lyrics' }}
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if($hymn->hasLyrics())
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="openHymnReader(this.closest('[data-hymn]'))"
                                    class="btn btn-ghost btn-sm !text-brand hover:!text-brand-dark">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Read
                            </button>
                            <button type="button" onclick="copyHymnText(this.closest('[data-hymn]'))"
                                    class="btn btn-ghost btn-sm !px-2"
                                    title="Copy lyrics" aria-label="Copy lyrics of {{ $hymn->title }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full card">
                <div class="empty-state">
                    <div class="empty-state-mark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                    </div>
                    <h4 class="empty-state-title">No hymns matched your search</h4>
                    <p class="empty-state-text">Try a different number, title, or lyric fragment.</p>
                    <a href="{{ route('hymns.index') }}" class="btn btn-secondary btn-sm mt-4">Clear search</a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($hymns->hasPages())
        <div>
            {{ $hymns->links() }}
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════
     HYMN READER MODAL
══════════════════════════════════════ --}}
<div id="hymnReaderModal" role="dialog" aria-modal="true" aria-labelledby="readerTitle"
     class="hidden fixed inset-0 z-50 overflow-y-auto bg-ink/60 backdrop-blur-sm flex items-start sm:items-center justify-center p-4 modal-backdrop"
     onclick="if(event.target===this) closeHymnReader()">
    <div class="relative max-w-2xl w-full my-auto rounded-3xl shadow-lg ring-1 ring-black/5 overflow-hidden modal-panel" style="background: #FCFAF6;">

        {{-- Page head --}}
        <div class="relative px-7 sm:px-10 pt-8 pb-6 text-center">
            <button type="button" onclick="closeHymnReader()" aria-label="Close"
                    class="topbar-action absolute right-3 top-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <span id="readerBadge" class="sr-only"></span>
            <span id="readerHymnal" class="inline-block text-[12.5px] font-semibold text-brand" aria-hidden="true"></span>
            <div id="readerNumber" class="mt-2 font-display text-[3.5rem] leading-none text-slate-900 tabular-nums tracking-[-0.03em]" style="font-weight: 400 !important; font-variation-settings: 'SOFT' 30, 'opsz' 144;"></div>
            <h3 id="readerTitle" class="mt-4 font-display text-[1.6rem] leading-tight text-slate-900 text-balance"></h3>
            <p id="readerMeta" class="mt-1.5 text-[12.5px] text-slate-500"></p>
            <div class="rule-ornament text-[9px] max-w-[14rem] mx-auto mt-5" aria-hidden="true">&#9670;</div>
        </div>

        {{-- Lyrics --}}
        <div class="px-7 sm:px-12 pb-8 max-h-[55vh] overflow-y-auto">
            <pre id="readerLyrics" class="font-display text-[1.0625rem] text-slate-800 leading-[1.95] whitespace-pre-wrap select-all max-w-md mx-auto" style="font-variation-settings: 'SOFT' 80, 'opsz' 18;"></pre>
        </div>

        {{-- Footer --}}
        <div class="border-t border-slate-200/70 bg-white/70 px-5 sm:px-7 py-3.5 flex items-center gap-2">
            <button type="button" id="readerCopyBtn" class="btn btn-secondary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy all stanzas
            </button>
            <button type="button" onclick="window.print()" class="btn btn-ghost btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <button type="button" onclick="closeHymnReader()" class="btn btn-ink btn-sm ml-auto">
                Done
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     HYMN EDITOR MODAL (office only)
══════════════════════════════════════ --}}
@if($canEdit)
<div id="hymnEditorModal" role="dialog" aria-modal="true" aria-labelledby="editorHeading"
     class="hidden fixed inset-0 z-50 overflow-y-auto bg-ink/50 backdrop-blur-sm flex items-start sm:items-center justify-center p-4 modal-backdrop"
     onclick="if(event.target===this) document.getElementById('hymnEditorModal').classList.add('hidden')">
    <form id="hymnEditorForm" method="POST"
          class="bg-white rounded-3xl max-w-2xl w-full my-auto shadow-lg ring-1 ring-black/5 p-6 sm:p-7 space-y-4 modal-panel">
        @csrf
        @method('PUT')
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="eyebrow">Hymn text</p>
                <h3 id="editorHeading" class="font-display text-xl text-slate-900">Edit hymn</h3>
                <p class="text-[12.5px] text-slate-500 mt-1">Enter the text exactly as printed in the chapel's licensed hymnal.</p>
            </div>
            <button type="button" onclick="document.getElementById('hymnEditorModal').classList.add('hidden')"
                    aria-label="Close" class="topbar-action -mr-2 -mt-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-3">
                <label for="editorTitle" class="field-label">Title</label>
                <input id="editorTitle" name="title" required maxlength="255" class="field">
            </div>
            <div class="sm:col-span-2">
                <label for="editorAuthor" class="field-label">Author / translator</label>
                <input id="editorAuthor" name="author" maxlength="255" class="field">
            </div>
            <div>
                <label for="editorTune" class="field-label">Tune</label>
                <input id="editorTune" name="tune" maxlength="100" class="field">
            </div>
        </div>

        <div>
            <label for="editorLyrics" class="field-label">
                Lyrics <span class="font-normal text-slate-400">(separate stanzas with a blank line)</span>
            </label>
            <textarea id="editorLyrics" name="lyrics" rows="12"
                      class="field font-display !text-[14px] !leading-[1.75]"></textarea>
        </div>

        <div>
            <label for="editorSource" class="field-label">Source &amp; licence</label>
            <input id="editorSource" name="lyrics_source" maxlength="255"
                   placeholder="e.g. Printed UMH (1989); CCLI licence 123456"
                   class="field">
            <p class="field-hint">Required. Copyrighted texts may only be stored under the chapel's reprint licence.</p>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('hymnEditorModal').classList.add('hidden')"
                    class="btn btn-ghost">Cancel</button>
            <button type="submit" class="btn btn-primary">Save hymn</button>
        </div>
    </form>
</div>
@endif

<script>
(function () {
    function getHymn(el) {
        const holder = el.closest('[data-hymn]');
        try { return JSON.parse(holder ? holder.dataset.hymn : '{}'); } catch { return {}; }
    }

    function copyText(text, label) {
        if (!navigator.clipboard) {
            alert('Please select and copy the lyrics manually.');
            return;
        }
        navigator.clipboard.writeText(text || '')
            .then(() => {
                const msg = document.getElementById('hymnCopyToast');
                if (msg) { msg.textContent = label + ' copied!'; msg.classList.remove('opacity-0'); setTimeout(() => msg.classList.add('opacity-0'), 2000); }
            })
            .catch(() => alert('Could not copy. Please select and copy manually.'));
    }

    // Hymn board: the number rolls onto its card reels (uses the shared .reel / .reel-col styles)
    function rollHymnNumber(el, text) {
        if (!el) return;
        el.textContent = '';
        const label = document.createElement('span');
        label.className = 'sr-only';
        label.textContent = text;
        el.appendChild(label);
        const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce || !/\d/.test(text)) {
            const plain = document.createElement('span');
            plain.setAttribute('aria-hidden', 'true');
            plain.textContent = text;
            el.appendChild(plain);
            return;
        }
        const cols = [];
        [...text].forEach((ch) => {
            if (!/\d/.test(ch)) {
                const s = document.createElement('span');
                s.setAttribute('aria-hidden', 'true');
                s.textContent = ch;
                el.appendChild(s);
                return;
            }
            const reel = document.createElement('span');
            reel.className = 'reel';
            reel.setAttribute('aria-hidden', 'true');
            const col = document.createElement('span');
            col.className = 'reel-col';
            for (let k = 0; k < 20; k++) {
                const d = document.createElement('span');
                d.textContent = String(k % 10);
                col.appendChild(d);
            }
            reel.appendChild(col);
            el.appendChild(reel);
            cols.push([col, parseInt(ch, 10)]);
        });
        requestAnimationFrame(() => requestAnimationFrame(() => {
            cols.forEach(([col, v], i) => {
                col.style.setProperty('--d', (i * 90 + 120) + 'ms');
                col.style.transform = 'translateY(-' + (10 + v) + 'em)';
            });
        }));
    }

    window.copyHymnText = function (el) {
        const h = getHymn(el);
        copyText(h.lyrics, h.badge);
    };

    let readerOpener = null;
    window.openHymnReader = function (el) {
        const h = getHymn(el);
        readerOpener = el;
        document.getElementById('readerBadge').textContent = h.badge || '';
        document.getElementById('readerTitle').textContent = h.title || '';
        const meta = [];
        if (h.author) meta.push(h.author);
        if (h.tune)   meta.push('Tune: ' + h.tune);
        document.getElementById('readerMeta').textContent = meta.join(' · ');
        document.getElementById('readerLyrics').textContent = h.lyrics || '';
        document.getElementById('readerCopyBtn').onclick = function () { copyText(h.lyrics, h.badge); };
        document.getElementById('hymnReaderModal').classList.remove('hidden');
        const badgeParts = String(h.badge || '').split(' ');
        document.getElementById('readerHymnal').textContent = badgeParts.length > 1 ? badgeParts[0] : '';
        rollHymnNumber(document.getElementById('readerNumber'), badgeParts.length > 1 ? badgeParts.slice(1).join(' ') : badgeParts[0]);
        document.getElementById('hymnReaderModal').querySelector('button').focus();
    };

    window.closeHymnReader = function () {
        document.getElementById('hymnReaderModal').classList.add('hidden');
        if (readerOpener) readerOpener.focus();
        readerOpener = null;
    };

    @if($canEdit)
    window.openHymnEditor = function (el) {
        const h = getHymn(el);
        const form = document.getElementById('hymnEditorForm');
        form.action = h.updateUrl;
        document.getElementById('editorHeading').textContent = 'Edit ' + h.badge;
        document.getElementById('editorTitle').value  = h.title  || '';
        document.getElementById('editorAuthor').value = h.author || '';
        document.getElementById('editorTune').value   = h.tune   || '';
        document.getElementById('editorLyrics').value = h.lyrics || '';
        document.getElementById('editorSource').value = h.source || '';
        document.getElementById('hymnEditorModal').classList.remove('hidden');
        document.getElementById('editorLyrics').focus();
    };
    @endif

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('hymnReaderModal').classList.contains('hidden')) closeHymnReader();
            @if($canEdit)
            if (!document.getElementById('hymnEditorModal').classList.contains('hidden'))
                document.getElementById('hymnEditorModal').classList.add('hidden');
            @endif
        }
    });
})();
</script>

{{-- Copy toast --}}
<div id="hymnCopyToast"
     class="fixed bottom-6 right-6 z-50 px-4 py-2.5 bg-ink text-white text-[12.5px] font-semibold rounded-xl shadow-lg ring-1 ring-white/10 opacity-0 transition-opacity duration-300 pointer-events-none"
     aria-live="polite"></div>

@endsection
