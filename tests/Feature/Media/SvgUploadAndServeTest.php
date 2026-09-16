<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SvgUploadAndServeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    public function test_svg_upload_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $svg = UploadedFile::fake()->create('xss.svg', 12, 'image/svg+xml');

        $this->actingAs($admin)
            ->postJson(route('api.entities.resources.upload-image'), [
                'file' => $svg,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    public function test_svg_is_not_served_as_a_document(): void
    {
        Storage::fake('public');
        $path = 'images/evil.svg';
        Storage::disk('public')->put($path, '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"></svg>');

        $this->get(route('media.show', ['path' => $path]))
            ->assertStatus(415)
            ->assertJsonFragment(['error' => 'Les SVG ne sont pas servis comme document (risque XSS).']);
    }
}
