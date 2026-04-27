<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('secure_storage_url')) {
    /**
     * Get a secure URL for a file.
     * Uses temporary (signed) URLs if on cloud storage, otherwise standard URLs.
     */
    function secure_storage_url(?string $path, int $minutes = 60, bool $forceSigned = false): ?string
    {
        if (!$path) return null;

        $disk = config('filesystems.default');

        if ($disk === 'r2' || $disk === 's3') {
            // Smart detection: files in these folders will use clean public URLs
            // unless $forceSigned is true
            $publicFolders = ['logos', 'favicons', 'site', 'invoice-headers', 'invoice-footers'];
            $isPublicFolder = false;
            foreach ($publicFolders as $folder) {
                if (str_starts_with($path, $folder . '/')) {
                    $isPublicFolder = true;
                    break;
                }
            }

            // Also check for temporary Livewire uploads - these MUST be signed to preview
            if (str_contains($path, 'livewire-tmp')) {
                $forceSigned = true;
            }

            try {
                if ($forceSigned || !$isPublicFolder) {
                    // Return temporary Signed URL for private files
                    return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($minutes));
                }
                
                // Return clean Public URL for logos, etc.
                return Storage::disk($disk)->url($path);
            } catch (\Exception $e) {
                return Storage::disk($disk)->url($path);
            }
        }

        // Return standard storage URL for local disk
        return Storage::url($path);
    }
}

if (!function_exists('storage_base64')) {
    /**
     * Get Base64 data for a file, useful for PDF rendering.
     */
    function storage_base64(?string $path): ?string
    {
        if (!$path) return null;

        $cacheKey = "base64_" . md5($path);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function() use ($path) {
            try {
                if (Storage::exists($path)) {
                    $content = Storage::get($path);
                    $mime = Storage::mimeType($path);
                    return 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            } catch (\Exception $e) {
                // Ignore
            }
            return null;
        });
    }
}

if (!function_exists('generate_qr_base64')) {
    /**
     * Generate a QR Code as Base64 PNG.
     */
    function generate_qr_base64(string $data): string
    {
        $options = new \chillerlan\QRCode\QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
            'quality'         => 90,
            'scale'           => 5,
        ]);

        return (new \chillerlan\QRCode\QRCode($options))->render($data);
    }
}
