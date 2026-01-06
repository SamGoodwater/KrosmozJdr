<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Resource;
use App\Models\Type\ResourceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ResourceBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs ressources en masse
 * - La validation fonctionne correctement
 * - La clé étrangère resource_type_id est validée
 * - Seuls les champs fournis sont modifiés
 */
class ResourceBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs ressources en masse
     */
    public function test_admin_can_bulk_update_resources(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $resource1 = Resource::factory()->create([
            'name' => 'Resource 1',
            'is_visible' => 'guest',
        ]);
        $resource2 = Resource::factory()->create([
            'name' => 'Resource 2',
            'is_visible' => 'user',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/resources/bulk', [
                'ids' => [$resource1->id, $resource2->id],
                'is_visible' => 'admin',
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('resources', [
            'id' => $resource1->id,
            'is_visible' => 'admin',
        ]);
        $this->assertDatabaseHas('resources', [
            'id' => $resource2->id,
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
            ->patchJson('/api/entities/resources/bulk', [
                'ids' => [99999, 99998],
                'is_visible' => 'admin',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('ids.0');
    }

    /**
     * Test : La clé étrangère resource_type_id est validée
     */
    public function test_resource_type_id_validation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $resource = Resource::factory()->create();
        $resourceType = ResourceType::factory()->create();

        // Test avec resource_type_id valide
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/resources/bulk', [
                'ids' => [$resource->id],
                'resource_type_id' => $resourceType->id,
            ]);

        $response->assertOk();

        $resource->refresh();
        $this->assertEquals($resourceType->id, $resource->resource_type_id);

        // Test avec resource_type_id invalide
        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/resources/bulk', [
                'ids' => [$resource->id],
                'resource_type_id' => 99999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('resource_type_id');
    }

    /**
     * Test : Seuls les champs fournis sont modifiés
     */
    public function test_only_provided_fields_are_updated(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $resource = Resource::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'is_visible' => 'guest',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/resources/bulk', [
                'ids' => [$resource->id],
                'is_visible' => 'admin',
                // name et description ne sont pas modifiés
            ]);

        $response->assertOk();

        $resource->refresh();
        $this->assertEquals('admin', $resource->is_visible);
        $this->assertEquals('Original Name', $resource->name); // Non modifié
        $this->assertEquals('Original Description', $resource->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_resources(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $resource = Resource::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/resources/bulk', [
                'ids' => [$resource->id],
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
        $resource = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/resources/bulk', [
                'ids' => [$resource->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}

