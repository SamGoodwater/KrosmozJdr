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

    public function test_guest_can_list_object_effects(): void
    {
        $item = Item::factory()->create();
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
