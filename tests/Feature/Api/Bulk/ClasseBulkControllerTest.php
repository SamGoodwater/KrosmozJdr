<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Classe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ClasseBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs classes en masse
 * - La validation fonctionne correctement
 * - Seuls les champs fournis sont modifiés
 */
class ClasseBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs classes en masse
     */
    public function test_admin_can_bulk_update_classes(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $classe1 = Classe::factory()->create(['life' => '50', 'life_dice' => '1d6']);
        $classe2 = Classe::factory()->create(['life' => '60', 'life_dice' => '1d8']);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/classes/bulk', [
                'ids' => [$classe1->id, $classe2->id],
                'life' => '100',
                'life_dice' => '1d10',
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('classes', [
            'id' => $classe1->id,
            'life' => '100',
            'life_dice' => '1d10',
        ]);
        $this->assertDatabaseHas('classes', [
            'id' => $classe2->id,
            'life' => '100',
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
            ->patchJson('/api/entities/classes/bulk', [
                'ids' => [99999, 99998],
                'life' => '100',
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
        $classe = Classe::factory()->create([
            'life' => '50',
            'life_dice' => '1d6',
            'description' => 'Description originale',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/classes/bulk', [
                'ids' => [$classe->id],
                'life' => '100',
                // life_dice et description ne sont pas modifiés
            ]);

        $response->assertOk();

        $classe->refresh();
        $this->assertEquals('100', $classe->life);
        $this->assertEquals('1d6', $classe->life_dice); // Non modifié
        $this->assertEquals('Description originale', $classe->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_classes(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $classe = Classe::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/classes/bulk', [
                'ids' => [$classe->id],
                'life' => '100',
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : La validation échoue si aucun champ n'est fourni
     */
    public function test_validation_fails_if_no_fields_provided(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $classe = Classe::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/classes/bulk', [
                'ids' => [$classe->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}

