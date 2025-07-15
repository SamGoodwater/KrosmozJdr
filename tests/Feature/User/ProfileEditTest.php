<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_screen_accessible()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user/edit');
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) =>
            $page->component('Organisms/User/Edit')
                ->where('user.data.id', $user->id)
        );
    }

    public function test_profile_can_be_updated()
    {
        $user = User::factory()->create();
        $newData = [
            'name' => 'New Name',
            'email' => 'newemail@example.com',
            'notifications_enabled' => false,
            'notification_channels' => ['database'],
        ];
        $response = $this->actingAs($user)->patch('/user', $newData);
        $response->assertRedirect(route('user.show', $user, absolute: false));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'newemail@example.com',
            'notifications_enabled' => false,
        ]);
    }

    public function test_profile_update_requires_authentication()
    {
        $user = User::factory()->create();
        $response = $this->patch('/user', [
            'name' => 'Hacker',
        ]);
        $response->assertRedirect('/login');
        $this->assertNotEquals('Hacker', $user->fresh()->name);
    }
}
