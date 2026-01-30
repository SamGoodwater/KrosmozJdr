<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour CampaignBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs campagnes en masse
 * - La validation fonctionne correctement
 * - Seuls les champs fournis sont modifiés
 */
class CampaignBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs campagnes en masse
     */
    public function test_admin_can_bulk_update_campaigns(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $campaign1 = Campaign::factory()->create([
            'name' => 'Campaign 1',
            'read_level' => User::ROLE_GUEST,
            'created_by' => $admin->id,
        ]);
        $campaign2 = Campaign::factory()->create([
            'name' => 'Campaign 2',
            'read_level' => User::ROLE_USER,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/campaigns/bulk', [
                'ids' => [$campaign1->id, $campaign2->id],
                'progress_state' => 1,
                'read_level' => User::ROLE_ADMIN,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign1->id,
            'read_level' => User::ROLE_ADMIN,
        ]);
        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign2->id,
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
            ->patchJson('/api/entities/campaigns/bulk', [
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
        $campaign = Campaign::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'read_level' => User::ROLE_GUEST,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/campaigns/bulk', [
                'ids' => [$campaign->id],
                'progress_state' => 1,
                'read_level' => User::ROLE_ADMIN,
                // name et description ne sont pas modifiés
            ]);

        $response->assertOk();

        $campaign->refresh();
        $this->assertEquals(User::ROLE_ADMIN, $campaign->read_level);
        $this->assertEquals('Original Name', $campaign->name); // Non modifié
        $this->assertEquals('Original Description', $campaign->description); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_campaigns(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $campaign = Campaign::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/campaigns/bulk', [
                'ids' => [$campaign->id],
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
        $campaign = Campaign::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/campaigns/bulk', [
                'ids' => [$campaign->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }

    public function test_validation_fails_if_read_level_invalid(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $campaign = Campaign::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/campaigns/bulk', [
                'ids' => [$campaign->id],
                'read_level' => 999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('read_level');
    }
}
