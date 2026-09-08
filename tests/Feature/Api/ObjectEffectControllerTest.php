<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Characteristic;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\ObjectEffect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectEffectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function playableItemAttrs(array $overrides = []): array
    {
        return array_merge([
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function draftItemAttrs(array $overrides = []): array
    {
        return array_merge([
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ], $overrides);
    }

    public function test_guest_can_list_object_effects_of_playable_item(): void
    {
        $item = Item::factory()->create($this->playableItemAttrs());
        ObjectEffect::query()->create([
            'object_effectable_type' => Item::class,
            'object_effectable_id' => $item->id,
            'action' => 'teleport',
            'characteristic_id' => null,
            'monster_id' => null,
            'value' => null,
        ]);

        $response = $this->getJson('/api/object-effects?entity_type=item&entity_id='.$item->id);

        $response->assertOk();
        $response->assertJsonPath('data.0.action', 'teleport');
    }

    public function test_guest_cannot_list_object_effects_of_draft_item(): void
    {
        $item = Item::factory()->create($this->draftItemAttrs());
        ObjectEffect::query()->create([
            'object_effectable_type' => Item::class,
            'object_effectable_id' => $item->id,
            'action' => 'teleport',
            'characteristic_id' => null,
            'monster_id' => null,
            'value' => null,
        ]);

        $this->getJson('/api/object-effects?entity_type=item&entity_id='.$item->id)
            ->assertForbidden();
    }

    public function test_game_master_can_list_object_effects_of_draft_item(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create($this->draftItemAttrs());
        ObjectEffect::query()->create([
            'object_effectable_type' => Item::class,
            'object_effectable_id' => $item->id,
            'action' => 'add',
            'characteristic_id' => null,
            'monster_id' => null,
            'value' => 3,
        ]);

        $this->actingAs($gm)
            ->getJson('/api/object-effects?entity_type=item&entity_id='.$item->id)
            ->assertOk()
            ->assertJsonPath('data.0.action', 'add')
            ->assertJsonPath('data.0.value', 3);
    }

    public function test_guest_list_hides_draft_invoke_monster_on_playable_item(): void
    {
        $item = Item::factory()->create($this->playableItemAttrs());
        $draftMonster = Monster::factory()->create([
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $draftMonster->creature()->update(['name' => 'Invocation secrète']);

        ObjectEffect::query()->create([
            'object_effectable_type' => Item::class,
            'object_effectable_id' => $item->id,
            'action' => 'invoke',
            'characteristic_id' => null,
            'monster_id' => $draftMonster->id,
            'value' => null,
        ]);

        $this->getJson('/api/object-effects?entity_type=item&entity_id='.$item->id)
            ->assertOk()
            ->assertJsonPath('data.0.action', 'invoke')
            ->assertJsonPath('data.0.monster_id', null)
            ->assertJsonPath('data.0.monster', null);
    }

    public function test_game_master_can_create_update_and_delete_object_effect(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $item = Item::factory()->create();
        $characteristic = Characteristic::query()->create([
            'key' => 'api_test_obj_fx',
            'name' => 'Test',
            'type' => 'int',
            'sort_order' => 0,
            'group' => 'object',
        ]);

        $create = $this->actingAs($user)->postJson('/api/object-effects', [
            'entity_type' => 'item',
            'entity_id' => $item->id,
            'action' => 'add',
            'characteristic_id' => $characteristic->id,
            'monster_id' => null,
            'value' => 2,
        ]);

        $create->assertCreated();
        $id = (int) $create->json('data.id');
        $this->assertGreaterThan(0, $id);

        $patch = $this->actingAs($user)->patchJson('/api/object-effects/'.$id, [
            'value' => 5,
        ]);
        $patch->assertOk();
        $this->assertSame(5, $patch->json('data.value'));

        $del = $this->actingAs($user)->deleteJson('/api/object-effects/'.$id);
        $del->assertNoContent();
        $this->assertDatabaseMissing('object_effects', ['id' => $id]);
    }

    public function test_invoke_requires_monster(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/object-effects', [
            'entity_type' => 'item',
            'entity_id' => $item->id,
            'action' => 'invoke',
            'characteristic_id' => null,
            'monster_id' => null,
            'value' => null,
        ]);

        $response->assertUnprocessable();
    }

    public function test_invoke_succeeds_with_monster(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $item = Item::factory()->create();
        $monster = Monster::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/object-effects', [
            'entity_type' => 'item',
            'entity_id' => $item->id,
            'action' => 'invoke',
            'characteristic_id' => null,
            'monster_id' => $monster->id,
            'value' => null,
        ]);

        $response->assertCreated();
        $this->assertSame($monster->id, $response->json('data.monster_id'));
    }
}
