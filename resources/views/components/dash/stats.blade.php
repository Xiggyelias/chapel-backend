@props(['stats' => []])

@php
    $tones = [
        'critical' => ['tile' => 'var(--color-brand)', 'value' => 'text-brand', 'flag' => 'Needs action'],
        'warning'  => ['tile' => '#C2410C',            'value' => 'text-amber-800', 'flag' => 'Watch'],
        'info'     => ['tile' => '#0369A1',            'value' => 'text-slate-900', 'flag' => null],
        'neutral'  => ['tile' => 'var(--color-gold)',  'value' => 'text-slate-900', 'flag' => null],
    ];
    // Pick a meaningful icon from the stat's own words
    $iconFor = function (string $label): string {
        $l = strtolower($label);
        return match (true) {
            str_contains($l, 'vc') => 'shield',
            str_contains($l, 'report') => 'reports',
            str_contains($l, 'equipment') => 'equipment',
            str_contains($l, 'clash') => 'alert',
            str_contains($l, 'charter') => 'clubs',
            str_contains($l, 'vault') || str_contains($l, 'record') => 'vault',
            str_contains($l, 'account') => 'users',
            str_contains($l, 'member') => 'members',
            str_contains($l, 'gathering') => 'members',
            str_contains($l, 'evening') => 'clock',
            str_contains($l, 'change') => 'pencil',
            str_contains($l, 'decision') => 'requests',
            default => 'calendar',
        };
    };
@endphp

<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
    @foreach($stats as $stat)
        @php
            $t = $tones[$stat['tone']] ?? $tones['neutral'];
            $isZero = in_array((string) $stat['value'], ['0', '0%'], true);
        @endphp
        <a href="{{ $stat['url'] }}" class="group dash-stat-card focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
            <div class="flex items-start justify-between gap-3">
                <span class="icon-tile" style="--tile: {{ $t['tile'] }}"><x-icon :name="$iconFor($stat['label'])" /></span>
                @if($t['flag'] && !$isZero)
                    <span class="badge {{ $stat['tone'] === 'critical' ? 'badge-brand' : 'badge-amber' }} badge-dot">{{ $t['flag'] }}</span>
                @else
                    <x-icon name="arrow-up-right" class="w-4 h-4 text-slate-300 transition group-hover:text-brand group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                @endif
            </div>
            <div class="mt-4 stat-value text-[2rem] {{ $isZero ? 'text-slate-900' : $t['value'] }}">{{ $stat['value'] }}</div>
            <div class="mt-1.5 text-[13.5px] font-semibold text-slate-800 leading-snug">{{ $stat['label'] }}</div>
            <div class="mt-0.5 text-[12.5px] text-slate-500 truncate">{{ $stat['hint'] }}</div>
        </a>
    @endforeach
</div>
