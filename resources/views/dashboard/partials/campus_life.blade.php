{{-- Campus Life & SRC: read-only awareness of chapel activities that affect campus logistics --}}
<div class="space-y-6">
    <x-dash.stats :stats="$dash['stats']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-dash.tasks :tasks="$dash['tasks']" title="Logistics watch: large or late activities" />

            <x-dash.panel title="Next 14 days" :link="route('calendar.index')" link-label="Full calendar">
                @forelse($dash['weekByDay'] as $date => $events)
                    <div class="pb-3 mb-3 border-b last:border-0 last:mb-0 last:pb-0 border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wide {{ \Carbon\Carbon::parse($date)->isToday() ? 'text-[#DC2626]' : 'text-slate-500' }}">
                            {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today' : \Carbon\Carbon::parse($date)->format('l, M j') }}
                        </h3>
                        @foreach($events as $event)
                            <x-dash.event-row :event="$event" :show-date="false" />
                        @endforeach
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No approved chapel activities in the next two weeks.</p>
                @endforelse
            </x-dash.panel>
        </div>

        <div class="space-y-6">
            <x-dash.panel title="Subscribe to the chapel calendar">
                <p class="text-xs text-slate-600 leading-relaxed">Add every approved chapel activity to Google, Outlook or Apple Calendar. It updates automatically.</p>
                <label for="icsFeed" class="sr-only">Calendar feed URL</label>
                <div class="mt-3 flex gap-2">
                    <input id="icsFeed" type="text" readonly value="{{ route('calendar.feed_ics') }}" class="min-w-0 flex-1 text-xs px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 font-mono">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('icsFeed').value).then(() => { this.textContent = 'Copied'; })" class="shrink-0 px-3 py-2 rounded-lg text-xs font-semibold bg-[#DC2626] hover:bg-[#B91C1C] text-white">Copy</button>
                </div>
                <a href="{{ route('calendar.public') }}" target="_blank" rel="noopener" class="mt-3 inline-block text-xs font-semibold text-[#DC2626] hover:underline">Shareable public calendar &rarr;</a>
            </x-dash.panel>

            <x-dash.panel title="Your access">
                <p class="text-xs text-slate-600 leading-relaxed">As a Campus Life / SRC observer you can see every approved activity. Proposals, club records and rosters stay private to the Chaplaincy and each fellowship.</p>
            </x-dash.panel>
        </div>
    </div>
</div>
