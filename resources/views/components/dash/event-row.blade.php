@props(['event', 'showDate' => true])

<div class="flex items-center gap-3.5 py-2.5">
    @if($showDate)
        <div class="w-11 shrink-0 text-center rounded-xl bg-white ring-1 ring-slate-200 shadow-2xs overflow-hidden">
            <div class="text-[9px] font-bold uppercase tracking-[0.14em] text-white bg-brand py-0.5">{{ $event->event_date->format('M') }}</div>
            <div class="font-display text-[1.15rem] text-slate-900 leading-none py-1.5 tabular-nums">{{ $event->event_date->format('j') }}</div>
        </div>
    @else
        <div class="w-11 shrink-0 text-right text-[11.5px] font-semibold text-slate-500 tabular-nums">{{ substr($event->start_time, 0, 5) }}</div>
    @endif
    <div class="min-w-0 flex-1">
        <p class="text-[13.5px] font-semibold text-slate-900 truncate">{{ $event->title }}</p>
        <p class="text-[12px] text-slate-500 truncate">
            <span class="tabular-nums">{{ substr($event->start_time, 0, 5) }}–{{ substr($event->end_time, 0, 5) }}</span>
            · {{ $event->venue?->name ?? $event->venue_name ?? 'Venue TBC' }}
            @if($event->club) · {{ $event->club?->name }} @endif
        </p>
    </div>
    @if((int) $event->expected_attendance >= 300)
        <span class="shrink-0 badge badge-amber tabular-nums">{{ (int) $event->expected_attendance }}+</span>
    @endif
</div>
