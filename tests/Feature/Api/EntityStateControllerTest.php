<?php

namespace Tests\Feature\Api;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour EntityStateController.
 *
 * @description
 * Vérifie que l'action d'état unitaire utilise bien la policy `update`.
 */
class EntityStateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    public function test_owner_can_update_entity_state(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create([
            'created_by' => $owner->id,
            'state' => Spell::STATE_DRAFT,
        ]);

        $response = $this->actingAs($owner)
            ->patchJson("/api/entities/spells/{$spell->id}/state", [
                'state' => Spell::STATE_PLAYABLE,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'entity' => [
                    'id' => $spell->id,
                    'type' => 'spells',
                    'state' => Spell::STATE_PLAYABLE,
                ],
            ]);

        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            'state' => Spell::STATE_PLAYABLE,
        ]);
    }

    public function test_non_owner_cannot_update_entity_state(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create([
            'created_by' => $owner->id,
            'state' => Spell::STATE_DRAFT,
        ]);

        $response = $this->actingAs($otherUser)
            ->patchJson("/api/entities/spells/{$spell->id}/state", [
                'state' => Spell::STATE_PLAYABLE,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            'state' => Spell::STATE_DRAFT,
        ]);
    }

    public function test_state_value_is_validated(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create(['state' => Spell::STATE_DRAFT]);

        $response = $this->actingAs($admin)
            ->patchJson("/api/entities/spells/{$spell->id}/state", [
                'state' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('state');
    }
}
