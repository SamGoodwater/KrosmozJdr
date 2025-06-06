<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_own_account()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/user');
        $response->assertRedirect(route('user.dashboard', absolute: false));
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_can_delete_other_user()
    {
        $admin = User::factory()->create(['role' => User::roleValue('admin')]);
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->delete('/user/' . $user->id);
        $response->assertRedirect(route('user.dashboard', absolute: false));
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_user_cannot_delete_other_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user1)->delete('/user/' . $user2->id);
        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $user2->id, 'deleted_at' => null]);
    }
}
