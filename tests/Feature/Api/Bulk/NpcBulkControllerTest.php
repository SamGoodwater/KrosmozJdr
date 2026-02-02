<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Npc;
use App\Models\Entity\Breed;
use App\Models\Entity\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour NpcBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs NPCs en masse
 * - La validation fonctionne correctement
 * - Les clés étrangères (breed_id, specialization_id) sont validées
 * - Seuls les champs fournis sont modifiés
 */
class NpcBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs NPCs en masse
     */
    public function test_admin_can_bulk_update_npcs(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc1 = Npc::factory()->create(['age' => '25', 'size' => 'Moyen']);
        $npc2 = Npc::factory()->create(['age' => '30', 'size' => 'Grand']);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [$npc1->id, $npc2->id],
                'age' => '40',
                'size' => 'Petit',
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('npcs', [
            'id' => $npc1->id,
            'age' => '40',
            'size' => 'Petit',
        ]);
        $this->assertDatabaseHas('npcs', [
            'id' => $npc2->id,
            'age' => '40',
            'size' => 'Petit',
        ]);
    }

    /**
     * Test : La validation échoue avec des IDs invalides
     */
    public function test_validation_fails_with_invalid_ids(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [99999, 99998],
                'age' => '40',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('ids.0');
    }

    /**
     * Test : Les clés étrangères sont validées
     */
    public function test_foreign_key_validation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc = Npc::factory()->create();
        $breed = Breed::factory()->create();
        $specialization = Specialization::factory()->create();

        // Test avec breed_id valide
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [$npc->id],
                'breed_id' => $breed->id,
            ]);

        $response->assertOk();

        $npc->refresh();
        $this->assertEquals($breed->id, $npc->breed_id);

        // Test avec breed_id invalide
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [$npc->id],
                'breed_id' => 99999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('breed_id');

        // Test avec specialization_id valide
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [$npc->id],
                'specialization_id' => $specialization->id,
            ]);

        $response->assertOk();

        $npc->refresh();
        $this->assertEquals($specialization->id, $npc->specialization_id);
    }

    /**
     * Test : Seuls les champs fournis sont modifiés
     */
    public function test_only_provided_fields_are_updated(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc = Npc::factory()->create([
            'age' => '25',
            'size' => 'Moyen',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [$npc->id],
                'age' => '40',
                // size n'est pas modifié
            ]);

        $response->assertOk();

        $npc->refresh();
        $this->assertEquals('40', $npc->age);
        $this->assertEquals('Moyen', $npc->size); // Non modifié
    }

    /**
     * Test : Les champs nullable peuvent être vidés
     */
    public function test_nullable_fields_can_be_cleared(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc = Npc::factory()->create([
            'age' => '25',
            'breed_id' => Breed::factory()->create()->id,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [$npc->id],
                'breed_id' => null,
                'specialization_id' => null,
            ]);

        $response->assertOk();

        $npc->refresh();
        $this->assertNull($npc->breed_id);
        $this->assertNull($npc->specialization_id);
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_npcs(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $npc = Npc::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [$npc->id],
                'age' => '40',
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : La validation échoue si aucun champ n'est fourni
     */
    public function test_validation_fails_if_no_fields_provided(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc = Npc::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/npcs/bulk', [
                'ids' => [$npc->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}

