@props(['title', 'link' => null, 'linkLabel' => 'View all', 'kicker' => null, 'icon' => null, 'flush' => false])

<section {{ $attributes->merge(['class' => 'card']) }}>
    <div class="card-header">
        <div class="flex items-center gap-3 min-w-0">
            @if($icon)
                <span class="icon-tile icon-tile-sm" style="--tile: var(--color-slate-600)"><x-icon :name="$icon" /></span>
            @endif
            <div class="min-w-0">
                @if($kicker)<p class="card-kicker">{{ $kicker }}</p>@endif
                <h2 class="card-title">{{ $title }}</h2>
            </div>
        </div>
        @if($link)
            <a href="{{ $link }}" class="card-link shrink-0">{{ $linkLabel }}</a>
        @endif
    </div>
    <div class="{{ $flush ? '' : 'card-body' }}">
        {{ $slot }}
    </div>
</section>
