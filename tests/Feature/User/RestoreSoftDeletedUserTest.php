<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature : restauration d'un utilisateur soft-deleted via la route.
 *
 * @description
 * Sans résolution via `withTrashed()`, la route `/user/{user}/restore` ne peut pas
 * restaurer un utilisateur supprimé (404).
 *
 * @example
 * POST /user/{id}/restore (super_admin) -> utilisateur restauré
 */
class RestoreSoftDeletedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_restore_soft_deleted_user(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $target = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);
        $target->delete();

        $response = $this->actingAs($superAdmin)->post(route('user.restore', $target->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'deleted_at' => null,
        ]);
    }
}


