@props(['value', 'size' => 220, 'label' => 'QR code'])

<div {{ $attributes->merge(['class' => 'inline-block bg-white leading-none']) }} role="img" aria-label="{{ $label }}">
    {!! \App\Services\QrCodeService::svg($value, (int) $size) !!}
</div>
