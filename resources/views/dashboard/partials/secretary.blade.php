{{-- Chapel Secretary: charters, accounts, records vault and institutional memory --}}
<div class="space-y-6">
    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" />

            <x-dash.panel title="Charter applications" icon="clubs" :link="route('clubs.index', ['status' => 'pending'])">
                @forelse($dash['charters'] as $club)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 py-3.5 first:pt-0 last:pb-0 border-b last:border-0 border-slate-100">
                        <div class="min-w-0 flex-1">
                            <p class="text-[14px] font-semibold text-slate-900">{{ $club->name }} <span class="text-[11.5px] font-medium text-slate-400 tabular-nums">({{ $club->code }})</span></p>
                            <p class="mt-0.5 text-[12.5px] text-slate-500">{{ ucfirst($club->type) }} · {{ $club->founding_president_name }} · <span class="tabular-nums">{{ $club->tracking_code }}</span> · {{ $club->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($club->constitution_path)
                                <a href="{{ route('clubs.constitution', $club->id) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">Constitution</a>
                            @endif
                            <form method="POST" action="{{ route('clubs.approve', $club->id) }}" data-club-name="{{ $club->name }}" onsubmit="return confirm('Charter ' + this.dataset.clubName + ' and create the leader account?')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Approve &amp; charter</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-[14px] text-slate-500">No charter applications are waiting.</p>
                @endforelse
            </x-dash.panel>

            <x-dash.panel title="Recent audit activity" icon="audit" :link="route('audit')">
                <ol class="relative -my-1 space-y-0">
                    @forelse($dash['auditLogs'] as $log)
                        <li class="relative pl-6 py-2.5">
                            <span class="absolute left-[3px] top-0 bottom-0 w-px bg-slate-200" aria-hidden="true"></span>
                            <span class="absolute left-0 top-[1.05rem] w-[7px] h-[7px] rounded-full bg-white ring-[1.5px] ring-gold" aria-hidden="true"></span>
                            <p class="text-[13px] leading-snug">
                                <span class="font-semibold text-slate-900">{{ $log->user_name }}</span>
                                <span class="text-slate-600">{{ str_replace('_', ' ', $log->action) }}</span>
                            </p>
                            <span class="block mt-0.5 text-[12px] text-slate-500 truncate">{{ $log->notes }} · {{ $log->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-[14px] text-slate-500">No activity recorded yet.</li>
                    @endforelse
                </ol>
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Vault compliance — weakest first" icon="shield" :link="route('vault.index')">
                <div class="space-y-3.5">
                    @forelse($dash['vaultLeague'] as $row)
                        <a href="{{ route('vault.index', ['club_id' => $row['club']->id]) }}" class="block group">
                            <div class="flex items-baseline justify-between gap-3">
                                <span class="text-[13px] font-semibold text-slate-800 group-hover:text-brand truncate transition-colors">{{ $row['club']->name }}</span>
                                <span class="font-display text-[15px] tabular-nums" style="color: {{ $row['health']['color'] }}">{{ $row['health']['score'] }}%</span>
                            </div>
                            <div class="mt-1.5 progress" aria-hidden="true">
                                <span style="width: {{ $row['health']['score'] }}%; background: {{ $row['health']['color'] }}"></span>
                            </div>
                        </a>
                    @empty
                        <p class="text-[14px] text-slate-500">No active fellowships yet.</p>
                    @endforelse
                </div>
            </x-dash.panel>

            <x-dash.panel title="Latest vault uploads" icon="upload" :link="route('vault.index')">
                <ul class="divide-y divide-slate-100 -my-2.5">
                    @forelse($dash['recentDocuments'] as $doc)
                        <li class="flex items-start gap-3 py-2.5">
                            <svg class="w-4 h-4 mt-0.5 shrink-0 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <div class="min-w-0">
                                <a href="{{ route('vault.download', $doc->id) }}" target="_blank" rel="noopener" class="text-[13px] font-semibold text-slate-900 hover:text-brand truncate block transition-colors">{{ $doc->title }}</a>
                                <span class="text-[12px] text-slate-500">{{ $doc->club?->name }} · {{ $doc->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="py-2.5 text-[14px] text-slate-500">No documents uploaded yet.</li>
                    @endforelse
                </ul>
            </x-dash.panel>
        </div>
    </div>
</div>
