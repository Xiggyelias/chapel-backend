@props(['event', 'showDate' => true])

@php $c = $event->club?->color_hex ?: '#DC2626'; @endphp
<div class="flex items-center gap-3.5 py-2.5">
    @if($showDate)
        <div class="date-leaf">
            <span class="date-leaf-m">{{ $event->event_date->format('M') }}</span>
            <span class="date-leaf-d">{{ $event->event_date->format('j') }}</span>
            <span class="date-leaf-w">{{ $event->event_date->format('D') }}</span>
        </div>
    @else
        <div class="w-12 shrink-0 text-[13px] font-bold text-slate-700 tabular-nums">{{ substr($event->start_time, 0, 5) }}</div>
    @endif
    <span class="w-1 self-stretch rounded-full shrink-0" style="background: {{ $c }}" aria-hidden="true"></span>
    <div class="min-w-0 flex-1">
        <p class="text-[14px] font-semibold text-slate-900 truncate">{{ $event->title }}</p>
        <p class="text-[12.5px] text-slate-500 truncate">
            <span class="tabular-nums">{{ substr($event->start_time, 0, 5) }}–{{ substr($event->end_time, 0, 5) }}</span>
            · {{ $event->venue?->name ?? $event->venue_name ?? 'Venue TBC' }}
            @if($event->club) · {{ $event->club?->name }} @endif
        </p>
    </div>
    @if((int) $event->expected_attendance >= 300)
        <span class="shrink-0 badge badge-amber tabular-nums" title="Expected attendance">{{ (int) $event->expected_attendance }}+</span>
    @endif
</div>
