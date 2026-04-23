<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasSecureStorage
{
    /**
     * Get a secure URL for a file. 
     * Uses temporary (signed) URLs if on cloud storage, otherwise standard URLs.
     */
    public function getSecureUrl(?string $path, int $minutes = 60): ?string
    {
        if (!$path) return null;

        $disk = config('filesystems.default');

        if ($disk === 'r2' || $disk === 's3') {
            try {
                return Storage::temporaryUrl($path, now()->addMinutes($minutes));
            } catch (\Exception $e) {
                return Storage::url($path);
            }
        }

        return Storage::url($path);
    }

    /**
     * Get Base64 data for a file, useful for PDF rendering.
     */
    public function getBase64Data(?string $path): ?string
    {
        if (!$path) return null;

        try {
            if (Storage::exists($path)) {
                $content = Storage::get($path);
                $mime = Storage::mimeType($path);
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        } catch (\Exception $e) {
            // Log error or ignore
        }

        return null;
    }
}
