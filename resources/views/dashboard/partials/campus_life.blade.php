{{-- Campus Life & SRC: read-only awareness of chapel activities that affect campus logistics --}}
<div class="space-y-6">
    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" title="Logistics watch: large or late activities" />

            <x-dash.panel title="Next 14 days" :link="route('calendar.index')" link-label="Full calendar">
                @forelse($dash['weekByDay'] as $date => $events)
                    @php $isToday = \Carbon\Carbon::parse($date)->isToday(); @endphp
                    <div class="pb-3 mb-3 border-b last:border-0 last:mb-0 last:pb-0 border-slate-100">
                        <h3 class="flex items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.14em] {{ $isToday ? 'text-brand' : 'text-slate-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isToday ? 'bg-brand breathe' : 'bg-gold' }}" style="{{ $isToday ? '--breathe: 165 28 48' : '' }}" aria-hidden="true"></span>
                            {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today' : \Carbon\Carbon::parse($date)->format('l, M j') }}
                        </h3>
                        @foreach($events as $event)
                            <x-dash.event-row :event="$event" :show-date="false" />
                        @endforeach
                    </div>
                @empty
                    <p class="text-[13px] text-slate-500">No approved chapel activities in the next two weeks.</p>
                @endforelse
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Subscribe to the chapel calendar" kicker="Calendar feed">
                <p class="text-[13px] text-slate-600 leading-relaxed">Add every approved chapel activity to Google, Outlook or Apple Calendar. It updates automatically.</p>
                <label for="icsFeed" class="sr-only">Calendar feed URL</label>
                <div class="mt-4 flex gap-2">
                    <input id="icsFeed" type="text" readonly value="{{ route('calendar.feed_ics') }}" class="field min-w-0 flex-1 py-2 text-[12px] font-mono bg-slate-50 text-slate-600">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('icsFeed').value).then(() => { this.textContent = 'Copied'; })" class="btn btn-primary shrink-0">Copy</button>
                </div>
                <a href="{{ route('calendar.public') }}" target="_blank" rel="noopener" class="card-link mt-4">Shareable public calendar</a>
            </x-dash.panel>

            <section class="card card-ink grain overflow-hidden p-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gold-light/80">Observer role</p>
                <h2 class="mt-1.5 font-display text-[1.15rem] text-white">Your access</h2>
                <p class="mt-2 text-[12.5px] text-white/65 leading-relaxed">As a Campus Life / SRC observer you can see every approved activity. Proposals, club records and rosters stay private to the Chaplaincy and each fellowship.</p>
            </section>
        </div>
    </div>
</div>
