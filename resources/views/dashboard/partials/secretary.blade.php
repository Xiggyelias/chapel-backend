{{-- Chapel Secretary: charters, accounts, records vault and institutional memory --}}
<div class="space-y-6">
    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" />

            <x-dash.panel title="Charter applications" :link="route('clubs.index', ['status' => 'pending'])">
                @forelse($dash['charters'] as $club)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 py-3 first:pt-0 last:pb-0 border-b last:border-0 border-slate-100">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900">{{ $club->name }} <span class="text-xs font-mono text-slate-500">({{ $club->code }})</span></p>
                            <p class="text-xs text-slate-500">{{ ucfirst($club->type) }} &bull; {{ $club->founding_president_name }} &bull; {{ $club->tracking_code }} &bull; {{ $club->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($club->constitution_path)
                                <a href="{{ route('clubs.constitution', $club->id) }}" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800">Constitution</a>
                            @endif
                            <form method="POST" action="{{ route('clubs.approve', $club->id) }}" data-club-name="{{ $club->name }}" onsubmit="return confirm('Charter ' + this.dataset.clubName + ' and create the leader account?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#DC2626] hover:bg-[#B91C1C] text-white">Approve &amp; charter</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No charter applications are waiting.</p>
                @endforelse
            </x-dash.panel>

            <x-dash.panel title="Recent audit activity" :link="route('audit')">
                <ul class="divide-y divide-slate-100 -my-2">
                    @forelse($dash['auditLogs'] as $log)
                        <li class="py-2 text-sm">
                            <span class="font-semibold text-slate-900">{{ $log->user_name }}</span>
                            <span class="text-slate-600">{{ str_replace('_', ' ', $log->action) }}</span>
                            <span class="block text-xs text-slate-500 truncate">{{ $log->notes }} &bull; {{ $log->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-slate-500">No activity recorded yet.</li>
                    @endforelse
                </ul>
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Vault compliance — weakest first" :link="route('vault.index')">
                @forelse($dash['vaultLeague'] as $row)
                    <a href="{{ route('vault.index', ['club_id' => $row['club']->id]) }}" class="block py-2 first:pt-0 group">
                        <div class="flex justify-between text-sm">
                            <span class="font-semibold text-slate-800 group-hover:text-[#DC2626] truncate">{{ $row['club']->name }}</span>
                            <span class="font-bold" style="color: {{ $row['health']['color'] }}">{{ $row['health']['score'] }}%</span>
                        </div>
                        <div class="mt-1 h-1.5 rounded-full bg-slate-100 overflow-hidden" aria-hidden="true">
                            <div class="h-full rounded-full" style="width: {{ $row['health']['score'] }}%; background: {{ $row['health']['color'] }}"></div>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No active fellowships yet.</p>
                @endforelse
            </x-dash.panel>

            <x-dash.panel title="Latest vault uploads" :link="route('vault.index')">
                <ul class="divide-y divide-slate-100 -my-2">
                    @forelse($dash['recentDocuments'] as $doc)
                        <li class="py-2">
                            <a href="{{ route('vault.download', $doc->id) }}" target="_blank" rel="noopener" class="text-sm font-semibold text-slate-900 hover:text-[#DC2626] truncate block">{{ $doc->title }}</a>
                            <span class="text-xs text-slate-500">{{ $doc->club?->name }} &bull; {{ $doc->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-slate-500">No documents uploaded yet.</li>
                    @endforelse
                </ul>
            </x-dash.panel>
        </div>
    </div>
</div>
