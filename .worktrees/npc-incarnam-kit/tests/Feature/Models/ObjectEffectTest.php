<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\ObjectEffectAction;
use App\Models\Characteristic;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectEffectTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_can_store_object_effects_with_characteristic_and_value(): void
    {
        $characteristic = Characteristic::query()->create([
            'key' => 'test_pv_object_effect',
            'name' => 'PV test',
            'type' => 'int',
            'sort_order' => 0,
            'group' => 'object',
        ]);
        $item = Item::factory()->create();

        $effect = $item->objectEffects()->create([
            'action' => ObjectEffectAction::Regenerate,
            'characteristic_id' => $characteristic->id,
            'monster_id' => null,
            'value' => 25,
        ]);

        $this->assertSame(ObjectEffectAction::Regenerate, $effect->action);
        $this->assertSame(25, $effect->value);
        $item->refresh();
        $this->assertCount(1, $item->objectEffects);
        $this->assertSame($characteristic->id, $effect->characteristic->id);
    }

    public function test_invoke_effect_has_monster_and_null_value(): void
    {
        $monster = Monster::factory()->create();
        $item = Item::factory()->create();

        $effect = $item->objectEffects()->create([
            'action' => ObjectEffectAction::Invoke,
            'characteristic_id' => null,
            'monster_id' => $monster->id,
            'value' => null,
        ]);

        $this->assertNull($effect->value);
        $this->assertSame($monster->id, $effect->monster->id);
    }

    public function test_teleport_effect_has_no_characteristic_nor_monster(): void
    {
        $item = Item::factory()->create();

        $effect = $item->objectEffects()->create([
            'action' => ObjectEffectAction::Teleport,
            'characteristic_id' => null,
            'monster_id' => null,
            'value' => null,
        ]);

        $this->assertNull($effect->value);
        $this->assertNull($effect->characteristic_id);
        $this->assertNull($effect->monster_id);
    }
}
