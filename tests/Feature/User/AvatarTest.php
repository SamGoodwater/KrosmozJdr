<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_can_be_uploaded()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->actingAs($user)->post('/user/avatar', [
            'avatar' => $file,
        ]);
        $response->assertRedirect();
        $user->refresh();
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_avatar_can_be_deleted()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatars/old-avatar.jpg');
        Storage::disk('public')->put('avatars/old-avatar.jpg', 'fake-content');
        $response = $this->actingAs($user)->delete('/user/avatar');
        $response->assertRedirect();
        $user->refresh();
        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
    }

    public function test_avatar_upload_requires_authentication()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->post('/user/avatar', [
            'avatar' => $file,
        ]);
        $response->assertRedirect('/login');
    }

    public function test_avatar_upload_validation()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('not-an-image.txt', 100, 'text/plain');
        $response = $this->actingAs($user)->post('/user/avatar', [
            'avatar' => $file,
        ]);
        $response->assertSessionHasErrors('avatar');
    }
}
