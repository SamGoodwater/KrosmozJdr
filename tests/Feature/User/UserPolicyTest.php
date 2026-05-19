<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Tests Feature pour UserPolicy
 *
 * Vérifie que les policies autorisent correctement :
 * - Un utilisateur peut modifier son propre profil
 * - Un admin peut modifier n'importe quel utilisateur
 * - Un super_admin peut modifier n'importe quel utilisateur
 * - Les règles de modification de rôle sont respectées
 */
class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test : Un utilisateur peut modifier son propre profil
     */
    public function test_user_can_update_own_profile(): void
    {
        $user = User::factory()->create();
        $policy = new UserPolicy;

        $this->assertTrue($policy->update($user, $user));
    }

    /**
     * Test : Un utilisateur ne peut pas modifier le profil d'un autre utilisateur
     */
    public function test_user_cannot_update_other_user_profile(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create();
        $policy = new UserPolicy;

        $this->assertFalse($policy->update($user, $otherUser));
    }

    /**
     * Test : Un admin peut modifier n'importe quel utilisateur
     */
    public function test_admin_can_update_any_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create();
        $policy = new UserPolicy;

        $this->assertTrue($policy->update($admin, $targetUser));
    }

    /**
     * Test : Un super_admin peut modifier n'importe quel utilisateur
     */
    public function test_super_admin_can_update_any_user(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $targetUser = User::factory()->create();
        $policy = new UserPolicy;

        // Le super_admin a tous les droits via before()
        $this->assertTrue($policy->update($superAdmin, $targetUser));
    }

    /**
     * Test : Un utilisateur ne peut pas modifier le rôle d'un autre utilisateur
     */
    public function test_user_cannot_update_other_user_role(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);
        $policy = new UserPolicy;

        $this->assertFalse($policy->updateRole($user, $targetUser));
    }

    /**
     * Test : Un admin peut modifier le rôle d'un utilisateur (mais pas admin/super_admin)
     */
    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);
        $policy = new UserPolicy;

        $this->assertTrue($policy->updateRole($admin, $targetUser));
    }

    /**
     * Test : Un admin ne peut pas modifier le rôle d'un admin
     */
    public function test_admin_cannot_update_admin_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $policy = new UserPolicy;

        $this->assertFalse($policy->updateRole($admin, $targetAdmin));
    }

    /**
     * Test : Un admin ne peut pas modifier le rôle d'un super_admin
     */
    public function test_admin_cannot_update_super_admin_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetSuperAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $policy = new UserPolicy;

        $this->assertFalse($policy->updateRole($admin, $targetSuperAdmin));
    }

    /**
     * Test : Un super_admin peut modifier le rôle d'un utilisateur (mais pas super_admin)
     */
    public function test_super_admin_can_update_user_role(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);
        $policy = new UserPolicy;

        // La gate applique bien `before(...)` avec `isInteractiveSuperAdmin()`.
        $this->assertTrue(Gate::forUser($superAdmin)->allows('updateRole', $targetUser));
    }

    /**
     * Compte système (is_system) : jamais super-admin interactif dans les policies métier HTTP.
     */
    public function test_system_super_admin_actor_has_no_privileged_super_policy(): void
    {
        $systemActor = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'is_system' => true,
        ]);
        $target = User::factory()->create(['role' => User::ROLE_USER]);
        $anotherSuperHuman = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $policy = new UserPolicy;

        $this->assertNull($policy->before($systemActor));
        $this->assertFalse($policy->resetPassword($systemActor, $target));
        $this->assertFalse($policy->forceDelete($systemActor, $target));

        // Un super humain existe déjà ; l'acteur système ne court-circuite pas updateRole().
        $this->assertFalse(Gate::forUser($systemActor)->allows('updateRole', $anotherSuperHuman));
    }

    /**
     * Test : Seuls admin/super_admin peuvent voir la liste des utilisateurs.
     */
    public function test_only_admin_can_view_any_users(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $policy = new UserPolicy;

        $this->assertFalse($policy->viewAny($user));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Test : Seuls admin/super_admin peuvent créer un utilisateur.
     */
    public function test_only_admin_can_create_users(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $policy = new UserPolicy;

        $this->assertFalse($policy->create($user));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * Test : Réinitialisation du mot de passe d'un tiers réservée au super_admin.
     */
    public function test_only_super_admin_can_reset_other_user_password(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $target = User::factory()->create(['role' => User::ROLE_USER]);
        $policy = new UserPolicy;

        $this->assertFalse($policy->resetPassword($admin, $target));
        $this->assertTrue($policy->resetPassword($superAdmin, $target));
        $this->assertFalse($policy->resetPassword($superAdmin, $superAdmin));
    }
}
