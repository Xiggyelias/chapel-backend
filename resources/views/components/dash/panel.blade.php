@props(['title', 'link' => null, 'linkLabel' => 'View all', 'kicker' => null])

<section {{ $attributes->merge(['class' => 'card']) }}>
    <div class="card-header">
        <div class="min-w-0">
            @if($kicker)<p class="card-kicker">{{ $kicker }}</p>@endif
            <h2 class="card-title truncate">{{ $title }}</h2>
        </div>
        @if($link)
            <a href="{{ $link }}" class="card-link shrink-0">{{ $linkLabel }}</a>
        @endif
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</section>
