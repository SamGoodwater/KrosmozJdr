<?php

namespace Tests\Feature\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaThumbnailRouteTest extends TestCase
{
    public function test_thumbnail_returns_404_when_source_file_missing(): void
    {
        Storage::fake('public');

        $url = route('media.thumbnail', ['path' => 'does/not/exist.jpg']).'?w=32&h=32&fm=webp';

        $response = $this->get($url);

        $response->assertStatus(404);
    }

    public function test_thumbnail_rejects_suspect_path_with_parent_segment(): void
    {
        $url = route('media.thumbnail', ['path' => 'images/../.env']);

        $response = $this->get($url);

        $response->assertStatus(400);
    }

    public function test_thumbnail_generates_webp_when_source_exists(): void
    {
        if (! extension_loaded('imagick')) {
            $this->markTestSkipped('L’extension imagick est requise pour la génération de miniatures (Intervention v3 + driver Imagick).');
        }

        Storage::fake('public');

        $path = 'images/thumb_test_source.jpg';
        $tmp = UploadedFile::fake()->image('thumb_test_source.jpg', 4, 4);
        $contents = (string) file_get_contents($tmp->getRealPath());
        $this->assertNotSame('', $contents);
        Storage::disk('public')->put($path, $contents);

        $url = route('media.thumbnail', ['path' => $path]).'?w=32&h=32&fit=contain&fm=webp&q=80';

        $response = $this->get($url);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/webp');
    }
}
