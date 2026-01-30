<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour AttributeBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs attributs en masse
 * - La validation fonctionne correctement
 * - Seuls les champs fournis sont modifiés
 */
class AttributeBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs attributs en masse
     */
    public function test_admin_can_bulk_update_attributes(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $attribute1 = Attribute::factory()->create([
            'name' => 'Attribute 1',
            'read_level' => User::ROLE_GUEST,
        ]);
        $attribute2 = Attribute::factory()->create([
            'name' => 'Attribute 2',
            'read_level' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/attributes/bulk', [
                'ids' => [$attribute1->id, $attribute2->id],
                'read_level' => User::ROLE_ADMIN,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('attributes', [
            'id' => $attribute1->id,
            'read_level' => User::ROLE_ADMIN,
        ]);
        $this->assertDatabaseHas('attributes', [
            'id' => $attribute2->id,
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
            ->patchJson('/api/entities/attributes/bulk', [
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
        $attribute = Attribute::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'read_level' => User::ROLE_GUEST,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/attributes/bulk', [
                'ids' => [$attribute->id],
                'read_level' => User::ROLE_ADMIN,
                // name et description ne sont pas modifiés
            ]);

        $response->assertOk();

        $attribute->refresh();
        $this->assertEquals(User::ROLE_ADMIN, $attribute->read_level);
        $this->assertEquals('Original Name', $attribute->name); // Non modifié
        $this->assertEquals('Original Description', $attribute->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_attributes(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $attribute = Attribute::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/attributes/bulk', [
                'ids' => [$attribute->id],
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
        $attribute = Attribute::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/attributes/bulk', [
                'ids' => [$attribute->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}
