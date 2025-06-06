<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $response = $this->actingAs($admin)->get('/user');
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) =>
            $page->component('Organisms/User/Index')
                ->has('users.data')
        );
    }

    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'notifications_enabled' => true,
            'notification_channels' => ['database'],
        ];
        $response = $this->actingAs($admin)->post('/user', $data);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'name' => 'Test User',
        ]);
    }

    public function test_admin_can_edit_user()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->get('/user/' . $user->id . '/edit');
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) =>
            $page->component('Organisms/User/Edit')
                ->where('user.data.id', $user->id)
        );
    }

    public function test_admin_can_update_user_role()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $user = User::factory()->create(['role' => User::roleValue('user')]);
        $response = $this->actingAs($admin)->patch('/user/' . $user->id . '/role', [
            'role' => User::roleValue('player'),
        ]);
        $response->assertRedirect();
        $this->assertEquals(User::roleValue('player'), $user->fresh()->role);
    }

    public function test_admin_can_restore_user()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $user = User::factory()->create();
        $user->delete();
        $response = $this->actingAs($admin)->post('/user/' . $user->id);
        $response->assertRedirect();
        $this->assertNull($user->fresh()->deleted_at);
    }

    public function test_super_admin_can_force_delete_user()
    {
        $superAdmin = User::factory()->create(['role' => User::roleValue('super_admin')]);
        $user = User::factory()->create();
        $response = $this->actingAs($superAdmin)->delete('/user/forcedDelete/' . $user->id);
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_modify_super_admin()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $superAdmin = User::factory()->create(['role' => User::roleValue('super_admin')]);
        $response = $this->actingAs($admin)->patch('/user/' . $superAdmin->id . '/role', [
            'role' => User::roleValue('player'),
        ]);
        $response->assertForbidden();
        $this->assertEquals(User::roleValue('super_admin'), $superAdmin->fresh()->role);
    }
}
