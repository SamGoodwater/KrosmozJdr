<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Monster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour MonsterBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs monstres en masse
 * - La validation fonctionne correctement
 * - L'hostility est validé (0-4)
 * - Seuls les champs fournis sont modifiés
 */
class MonsterBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs monstres en masse
     */
    public function test_admin_can_bulk_update_monsters(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster1 = Monster::factory()->create([
            'size' => 1,
            'is_boss' => false,
        ]);
        $monster2 = Monster::factory()->create([
            'size' => 2,
            'is_boss' => true,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/monsters/bulk', [
                'ids' => [$monster1->id, $monster2->id],
                'size' => 3,
                'is_boss' => true,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('monsters', [
            'id' => $monster1->id,
            'size' => 3,
            'is_boss' => 1,
        ]);
        $this->assertDatabaseHas('monsters', [
            'id' => $monster2->id,
            'size' => 3,
            'is_boss' => 1,
        ]);
    }

    /**
     * Test : La validation échoue avec des IDs invalides
     */
    public function test_validation_fails_with_invalid_ids(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/monsters/bulk', [
                'ids' => [99999, 99998],
                'size' => 3,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('ids.0');
    }

    /**
     * Test : La taille (size) est validée (0-5)
     */
    public function test_size_validation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();

        // Test avec size valide (0-5)
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/monsters/bulk', [
                'ids' => [$monster->id],
                'size' => 2,
            ]);

        $response->assertOk();

        $monster->refresh();
        $this->assertEquals(2, $monster->size);

        // Test avec size invalide (hors limites)
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/monsters/bulk', [
                'ids' => [$monster->id],
                'size' => 10,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('size');
    }

    /**
     * Test : Seuls les champs fournis sont modifiés
     */
    public function test_only_provided_fields_are_updated(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create([
            'size' => 1,
            'is_boss' => false,
            'auto_update' => false,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/monsters/bulk', [
                'ids' => [$monster->id],
                'size' => 3,
                // is_boss et auto_update ne sont pas modifiés
            ]);

        $response->assertOk();

        $monster->refresh();
        $this->assertEquals(3, $monster->size);
        $this->assertEquals(0, $monster->is_boss); // Non modifié
        $this->assertEquals(0, $monster->auto_update); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_monsters(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $monster = Monster::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/monsters/bulk', [
                'ids' => [$monster->id],
                'size' => 3,
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : La validation échoue si aucun champ n'est fourni
     */
    public function test_validation_fails_if_no_fields_provided(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/monsters/bulk', [
                'ids' => [$monster->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}

