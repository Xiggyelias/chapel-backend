{{-- University Chaplain: final decisions, VC liaison, worship & preaching, pastoral oversight --}}
<div class="space-y-6">
    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" />

            <x-dash.panel title="Decision queue — soonest first" :link="route('requests.index', ['status' => 'submitted'])">
                <x-dash.decisions :requests="$dash['decisionQueue']" />
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Worship readiness" :link="route('worship.index')">
                <x-dash.services :services="$dash['services']" :can-edit="true" />
            </x-dash.panel>

            @php $pulse = $dash['pulse']; @endphp
            <x-dash.panel title="Ministry pulse ({{ now()->year }})" :link="route('reports.annual_report')" link-label="Annual report">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500">Activities held</dt>
                        <dd class="text-xl font-extrabold text-slate-900">{{ $pulse['activities'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Reported attendance</dt>
                        <dd class="text-xl font-extrabold text-slate-900">{{ number_format($pulse['attendance']) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Reports filed</dt>
                        <dd class="text-xl font-extrabold {{ $pulse['reportRate'] < 70 ? 'text-amber-700' : 'text-slate-900' }}">{{ $pulse['reportRate'] }}%</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Active fellowships</dt>
                        <dd class="text-xl font-extrabold text-slate-900">{{ $pulse['activeClubs'] }}</dd>
                    </div>
                </dl>
                @php $used = $pulse['budget'] > 0 ? min(100, round($pulse['spent'] / $pulse['budget'] * 100)) : 0; @endphp
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Spent ${{ number_format($pulse['spent'], 2) }}</span>
                        <span>of ${{ number_format($pulse['budget'], 2) }} approved</span>
                    </div>
                    <div class="mt-1 h-2 rounded-full bg-slate-100 overflow-hidden" role="progressbar" aria-valuenow="{{ $used }}" aria-valuemin="0" aria-valuemax="100" aria-label="Budget used">
                        <div class="h-full {{ $pulse['spent'] > $pulse['budget'] ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $used }}%"></div>
                    </div>
                </div>
            </x-dash.panel>

            <x-dash.panel title="Next 7 days on campus" :link="route('calendar.index')">
                @forelse($dash['upcomingEvents']->take(6) as $event)
                    <x-dash.event-row :event="$event" />
                @empty
                    <p class="text-sm text-slate-500">No approved activities in the next week.</p>
                @endforelse
            </x-dash.panel>
        </div>
    </div>
</div>
