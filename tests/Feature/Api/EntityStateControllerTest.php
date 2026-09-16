<?php

namespace Tests\Feature\Api;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Item;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * EntityStateController : `update` pour les états non jouables, `publish` pour `playable`.
 */
class EntityStateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    public function test_owner_can_set_draft_but_cannot_publish_playable(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create([
            'created_by' => $owner->id,
            'state' => Spell::STATE_RAW,
        ]);

        $this->actingAs($owner)
            ->patchJson("/api/entities/spells/{$spell->id}/state", [
                'state' => Spell::STATE_DRAFT,
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'entity' => [
                    'id' => $spell->id,
                    'type' => 'spells',
                    'state' => Spell::STATE_DRAFT,
                ],
            ]);

        $this->actingAs($owner)
            ->patchJson("/api/entities/spells/{$spell->id}/state", [
                'state' => Spell::STATE_PLAYABLE,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            'state' => Spell::STATE_DRAFT,
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

    public function test_admin_can_set_auto_state(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create(['state' => Spell::STATE_DRAFT]);

        $response = $this->actingAs($admin)
            ->patchJson("/api/entities/spells/{$spell->id}/state", [
                'state' => Spell::STATE_AUTO,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'entity' => [
                    'id' => $spell->id,
                    'type' => 'spells',
                    'state' => Spell::STATE_AUTO,
                ],
            ]);

        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            'state' => Spell::STATE_AUTO,
        ]);
    }

    public function test_admin_can_publish_auto_to_playable(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create(['state' => Spell::STATE_AUTO]);

        $this->actingAs($admin)
            ->patchJson("/api/entities/spells/{$spell->id}/state", [
                'state' => Spell::STATE_PLAYABLE,
            ])
            ->assertOk();

        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            'state' => Spell::STATE_PLAYABLE,
        ]);
    }

    public function test_game_master_can_publish_item_but_not_spell(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create([
            'state' => Item::STATE_DRAFT,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $spell = Spell::factory()->create([
            'state' => Spell::STATE_DRAFT,
            'created_by' => User::factory(),
        ]);

        $this->actingAs($gm)
            ->patchJson("/api/entities/items/{$item->id}/state", [
                'state' => Item::STATE_PLAYABLE,
            ])
            ->assertOk();

        $this->actingAs($gm)
            ->patchJson("/api/entities/spells/{$spell->id}/state", [
                'state' => Spell::STATE_PLAYABLE,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'state' => Item::STATE_PLAYABLE,
        ]);
        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            'state' => Spell::STATE_DRAFT,
        ]);
    }

    public function test_author_cannot_publish_item_via_form_but_can_edit_draft(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $item = Item::factory()->create([
            'created_by' => $owner->id,
            'state' => Item::STATE_DRAFT,
            'name' => 'Brouillon',
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($owner)
            ->from(route('entities.items.edit', $item))
            ->patch(route('entities.items.update', $item), [
                'name' => 'Brouillon annoté',
            ])
            ->assertRedirect();

        $this->assertSame('Brouillon annoté', $item->fresh()->name);

        $this->actingAs($owner)
            ->from(route('entities.items.edit', $item))
            ->patch(route('entities.items.update', $item), [
                'name' => 'Brouillon annoté',
                'state' => Item::STATE_PLAYABLE,
            ])
            ->assertSessionHasErrors('state');

        $this->assertSame(Item::STATE_DRAFT, $item->fresh()->state);
    }
}
