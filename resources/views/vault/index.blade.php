@extends('layouts.app')

@section('title', 'Club Records Vault')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                Institutional Records Management
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                Club Records Vault
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Permanent institutional repository owned by the Chaplaincy Office to ensure seamless leadership continuity.
            </p>
        </div>

        <button type="button" onclick="document.getElementById('uploadDocModal').classList.remove('hidden')" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm active:scale-95 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span>Upload Record</span>
        </button>
    </div>

    <!-- Repository Controls & Archive Health Bar -->
    @php
        $health = $selectedClub->getArchiveHealthScore();
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <!-- Club Info & Switcher -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-5">
                <div class="flex items-center space-x-3">
                    <span class="w-3.5 h-3.5 rounded-full shrink-0 ring-4 ring-slate-100" style="background-color: {{ $selectedClub->color_hex ?? '#DC2626' }}"></span>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Active Repository</div>
                        <h3 class="text-base font-bold text-slate-900">{{ $selectedClub->name }}</h3>
                    </div>
                </div>

                <div class="text-xs text-slate-500 sm:border-l sm:border-slate-200 sm:pl-5">
                    <span class="text-slate-400">Official Contact:</span>
                    <span class="font-mono font-medium text-slate-700 ml-1">{{ $selectedClub->official_email }}</span>
                </div>

                @if(auth()->user()->isOffice())
                    <div class="sm:border-l sm:border-slate-200 sm:pl-5">
                        <form method="GET" action="{{ route('vault.index') }}" class="flex items-center space-x-2">
                            <label class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">Switch Club:</label>
                            <select name="club_id" onchange="this.form.submit()" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                                @foreach($clubs as $c)
                                    <option value="{{ $c->id }}" {{ $selectedClub->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Compact Health Sentinel Indicator -->
            <div class="flex items-center space-x-4 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200/80 self-start xl:self-auto shrink-0">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Vault Sentinel:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $health['score'] >= 70 ? 'bg-emerald-100 text-emerald-800' : ($health['score'] >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $health['score'] }}% &bull; {{ $health['grade'] }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">
                        @if(empty($health['missing']))
                            <span class="text-emerald-700 font-medium">All core constitutional records deposited</span>
                        @else
                            <span class="text-slate-600">Missing: <strong class="text-[#DC2626]">{{ count($health['missing']) }}</strong> essential {{ count($health['missing']) === 1 ? 'record' : 'records' }}</span>
                        @endif
                    </div>
                </div>

                @if(!empty($health['missing']))
                    <button type="button" onclick="document.getElementById('uploadDocModal').classList.remove('hidden')" class="px-2.5 py-1 rounded-lg bg-[#DC2626] hover:bg-[#B91C1C] text-white text-[10px] font-bold tracking-wider uppercase cursor-pointer">
                        Deposit
                    </button>
                @endif
            </div>
        </div>

        <!-- Retention Policy & File Naming Note (Sleek Compact Bar) -->
        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between text-[11px] text-slate-500 gap-2">
            <div class="flex items-center space-x-2">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Permanent storage: Records are retained permanently in Chaplaincy custody and cannot be deleted by clubs.</span>
            </div>
            <div class="font-mono text-[10px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                Format: YYYY-MM-DD_{{ $selectedClub->code }}_Document-Title.pdf
            </div>
        </div>
    </div>

    <!-- Folder Navigation Tabs -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('vault.index', ['club_id' => $selectedClub->id, 'folder' => 'all']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $folder === 'all' ? 'bg-[#DC2626] text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 border border-slate-200' }}">
            <span>All Records</span>
            <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px] {{ $folder === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $documents->total() }}
            </span>
        </a>

        @foreach($folderLabels as $key => $label)
            <a href="{{ route('vault.index', ['club_id' => $selectedClub->id, 'folder' => $key]) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $folder === $key ? 'bg-[#DC2626] text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 border border-slate-200' }}">
                <span>{{ $label }}</span>
                <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px] {{ $folder === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $folderCounts[$key] ?? 0 }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Showing: {{ $folder === 'all' ? 'All Club Records' : ($folderLabels[$folder] ?? $folder) }}
            </h3>
            <span class="text-xs text-slate-500">{{ $documents->total() }} {{ $documents->total() === 1 ? 'file' : 'files' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-[11px] font-bold uppercase text-slate-600">
                        <th class="p-4">Standardized File Name & Title</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Year</th>
                        <th class="p-4">Uploaded By</th>
                        <th class="p-4">Deposit Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-4">
                                <div class="font-mono font-bold text-slate-900 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span class="text-[#DC2626]">{{ $doc->standardized_filename }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $doc->title }}</div>
                            </td>

                            <td class="p-4 whitespace-nowrap">
                                <span class="text-[10px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md font-semibold uppercase">
                                    {{ $folderLabels[$doc->folder] ?? $doc->folder }}
                                </span>
                            </td>

                            <td class="p-4 whitespace-nowrap font-bold text-slate-800 font-mono">
                                {{ $doc->year }}
                            </td>

                            <td class="p-4 whitespace-nowrap text-slate-700">
                                {{ $doc->uploader->name ?? 'Chaplaincy Archive' }}
                            </td>

                            <td class="p-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                {{ $doc->created_at->format('Y-m-d H:i') }}
                            </td>

                            <td class="p-4 text-right whitespace-nowrap space-x-2">
                                <a href="/storage/{{ $doc->file_path }}" target="_blank" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-[#DC2626] hover:text-white text-slate-700 font-semibold transition text-[11px] active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Download</span>
                                </a>

                                @if(auth()->user()->isOffice() && !$doc->is_archived)
                                    <form method="POST" action="{{ route('vault.archive', $doc->id) }}" class="inline" onsubmit="return confirm('Archive this record? It will be marked as archived without data loss.');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 font-semibold transition text-[11px] active:scale-95 cursor-pointer">
                                            Archive
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-slate-500">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="font-medium text-slate-700">No records stored in this folder</div>
                                <p class="text-xs text-slate-400 mt-1">Upload a document to preserve it permanently in the vault.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Upload Record Modal -->
<div id="uploadDocModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full border border-slate-200 overflow-hidden">
        <div class="bg-[#DC2626] p-5 text-white flex items-center justify-between">
            <div>
                <span class="text-xs uppercase text-rose-200 tracking-wider font-semibold">Chapel Records Vault</span>
                <h3 class="text-base font-bold text-white mt-0.5">Upload Permanent Document</h3>
            </div>
            <button type="button" onclick="document.getElementById('uploadDocModal').classList.add('hidden')" class="text-white/80 hover:text-white p-1 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('vault.store') }}" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
            @csrf

            <!-- Club selection -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Target Club</label>
                @if(auth()->user()->isOffice())
                    <select name="club_id" id="vault_club_id" class="w-full text-xs p-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}" data-code="{{ $c->code }}" {{ $selectedClub->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="club_id" value="{{ auth()->user()->club_id }}">
                    <div class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50 font-bold text-[#DC2626]">
                        {{ auth()->user()->club->name }} ({{ auth()->user()->club->code }})
                    </div>
                @endif
            </div>

            <!-- Folder Category -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Vault Folder Category <span class="text-rose-600">*</span></label>
                <select name="folder" required class="w-full text-xs p-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                    @foreach($folderLabels as $key => $label)
                        <option value="{{ $key }}" {{ $folder === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Title & Year -->
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Document Title <span class="text-rose-600">*</span></label>
                    <input type="text" name="title" id="vault_title" required placeholder="e.g. Constitution 2026, Annual Report" class="w-full text-xs p-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Year</label>
                    <input type="number" name="year" value="{{ date('Y') }}" required class="w-full text-xs p-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
            </div>

            <!-- Enforced File Naming Preview -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Automated File Name:</span>
                <span id="namingPreview" class="font-mono text-xs font-bold text-[#DC2626] break-all">
                    {{ date('Y-m-d') }}_{{ $selectedClub->code }}_document-title.pdf
                </span>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Select File (PDF, Word, Excel, Images) <span class="text-rose-600">*</span></label>
                <input type="file" name="file" required class="w-full text-xs p-2 border border-slate-300 rounded-xl bg-white">
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Historical Notes / Remarks</label>
                <textarea name="notes" rows="2" placeholder="Optional notes for incoming executive..." class="w-full text-xs p-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('uploadDocModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold hover:bg-slate-200 transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold shadow-sm active:scale-95 cursor-pointer">Upload to Vault</button>
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
