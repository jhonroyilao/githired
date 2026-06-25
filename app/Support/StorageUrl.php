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
            $path = self::imageObjectPath($path);

            return self::supabasePublicUrl(
                config('filesystems.disks.supabase-images.endpoint'),
                config('filesystems.disks.supabase-images.bucket'),
                $path,
            );
        }

        return Storage::disk($disk)->url($path);
    }

    public static function imageObjectPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $bucket = config('filesystems.image_disk') === 'supabase-images'
            ? config('filesystems.disks.supabase-images.bucket')
            : null;

        return self::withoutBucketPrefix($path, $bucket);
    }

    private static function withoutBucketPrefix(string $path, ?string $bucket): string
    {
        $path = ltrim($path, '/');
        $bucket = trim((string) $bucket, '/');

        if ($bucket !== '' && Str::startsWith($path, $bucket.'/')) {
            return Str::after($path, $bucket.'/');
        }

        return $path;
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

        return $baseUrl.'/'.trim($bucket, '/').'/'.ltrim($path, '/');
    }
}
