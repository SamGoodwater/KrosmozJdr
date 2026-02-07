<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tests Feature pour UserController
 * 
 * Vérifie que :
 * - Un utilisateur peut modifier son propre profil
 * - Un admin peut modifier n'importe quel utilisateur
 * - Un super_admin peut modifier n'importe quel utilisateur
 * - Les validations de mot de passe fonctionnent correctement
 * - Les policies fonctionnent correctement
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Désactiver le middleware role pour les tests (on teste les policies directement)
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
        
        // Désactiver explicitement le CSRF pour les tests
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);
    }

    /**
     * Test : Un utilisateur peut modifier son propre profil
     */
    public function test_user_can_update_own_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this->actingAs($user)
            ->from(route('user.edit'))
            ->patch(route('user.update'), [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }

    /**
     * Test : Un utilisateur ne peut pas modifier le profil d'un autre utilisateur
     */
    public function test_user_cannot_update_other_user_profile(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('user.admin.update', $otherUser), [
            'name' => 'Hacked Name',
            'email' => 'hacked@example.com',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', [
            'id' => $otherUser->id,
            'name' => 'Hacked Name',
        ]);
    }

    /**
     * Test : Un admin peut modifier n'importe quel utilisateur
     */
    public function test_admin_can_update_any_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create([
            'name' => 'Target User',
            'email' => 'target@example.com',
        ]);

        $response = $this->actingAs($admin)->patch(route('user.admin.update', $targetUser), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect(route('user.show', $targetUser));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test : Un super_admin peut modifier n'importe quel utilisateur
     */
    public function test_super_admin_can_update_any_user(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $targetUser = User::factory()->create([
            'name' => 'Target User',
            'email' => 'target@example.com',
        ]);

        $this->assertTrue($superAdmin->can('update', $targetUser));

        $response = $this->actingAs($superAdmin)->patch(route('user.admin.update', $targetUser), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test : Un utilisateur peut modifier son propre mot de passe avec current_password
     */
    public function test_user_can_update_own_password_with_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($user)
            ->from(route('user.edit'))
            ->patch('/user/password', [
                'current_password' => 'oldpassword',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect();
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

        $response = $this->actingAs($user)
            ->from(route('user.edit'))
            ->patch('/user/password', [
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        // La validation devrait échouer car current_password est requis
        // Vérifier soit les erreurs de session, soit le code de statut 422
        if ($response->status() === 422) {
            $response->assertStatus(422);
        } else {
            $response->assertSessionHasErrors('current_password');
        }
        $user->refresh();
        $this->assertTrue(Hash::check('oldpassword', $user->password));
    }

    /**
     * Test : Un utilisateur ne peut pas modifier son mot de passe avec un current_password incorrect
     */
    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($user)
            ->from(route('user.edit'))
            ->withSession(['_token' => 'test-token'])
            ->patch('/user/password', [
                'current_password' => 'wrongpassword',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertSessionHasErrors('current_password');
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

        $response = $this->actingAs($admin)->patch(route('user.admin.updatePassword', $targetUser), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $targetUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $targetUser->password));
    }

    /**
     * Test : Un super_admin peut modifier le mot de passe d'un autre utilisateur sans current_password
     */
    public function test_super_admin_can_update_other_user_password_without_current_password(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $targetUser = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($superAdmin)->patch(route('user.admin.updatePassword', $targetUser), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $targetUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $targetUser->password));
    }

    /**
     * Test : Un utilisateur ne peut pas modifier le rôle d'un autre utilisateur
     */
    public function test_user_cannot_update_other_user_role(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->patch(route('user.admin.updateRole', $targetUser), [
            'role' => User::ROLE_ADMIN,
        ]);

        $response->assertForbidden();
    }

    /**
     * Test : Un admin peut modifier le rôle d'un utilisateur (mais pas admin/super_admin)
     */
    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($admin)->patch(route('user.admin.updateRole', $targetUser), [
            'role' => User::ROLE_PLAYER,
        ]);

        $response->assertRedirect();
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

        $response = $this->actingAs($admin)->patch(route('user.admin.updateRole', $targetUser), [
            'role' => User::ROLE_ADMIN,
        ]);

        $response->assertSessionHasErrors('role');
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

        $response = $this->actingAs($superAdmin)->patch(route('user.admin.updateRole', $targetUser), [
            'role' => User::ROLE_ADMIN,
        ]);

        $response->assertRedirect();
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

        $response = $this->actingAs($superAdmin)->patch(route('user.admin.updateRole', $targetUser), [
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response->assertSessionHasErrors('role');
        $targetUser->refresh();
        $this->assertNotEquals(User::ROLE_SUPER_ADMIN, $targetUser->role);
    }

    /**
     * Test : Un utilisateur peut accéder à la page d'édition de son propre profil
     */
    public function test_user_can_access_own_edit_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.edit'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/user/Edit')
            ->has('user')
        );
    }

    /**
     * Test : Un admin peut accéder à la page d'édition de n'importe quel utilisateur
     */
    public function test_admin_can_access_any_user_edit_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create();

        $this->assertTrue($admin->can('update', $targetUser));

        $response = $this->actingAs($admin)->get(route('user.admin.edit', $targetUser));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/user/Edit')
            ->has('user')
        );
    }

    /**
     * Test : Un utilisateur ne peut pas accéder à la page d'édition d'un autre utilisateur
     */
    public function test_user_cannot_access_other_user_edit_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.admin.edit', $targetUser));

        $response->assertForbidden();
    }

    /**
     * Test : Un utilisateur peut uploader son avatar (Media Library).
     */
    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $file = UploadedFile::fake()->image('avatar.png', 200, 200);

        $response = $this->actingAs($user)
            ->post(route('user.updateAvatar'), [
                'avatar' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertCount(1, $user->getMedia('avatars'));
        $this->assertNotEmpty($user->avatar);
    }

    /**
     * Test : Un utilisateur peut supprimer son avatar.
     */
    public function test_user_can_delete_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $user->addMedia(UploadedFile::fake()->image('avatar.png'))->toMediaCollection('avatars');
        $user->update(['avatar' => $user->getFirstMediaUrl('avatars')]);

        $response = $this->actingAs($user)
            ->delete(route('user.deleteAvatar'));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertCount(0, $user->getMedia('avatars'));
        $this->assertNull($user->avatar);
    }
}

