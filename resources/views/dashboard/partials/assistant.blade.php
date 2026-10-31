{{-- Assistant Chaplain: daily operations — review queue, venues, equipment desk, liturgy coordination --}}
<div class="space-y-6">
    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" />

            <x-dash.panel title="Today across all venues — {{ now()->format('l, M j') }}" :link="route('calendar.index')">
                @forelse($dash['todaysSchedule'] as $event)
                    <x-dash.event-row :event="$event" :show-date="false" />
                @empty
                    <p class="text-sm text-slate-500">No activities are booked for today.</p>
                @endforelse
            </x-dash.panel>

            <x-dash.panel title="Decision queue" :link="route('requests.index', ['status' => 'submitted'])">
                <x-dash.decisions :requests="$dash['decisionQueue']" />
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Worship coordination" :link="route('worship.index')">
                <x-dash.services :services="$dash['services']" :can-edit="true" />
            </x-dash.panel>

            <x-dash.panel title="Venue week ahead" :link="route('calendar.index')">
                @forelse($dash['weekByDay'] as $date => $events)
                    <div class="pb-3 mb-3 border-b last:border-0 last:mb-0 last:pb-0 border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ \Carbon\Carbon::parse($date)->format('l, M j') }}</h3>
                        @foreach($events as $event)
                            <x-dash.event-row :event="$event" :show-date="false" />
                        @endforeach
                    </div>
                @empty
                    <p class="text-sm text-slate-500">The week ahead is clear.</p>
                @endforelse
            </x-dash.panel>
        </div>
    </div>
</div>
