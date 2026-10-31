@props(['stats' => []])

@php
    $tones = [
        'critical' => ['ring' => 'border-rose-200 hover:border-rose-300', 'value' => 'text-rose-700', 'dot' => 'bg-rose-500'],
        'warning' => ['ring' => 'border-amber-200 hover:border-amber-300', 'value' => 'text-amber-700', 'dot' => 'bg-amber-500'],
        'info' => ['ring' => 'border-slate-200 hover:border-sky-300', 'value' => 'text-slate-900', 'dot' => 'bg-sky-500'],
        'neutral' => ['ring' => 'border-slate-200 hover:border-slate-300', 'value' => 'text-slate-900', 'dot' => 'bg-slate-300'],
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    @foreach($stats as $stat)
        @php $t = $tones[$stat['tone']] ?? $tones['neutral']; @endphp
        <a href="{{ $stat['url'] }}" class="group bg-white rounded-2xl p-4 sm:p-5 border {{ $t['ring'] }} shadow-2xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#DC2626]">
            <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                <span class="w-2 h-2 rounded-full {{ $t['dot'] }}" aria-hidden="true"></span>
                <span class="truncate">{{ $stat['label'] }}</span>
            </div>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight {{ $t['value'] }}">{{ $stat['value'] }}</div>
            <div class="mt-0.5 text-xs text-slate-500 truncate">{{ $stat['hint'] }}</div>
        </a>
    @endforeach
</div>
