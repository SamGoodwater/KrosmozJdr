<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ItemBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs objets en masse
 * - La validation fonctionne correctement
 * - La validation du champ rarity fonctionne
 * - Seuls les champs fournis sont modifiés
 */
class ItemBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs objets en masse
     */
    public function test_admin_can_bulk_update_items(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item1 = Item::factory()->create([
            'name' => 'Item 1',
            'is_visible' => 'guest',
        ]);
        $item2 = Item::factory()->create([
            'name' => 'Item 2',
            'is_visible' => 'user',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/items/bulk', [
                'ids' => [$item1->id, $item2->id],
                'is_visible' => 'admin',
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('items', [
            'id' => $item1->id,
            'is_visible' => 'admin',
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $item2->id,
            'is_visible' => 'admin',
        ]);
    }

    /**
     * Test : La validation échoue avec des IDs invalides
     */
    public function test_validation_fails_with_invalid_ids(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/items/bulk', [
                'ids' => [99999, 99998],
                'is_visible' => 'admin',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('ids.0');
    }

    /**
     * Test : La validation du champ rarity fonctionne
     */
    public function test_rarity_validation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();

        // Test avec rarity valide
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/items/bulk', [
                'ids' => [$item->id],
                'rarity' => 3,
            ]);

        $response->assertOk();

        $item->refresh();
        $this->assertEquals(3, $item->rarity);

        // Test avec rarity invalide (hors limites 0-4)
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/items/bulk', [
                'ids' => [$item->id],
                'rarity' => 10,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('rarity');
    }

    /**
     * Test : Seuls les champs fournis sont modifiés
     */
    public function test_only_provided_fields_are_updated(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'is_visible' => 'guest',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/items/bulk', [
                'ids' => [$item->id],
                'is_visible' => 'admin',
                // name et description ne sont pas modifiés
            ]);

        $response->assertOk();

        $item->refresh();
        $this->assertEquals('admin', $item->is_visible);
        $this->assertEquals('Original Name', $item->name); // Non modifié
        $this->assertEquals('Original Description', $item->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_items(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/items/bulk', [
                'ids' => [$item->id],
                'is_visible' => 'admin',
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : La validation échoue si aucun champ n'est fourni
     */
    public function test_validation_fails_if_no_fields_provided(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/items/bulk', [
                'ids' => [$item->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}

