<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ConditionBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs états en masse
 * - La validation fonctionne correctement
 * - Seuls les champs fournis sont modifiés
 */
class ConditionBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs états en masse
     */
    public function test_admin_can_bulk_update_conditions(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $condition1 = Condition::factory()->create([
            'name' => 'Condition 1',
            'read_level' => User::ROLE_GUEST,
        ]);
        $condition2 = Condition::factory()->create([
            'name' => 'Condition 2',
            'read_level' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/conditions/bulk', [
                'ids' => [$condition1->id, $condition2->id],
                'read_level' => User::ROLE_ADMIN,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('conditions', [
            'id' => $condition1->id,
            'read_level' => User::ROLE_ADMIN,
        ]);
        $this->assertDatabaseHas('conditions', [
            'id' => $condition2->id,
            'read_level' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * Test : bulk — champ dissipable
     */
    public function test_admin_can_bulk_update_dissipable(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $condition = Condition::factory()->create([
            'dissipable' => true,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/conditions/bulk', [
                'ids' => [$condition->id],
                'dissipable' => false,
            ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('conditions', [
            'id' => $condition->id,
            'dissipable' => false,
        ]);
    }

    /**
     * Test : La validation échoue avec des IDs invalides
     */
    public function test_validation_fails_with_invalid_ids(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/conditions/bulk', [
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
        $condition = Condition::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'read_level' => User::ROLE_GUEST,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/conditions/bulk', [
                'ids' => [$condition->id],
                'read_level' => User::ROLE_ADMIN,
                // name et description ne sont pas modifiés
            ]);

        $response->assertOk();

        $condition->refresh();
        $this->assertEquals(User::ROLE_ADMIN, $condition->read_level);
        $this->assertEquals('Original Name', $condition->name); // Non modifié
        $this->assertEquals('Original Description', $condition->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_conditions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $condition = Condition::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/conditions/bulk', [
                'ids' => [$condition->id],
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
        $condition = Condition::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/conditions/bulk', [
                'ids' => [$condition->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}
