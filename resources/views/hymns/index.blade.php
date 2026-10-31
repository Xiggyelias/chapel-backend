@extends('layouts.app')

@section('title', 'The United Methodist Hymnal & Chapel Hymnody')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">

    <!-- Hero Header with Hymnal Book Motif -->
    <div class="bg-gradient-to-r from-[#991B1B] via-[#DC2626] to-[#7F1D1D] rounded-3xl p-6 sm:p-8 text-white card-elevation relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2.5 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-black/25 border border-white/20 text-rose-200 text-xs font-semibold uppercase tracking-wider font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>{{ number_format($totalHymns - $missingByHymnal->sum()) }} of {{ number_format($totalHymns) }} hymn texts entered</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight font-cinzel text-white">
                    The United Methodist Hymnal
                </h1>
                <p class="text-sm sm:text-base text-rose-100/90 leading-relaxed">
                    Every hymn is catalogued by number, title, author and tune. Look up any number, or search themes and tunes for Sunday and Midweek services. Hymns marked "lyrics not yet entered" can be completed by the Chaplaincy Office from the chapel's licensed printed hymnals.
                </p>
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs text-rose-200">
                    <span class="font-bold uppercase tracking-wider text-[11px] text-white">Chapel Classics:</span>
                    <a href="{{ route('hymns.index', ['search' => '57']) }}" class="bg-black/20 hover:bg-black/35 px-2.5 py-1 rounded-lg border border-white/10 text-white transition">UMH 57: O For a Thousand Tongues to Sing</a>
                    <a href="{{ route('hymns.index', ['search' => '378']) }}" class="bg-black/20 hover:bg-black/35 px-2.5 py-1 rounded-lg border border-white/10 text-white transition">UMH 378: Amazing Grace</a>
                    <a href="{{ route('hymns.index', ['search' => '64']) }}" class="bg-black/20 hover:bg-black/35 px-2.5 py-1 rounded-lg border border-white/10 text-white transition">UMH 64: Holy, Holy, Holy</a>
                    <a href="{{ route('hymns.index', ['search' => '1', 'hymnal' => 'MHB']) }}" class="bg-black/20 hover:bg-black/35 px-2.5 py-1 rounded-lg border border-white/10 text-white transition">MHB 1</a>
                    <a href="{{ route('hymns.index', ['search' => '1', 'hymnal' => 'SHONA']) }}" class="bg-black/20 hover:bg-black/35 px-2.5 py-1 rounded-lg border border-white/10 text-white transition">Shona 1</a>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                @if(auth()->user() && auth()->user()->isOffice())
                    <a href="{{ route('worship.create') }}" class="px-5 py-3 rounded-2xl bg-white text-[#DC2626] hover:bg-rose-50 font-bold text-xs uppercase tracking-wider transition shadow-lg flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>New Service Sheet</span>
                    </a>
                @endif
                @if(!auth()->user() || !auth()->user()->isCampusLifeSrc())
                <a href="{{ route('worship.index') }}" class="px-5 py-3 rounded-2xl bg-black/25 hover:bg-black/40 text-white border border-white/20 font-bold text-xs uppercase tracking-wider transition flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Worship Sheets</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 card-elevation space-y-4">
        <form method="GET" action="{{ route('hymns.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-5 relative">
                <label for="hymnSearch" class="sr-only">Search hymns</label>
                <input type="text" id="hymnSearch" name="search" value="{{ $search }}" placeholder="Search by hymn number (e.g. 1, 57, 140, 378, 734, 984) or title / lyric..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626] font-medium">
                <div class="absolute left-3.5 top-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="sm:col-span-3">
                <label for="hymnalFilter" class="sr-only">Hymnal collection</label>
                <select id="hymnalFilter" name="hymnal" onchange="this.form.submit()" class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626] bg-white text-slate-800 font-medium">
                    <option value="">All Hymnal Collections ({{ $totalHymns }})</option>
                    <option value="UMH" {{ $hymnal === 'UMH' ? 'selected' : '' }}>The United Methodist Hymnal (1–734)</option>
                    <option value="MHB" {{ $hymnal === 'MHB' ? 'selected' : '' }}>The Methodist Hymn Book (1–984)</option>
                    <option value="SHONA" {{ $hymnal === 'SHONA' ? 'selected' : '' }}>Shona Methodist Hymnal (1–311)</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="lyricsFilter" class="sr-only">Lyrics status</label>
                <select id="lyricsFilter" name="lyrics" onchange="this.form.submit()" class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626] bg-white text-slate-800 font-medium">
                    <option value="">Any lyrics status</option>
                    <option value="available" @selected($lyricsFilter === 'available')>Lyrics entered</option>
                    <option value="missing" @selected($lyricsFilter === 'missing')>Lyrics not yet entered ({{ $missingByHymnal->sum() }})</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center space-x-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                    Search
                </button>
                @if($search || $hymnal || $lyricsFilter)
                    <a href="{{ route('hymns.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Clear Filters" aria-label="Clear filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>

        <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t border-slate-100">
            <span>Showing {{ $hymns->total() }} hymn entries ({{ $totalHymns }} total across all 3 hymnals)</span>
            <span class="font-mono text-[11px]">Kwang-Lim Chapel &bull; Africa University</span>
        </div>
    </div>

    <!-- Hymns Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($hymns as $hymn)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 card-elevation hover:border-[#DC2626]/40 transition group flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center space-x-2.5">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono {{ $hymn->hymnal_code === 'SHONA' ? 'bg-amber-100 text-amber-800' : ($hymn->hymnal_code === 'MHB' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-[#DC2626]') }}">
                                {{ $hymn->hymnal_code }} {{ $hymn->number }}
                            </span>
                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">{{ $hymn->category ?: 'General Worship' }}</span>
                        </div>
                        @if($hymn->tune)
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600">Tune: {{ $hymn->tune }}</span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-[#DC2626] transition font-cinzel mt-2.5 leading-snug">
                        {{ $hymn->title }}
                    </h3>

                    <div class="text-[11px] text-slate-500 mt-1 space-x-2">
                        <span>{{ $hymn->author ?: 'Traditional' }}</span>
                        @if($hymn->scripture_ref)
                            <span>&bull;</span>
                            <span class="italic text-slate-600">{{ $hymn->scripture_ref }}</span>
                        @endif
                    </div>

                    <!-- Lyrics Preview -->
                    @if($hymn->hasLyrics())
                        <div class="mt-3 p-3 bg-slate-50 rounded-xl border border-slate-200/70 font-mono text-xs text-slate-700 leading-relaxed whitespace-pre-line line-clamp-4">{{ $hymn->lyrics }}</div>
                    @else
                        <div class="mt-3 p-3 rounded-xl border border-dashed border-amber-300 bg-amber-50/60 text-xs text-amber-900">
                            Lyrics not yet entered. Please sing from the printed hymnal{{ $canEdit ? ', or enter the text from the chapel\'s licensed copy.' : '.' }}
                        </div>
                    @endif
                </div>

                <!-- Action Toolbar -->
                @php
                    $hymnPayload = [
                        'badge' => trim(($hymn->hymnal_code ?? '') . ' ' . $hymn->number),
                        'title' => $hymn->title,
                        'author' => $hymn->author ?: 'Traditional',
                        'tune' => $hymn->tune,
                        'lyrics' => $hymn->hasLyrics() ? $hymn->lyrics : '',
                    ];
                @endphp
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 text-xs" data-hymn="{{ json_encode($hymnPayload) }}">
                    @if($canEdit)
                        <button type="button" onclick="openHymnEditor(this)"
                            data-update-url="{{ route('hymns.update', $hymn->id) }}"
                            data-title="{{ $hymn->title }}" data-author="{{ $hymn->author }}" data-tune="{{ $hymn->tune }}"
                            data-lyrics="{{ $hymn->hasLyrics() ? $hymn->lyrics : '' }}" data-source="{{ $hymn->lyrics_source }}"
                            data-label="{{ $hymn->hymnal_code }} {{ $hymn->number }}"
                            class="px-2.5 py-1 rounded-lg font-semibold {{ $hymn->hasLyrics() ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-amber-500 hover:bg-amber-600 text-white' }}">
                            {{ $hymn->hasLyrics() ? 'Edit' : 'Enter lyrics' }}
                        </button>
                    @endif
                    @if($hymn->hasLyrics())
                    <button type="button" onclick="openHymnLyricsModal(this)" class="font-bold text-[#DC2626] hover:underline flex items-center space-x-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>View All Stanzas</span>
                    </button>

                    <button type="button" onclick="copyHymnLyrics(this)" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Copy Stanzas" aria-label="Copy lyrics of {{ $hymn->title }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 bg-white rounded-3xl border border-slate-200 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-red-50 text-[#DC2626] flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="text-base font-bold text-slate-900">No hymns matched your search</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Try entering a different hymn number or title keyword.</p>
                <a href="{{ route('hymns.index') }}" class="inline-block mt-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Clear search</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($hymns->hasPages())
        <div class="mt-6">
            {{ $hymns->links() }}
        </div>
    @endif
</div>

<!-- Full Hymn Lyrics Modal -->
<div id="hymnDetailModal" role="dialog" aria-modal="true" aria-labelledby="modalHymnTitle" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" onclick="if (event.target === this) closeHymnDetailModal()">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 space-y-5 shadow-2xl border border-slate-200 relative animate-fade-in">
        <button type="button" onclick="closeHymnDetailModal()" aria-label="Close hymn" class="absolute top-5 right-5 p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div>
            <span id="modalHymnBadge" class="px-2.5 py-1 rounded text-xs font-bold bg-[#DC2626] text-white"></span>
            <h3 id="modalHymnTitle" class="text-xl sm:text-2xl font-bold font-cinzel text-slate-900 mt-2"></h3>
            <div id="modalHymnMeta" class="text-xs text-slate-500 mt-1 space-x-2"></div>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-2 font-cinzel">Hymn Lyrics &amp; Stanzas</span>
            <div id="modalHymnLyrics" class="bg-slate-50 p-4 rounded-2xl border border-slate-200 font-serif text-sm sm:text-base text-slate-900 leading-relaxed whitespace-pre-wrap max-h-96 overflow-y-auto select-all"></div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <button type="button" id="modalCopyBtn" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span>Copy All Stanzas</span>
            </button>
            <button type="button" onclick="closeHymnDetailModal()" class="px-5 py-2 bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold text-xs rounded-xl transition cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function readHymn(el) {
        const holder = el.closest('[data-hymn]');
        try { return JSON.parse(holder ? holder.dataset.hymn : '{}'); } catch (e) { return {}; }
    }

    function copyToClipboard(text, okMessage) {
        if (!navigator.clipboard) {
            alert('Clipboard is not available in this browser. Please select and copy the lyrics manually.');
            return;
        }
        navigator.clipboard.writeText(text || '')
            .then(() => alert(okMessage))
            .catch(() => alert('Could not copy to clipboard. Please select and copy the lyrics manually.'));
    }

    function copyHymnLyrics(btn) {
        const hymn = readHymn(btn);
        copyToClipboard(hymn.lyrics, 'Copied lyrics of ' + (hymn.badge || '') + ' ' + (hymn.title || '') + ' to clipboard!');
    }

    let hymnModalOpener = null;
    function openHymnLyricsModal(btn) {
        const hymn = readHymn(btn);
        hymnModalOpener = btn;
        document.getElementById('modalHymnBadge').textContent = hymn.badge || '';
        document.getElementById('modalHymnTitle').textContent = hymn.title || '';

        const meta = document.getElementById('modalHymnMeta');
        meta.replaceChildren();
        const author = document.createElement('span');
        author.textContent = hymn.author || 'Traditional';
        meta.appendChild(author);
        if (hymn.tune) {
            const tune = document.createElement('span');
            tune.className = 'font-mono';
            tune.textContent = '\u2022 Tune: ' + hymn.tune;
            meta.appendChild(tune);
        }

        document.getElementById('modalHymnLyrics').textContent = hymn.lyrics || '';
        document.getElementById('modalCopyBtn').onclick = function() {
            copyToClipboard(hymn.lyrics, 'Copied all stanzas to clipboard!');
        };

        document.getElementById('hymnDetailModal').classList.remove('hidden');
        document.querySelector('#hymnDetailModal button').focus();
    }

    function closeHymnDetailModal() {
        document.getElementById('hymnDetailModal').classList.add('hidden');
        if (hymnModalOpener) hymnModalOpener.focus();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('hymnDetailModal').classList.contains('hidden')) {
            closeHymnDetailModal();
        }
    });
