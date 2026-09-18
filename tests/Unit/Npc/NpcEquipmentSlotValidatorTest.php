<?php

declare(strict_types=1);

namespace Tests\Unit\Npc;

use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use App\Services\Npc\NpcEquipmentSlotValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Kit PNJ : 1 objet / emplacement, 2 anneaux, refus des types inconnus.
 */
class NpcEquipmentSlotValidatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_rings_are_allowed(): void
    {
        $type = ItemType::factory()->create(['dofusdb_type_id' => 9]);
        $items = collect([
            Item::factory()->create(['item_type_id' => $type->id])->load('itemType'),
            Item::factory()->create(['item_type_id' => $type->id])->load('itemType'),
        ]);

        (new NpcEquipmentSlotValidator)->assertWornKit($items);
        $this->addToAssertionCount(1);
    }

    public function test_two_hats_are_rejected(): void
    {
        $type = ItemType::factory()->create(['dofusdb_type_id' => 16]);
        $items = collect([
            Item::factory()->create(['item_type_id' => $type->id])->load('itemType'),
            Item::factory()->create(['item_type_id' => $type->id])->load('itemType'),
        ]);

        $this->expectException(ValidationException::class);
        (new NpcEquipmentSlotValidator)->assertWornKit($items);
    }

    public function test_unknown_type_is_rejected(): void
    {
        $type = ItemType::factory()->create(['dofusdb_type_id' => 51]);
        $items = collect([
            Item::factory()->create(['item_type_id' => $type->id])->load('itemType'),
        ]);

        $this->expectException(ValidationException::class);
        (new NpcEquipmentSlotValidator)->assertWornKit($items);
    }

    public function test_errors_for_worn_kit_returns_messages_instead_of_throwing(): void
    {
        $type = ItemType::factory()->create(['dofusdb_type_id' => 16]);
        $items = collect([
            Item::factory()->create(['item_type_id' => $type->id])->load('itemType'),
            Item::factory()->create(['item_type_id' => $type->id])->load('itemType'),
        ]);

        $errors = (new NpcEquipmentSlotValidator)->errorsForWornKit($items);
        $this->assertNotSame([], $errors);
        $this->assertStringContainsString('chapeau', $errors[0]);
    }
}
