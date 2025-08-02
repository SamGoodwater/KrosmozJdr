<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_it_verifies_role_hierarchy_correctly()
    {
        $user = User::factory()->create(['role' => 2]); // player

        $this->assertTrue($user->verifyRole('guest'));
        $this->assertTrue($user->verifyRole('user'));
        $this->assertTrue($user->verifyRole('player'));
        $this->assertFalse($user->verifyRole('game_master'));
        $this->assertFalse($user->verifyRole('admin'));
        $this->assertFalse($user->verifyRole('super_admin'));
    }

    public function test_super_admin_has_all_permissions()
    {
        $superAdmin = User::factory()->create(['role' => 5]); // super_admin
        $user = User::factory()->create(['role' => 1]); // user

        // Un super_admin peut accéder à tous les rôles
        $this->assertTrue($superAdmin->verifyRole('guest'));
        $this->assertTrue($superAdmin->verifyRole('user'));
        $this->assertTrue($superAdmin->verifyRole('player'));
        $this->assertTrue($superAdmin->verifyRole('game_master'));
        $this->assertTrue($superAdmin->verifyRole('admin'));
        $this->assertTrue($superAdmin->verifyRole('super_admin'));
    }

    public function test_avatar_path_returns_default_if_none()
    {
        $user = User::factory()->create(['avatar' => null]);
        $this->assertStringContainsString('default-avatar.webp', $user->avatarPath());
    }

    public function test_it_returns_true_for_wants_profile_notification()
    {
        $user = User::factory()->create();
        $this->assertTrue($user->wantsProfileNotification());
    }

    public function test_it_casts_notifications_enabled_to_boolean()
    {
        $user = User::factory()->create(['notifications_enabled' => 1]);
        $this->assertTrue($user->notifications_enabled);
    }

    public function test_it_casts_notification_channels_to_array()
    {
        $user = User::factory()->create(['notification_channels' => ['database', 'email']]);
        $this->assertIsArray($user->notification_channels);
    }

    public function test_user_policy_basic_functionality()
    {
        $policy = new UserPolicy();
        $admin = User::factory()->create(['role' => 4]);
        $user = User::factory()->create(['role' => 1]);

        // Test basique de la politique
        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->viewAny($user));
    }
}
