<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Renders genuine, scannable QR codes as inline SVG (no external service, works offline).
 */
class QrCodeService
{
    public static function svg(string $content, int $size = 220): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd()));

        // Drop the XML prolog so the markup can be embedded directly in HTML
        return preg_replace('/^<\?xml[^>]*>\s*/', '', $writer->writeString($content));
    }
}
