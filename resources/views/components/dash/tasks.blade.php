@props(['tasks', 'title' => 'Needs your attention', 'limit' => 8])

@php
    $tones = [
        'critical' => ['border' => 'border-l-rose-500',  'bg' => 'bg-rose-50/60',   'cta' => 'bg-brand hover:bg-brand-dark text-white',          'label' => 'Urgent'],
        'warning'  => ['border' => 'border-l-amber-500', 'bg' => 'bg-amber-50/60',  'cta' => 'bg-amber-500 hover:bg-amber-600 text-white',        'label' => 'Soon'],
        'info'     => ['border' => 'border-l-sky-400',   'bg' => 'bg-sky-50/40',    'cta' => 'bg-slate-100 hover:bg-slate-200 text-slate-800',    'label' => 'To do'],
    ];
    $visible = $tasks->take($limit);
@endphp

<section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm" aria-labelledby="tasks-heading">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
        <h2 id="tasks-heading" class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
            {{ $title }}
            @if($tasks->isNotEmpty())
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-brand text-white">{{ $tasks->count() }}</span>
            @endif
        </h2>
        @if($tasks->count() > $limit)
            <span class="text-xs text-slate-400">Showing {{ $limit }} of {{ $tasks->count() }}</span>
        @endif
    </div>

    @if($visible->isEmpty())
        <div class="px-5 py-10 text-center">
            <div class="mx-auto w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2" aria-hidden="true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-800">All caught up</p>
            <p class="text-xs text-slate-400 mt-0.5">Nothing needs your attention right now.</p>
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($visible as $task)
                @php $t = $tones[$task['tone']] ?? $tones['info']; @endphp
                <li class="flex items-stretch border-l-3 {{ $t['border'] }} {{ $t['bg'] }}">
                    <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 px-4 py-3.5">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 leading-snug">
                                <span class="sr-only">{{ $t['label'] }}:</span>{{ $task['title'] }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $task['meta'] }}</p>
                        </div>
                        <a href="{{ $task['url'] }}" class="self-start sm:self-auto shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $t['cta'] }} focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
                            {{ $task['cta'] }}
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</section>
