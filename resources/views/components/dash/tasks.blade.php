@props(['tasks', 'title' => 'Needs your attention', 'limit' => 8])

@php
    $tones = [
        'critical' => ['dot' => 'bg-brand',     'ring' => 'ring-brand/15',     'cta' => 'btn-primary',   'label' => 'Urgent', 'tag' => 'badge-brand'],
        'warning'  => ['dot' => 'bg-amber-500', 'ring' => 'ring-amber-500/15', 'cta' => 'btn-secondary', 'label' => 'Soon',   'tag' => 'badge-amber'],
        'info'     => ['dot' => 'bg-sky-500',   'ring' => 'ring-sky-500/15',   'cta' => 'btn-secondary', 'label' => 'To do',  'tag' => 'badge-sky'],
    ];
    $visible = $tasks->take($limit);
@endphp

<section class="card" aria-labelledby="tasks-heading">
    <div class="card-header">
        <div class="flex items-center gap-2.5 min-w-0">
            <h2 id="tasks-heading" class="card-title">{{ $title }}</h2>
            @if($tasks->isNotEmpty())
                <span class="min-w-[1.5rem] h-6 px-2 rounded-full bg-brand text-white text-[11px] font-bold grid place-items-center tabular-nums">{{ $tasks->count() }}</span>
            @endif
        </div>
        @if($tasks->count() > $limit)
            <span class="text-[11.5px] text-slate-400">Showing {{ $limit }} of {{ $tasks->count() }}</span>
        @endif
    </div>

    @if($visible->isEmpty())
        <div class="empty-state">
            <div class="empty-state-mark" aria-hidden="true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="empty-state-title">All is in good order</p>
            <p class="empty-state-text">Nothing needs your attention right now.</p>
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($visible as $task)
                @php $t = $tones[$task['tone']] ?? $tones['info']; @endphp
                <li class="group flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5 px-5 py-4 transition-colors hover:bg-slate-50/70">
                    <span class="hidden sm:grid place-items-center w-8 h-8 rounded-full bg-white ring-4 {{ $t['ring'] }} shrink-0" aria-hidden="true">
                        <span class="w-2 h-2 rounded-full {{ $t['dot'] }}"></span>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[13.5px] font-semibold text-slate-900 leading-snug">
                            <span class="sr-only">{{ $t['label'] }}:</span>{{ $task['title'] }}
                        </p>
                        <p class="text-[12px] text-slate-500 mt-0.5 truncate">{{ $task['meta'] }}</p>
                    </div>
                    <span class="hidden md:inline-flex badge {{ $t['tag'] }}">{{ $t['label'] }}</span>
                    <a href="{{ $task['url'] }}" class="btn btn-sm {{ $t['cta'] }} self-start sm:self-auto shrink-0">{{ $task['cta'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
