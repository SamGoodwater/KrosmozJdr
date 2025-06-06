<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_verifies_role_hierarchy_correctly()
    {
        $admin = User::factory()->create();
        $admin->role = array_search('admin', \App\Models\User::ROLES, true);
        $admin->save();
        $player = User::factory()->create();
        $player->role = array_search('player', \App\Models\User::ROLES, true);
        $player->save();
        $superAdmin = User::factory()->create();
        $superAdmin->role = array_search('super_admin', \App\Models\User::ROLES, true);
        $superAdmin->save();

        $this->assertTrue($admin->verifyRole('player'));
        $this->assertFalse($player->verifyRole('admin'));
        $this->assertTrue($superAdmin->verifyRole('admin'));
        $this->assertTrue($superAdmin->verifyRole('super_admin'));
    }

    /** @test */
    public function only_admin_or_super_admin_can_update_role_of_other_users()
    {
        $admin = User::factory()->create();
        $admin->role = array_search('admin', \App\Models\User::ROLES, true);
        $admin->save();
        $user = User::factory()->create();
        $user->role = array_search('user', \App\Models\User::ROLES, true);
        $user->save();
        $superAdmin = User::factory()->create();
        $superAdmin->role = array_search('super_admin', \App\Models\User::ROLES, true);
        $superAdmin->save();

        $this->assertTrue($admin->updateRole($user));
        $this->assertTrue($superAdmin->updateRole($user));
        $this->assertFalse($user->updateRole($admin));
        $this->assertFalse($admin->updateRole($superAdmin));
        $this->assertFalse($admin->updateRole($admin)); // ne peut pas se modifier lui-même
    }

    /** @test */
    public function only_one_super_admin_can_exist()
    {
        $first = User::factory()->create();
        $first->role = array_search('super_admin', \App\Models\User::ROLES, true);
        $first->save();
        $this->expectException(\Illuminate\Database\QueryException::class);
        $second = User::factory()->create();
        $second->role = array_search('super_admin', \App\Models\User::ROLES, true);
        $second->save();
    }

    /** @test */
    public function avatar_path_returns_default_if_none()
    {
        $user = User::factory()->make(['avatar' => null]);
        $this->assertStringContainsString('default-avatar', $user->avatarPath());
    }

    /** @test */
    public function it_returns_true_for_wants_profile_notification()
    {
        $user = User::factory()->make();
        $this->assertTrue($user->wantsProfileNotification());
    }

    /** @test */
    public function it_returns_notification_channels_or_default()
    {
        $user = User::factory()->make(['notification_channels' => ['email']]);
        $this->assertEquals(['email'], $user->notificationChannels());

        $user = User::factory()->make(['notification_channels' => null]);
        $this->assertEquals(['database'], $user->notificationChannels());
    }

    /** @test */
    public function it_casts_notifications_enabled_to_boolean()
    {
        $user = User::factory()->make(['notifications_enabled' => 1]);
        $this->assertTrue($user->notifications_enabled);

        $user = User::factory()->make(['notifications_enabled' => 0]);
        $this->assertFalse($user->notifications_enabled);
    }

    /** @test */
    public function it_casts_notification_channels_to_array()
    {
        $user = User::factory()->make(['notification_channels' => json_encode(['database', 'email'])]);
        $this->assertIsArray($user->notification_channels);
    }

    /** @test */
    public function user_policy_allows_and_denies_actions_correctly()
    {
        $admin = User::factory()->create();
        $admin->role = array_search('admin', \App\Models\User::ROLES, true);
        $admin->save();
        $user = User::factory()->create();
        $user->role = array_search('user', \App\Models\User::ROLES, true);
        $user->save();
        $superAdmin = User::factory()->create();
        $superAdmin->role = array_search('super_admin', \App\Models\User::ROLES, true);
        $superAdmin->save();
        $policy = new \App\Policies\UserPolicy();

        // update
        $this->assertTrue($policy->update($admin, $user));
        $this->assertTrue($policy->update($user, $user));
        $this->assertFalse($policy->update($user, $admin));
        // delete
        $this->assertTrue($policy->delete($admin, $user));
        $this->assertTrue($policy->delete($user, $user));
        $this->assertFalse($policy->delete($user, $admin));
        // restore
        $this->assertTrue($policy->restore($admin, $user));
        $this->assertFalse($policy->restore($user, $admin));
        // forceDelete
        $this->assertTrue($policy->forceDelete($superAdmin, $user));
        $this->assertFalse($policy->forceDelete($admin, $user));
        // updateRole
        $this->assertTrue($policy->updateRole($superAdmin, $user));
        $this->assertTrue($policy->updateRole($admin, $user));
        $this->assertFalse($policy->updateRole($user, $admin));
    }
}
