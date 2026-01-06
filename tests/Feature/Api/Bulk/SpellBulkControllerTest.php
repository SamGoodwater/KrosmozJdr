<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour SpellBulkController
 *
 * @description
 * Vérifie que :
 * - Un admin peut mettre à jour plusieurs sorts en masse
 * - La validation fonctionne correctement
 * - Seuls les champs fournis sont modifiés
 */
class SpellBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un admin peut mettre à jour plusieurs sorts en masse
     */
    public function test_admin_can_bulk_update_spells(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell1 = Spell::factory()->create(['level' => '10', 'pa' => '3', 'usable' => false]);
        $spell2 = Spell::factory()->create(['level' => '20', 'pa' => '4', 'usable' => true]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/spells/bulk', [
                'ids' => [$spell1->id, $spell2->id],
                'level' => '50',
                'pa' => '5',
                'usable' => true,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'summary' => ['requested', 'updated', 'errors'],
            ]);

        $this->assertDatabaseHas('spells', [
            'id' => $spell1->id,
            'level' => '50',
            'pa' => '5',
            'usable' => 1,
        ]);
        $this->assertDatabaseHas('spells', [
            'id' => $spell2->id,
            'level' => '50',
            'pa' => '5',
            'usable' => 1,
        ]);
    }

    /**
     * Test : La validation échoue avec des IDs invalides
     */
    public function test_validation_fails_with_invalid_ids(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/spells/bulk', [
                'ids' => [99999, 99998],
                'level' => '50',
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
        $spell = Spell::factory()->create([
            'level' => '10',
            'pa' => '3',
            'po' => '2',
            'usable' => false,
        ]);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/spells/bulk', [
                'ids' => [$spell->id],
                'level' => '50',
                // pa, po, usable ne sont pas modifiés
            ]);

        $response->assertOk();

        $spell->refresh();
        $this->assertEquals('50', $spell->level);
        $this->assertEquals('3', $spell->pa); // Non modifié
        $this->assertEquals('2', $spell->po); // Non modifié
        $this->assertEquals(0, $spell->usable); // Non modifié
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas faire de bulk update
     */
    public function test_user_cannot_bulk_update_spells(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create();

        $response = $this->actingAs($user)
            ->patchJson('/api/entities/spells/bulk', [
                'ids' => [$spell->id],
                'level' => '50',
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : La validation échoue si aucun champ n'est fourni
     */
    public function test_validation_fails_if_no_fields_provided(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create();

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/spells/bulk', [
                'ids' => [$spell->id],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJson(['message' => 'Aucun champ à mettre à jour.']);
    }
}

