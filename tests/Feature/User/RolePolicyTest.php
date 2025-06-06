<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RolePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_access_admin_routes()
    {
        $user = User::factory()->create(['role' => User::roleValue('user')]);
        $response = $this->actingAs($user)->get('/user/create');
        $response->assertForbidden();
    }

    public function test_only_super_admin_can_access_super_admin_routes()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->delete('/user/forcedDelete/' . $user->id);
        $response->assertForbidden();
    }

    public function test_user_cannot_promote_to_super_admin()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $user = User::factory()->create(['role' => User::roleValue('user')]);
        $response = $this->actingAs($admin)->patch('/user/' . $user->id . '/role', [
            'role' => User::roleValue('super_admin'),
        ]);
        $response->assertSessionHasErrors('role');
        $this->assertEquals(User::roleValue('user'), $user->fresh()->role);
    }

    public function test_admin_cannot_modify_own_role()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $response = $this->actingAs($admin)->patch('/user/' . $admin->id . '/role', [
            'role' => User::roleValue('player'),
        ]);
        $response->assertForbidden();
        $this->assertEquals(User::roleValue('admin'), $admin->fresh()->role);
    }

    public function test_only_one_super_admin_can_exist()
    {
        $superAdmin = User::factory()->create(['role' => User::roleValue('super_admin')]);
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $response = $this->actingAs($admin)->patch('/user/' . $superAdmin->id . '/role', [
            'role' => User::roleValue('admin'),
        ]);
        $response->assertForbidden();
        $this->assertEquals(User::roleValue('super_admin'), $superAdmin->fresh()->role);
    }
}
