<?php

namespace Tests\Unit\User;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests Unitaires pour UserController
 * 
 * Teste directement la logique métier sans passer par les routes HTTP
 * pour éviter les problèmes de CSRF et de middleware
 */
class UserControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    // Note: Les tests de mise à jour de profil sont testés via les tests de policy
    // qui vérifient les autorisations. Les tests HTTP complets sont dans UserControllerTest

    /**
     * Test : Un utilisateur peut modifier son propre mot de passe avec current_password
     */
    public function test_user_can_update_own_password_with_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        Auth::login($user);

        $request = Request::create('/user/password', 'PATCH', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $request->setUserResolver(fn() => $user);

        $controller = new UserController();
        $response = $controller->updatePassword($request, $user);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /**
     * Test : Un utilisateur ne peut pas modifier son mot de passe sans current_password
     */
    public function test_user_cannot_update_password_without_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        Auth::login($user);

        $request = Request::create('/user/password', 'PATCH', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $request->setUserResolver(fn() => $user);

        $controller = new UserController();
        
        try {
            $response = $controller->updatePassword($request, $user);
            $this->fail('Expected validation exception');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('current_password', $e->errors());
        }

        $user->refresh();
        $this->assertTrue(Hash::check('oldpassword', $user->password));
    }

    /**
     * Test : Un admin peut modifier le mot de passe d'un autre utilisateur sans current_password
     */
    public function test_admin_can_update_other_user_password_without_current_password(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        Auth::login($admin);

        $request = Request::create("/user/{$targetUser->id}/password", 'PATCH', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $request->setUserResolver(fn() => $admin);

        $controller = new UserController();
        $response = $controller->updatePassword($request, $targetUser);

        $targetUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $targetUser->password));
    }

    /**
     * Test : Un admin peut modifier le rôle d'un utilisateur (mais pas admin/super_admin)
     */
    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);

        Auth::login($admin);

        $request = Request::create(route('user.admin.updateRole', $targetUser), 'PATCH', [
            'role' => User::ROLE_PLAYER,
        ]);
        $request->setUserResolver(fn() => $admin);

        $controller = new UserController();
        $response = $controller->updateRole($request, $targetUser);

        $targetUser->refresh();
        $this->assertEquals(User::ROLE_PLAYER, $targetUser->role);
    }

    /**
     * Test : Un admin ne peut pas promouvoir un utilisateur en admin
     */
    public function test_admin_cannot_promote_user_to_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);

        Auth::login($admin);

        $request = Request::create(route('user.admin.updateRole', $targetUser), 'PATCH', [
            'role' => User::ROLE_ADMIN,
        ]);
        $request->setUserResolver(fn() => $admin);

        $controller = new UserController();
        $response = $controller->updateRole($request, $targetUser);

        // Le contrôleur doit retourner avec une erreur
        $this->assertTrue($response->getSession()->has('errors'));
        $targetUser->refresh();
        $this->assertNotEquals(User::ROLE_ADMIN, $targetUser->role);
    }

    /**
     * Test : Un super_admin peut promouvoir un utilisateur en admin
     */
    public function test_super_admin_can_promote_user_to_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);

        Auth::login($superAdmin);

        $request = Request::create(route('user.admin.updateRole', $targetUser), 'PATCH', [
            'role' => User::ROLE_ADMIN,
        ]);
        $request->setUserResolver(fn() => $superAdmin);

        $controller = new UserController();
        $response = $controller->updateRole($request, $targetUser);

        $targetUser->refresh();
        $this->assertEquals(User::ROLE_ADMIN, $targetUser->role);
    }

    /**
     * Test : Personne ne peut promouvoir un utilisateur en super_admin
     */
    public function test_nobody_can_promote_user_to_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);

        Auth::login($superAdmin);

        $request = Request::create(route('user.admin.updateRole', $targetUser), 'PATCH', [
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $request->setUserResolver(fn() => $superAdmin);

        $controller = new UserController();
        $response = $controller->updateRole($request, $targetUser);

        // Le contrôleur doit retourner avec une erreur
        $this->assertTrue($response->getSession()->has('errors'));
        $targetUser->refresh();
        $this->assertNotEquals(User::ROLE_SUPER_ADMIN, $targetUser->role);
    }
}

