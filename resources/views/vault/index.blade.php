@extends('layouts.app')

@section('title', 'Club Records Vault')

@section('content')
@php
    $health = $selectedClub->getArchiveHealthScore();
    $healthBadge = $health['score'] >= 70 ? 'badge-emerald' : ($health['score'] >= 50 ? 'badge-amber' : 'badge-rose');
@endphp
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Institutional records management" title="Club Records Vault"
                   description="Permanent institutional repository owned by the Chaplaincy Office to ensure seamless leadership continuity.">
        <button type="button" onclick="document.getElementById('uploadDocModal').classList.remove('hidden')" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Upload Record
        </button>
    </x-page-header>

    {{-- Repository + archive health --}}
    <section class="card">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_auto]">
            <div class="card-body flex flex-wrap items-start gap-x-8 gap-y-5">
                <div class="flex items-start gap-3 min-w-0">
                    <span class="mt-1.5 w-3 h-3 rounded-full shrink-0 ring-4 ring-slate-100" style="background-color: {{ $selectedClub->color_hex ?? '#A51C30' }}" aria-hidden="true"></span>
                    <div class="min-w-0">
                        <p class="card-kicker">Active repository</p>
                        <h2 class="card-title mt-0.5 text-[1.25rem]">{{ $selectedClub->name }}</h2>
                        <p class="mt-1 text-[12px] text-slate-500">
                            Official contact
                            <span class="font-mono text-slate-700 ml-1">{{ $selectedClub->official_email }}</span>
                        </p>
                    </div>
                </div>

                @if(auth()->user()->isOffice())
                    <form method="GET" action="{{ route('vault.index') }}" class="min-w-[14rem] sm:pl-8 sm:border-l sm:border-slate-100">
                        <label for="vault_switch_club" class="field-label">Switch club</label>
                        <select id="vault_switch_club" name="club_id" onchange="this.form.submit()" class="field py-2">
                            @foreach($clubs as $c)
                                <option value="{{ $c->id }}" {{ $selectedClub->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>

            {{-- Vault sentinel --}}
            <div class="card-body lg:w-[22rem] border-t lg:border-t-0 lg:border-l border-slate-100 bg-slate-50/60 lg:rounded-r-[var(--radius-card)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="card-kicker">Vault sentinel</p>
                        <p class="mt-1.5 flex items-baseline gap-2">
                            <span class="stat-value text-[2.25rem] text-slate-900">{{ $health['score'] }}</span><span class="font-display text-lg text-slate-400">%</span>
                        </p>
                    </div>
                    <span class="badge {{ $healthBadge }}">Grade {{ $health['grade'] }}</span>
                </div>
                <div class="progress mt-3" aria-hidden="true"><span style="width: {{ max(0, min(100, $health['score'])) }}%"></span></div>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <p class="text-[12px] text-slate-500">
                        @if(empty($health['missing']))
                            <span class="text-emerald-700 font-medium">All core constitutional records deposited</span>
                        @else
                            Missing <strong class="text-brand font-semibold">{{ count($health['missing']) }}</strong> essential {{ count($health['missing']) === 1 ? 'record' : 'records' }}
                        @endif
                    </p>
                    @if(!empty($health['missing']))
                        <button type="button" onclick="document.getElementById('uploadDocModal').classList.remove('hidden')" class="btn btn-secondary btn-sm shrink-0">
                            Deposit
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Retention policy & naming convention --}}
        <div class="px-5 py-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-[11.5px] text-slate-500">
            <p class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Permanent storage: records are retained permanently in Chaplaincy custody and cannot be deleted by clubs.</span>
            </p>
            <code class="font-mono text-[10.5px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md self-start sm:self-auto break-all">Format: YYYY-MM-DD_{{ $selectedClub->code }}_Document-Title.pdf</code>
        </div>
    </section>

    {{-- Folder filters --}}
    <nav class="overflow-x-auto -mx-1 px-1 pb-1" aria-label="Vault folders">
        <div class="segmented">
            <a href="{{ route('vault.index', ['club_id' => $selectedClub->id, 'folder' => 'all']) }}"
               class="whitespace-nowrap inline-flex items-center gap-1.5 {{ $folder === 'all' ? 'is-active' : '' }}" @if($folder === 'all') aria-current="page" @endif>
                All Records
                <span class="tabular-nums text-[10.5px] {{ $folder === 'all' ? 'text-brand' : 'text-slate-400' }}">{{ $documents->total() }}</span>
            </a>
            @foreach($folderLabels as $key => $label)
                <a href="{{ route('vault.index', ['club_id' => $selectedClub->id, 'folder' => $key]) }}"
                   class="whitespace-nowrap inline-flex items-center gap-1.5 {{ $folder === $key ? 'is-active' : '' }}" @if($folder === $key) aria-current="page" @endif>
                    {{ $label }}
                    <span class="tabular-nums text-[10.5px] {{ $folder === $key ? 'text-brand' : 'text-slate-400' }}">{{ $folderCounts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Documents --}}
    <section class="card overflow-hidden">
        <div class="card-header">
            <div class="min-w-0">
                <p class="card-kicker">Showing</p>
                <h2 class="card-title mt-0.5 truncate">{{ $folder === 'all' ? 'All Club Records' : ($folderLabels[$folder] ?? $folder) }}</h2>
            </div>
            <span class="text-[12px] text-slate-500 tabular-nums shrink-0">{{ $documents->total() }} {{ $documents->total() === 1 ? 'file' : 'files' }}</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Standardized file name &amp; title</th>
                        <th>Category</th>
                        <th>Year</th>
                        <th>Uploaded by</th>
                        <th>Deposit date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td>
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 grid place-items-center w-8 h-8 rounded-lg bg-brand-soft text-brand shrink-0" aria-hidden="true">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-mono text-[12px] font-semibold text-slate-900 break-all">{{ $doc->standardized_filename }}</div>
                                        <div class="text-[12px] text-slate-500 mt-0.5">{{ $doc->title }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="badge">{{ $folderLabels[$doc->folder] ?? $doc->folder }}</span>
                            </td>
                            <td class="whitespace-nowrap font-semibold text-slate-800 tabular-nums">{{ $doc->year }}</td>
                            <td class="whitespace-nowrap text-slate-700">{{ $doc->uploader?->name ?? 'Chaplaincy Archive' }}</td>
                            <td class="whitespace-nowrap text-slate-500 text-[12px] tabular-nums">{{ $doc->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('vault.download', $doc->id) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Download
                                    </a>
                                    @if(auth()->user()->isOffice() && !$doc->is_archived)
                                        <form method="POST" action="{{ route('vault.archive', $doc->id) }}" class="inline" onsubmit="return confirm('Archive this record? It will be marked as archived without data loss.');">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost btn-sm hover:!text-rose-700">Archive</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!p-0">
                                <div class="empty-state">
                                    <div class="empty-state-mark" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="empty-state-title">No records stored in this folder</p>
                                    <p class="empty-state-text">Upload a document to preserve it permanently in the vault.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $documents->links() }}
            </div>
        @endif
    </section>

</div>

{{-- Upload record --}}
<div id="uploadDocModal" role="dialog" aria-modal="true" aria-labelledby="uploadDocTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop">
    <div class="bg-white rounded-3xl shadow-lg max-w-lg w-full ring-1 ring-black/5 overflow-hidden modal-panel max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="px-6 pt-6 pb-4 flex items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Chapel records vault</p>
                <h3 id="uploadDocTitle" class="font-display text-xl text-slate-900">Upload permanent document</h3>
            </div>
            <button type="button" onclick="document.getElementById('uploadDocModal').classList.add('hidden')" class="topbar-action -mr-2 -mt-1" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('vault.store') }}" enctype="multipart/form-data" class="px-6 pb-6 space-y-4">
            @csrf

            <div>
                <label for="vault_club_id" class="field-label">Target club</label>
                @if(auth()->user()->isOffice())
                    <select name="club_id" id="vault_club_id" class="field">
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}" data-code="{{ $c->code }}" {{ $selectedClub->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="club_id" value="{{ auth()->user()->club_id }}">
                    <div class="field bg-slate-50 font-semibold text-brand-dark">
                        {{ auth()->user()->club?->name }} ({{ auth()->user()->club?->code }})
                    </div>
                @endif
            </div>

            <div>
                <label for="vault_folder" class="field-label">Vault folder category <span class="text-brand">*</span></label>
                <select name="folder" id="vault_folder" required class="field">
                    @foreach($folderLabels as $key => $label)
                        <option value="{{ $key }}" {{ $folder === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label for="vault_title" class="field-label">Document title <span class="text-brand">*</span></label>
                    <input type="text" name="title" id="vault_title" required placeholder="e.g. Constitution 2026, Annual Report" class="field">
                </div>
                <div>
                    <label for="vault_year" class="field-label">Year</label>
                    <input type="number" name="year" id="vault_year" value="{{ date('Y') }}" required class="field tabular-nums">
                </div>
            </div>

            <div class="rounded-xl bg-gold-soft/60 ring-1 ring-gold/20 px-3.5 py-3">
                <span class="text-[10px] uppercase font-bold tracking-[0.12em] text-[#7A5A12] block mb-0.5">Automated file name</span>
                <span id="namingPreview" class="font-mono text-[12px] font-semibold text-slate-900 break-all">
                    {{ date('Y-m-d') }}_{{ $selectedClub->code }}_document-title.pdf
                </span>
            </div>

            <div>
                <label for="vault_file" class="field-label">Select file (PDF, Word, Excel, images) <span class="text-brand">*</span></label>
                <input type="file" name="file" id="vault_file" required class="field py-2 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
            </div>

            <div>
                <label for="vault_notes" class="field-label">Historical notes / remarks</label>
                <textarea name="notes" id="vault_notes" rows="2" placeholder="Optional notes for incoming executive..." class="field"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('uploadDocModal').classList.add('hidden')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload to Vault</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('vault_title');
        const preview = document.getElementById('namingPreview');
        const clubCode = '{{ $selectedClub->code }}';
        const todayStr = '{{ date('Y-m-d') }}';

        if (titleInput && preview) {
            titleInput.addEventListener('input', function() {
                const slug = titleInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                preview.innerText = `${todayStr}_${clubCode}_${slug || 'document-title'}.pdf`;
            });
        }
    });
</script>
@endsection
