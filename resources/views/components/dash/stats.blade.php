@props(['stats' => []])

@php
    $tones = [
        'critical' => ['border' => 'border-l-brand',   'value' => 'text-rose-700',  'dot' => 'bg-rose-500',   'ring' => 'hover:border-rose-200'],
        'warning'  => ['border' => 'border-l-amber-500','value' => 'text-amber-700', 'dot' => 'bg-amber-500',  'ring' => 'hover:border-amber-200'],
        'info'     => ['border' => 'border-l-sky-500',  'value' => 'text-sky-700',   'dot' => 'bg-sky-500',    'ring' => 'hover:border-sky-200'],
        'neutral'  => ['border' => 'border-l-slate-300','value' => 'text-slate-900', 'dot' => 'bg-slate-300',  'ring' => 'hover:border-slate-300'],
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    @foreach($stats as $stat)
        @php $t = $tones[$stat['tone']] ?? $tones['neutral']; @endphp
        <a href="{{ $stat['url'] }}"
           class="group dash-stat-card border-l-4 {{ $t['border'] }} {{ $t['ring'] }} focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
            <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                <span class="w-1.5 h-1.5 rounded-full {{ $t['dot'] }}" aria-hidden="true"></span>
                <span class="truncate">{{ $stat['label'] }}</span>
            </div>
            <div class="mt-2.5 text-3xl font-extrabold tracking-tight tabular-nums {{ $t['value'] }}">{{ $stat['value'] }}</div>
            <div class="mt-1 text-[11px] text-slate-400 truncate">{{ $stat['hint'] }}</div>
        </a>
    @endforeach
</div>
