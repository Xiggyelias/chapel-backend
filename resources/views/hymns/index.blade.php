@extends('layouts.app')

@section('title', 'Hymnary')

@section('content')
@php
    $hymnalMeta = [
        'UMH'   => ['label' => 'United Methodist Hymnal', 'short' => 'UMH', 'range' => '1–734',  'color' => 'brand',  'dot' => '#A51C30'],
        'MHB'   => ['label' => 'Methodist Hymn Book',     'short' => 'MHB', 'range' => '1–984',  'color' => 'sky',    'dot' => '#0369A1'],
        'SHONA' => ['label' => 'Shona Methodist Hymnal',  'short' => 'SHO', 'range' => '1–311',  'color' => 'amber',  'dot' => '#B45309'],
    ];
    $totalWithLyrics = $totalHymns - $missingByHymnal->sum();
    $pctComplete = $totalHymns ? round(100 * $totalWithLyrics / $totalHymns) : 0;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

    {{-- ═══ HEADER CARD ═══ --}}
    <div class="relative overflow-hidden rounded-2xl bg-ink text-white p-6 sm:p-8">
        {{-- cross-hatch pattern --}}
        <div class="absolute inset-0 opacity-[0.035]" aria-hidden="true"
             style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22%3E%3Crect width=%2240%22 height=%2240%22 fill=%22none%22/%3E%3Cpath d=%22M0 20h40M20 0v40%22 stroke=%22%23fff%22 stroke-width=%220.5%22/%3E%3C/svg%3E')">
        </div>
        {{-- gold accent bar --}}
        <div class="absolute bottom-0 left-0 right-0 h-0.5"
             style="background: linear-gradient(90deg, transparent, var(--color-gold) 30%, var(--color-brand) 70%, transparent)"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div>
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-brand/25 ring-1 ring-brand/40 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-brand" style="color:#E8798A" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gold opacity-80">Kwang-Lim Chapel · Africa University</p>
                        <h1 class="text-xl sm:text-2xl font-display font-semibold text-white leading-tight">Hymnary</h1>
                    </div>
                </div>
                <p class="text-sm text-white/50 leading-relaxed max-w-lg">
                    Three hymnals · {{ number_format($totalWithLyrics) }} of {{ number_format($totalHymns) }} hymn texts entered.
                    Hymns without text may be sung from the printed copies in the chapel.
                </p>

                {{-- hymnal quick-links --}}
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach($hymnalMeta as $code => $meta)
                        <a href="{{ route('hymns.index', ['hymnal' => $code]) }}"
                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition
                               {{ ($hymnal === $code) ? 'bg-brand text-white ring-1 ring-brand/40' : 'bg-white/8 hover:bg-white/14 text-white/70 border border-white/10' }}">
                            <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $meta['dot'] }}"></span>
                            {{ $meta['short'] }} <span class="opacity-60">{{ $meta['range'] }}</span>
                        </a>
                    @endforeach
                    @if($hymnal)
                        <a href="{{ route('hymns.index') }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-white/5 hover:bg-white/10 text-white/50 border border-white/8 transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            All hymnals
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                @if(auth()->user()?->isOffice())
                    <a href="{{ route('worship.create') }}"
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-semibold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New service sheet
                    </a>
                @endif
                @if(!auth()->user()?->isCampusLifeSrc())
                    <a href="{{ route('worship.index') }}"
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-white/8 hover:bg-white/14 text-white text-xs font-semibold border border-white/12 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Worship sheets
                    </a>
                @endif
            </div>
        </div>

        {{-- per-hymnal progress strip --}}
        <div class="relative mt-5 pt-4 border-t border-white/8 grid grid-cols-3 gap-4">
            @foreach($hymnalMeta as $code => $meta)
                @php
                    $total  = \App\Models\Hymn::where('hymnal_code', $code)->count();
                    $haveL  = \App\Models\Hymn::where('hymnal_code', $code)->where('lyrics_status', 'available')->count();
                    $pct    = $total ? round(100 * $haveL / $total) : 0;
                @endphp
                <div class="text-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-white/70">{{ $meta['short'] }}</span>
                        <span class="font-mono text-white/40">{{ $pct }}%</span>
                    </div>
                    <div class="h-1 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="width:{{ $pct }}%; background:{{ $meta['dot'] }}"></div>
                    </div>
                    <div class="mt-0.5 text-[10px] text-white/35">{{ number_format($haveL) }} / {{ number_format($total) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ SEARCH & FILTER ═══ --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route('hymns.index') }}" id="hymnSearchForm"
              class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">

            {{-- Search --}}
            <div class="sm:col-span-5 relative">
                <label for="hymnSearch" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Search</label>
                <div class="relative">
                    <input type="text" id="hymnSearch" name="search" value="{{ $search }}"
                           placeholder="Hymn number, title or lyric fragment…"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-brand focus:border-brand font-medium bg-white">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Hymnal --}}
            <div class="sm:col-span-3">
                <label for="hymnalFilter" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Hymnal</label>
                <select id="hymnalFilter" name="hymnal" onchange="this.form.submit()"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-brand focus:border-brand bg-white font-medium">
                    <option value="">All collections ({{ $totalHymns }})</option>
                    @foreach($hymnalMeta as $code => $meta)
                        <option value="{{ $code }}" {{ $hymnal === $code ? 'selected' : '' }}>{{ $meta['short'] }} — {{ $meta['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Lyrics status --}}
            <div class="sm:col-span-2">
                <label for="lyricsFilter" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Lyrics</label>
                <select id="lyricsFilter" name="lyrics" onchange="this.form.submit()"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-brand focus:border-brand bg-white font-medium">
                    <option value="">Any status</option>
                    <option value="available" @selected($lyricsFilter === 'available')>Have text</option>
                    <option value="missing"   @selected($lyricsFilter === 'missing')>Missing ({{ $missingByHymnal->sum() }})</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit"
                        class="flex-1 py-2.5 px-4 bg-brand hover:bg-brand-dark text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Search
                </button>
                @if($search || $hymnal || $lyricsFilter)
                    <a href="{{ route('hymns.index') }}"
                       class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl transition"
                       title="Clear" aria-label="Clear filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>

        <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
            <p class="text-[11px] text-slate-400">
                Showing <span class="font-semibold text-slate-600">{{ number_format($hymns->total()) }}</span> entries
                @if($search) for &ldquo;{{ $search }}&rdquo;@endif
            </p>
            <p class="text-[11px] font-mono text-slate-400">{{ $hymns->currentPage() }} / {{ $hymns->lastPage() }} pages</p>
        </div>
    </div>

    {{-- ═══ HYMN CARDS ═══ --}}
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
            @endphp
            <article class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-brand/30 transition group flex flex-col"
                     data-hymn="{{ json_encode($hymnPayload) }}">

                {{-- Card header --}}
                <div class="flex items-start justify-between gap-3 px-5 pt-5 pb-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold font-mono {{ $badgeClasses }}">
                            {{ $hymn->hymnal_code }} {{ $hymn->number }}
                        </span>
                        @if($hymn->category)
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $hymn->category }}</span>
                        @endif
                    </div>
                    @if($hymn->tune)
                        <span class="shrink-0 text-[10px] font-mono font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 uppercase tracking-wide">{{ $hymn->tune }}</span>
                    @endif
                </div>

                {{-- Title + meta --}}
                <div class="px-5 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 font-display group-hover:text-brand transition leading-snug">
                        {{ $hymn->title }}
                    </h3>
                    <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-400">
                        <span>{{ $hymn->author ?: 'Traditional' }}</span>
                        @if($hymn->scripture_ref)
                            <span aria-hidden="true">&bull;</span>
                            <span class="italic text-slate-500">{{ $hymn->scripture_ref }}</span>
                        @endif
                    </div>
                </div>

                {{-- Lyrics preview --}}
                <div class="px-5 pb-4 flex-1">
                    @if($hymn->hasLyrics())
                        <div class="p-3.5 bg-paper rounded-xl border border-slate-200/60 text-xs font-serif text-slate-700 leading-relaxed whitespace-pre-line line-clamp-4">{{ $hymn->lyrics }}</div>
                    @else
                        <div class="p-3.5 rounded-xl border border-dashed border-amber-300/70 bg-amber-50/50 text-xs text-amber-800 flex items-start gap-2">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Lyrics not yet entered — please sing from the printed hymnal{{ $canEdit ? '.' : '.' }}</span>
                        </div>
                    @endif
                </div>

                {{-- Action bar --}}
                <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if($canEdit)
                        <button type="button" onclick="openHymnEditor(this.closest('[data-hymn]'))"
                                class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition
                                    {{ $hymn->hasLyrics() ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-amber-500 hover:bg-amber-600 text-white' }}">
                            {{ $hymn->hasLyrics() ? 'Edit text' : 'Enter lyrics' }}
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if($hymn->hasLyrics())
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="openHymnReader(this.closest('[data-hymn]'))"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-semibold text-brand hover:bg-brand/8 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Read
                            </button>
                            <button type="button" onclick="copyHymnText(this.closest('[data-hymn]'))"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                    title="Copy lyrics" aria-label="Copy lyrics of {{ $hymn->title }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200/80 p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-red-50 text-brand flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900">No hymns matched your search</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Try a different number, title, or lyric fragment.</p>
                <a href="{{ route('hymns.index') }}" class="inline-block mt-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Clear search</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($hymns->hasPages())
        <div class="mt-2">
            {{ $hymns->links() }}
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════
     HYMN READER MODAL
══════════════════════════════════════ --}}
<div id="hymnReaderModal" role="dialog" aria-modal="true" aria-labelledby="readerTitle"
     class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
     onclick="if(event.target===this) closeHymnReader()">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200/60 overflow-hidden animate-fade-in">

        {{-- Modal header --}}
        <div class="bg-ink text-white px-6 py-4 flex items-start justify-between gap-3">
            <div>
                <span id="readerBadge" class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-brand/40 text-white mb-1.5"></span>
                <h3 id="readerTitle" class="text-lg font-display font-semibold text-white leading-tight"></h3>
                <p id="readerMeta" class="text-xs text-white/50 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeHymnReader()" aria-label="Close"
                    class="mt-0.5 p-2 rounded-xl text-white/40 hover:text-white hover:bg-white/10 transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Lyrics body --}}
        <div class="p-6 max-h-[60vh] overflow-y-auto">
            <pre id="readerLyrics" class="font-serif text-sm text-slate-900 leading-loose whitespace-pre-wrap select-all"></pre>
        </div>

        {{-- Footer --}}
        <div class="border-t border-slate-100 px-6 py-3.5 flex items-center justify-between gap-2">
            <button type="button" id="readerCopyBtn"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy all stanzas
            </button>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <button type="button" onclick="closeHymnReader()"
                    class="ml-auto px-4 py-2 bg-brand hover:bg-brand-dark text-white text-xs font-bold rounded-xl transition">
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
     class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
     onclick="if(event.target===this) document.getElementById('hymnEditorModal').classList.add('hidden')">
    <form id="hymnEditorForm" method="POST"
          class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200/60 p-6 space-y-4 text-xs">
        @csrf
        @method('PUT')
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 id="editorHeading" class="text-base font-bold text-slate-900">Edit hymn</h3>
                <p class="text-slate-400 mt-0.5">Enter the text exactly as printed in the chapel's licensed hymnal.</p>
            </div>
            <button type="button" onclick="document.getElementById('hymnEditorModal').classList.add('hidden')"
                    aria-label="Close" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-3">
                <label for="editorTitle" class="block font-semibold text-slate-700 mb-1">Title</label>
                <input id="editorTitle" name="title" required maxlength="255"
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand focus:border-brand bg-white">
            </div>
            <div class="sm:col-span-2">
                <label for="editorAuthor" class="block font-semibold text-slate-700 mb-1">Author / translator</label>
                <input id="editorAuthor" name="author" maxlength="255"
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand focus:border-brand bg-white">
            </div>
            <div>
                <label for="editorTune" class="block font-semibold text-slate-700 mb-1">Tune</label>
                <input id="editorTune" name="tune" maxlength="100"
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand focus:border-brand bg-white">
            </div>
        </div>

        <div>
            <label for="editorLyrics" class="block font-semibold text-slate-700 mb-1">
                Lyrics <span class="font-normal text-slate-400">(separate stanzas with a blank line)</span>
            </label>
            <textarea id="editorLyrics" name="lyrics" rows="12"
                      class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand focus:border-brand font-mono leading-relaxed bg-white"></textarea>
        </div>

        <div>
            <label for="editorSource" class="block font-semibold text-slate-700 mb-1">Source &amp; licence</label>
            <input id="editorSource" name="lyrics_source" maxlength="255"
                   placeholder="e.g. Printed UMH (1989); CCLI licence 123456"
                   class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand focus:border-brand bg-white">
            <p class="text-slate-400 mt-1">Required. Copyrighted texts may only be stored under the chapel's reprint licence.</p>
        </div>

        <div class="flex justify-end gap-2 pt-1">
            <button type="button" onclick="document.getElementById('hymnEditorModal').classList.add('hidden')"
                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 font-semibold transition">Cancel</button>
            <button type="submit"
                    class="px-4 py-2 rounded-xl bg-brand hover:bg-brand-dark text-white font-bold transition">Save hymn</button>
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
     class="fixed bottom-6 right-6 z-50 px-4 py-2.5 bg-ink text-white text-xs font-semibold rounded-xl shadow-xl opacity-0 transition-opacity duration-300 pointer-events-none"
     aria-live="polite"></div>

@endsection
