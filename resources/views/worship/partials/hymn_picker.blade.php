<!-- The United Methodist Hymnal (UMH), MHB & Shona Interactive Picker & Lyrics Auto-Fill -->
<div class="rounded-2xl bg-paper/70 ring-1 ring-slate-200/80 p-4 sm:p-5 space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center shrink-0 text-gold bg-white ring-1 ring-gold/25 shadow-xs" aria-hidden="true">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="min-w-0">
                <h4 class="font-display text-[15px] leading-tight text-slate-900">The Chapel Hymnals <span class="text-slate-400">(UMH &bull; MHB &bull; Shona)</span></h4>
                <p class="mt-0.5 text-[11.5px] text-slate-500">Complete hymns from #1 to the end (UMH 1–734, MHB 1–984, Shona 1–311)</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="badge badge-gold">2,029 Complete Hymns</span>
            <a href="{{ route('hymns.index') }}" target="_blank" class="inline-flex items-center gap-1 text-[12px] font-semibold text-brand hover:text-brand-dark" title="Open full digital hymnal in new tab">
                <span>Digital Hymnal</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <!-- Hymn Number Input / Quick Selector -->
    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
        <div class="sm:col-span-5">
            <label for="hymnNumberInput" class="field-label">Pick Hymn # (e.g. 1, 57, 140, 378, 734)</label>
            <div class="relative">
                <input type="text" id="hymnNumberInput" placeholder="Type hymn no. (e.g. 1, 57, 378, MHB 1)..." class="field pl-9 pr-24" oninput="handleHymnNumberInput(this.value)" onkeydown="if(event.key==='Enter'){event.preventDefault(); fetchHymnByNumber();}">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" aria-hidden="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="button" onclick="fetchHymnByNumber()" class="btn btn-primary btn-sm absolute right-1.5 top-1/2 -translate-y-1/2 !py-1">
                    Lookup
                </button>
            </div>
        </div>

        <div class="sm:col-span-7">
            <label for="hymnQuickSelect" class="field-label">Or Choose from Index</label>
            <select id="hymnQuickSelect" onchange="if(this.value){ const parts = this.value.split(' '); document.getElementById('hymnNumberInput').value = this.value; fetchHymnByNumber(parts[1], parts[0]); }" class="field">
                <option value="">-- Choose from Complete Hymnal Registry --</option>
                <optgroup label="Chapel Classics & Standards">
                    <option value="UMH 57">UMH 57: O For a Thousand Tongues to Sing</option>
                    <option value="UMH 378">UMH 378: Amazing Grace</option>
                    <option value="UMH 64">UMH 64: Holy, Holy, Holy! Lord God Almighty</option>
                    <option value="UMH 1">UMH 1: The Basic Pattern of Worship</option>
                    <option value="MHB 1">MHB 1: O For A Thousand Tongues To Sing</option>
                    <option value="SHONA 1">SHONA 1: Pururudzai nyika dzose</option>
                    <option value="SHONA 109">SHONA 109: Ishe Komborera Africa</option>
                </optgroup>
                <optgroup label="The United Methodist Hymnal (UMH 1 to 734)">
                    @foreach(\App\Models\Hymn::where('hymnal_code', 'UMH')->orderByRaw('CAST(number AS INTEGER) ASC')->get() as $quickHymn)
                        <option value="UMH {{ $quickHymn->number }}">UMH {{ $quickHymn->number }}: {{ $quickHymn->title }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="The Methodist Hymn Book (MHB 1 to 984)">
                    @foreach(\App\Models\Hymn::where('hymnal_code', 'MHB')->orderByRaw('CAST(number AS INTEGER) ASC')->get() as $quickHymn)
                        <option value="MHB {{ $quickHymn->number }}">MHB {{ $quickHymn->number }}: {{ $quickHymn->title }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Shona Methodist Hymnal (SHONA 1 to 311)">
                    @foreach(\App\Models\Hymn::where('hymnal_code', 'SHONA')->orderByRaw('CAST(number AS INTEGER) ASC')->get() as $quickHymn)
                        <option value="SHONA {{ $quickHymn->number }}">SHONA {{ $quickHymn->number }}: {{ $quickHymn->title }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>
    </div>

    <!-- Live Preview Card (Hidden until hymn chosen or loaded) -->
    <div id="hymnPreviewCard" class="hidden bg-white rounded-xl ring-1 ring-slate-200 shadow-sm p-4 sm:p-5 space-y-4" aria-live="polite">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="hymnBadge" class="badge badge-brand">UMH 57</span>
                    <h5 id="hymnTitle" class="font-display text-[17px] leading-snug text-slate-900">O For a Thousand Tongues to Sing</h5>
                </div>
                <div class="text-[11.5px] text-slate-500 mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span id="hymnAuthor">Charles Wesley, 1739</span>
                    <span class="text-slate-300" aria-hidden="true">&bull;</span>
                    <span id="hymnTune" class="text-slate-600">Tune: AZMON</span>
                    <span class="text-slate-300" aria-hidden="true">&bull;</span>
                    <span id="hymnCategory" class="text-gold font-semibold">Praise & Thanksgiving</span>
                    <span id="hymnScriptureDot" class="text-slate-300" aria-hidden="true">&bull;</span>
                    <span id="hymnScripture" class="text-slate-600 italic"></span>
                </div>
            </div>
            <div class="shrink-0">
                <button type="button" onclick="insertSelectedHymn()" class="btn btn-primary btn-sm">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Insert Title & Lyrics</span>
                </button>
            </div>
        </div>

        <!-- Alternates Bar if same number exists in multiple hymnals -->
        <div id="hymnAlternatesContainer" class="hidden rounded-lg bg-paper/70 ring-1 ring-slate-200/70 p-3">
            <span class="text-[10.5px] font-bold uppercase tracking-[0.1em] text-slate-500 block mb-2">Also available in other chapel hymnals with this number:</span>
            <div id="hymnAlternatesList" class="flex flex-wrap gap-1.5"></div>
        </div>

        <div class="border-t border-slate-100 pt-3">
            <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 mb-2">
                <span class="card-kicker">Full Hymn Stanzas & Verses</span>
                <span class="text-[11px] text-slate-400">Will be placed into Hymns & Lyrics fields</span>
            </div>
            <div id="hymnLyricsContent" class="hymn-lyrics-block !text-[14px] rounded-lg bg-paper/60 px-4 py-3 max-h-56 overflow-y-auto select-all"></div>
        </div>
    </div>

    <!-- Error/Notice box -->
    <div id="hymnLookupError" class="hidden" role="status">
        <div class="flex items-start gap-2.5 rounded-xl bg-amber-50 ring-1 ring-amber-600/20 px-3.5 py-3 text-[12.5px] text-amber-900">
            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-px" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span id="hymnLookupErrorMsg">Hymn number not found.</span>
        </div>
    </div>

    <!-- Toast Confirmation box -->
    <div id="hymnInsertSuccess" class="hidden" role="status">
        <div class="flex items-start gap-2.5 rounded-xl bg-emerald-50 ring-1 ring-emerald-600/20 px-3.5 py-3 text-[12.5px] font-semibold text-emerald-900">
            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span id="hymnInsertSuccessMsg">Hymn title and full lyrics have been added to the fields below!</span>
        </div>
    </div>
</div>

<script>
    let hymnLookupTimer = null;
    window.currentSelectedHymn = null;

    function handleHymnNumberInput(val) {
        clearTimeout(hymnLookupTimer);
        const trimmed = val ? val.trim() : '';
        if (!trimmed) {
            document.getElementById('hymnPreviewCard').classList.add('hidden');
            document.getElementById('hymnLookupError').classList.add('hidden');
            return;
        }
        hymnLookupTimer = setTimeout(() => {
            fetchHymnByNumber(trimmed);
        }, 280);
    }

    function fetchHymnByNumber(numberParam, hymnalParam) {
        let inputVal = numberParam !== undefined ? numberParam : document.getElementById('hymnNumberInput').value;
        const clean = inputVal ? inputVal.trim() : '';
        if (!clean) return;

        let url = '{{ route("api.hymns.lookup") }}?number=' + encodeURIComponent(clean);
        if (hymnalParam) {
            url += '&hymnal=' + encodeURIComponent(hymnalParam);
        }

        const errorBox = document.getElementById('hymnLookupError');
        const previewCard = document.getElementById('hymnPreviewCard');
        const successBox = document.getElementById('hymnInsertSuccess');
        if (successBox) successBox.classList.add('hidden');

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Hymn not found');
                return res.json();
            })
            .then(data => {
                if (data.found && data.hymn) {
                    const h = data.hymn;
                    window.currentSelectedHymn = h;
                    document.getElementById('hymnBadge').textContent = `${h.hymnal_code} ${h.number}`;
                    document.getElementById('hymnBadge').className = 'badge ' +
                        (h.hymnal_code === 'SHONA' ? 'badge-gold' : (h.hymnal_code === 'MHB' ? 'badge-sky' : 'badge-brand'));
                    document.getElementById('hymnTitle').textContent = h.title;
                    document.getElementById('hymnAuthor').textContent = h.author || 'Author Unspecified';
                    document.getElementById('hymnTune').textContent = h.tune ? `Tune: ${h.tune}` : '';
                    document.getElementById('hymnCategory').textContent = h.category || 'General Worship';
                    
                    const scriptureEl = document.getElementById('hymnScripture');
                    const scriptureDot = document.getElementById('hymnScriptureDot');
                    if (h.scripture_ref) {
                        scriptureEl.textContent = `Ref: ${h.scripture_ref}`;
                        scriptureEl.classList.remove('hidden');
                        if (scriptureDot) scriptureDot.classList.remove('hidden');
                    } else {
                        scriptureEl.textContent = '';
                        scriptureEl.classList.add('hidden');
                        if (scriptureDot) scriptureDot.classList.add('hidden');
                    }

                    document.getElementById('hymnLyricsContent').textContent = h.lyrics_available
                        ? h.lyrics
                        : 'Lyrics not yet entered for this hymn. The title will be added to the order of service; please sing from the printed hymnal.';

                    // Alternates
                    const altContainer = document.getElementById('hymnAlternatesContainer');
                    const altList = document.getElementById('hymnAlternatesList');
                    if (data.alternates && data.alternates.length > 0) {
                        altList.replaceChildren(...data.alternates.map(alt => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'px-2.5 py-1 rounded-lg bg-white text-left text-slate-700 text-[11.5px] font-semibold ring-1 ring-slate-200 hover:ring-slate-300 hover:text-slate-900 transition cursor-pointer';
                            btn.textContent = `Switch to ${alt.hymnal_code} #${alt.number}: ${alt.title}`;
                            btn.addEventListener('click', () => fetchHymnByNumber(String(alt.number), alt.hymnal_code));
                            return btn;
                        }));
                        altContainer.classList.remove('hidden');
                    } else if (altContainer) {
                        altContainer.classList.add('hidden');
                    }

                    errorBox.classList.add('hidden');
                    previewCard.classList.remove('hidden');
                } else {
                    throw new Error(data.message || 'Hymn not found');
                }
            })
            .catch(err => {
                window.currentSelectedHymn = null;
                previewCard.classList.add('hidden');
                document.getElementById('hymnLookupErrorMsg').textContent = `Hymn #${clean} was not found in The United Methodist Hymnal registry. You can browse the index or type any custom hymn manually.`;
                errorBox.classList.remove('hidden');
            });
    }

    function insertSelectedHymn() {
        if (!window.currentSelectedHymn) return;
        const hymn = window.currentSelectedHymn;

        // 1. Append to Hymns field
        const hymnsInput = document.querySelector('textarea[name="hymns"]') || document.querySelector('input[name="hymns"]');
        if (hymnsInput) {
            const currentHymns = hymnsInput.value ? hymnsInput.value.trim() : '';
            if (currentHymns.length > 0) {
                // Check if not already in input
                if (!currentHymns.includes(hymn.full_title) && !currentHymns.includes(hymn.title)) {
                    hymnsInput.value = currentHymns.endsWith(';') ? `${currentHymns} ${hymn.full_title}` : `${currentHymns}; ${hymn.full_title}`;
                }
            } else {
                hymnsInput.value = hymn.full_title;
            }
        }

        // 2. Append to Lyrics field
        const lyricsInput = document.querySelector('textarea[name="lyrics"]');
        if (lyricsInput && hymn.lyrics_available) {
            const currentLyrics = lyricsInput.value ? lyricsInput.value.trim() : '';
            const sectionHeader = `[${hymn.full_title}]`;
            const hymnBlock = `${sectionHeader}\n${hymn.lyrics}`;

            if (currentLyrics.length > 0) {
                if (!currentLyrics.includes(sectionHeader)) {
                    lyricsInput.value = `${currentLyrics}\n\n${hymnBlock}`;
                }
            } else {
                lyricsInput.value = hymnBlock;
            }
        }

        // 3. Show feedback banner
        const successBox = document.getElementById('hymnInsertSuccess');
        const successMsg = document.getElementById('hymnInsertSuccessMsg');
        if (successBox && successMsg) {
            successMsg.textContent = hymn.lyrics_available
                ? `✓ Inserted "${hymn.full_title}" into Hymns and pasted all stanzas into the Lyrics field below.`
                : `✓ Inserted "${hymn.full_title}" into Hymns. Its lyrics are not yet entered, so nothing was added to the Lyrics field.`;
            successBox.classList.remove('hidden');
            setTimeout(() => {
                successBox.classList.add('hidden');
            }, 6000);
        }
    }
</script>
