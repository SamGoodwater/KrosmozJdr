<?php

namespace Tests\Feature\Api\Bulk;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Breed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour BreedBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs breeds (classes) en masse
 * - La validation fonctionne correctement
 * - Seuls les champs fournis sont modifiés
 */
class BreedBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs breeds en masse
     */
    public function test_admin_can_bulk_update_breeds(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $breed1 = Breed::factory()->create(['life_dice' => '1d6']);
        $breed2 = Breed::factory()->create(['life_dice' => '1d8']);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/breeds/bulk', [
                'ids' => [$breed1->id, $breed2->id],
                'life_dice' => '1d10',
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('breeds', [
            'id' => $breed1->id,
            'life_dice' => '1d10',
        ]);
        $this->assertDatabaseHas('breeds', [
            'id' => $breed2->id,
            'life_dice' => '1d10',
        ]);
    }

    /**
     * Test : La validation échoue avec des IDs invalides
     */
    public function test_validation_fails_with_invalid_ids(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/breeds/bulk', [
                'ids' => [99999, 99998],
                'life_dice' => '1d10',
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
        $breed = Breed::factory()->create([
            'life_dice' => '1d6',
            'description' => 'Description originale',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/breeds/bulk', [
                'ids' => [$breed->id],
                'life_dice' => '1d10',
                // description ne sont pas modifiés
            ]);

        $response->assertOk();

        $breed->refresh();
        $this->assertEquals('1d10', $breed->life_dice);
        $this->assertEquals('Description originale', $breed->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_breeds(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $breed = Breed::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/breeds/bulk', [
                'ids' => [$breed->id],
                'life_dice' => '1d10',
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : La validation échoue si aucun champ n'est fourni
     */
    public function test_validation_fails_if_no_fields_provided(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $breed = Breed::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/breeds/bulk', [
                'ids' => [$breed->id],
            ]);

        $response->assertStatus(422);
    }
}
