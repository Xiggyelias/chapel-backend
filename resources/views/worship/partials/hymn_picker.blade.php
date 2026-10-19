<!-- The United Methodist Hymnal (UMH), MHB & Shona Interactive Picker & Lyrics Auto-Fill -->
<div class="bg-gradient-to-r from-red-50/70 via-slate-50 to-red-50/40 p-4 sm:p-5 rounded-2xl border-2 border-red-200/80 shadow-xs space-y-3.5">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#DC2626] text-white flex items-center justify-center font-bold text-xs shadow-xs shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 font-cinzel">The Chapel Hymnals (UMH &bull; MHB &bull; Shona)</h4>
                <p class="text-[11px] text-slate-500">Complete hymns from #1 to the end (UMH 1–734, MHB 1–984, Shona 1–311)</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 uppercase">2,029 Complete Hymns</span>
            <a href="{{ route('hymns.index') }}" target="_blank" class="text-[11px] font-semibold text-[#DC2626] hover:underline flex items-center space-x-1" title="Open full digital hymnal in new tab">
                <span>Digital Hymnal</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <!-- Hymn Number Input / Quick Selector -->
    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
        <div class="sm:col-span-5 relative">
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Pick Hymn # (e.g. 1, 57, 140, 378, 734)</label>
            <div class="relative">
                <input type="text" id="hymnNumberInput" placeholder="Type hymn no. (e.g. 1, 57, 378, MHB 1)..." class="w-full text-xs pl-8 pr-20 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626] bg-white font-medium" oninput="handleHymnNumberInput(this.value)" onkeydown="if(event.key==='Enter'){event.preventDefault(); fetchHymnByNumber();}">
                <div class="absolute left-2.5 top-2.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="button" onclick="fetchHymnByNumber()" class="absolute right-1.5 top-1 px-2.5 py-1.5 bg-[#DC2626] hover:bg-[#B91C1C] text-white rounded-lg text-[10px] font-bold transition cursor-pointer">
                    Lookup
                </button>
            </div>
        </div>

        <div class="sm:col-span-7">
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Or Choose from Index</label>
            <select id="hymnQuickSelect" onchange="if(this.value){ const parts = this.value.split(' '); document.getElementById('hymnNumberInput').value = this.value; fetchHymnByNumber(parts[1], parts[0]); }" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626] bg-white text-slate-800 font-medium">
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
    <div id="hymnPreviewCard" class="hidden bg-white p-4 rounded-xl border border-red-200/90 shadow-sm space-y-3 transition-all animate-fade-in">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div>
                <div class="flex items-center space-x-2">
                    <span id="hymnBadge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#DC2626] text-white">UMH 57</span>
                    <h5 id="hymnTitle" class="text-sm font-bold text-slate-900 font-cinzel">O For a Thousand Tongues to Sing</h5>
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span id="hymnAuthor">Charles Wesley, 1739</span>
                    <span>&bull;</span>
                    <span id="hymnTune" class="font-mono text-slate-600">Tune: AZMON</span>
                    <span>&bull;</span>
                    <span id="hymnCategory" class="text-emerald-700 font-semibold">Praise & Thanksgiving</span>
                    <span id="hymnScriptureDot">&bull;</span>
                    <span id="hymnScripture" class="text-slate-600 italic"></span>
                </div>
            </div>
            <div class="flex items-center space-x-2 shrink-0">
                <button type="button" onclick="insertSelectedHymn()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center space-x-1.5 cursor-pointer shadow-xs active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Insert Title & Lyrics</span>
                </button>
            </div>
        </div>

        <!-- Alternates Bar if same number exists in multiple hymnals -->
        <div id="hymnAlternatesContainer" class="hidden p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Also available in other chapel hymnals with this number:</span>
            <div id="hymnAlternatesList" class="flex flex-wrap gap-1.5"></div>
        </div>

        <div class="border-t border-slate-100 pt-2.5">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Full Hymn Stanzas & Verses</span>
                <span class="text-[10px] text-slate-400 font-mono">Will be placed into Hymns & Lyrics fields</span>
            </div>
            <div id="hymnLyricsContent" class="bg-slate-50 p-3 rounded-lg border border-slate-200/80 font-mono text-xs text-slate-800 leading-relaxed whitespace-pre-wrap max-h-56 overflow-y-auto select-all"></div>
        </div>
    </div>

    <!-- Error/Notice box -->
    <div id="hymnLookupError" class="hidden p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center space-x-2">
        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span id="hymnLookupErrorMsg">Hymn number not found.</span>
    </div>

    <!-- Toast Confirmation box -->
    <div id="hymnInsertSuccess" class="hidden p-2.5 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-semibold flex items-center space-x-2 animate-fade-in">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span id="hymnInsertSuccessMsg">Hymn title and full lyrics have been added to the fields below!</span>
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
                    document.getElementById('hymnBadge').className = 'px-2 py-0.5 rounded text-[10px] font-bold ' + 
                        (h.hymnal_code === 'SHONA' ? 'bg-amber-100 text-amber-800' : (h.hymnal_code === 'MHB' ? 'bg-blue-100 text-blue-800' : 'bg-[#DC2626] text-white'));
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

                    document.getElementById('hymnLyricsContent').textContent = h.lyrics;

                    // Alternates
                    const altContainer = document.getElementById('hymnAlternatesContainer');
                    const altList = document.getElementById('hymnAlternatesList');
                    if (data.alternates && data.alternates.length > 0) {
                        let altHtml = '';
                        data.alternates.forEach(alt => {
                            altHtml += `<button type="button" onclick="fetchHymnByNumber('${alt.number}', '${alt.hymnal_code}')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold border border-slate-200 transition cursor-pointer">Switch to ${alt.hymnal_code} #${alt.number}: ${alt.title}</button>`;
                        });
                        altList.innerHTML = altHtml;
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
        if (lyricsInput) {
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
            successMsg.textContent = `✓ Inserted "${hymn.full_title}" into Hymns and pasted all stanzas into the Lyrics field below!`;
            successBox.classList.remove('hidden');
            setTimeout(() => {
                successBox.classList.add('hidden');
            }, 6000);
        }
    }
</script>
