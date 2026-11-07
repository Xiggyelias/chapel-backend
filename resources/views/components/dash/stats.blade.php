@props(['stats' => []])

@php
    $tones = [
        'critical' => ['tone' => 'var(--color-brand)', 'value' => 'text-brand-dark', 'dot' => 'bg-brand'],
        'warning'  => ['tone' => '#D97706',            'value' => 'text-amber-800',   'dot' => 'bg-amber-500'],
        'info'     => ['tone' => '#0284C7',            'value' => 'text-slate-900',   'dot' => 'bg-sky-500'],
        'neutral'  => ['tone' => 'var(--color-gold)',  'value' => 'text-slate-900',   'dot' => 'bg-gold'],
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    @foreach($stats as $stat)
        @php $t = $tones[$stat['tone']] ?? $tones['neutral']; @endphp
        <a href="{{ $stat['url'] }}" class="group dash-stat-card focus:outline-none focus-visible:ring-2 focus-visible:ring-brand" style="--tone: {{ $t['tone'] }}">
            <div class="flex items-start justify-between gap-2">
                <span class="flex items-start gap-2 text-[10.5px] font-bold uppercase tracking-[0.12em] sm:tracking-[0.14em] text-slate-500 min-w-0 leading-snug">
                    <span class="w-1.5 h-1.5 mt-[0.3em] rounded-full shrink-0 {{ $t['dot'] }}" aria-hidden="true"></span>
                    <span>{{ $stat['label'] }}</span>
                </span>
                <svg class="w-4 h-4 text-slate-300 shrink-0 -translate-x-1 opacity-0 transition duration-300 group-hover:translate-x-0 group-hover:opacity-100 group-hover:text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H8M17 7v9"/></svg>
            </div>
            <div class="mt-4 stat-value text-[2.6rem] {{ $t['value'] }}">{{ $stat['value'] }}</div>
            <div class="mt-2 text-[11.5px] text-slate-500 truncate">{{ $stat['hint'] }}</div>
        </a>
    @endforeach
</div>
