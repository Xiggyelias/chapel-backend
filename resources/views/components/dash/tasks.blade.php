@props(['tasks', 'title' => 'Needs your attention', 'limit' => 8])

@php
    $tones = [
        'critical' => ['bar' => 'bg-rose-500', 'cta' => 'bg-rose-600 hover:bg-rose-700 text-white', 'label' => 'Urgent'],
        'warning' => ['bar' => 'bg-amber-500', 'cta' => 'bg-amber-500 hover:bg-amber-600 text-white', 'label' => 'Soon'],
        'info' => ['bar' => 'bg-sky-500', 'cta' => 'bg-slate-100 hover:bg-slate-200 text-slate-800', 'label' => 'To do'],
    ];
    $visible = $tasks->take($limit);
@endphp

<section class="bg-white rounded-2xl border border-slate-200 shadow-2xs" aria-labelledby="tasks-heading">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 id="tasks-heading" class="text-sm font-bold text-slate-900 flex items-center gap-2">
            {{ $title }}
            @if($tasks->isNotEmpty())
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#DC2626] text-white">{{ $tasks->count() }}</span>
            @endif
        </h2>
        @if($tasks->count() > $limit)
            <span class="text-xs text-slate-500">Showing {{ $limit }} most urgent</span>
        @endif
    </div>

    @if($visible->isEmpty())
        <div class="px-5 py-10 text-center">
            <div class="mx-auto w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg" aria-hidden="true">✓</div>
            <p class="mt-2 text-sm font-semibold text-slate-800">You're all caught up</p>
            <p class="text-xs text-slate-500">Nothing needs your action right now.</p>
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($visible as $task)
                @php $t = $tones[$task['tone']] ?? $tones['info']; @endphp
                <li class="flex items-stretch">
                    <span class="w-1 shrink-0 {{ $t['bar'] }}" aria-hidden="true"></span>
                    <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 truncate">
                                <span class="sr-only">{{ $t['label'] }}:</span>{{ $task['title'] }}
                            </p>
                            <p class="text-xs text-slate-500 truncate">{{ $task['meta'] }}</p>
                        </div>
                        <a href="{{ $task['url'] }}" class="self-start sm:self-auto shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $t['cta'] }} focus:outline-none focus-visible:ring-2 focus-visible:ring-[#DC2626]">
                            {{ $task['cta'] }}
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</section>
