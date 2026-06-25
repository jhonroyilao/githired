<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class StorageUrl
{
    public static function image(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $disk = config('filesystems.image_disk', 'public');

        if ($disk === 'supabase-images') {
            return self::supabasePublicUrl(
                config('filesystems.disks.supabase-images.endpoint'),
                config('filesystems.disks.supabase-images.bucket'),
                $path,
            );
        }

        return Storage::disk($disk)->url($path);
    }

    private static function supabasePublicUrl(?string $endpoint, ?string $bucket, string $path): ?string
    {
        if (blank($endpoint) || blank($bucket)) {
            return null;
        }

        $baseUrl = Str::of($endpoint)
            ->replace('.storage.supabase.co/storage/v1/s3', '.supabase.co/storage/v1/object/public')
            ->replace('/storage/v1/s3', '/storage/v1/object/public')
            ->rtrim('/');

        return $baseUrl . '/' . trim($bucket, '/') . '/' . ltrim($path, '/');
    }
}
