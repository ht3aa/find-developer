<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get URL for a file stored on S3 disk.
     * Uses CloudFront URL if configured, otherwise falls back to temporaryUrl.
     *
     * @param  string  $path  The file path
     * @param  string  $disk  The disk name (default: 's3')
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration  Expiration time for temporary URL (default: 5 hours)
     * @return string|null
     */
    public static function url(string $path, string $disk = 's3', $expiration = null): ?string
    {
        if (empty($path)) {
            return null;
        }

        $diskConfig = config("filesystems.disks.{$disk}", []);

        // Check if CloudFront URL is configured
        $cloudfrontUrl = $diskConfig['cloudfront_url'] ?? null;

        if ($cloudfrontUrl) {
            // Use CloudFront URL
            $cloudfrontUrl = rtrim($cloudfrontUrl, '/');
            $filePath = ltrim($path, '/');

            return "{$cloudfrontUrl}/{$filePath}";
        }

        // Fall back to temporary URL
        $expiration = $expiration ?? now()->addHours(5);

        /** @var \Illuminate\Contracts\Filesystem\Filesystem $storageDisk */
        $storageDisk = Storage::disk($disk);

        return $storageDisk->temporaryUrl($path, $expiration);
    }
}
