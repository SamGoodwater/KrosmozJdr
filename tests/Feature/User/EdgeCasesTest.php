<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_nonexistent_user_returns_404()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $response = $this->actingAs($admin)->get('/user/999999/edit');
        $response->assertNotFound();
    }

    public function test_cannot_promote_to_super_admin()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $user = User::factory()->create(['role' => User::roleValue('user')]);
        $response = $this->actingAs($admin)->patch('/user/' . $user->id . '/role', [
            'role' => User::roleValue('super_admin'),
        ]);
        $response->assertSessionHasErrors('role');
        $this->assertEquals(User::roleValue('user'), $user->fresh()->role);
    }

    public function test_upload_invalid_avatar_type()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('avatar.pdf', 100, 'application/pdf');
        $response = $this->actingAs($user)->post('/user/avatar', [
            'avatar' => $file,
        ]);
        $response->assertSessionHasErrors('avatar');
    }

    public function test_restore_already_active_user()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->post('/user/' . $user->id);
        $response->assertSessionHasErrors();
        $this->assertNull($user->fresh()->deleted_at);
    }
}
