<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour SpecializationBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs spécialisations en masse
 * - La validation fonctionne correctement
 * - Seuls les champs fournis sont modifiés
 */
class SpecializationBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs spécialisations en masse
     */
    public function test_admin_can_bulk_update_specializations(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $specialization1 = Specialization::factory()->create([
            'name' => 'Specialization 1',
            'read_level' => User::ROLE_GUEST,
        ]);
        $specialization2 = Specialization::factory()->create([
            'name' => 'Specialization 2',
            'read_level' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/specializations/bulk', [
                'ids' => [$specialization1->id, $specialization2->id],
                'read_level' => User::ROLE_ADMIN,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('specializations', [
            'id' => $specialization1->id,
            'read_level' => User::ROLE_ADMIN,
        ]);
        $this->assertDatabaseHas('specializations', [
            'id' => $specialization2->id,
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
            ->patchJson('/api/entities/specializations/bulk', [
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
        $specialization = Specialization::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'read_level' => User::ROLE_GUEST,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/specializations/bulk', [
                'ids' => [$specialization->id],
                'read_level' => User::ROLE_ADMIN,
                // name et description ne sont pas modifiés
            ]);

        $response->assertOk();

        $specialization->refresh();
        $this->assertEquals(User::ROLE_ADMIN, $specialization->read_level);
        $this->assertEquals('Original Name', $specialization->name); // Non modifié
        $this->assertEquals('Original Description', $specialization->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_specializations(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $specialization = Specialization::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/specializations/bulk', [
                'ids' => [$specialization->id],
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
        $specialization = Specialization::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/specializations/bulk', [
                'ids' => [$specialization->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}
