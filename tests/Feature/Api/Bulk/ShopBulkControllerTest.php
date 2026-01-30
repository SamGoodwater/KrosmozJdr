<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ShopBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs boutiques en masse
 * - La validation fonctionne correctement
 * - Seuls les champs fournis sont modifiés
 */
class ShopBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs boutiques en masse
     */
    public function test_admin_can_bulk_update_shops(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop1 = Shop::factory()->create([
            'name' => 'Shop 1',
            'read_level' => User::ROLE_GUEST,
        ]);
        $shop2 = Shop::factory()->create([
            'name' => 'Shop 2',
            'read_level' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/shops/bulk', [
                'ids' => [$shop1->id, $shop2->id],
                'read_level' => User::ROLE_ADMIN,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('shops', [
            'id' => $shop1->id,
            'read_level' => User::ROLE_ADMIN,
        ]);
        $this->assertDatabaseHas('shops', [
            'id' => $shop2->id,
            'read_level' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * Test : La validation échoue avec des IDs invalides
     */
    public function test_validation_fails_with_invalid_ids(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/shops/bulk', [
                'ids' => [99999, 99998],
                'read_level' => User::ROLE_ADMIN,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('ids.0');
    }

    /**
     * Test : Seuls les champs fournis sont modifiés
     */
    public function test_only_provided_fields_are_updated(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'read_level' => User::ROLE_GUEST,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/shops/bulk', [
                'ids' => [$shop->id],
                'read_level' => User::ROLE_ADMIN,
                // name et description ne sont pas modifiés
            ]);

        $response->assertOk();

        $shop->refresh();
        $this->assertEquals(User::ROLE_ADMIN, $shop->read_level);
        $this->assertEquals('Original Name', $shop->name); // Non modifié
        $this->assertEquals('Original Description', $shop->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_shops(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $shop = Shop::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/shops/bulk', [
                'ids' => [$shop->id],
                'read_level' => User::ROLE_ADMIN,
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : La validation échoue si aucun champ n'est fourni
     */
    public function test_validation_fails_if_no_fields_provided(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/shops/bulk', [
                'ids' => [$shop->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}

