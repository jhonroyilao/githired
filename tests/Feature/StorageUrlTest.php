<?php

namespace Tests\Feature;

use App\Support\StorageUrl;
use Tests\TestCase;

class StorageUrlTest extends TestCase
{
    public function test_supabase_image_url_strips_bucket_prefix_from_stored_path(): void
    {
        config([
            'filesystems.image_disk' => 'supabase-images',
            'filesystems.disks.supabase-images.endpoint' => 'https://example.storage.supabase.co/storage/v1/s3',
            'filesystems.disks.supabase-images.bucket' => 'images',
        ]);

        $url = StorageUrl::image('images/avatars/profile.png');

        $this->assertSame(
            'https://example.supabase.co/storage/v1/object/public/images/avatars/profile.png',
            $url,
        );
        $this->assertSame('avatars/profile.png', StorageUrl::imageObjectPath('images/avatars/profile.png'));
    }
}
