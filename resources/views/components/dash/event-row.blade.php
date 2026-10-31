@props(['event', 'showDate' => true])

<div class="flex items-start gap-3 py-2.5">
    @if($showDate)
        <div class="w-11 shrink-0 text-center rounded-lg bg-slate-50 border border-slate-200 py-1">
            <div class="text-[10px] font-bold uppercase text-[#DC2626]">{{ $event->event_date->format('M') }}</div>
            <div class="text-base font-extrabold text-slate-900 leading-none">{{ $event->event_date->format('j') }}</div>
        </div>
    @endif
    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-slate-900 truncate">{{ $event->title }}</p>
        <p class="text-xs text-slate-500 truncate">
            {{ substr($event->start_time, 0, 5) }}–{{ substr($event->end_time, 0, 5) }}
            &bull; {{ $event->venue?->name ?? $event->venue_name ?? 'Venue TBC' }}
            @if($event->club) &bull; {{ $event->club?->name }} @endif
        </p>
    </div>
    @if((int) $event->expected_attendance >= 300)
        <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ (int) $event->expected_attendance }}+</span>
    @endif
</div>
