<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Stores uploaded records on the private disk so they are only reachable
 * through authorised controller downloads (never via a guessable /storage URL).
 */
class PrivateFileService
{
    private const PRIVATE_DISK = 'local';

    // Files uploaded before the move to private storage still live here
    private const LEGACY_DISK = 'public';

    public static function store(UploadedFile $file, string $directory, ?string $filename = null): string
    {
        return $filename
            ? $file->storeAs($directory, $filename, self::PRIVATE_DISK)
            : $file->store($directory, self::PRIVATE_DISK);
    }

    public static function download(?string $path, ?string $downloadName = null, bool $inline = true): StreamedResponse
    {
        $disk = self::locate($path);
        abort_if($disk === null, 404, 'The requested file could not be found.');

        $name = $downloadName ?: basename($path);
        $headers = ['X-Content-Type-Options' => 'nosniff'];

        return $inline
            ? Storage::disk($disk)->response($path, $name, $headers)
            : Storage::disk($disk)->download($path, $name, $headers);
    }

    private static function locate(?string $path): ?string
    {
        if (!$path || str_contains($path, '..')) {
            return null;
        }

        foreach ([self::PRIVATE_DISK, self::LEGACY_DISK] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }
}
