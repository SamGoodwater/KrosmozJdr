<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Consumable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ConsumableBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs consommables en masse
 * - La validation fonctionne correctement
 * - Le type de consommable (consumable_type_id) est validé
 * - Seuls les champs fournis sont modifiés
 */
class ConsumableBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs consommables en masse
     */
    public function test_admin_can_bulk_update_consumables(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $consumable1 = Consumable::factory()->create(['level' => '10', 'usable' => false]);
        $consumable2 = Consumable::factory()->create(['level' => '20', 'usable' => true]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/consumables/bulk', [
                'ids' => [$consumable1->id, $consumable2->id],
                'level' => '50',
                'usable' => true,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('consumables', [
            'id' => $consumable1->id,
            'level' => '50',
            'usable' => 1,
        ]);
        $this->assertDatabaseHas('consumables', [
            'id' => $consumable2->id,
            'level' => '50',
            'usable' => 1,
        ]);
    }

    /**
     * Test : La validation échoue avec des IDs invalides
     */
    public function test_validation_fails_with_invalid_ids(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/consumables/bulk', [
                'ids' => [99999, 99998],
                'level' => '50',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('ids.0');
    }

    /**
     * Test : Le type de consommable est validé
     */
    public function test_consumable_type_validation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $consumable = Consumable::factory()->create();

        // Test avec consumable_type_id invalide (doit échouer)
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/consumables/bulk', [
                'ids' => [$consumable->id],
                'consumable_type_id' => 99999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('consumable_type_id');
    }

    /**
     * Test : Seuls les champs fournis sont modifiés
     */
    public function test_only_provided_fields_are_updated(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $consumable = Consumable::factory()->create([
            'level' => '10',
            'rarity' => 1,
            'usable' => false,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/consumables/bulk', [
                'ids' => [$consumable->id],
                'level' => '50',
                // rarity et usable ne sont pas modifiés
            ]);

        $response->assertOk();

        $consumable->refresh();
        $this->assertEquals('50', $consumable->level);
        $this->assertEquals(1, $consumable->rarity); // Non modifié
        $this->assertEquals(0, $consumable->usable); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_consumables(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $consumable = Consumable::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/consumables/bulk', [
                'ids' => [$consumable->id],
                'level' => '50',
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : La validation échoue si aucun champ n'est fourni
     */
    public function test_validation_fails_if_no_fields_provided(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $consumable = Consumable::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/consumables/bulk', [
                'ids' => [$consumable->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}

