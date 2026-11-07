@props(['tasks', 'title' => 'Needs your attention', 'limit' => 8])

@php
    $tones = [
        'critical' => ['bar' => 'bg-brand',     'icon' => 'alert', 'tile' => 'var(--color-brand)', 'cta' => 'btn-primary',   'label' => 'Urgent', 'tag' => 'badge-brand'],
        'warning'  => ['bar' => 'bg-amber-500', 'icon' => 'clock', 'tile' => '#C2410C',            'cta' => 'btn-secondary', 'label' => 'Soon',   'tag' => 'badge-amber'],
        'info'     => ['bar' => 'bg-sky-500',   'icon' => 'info',  'tile' => '#0369A1',            'cta' => 'btn-secondary', 'label' => 'To do',  'tag' => 'badge-sky'],
    ];
    $visible = $tasks->take($limit);
    $urgent = $tasks->where('tone', 'critical')->count();
@endphp

<section class="card overflow-hidden" aria-labelledby="tasks-heading">
    <div class="card-header">
        <div class="flex items-center gap-2.5 min-w-0">
            <h2 id="tasks-heading" class="card-title">{{ $title }}</h2>
            @if($tasks->isNotEmpty())
                <span class="badge tabular-nums">{{ $tasks->count() }}</span>
            @endif
            @if($urgent)
                <span class="badge badge-brand badge-dot tabular-nums">{{ $urgent }} urgent</span>
            @endif
        </div>
        @if($tasks->count() > $limit)
            <span class="text-[12.5px] text-slate-500">Showing {{ $limit }} of {{ $tasks->count() }}</span>
        @endif
    </div>

    @if($visible->isEmpty())
        <div class="empty-state">
            <div class="empty-state-mark" style="color:#047857;background:#E9F7EF" aria-hidden="true">
                <x-icon name="check" class="w-6 h-6" stroke="2" />
            </div>
            <p class="empty-state-title">All is in good order</p>
            <p class="empty-state-text">Nothing needs your attention right now.</p>
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($visible as $task)
                @php $t = $tones[$task['tone']] ?? $tones['info']; @endphp
                <li class="relative flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 pl-5 pr-4 sm:pr-5 py-3.5 transition-colors hover:bg-slate-50">
                    <span class="absolute left-0 top-3 bottom-3 w-[3px] rounded-r {{ $t['bar'] }}" aria-hidden="true"></span>
                    <span class="hidden sm:grid icon-tile icon-tile-sm" style="--tile: {{ $t['tile'] }}"><x-icon :name="$t['icon']" /></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[14px] font-semibold text-slate-900 leading-snug">
                            <span class="sr-only">{{ $t['label'] }}:</span>{{ $task['title'] }}
                        </p>
                        <p class="text-[12.5px] text-slate-500 mt-0.5 truncate">{{ $task['meta'] }}</p>
                    </div>
                    <span class="hidden md:inline-flex badge {{ $t['tag'] }}">{{ $t['label'] }}</span>
                    <a href="{{ $task['url'] }}" class="btn btn-sm {{ $t['cta'] }} self-start sm:self-auto shrink-0">{{ $task['cta'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
