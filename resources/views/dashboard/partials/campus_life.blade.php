{{-- Campus Life & SRC: read-only awareness of chapel activities that affect campus logistics --}}
<div class="space-y-6">
    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" title="Logistics watch: large or late activities" />

            <x-dash.panel title="Next 14 days" icon="calendar" :link="route('calendar.index')" link-label="Full calendar">
                @forelse($dash['weekByDay'] as $date => $events)
                    @php $isToday = \Carbon\Carbon::parse($date)->isToday(); @endphp
                    <div class="pb-3 mb-3 border-b last:border-0 last:mb-0 last:pb-0 border-slate-100">
                        <h3 class="flex items-center gap-2 text-[12.5px] font-bold {{ $isToday ? 'text-brand' : 'text-slate-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isToday ? 'bg-brand breathe' : 'bg-gold' }}" style="{{ $isToday ? '--breathe: 220 38 38' : '' }}" aria-hidden="true"></span>
                            {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today' : \Carbon\Carbon::parse($date)->format('l, M j') }}
                        </h3>
                        @foreach($events as $event)
                            <x-dash.event-row :event="$event" :show-date="false" />
                        @endforeach
                    </div>
                @empty
                    <p class="text-[14px] text-slate-500">No approved chapel activities in the next two weeks.</p>
                @endforelse
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Subscribe to the chapel calendar" kicker="On your phone" icon="phone">
                <p class="text-[14px] text-slate-600 leading-relaxed mb-3">One tap adds every approved chapel activity to the calendar you already use. It stays up to date on its own.</p>
                <x-calendar-subscribe compact />
                <a href="{{ route('calendar.public') }}" target="_blank" rel="noopener" class="card-link mt-4">Shareable public calendar</a>
            </x-dash.panel>

            <section class="card p-5 bg-slate-50">
                <div class="flex items-center gap-3"><span class="icon-tile icon-tile-sm" style="--tile:#0369A1"><x-icon name="shield" /></span><div><p class="card-kicker">Observer role</p>
                <h2 class="card-title">Your access</h2></div></div>
                <p class="mt-3 text-[13.5px] text-slate-600 leading-relaxed">As a Campus Life / SRC observer you can see every approved activity. Proposals, club records and rosters stay private to the Chaplaincy and each fellowship.</p>
            </section>
        </div>
    </div>
</div>
