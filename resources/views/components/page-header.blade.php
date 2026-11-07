{{--
    Editorial page header used by every module page.

    <x-page-header eyebrow="Clearance & approvals" title="Proposals & Requests"
                   description="Fellowship gatherings, venue bookings, equipment and outings.">
        <a href="…" class="btn btn-primary">…</a>   (optional actions slot)
    </x-page-header>
--}}
@props(['eyebrow' => null, 'title', 'description' => null])

<header {{ $attributes->merge(['class' => 'page-header']) }}>
    <div class="min-w-0">
        @if($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="page-title">{{ $title }}</h1>
        @if($description)
            <p class="page-lede">{{ $description }}</p>
        @endif
    </div>
    @if(trim($slot) !== '')
        <div class="page-actions">{{ $slot }}</div>
    @endif
</header>