</script>
@if($canEdit)
<!-- Office hymn text editor -->
<div id="hymnEditorModal" role="dialog" aria-modal="true" aria-labelledby="hymnEditorTitle" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4" onclick="if (event.target === this) this.classList.add('hidden')">
    <form id="hymnEditorForm" method="POST" class="bg-white rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 text-xs">
        @csrf
        @method('PUT')
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 id="hymnEditorTitle" class="text-lg font-bold text-slate-900">Edit hymn</h3>
                <p class="text-slate-500 mt-0.5">Enter the text exactly as printed in the chapel's licensed hymnal. Lyrics appear in the hymn picker and projection slides.</p>
            </div>
            <button type="button" onclick="document.getElementById('hymnEditorModal').classList.add('hidden')" aria-label="Close editor" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100">&times;</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-3">
                <label for="editorTitle" class="block font-semibold text-slate-700 mb-1">Title</label>
                <input id="editorTitle" name="title" required maxlength="255" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>
            <div class="sm:col-span-2">
                <label for="editorAuthor" class="block font-semibold text-slate-700 mb-1">Author / translator</label>
                <input id="editorAuthor" name="author" maxlength="255" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>
            <div>
                <label for="editorTune" class="block font-semibold text-slate-700 mb-1">Tune</label>
                <input id="editorTune" name="tune" maxlength="100" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>
        </div>
        <div>
            <label for="editorLyrics" class="block font-semibold text-slate-700 mb-1">Lyrics <span class="font-normal text-slate-500">(separate stanzas with a blank line)</span></label>
            <textarea id="editorLyrics" name="lyrics" rows="12" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono leading-relaxed"></textarea>
        </div>
        <div>
            <label for="editorSource" class="block font-semibold text-slate-700 mb-1">Source &amp; licence</label>
            <input id="editorSource" name="lyrics_source" maxlength="255" placeholder="e.g. Printed UMH (1989), CCLI licence 123456" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            <p class="text-slate-500 mt-1">Required when lyrics are entered. Copyrighted texts may only be stored under the chapel's reprint licence.</p>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="document.getElementById('hymnEditorModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 font-semibold">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold">Save hymn</button>
        </div>
    </form>
</div>
<script>
    function openHymnEditor(button) {
        const d = button.dataset;
        const form = document.getElementById('hymnEditorForm');
        form.action = d.updateUrl;
        document.getElementById('hymnEditorTitle').textContent = 'Edit ' + d.label;
        document.getElementById('editorTitle').value = d.title || '';
        document.getElementById('editorAuthor').value = d.author || '';
        document.getElementById('editorTune').value = d.tune || '';
        document.getElementById('editorLyrics').value = d.lyrics || '';
        document.getElementById('editorSource').value = d.source || '';
        document.getElementById('hymnEditorModal').classList.remove('hidden');
        document.getElementById('editorLyrics').focus();
    }
</script>
@endif
@endsection
