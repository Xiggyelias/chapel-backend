<!-- CHAPEL SECRETARY REGISTRAR & VAULT SENTINEL DASHBOARD -->
<div class="space-y-6">

    <!-- Priority Administrative Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pending Club Charters -->
        <a href="{{ route('clubs.index', ['status' => 'pending']) }}" class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                @if(($roleData['pending_club_charters'] ?? 0) > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-[#DC2626]">
                        Review Needed
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                        Zero Pending
                    </span>
                @endif
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight group-hover:text-[#DC2626] transition-colors">
                {{ $roleData['pending_club_charters'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Pending Charters</div>
            <p class="text-[11px] text-slate-500 mt-0.5">New fellowship applications</p>
        </a>

        <!-- Active Chartered Clubs -->
        <a href="{{ route('clubs.index', ['status' => 'active']) }}" class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-medium text-slate-600">Recognized</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight group-hover:text-slate-800 transition-colors">
                {{ $roleData['active_chartered_clubs'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Chartered Groups</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Active campus student ministries</p>
        </a>

        <!-- Records Vault Documents -->
        <a href="{{ route('vault.index') }}" class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <span class="text-[10px] font-medium text-slate-500">Vault Files</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight group-hover:text-[#DC2626] transition-colors">
                {{ $roleData['total_vault_documents'] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Vault Documents</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Permanent institutional archives</p>
        </a>

        <!-- Vault Sentinel Average -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition group block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">Audit</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $roleData['vault_compliance_avg'] ?? 85 }}%
            </div>
            <div class="text-xs font-semibold text-slate-800 mt-1">Vault Compliance</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Archive completeness score</p>
        </div>
    </div>

    <!-- Fast User & Staff Provisioning Hub -->
    <div class="p-4 sm:p-5 rounded-3xl bg-slate-50 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-[#DC2626] text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Staff &amp; Executive Account Management</h3>
                <p class="text-xs text-slate-500">Register new staff ministers, assistants, and student fellowship executives</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('users.index') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200 transition">
                View Directory
            </a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-xs active:scale-95">
                + Register Account
            </a>
        </div>
    </div>

    <!-- Pending Fellowship Charters Awaiting Action -->
    @if(isset($roleData['pendingCharters']) && $roleData['pendingCharters']->count() > 0)
        <div class="bg-amber-50/40 rounded-2xl border border-amber-200/80 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                    <h3 class="text-sm font-bold text-amber-950 uppercase tracking-wider">
                        Pending Fellowship Charter Applications ({{ $roleData['pendingCharters']->count() }})
                    </h3>
                </div>
                <a href="{{ route('clubs.index', ['status' => 'pending']) }}" class="text-xs font-bold text-amber-800 hover:underline">
                    Manage Registry &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($roleData['pendingCharters'] as $pClub)
                    <div class="bg-white p-4 rounded-xl border border-amber-200/80 shadow-2xs flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900">{{ $pClub->name }} ({{ $pClub->code }})</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 uppercase">Reviewing</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $pClub->description }}</p>
                            <div class="text-[11px] text-slate-500 mt-2 space-y-0.5">
                                <div>President: <strong>{{ $pClub->founding_president_name }}</strong> ({{ $pClub->founding_president_email }})</div>
                                <div>Schedule: {{ $pClub->meeting_schedule }} &bull; Venue: {{ $pClub->venue }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            @if($pClub->constitution_path)
                                <a href="{{ route('clubs.constitution', $pClub->id) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-[#DC2626] hover:underline flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Constitution (PDF)</span>
                                </a>
                            @else
                                <span class="text-[11px] text-slate-400 italic">No file attached</span>
                            @endif

                            <form method="POST" action="{{ route('clubs.approve', $pClub->id) }}" data-club-name="{{ $pClub->name }}" onsubmit="return confirm('Officially charter ' + this.dataset.clubName + ' under Chaplaincy oversight?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-xs active:scale-95">
                                    Approve &amp; Charter
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Institutional Exports & Outbox Dispatch Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Official Institutional Data Exports &bull; Open Standards
                </h3>
                <p class="text-xs text-slate-500">Download Kwang-Lim Chapel archives in open CSV format for institutional reporting</p>
            </div>
            <a href="{{ route('notifications.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-[#DC2626] hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Dispatch Center &rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
            <a href="{{ route('exports.requests') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-between text-xs font-semibold text-slate-700 transition">
                <span>Export Proposals (CSV)</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
            <a href="{{ route('exports.calendar') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-between text-xs font-semibold text-slate-700 transition">
                <span>Export Master Calendar (CSV)</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
            <a href="{{ route('exports.clubs') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-between text-xs font-semibold text-slate-700 transition">
                <span>Export Fellowships Directory (CSV)</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
        </div>
    </div>

    <!-- Recent Vault Archival Uploads & System Audit Stream -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Records Uploaded to Vault -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Recent Vault Submissions
                    </h3>
                    <p class="text-xs text-slate-500">Permanent records filed across active fellowships</p>
                </div>
                <a href="{{ route('vault.index') }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                    Open Vault &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($roleData['recentDocuments'] ?? [] as $doc)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-900">{{ $doc->title }}</span>
                            <div class="text-[11px] text-slate-500">
                                <span>{{ $doc->club->name ?? 'Chaplaincy' }}</span>
                                <span class="mx-1">&bull;</span>
                                <span class="capitalize">{{ str_replace('_', ' ', $doc->folder) }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono font-bold text-slate-400">{{ $doc->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-slate-500">
                        <span>No recent archival uploads.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- System Audit Trail -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Security &amp; Activity Audit Log
                    </h3>
                    <p class="text-xs text-slate-500">Real-time ledger of administrative actions &amp; state changes</p>
                </div>
                <a href="{{ route('audit') }}" class="text-xs font-bold text-[#DC2626] hover:underline">
                    Full Ledger &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($roleData['auditLogs'] ?? [] as $log)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <span class="font-bold text-slate-800">{{ $log->action }}</span>
                            <div class="text-[11px] text-slate-500">{{ $log->notes }}</div>
                        </div>
                        <span class="text-[10px] font-mono text-slate-400 shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-slate-500">
                        <span>No audit events recorded recently.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
