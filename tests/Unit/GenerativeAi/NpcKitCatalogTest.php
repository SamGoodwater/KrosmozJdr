<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Breed;
use App\Models\Entity\Item;
use App\Models\Entity\Npc;
use App\Models\Entity\Spell;
use App\Models\Type\ItemType;
use App\Services\GenerativeAi\NpcKitCatalog;
use App\Services\Npc\NpcEquipmentSlotValidator;
use App\Support\ElementBitmask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NpcKitCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_equipment_filters_level_voie_and_excludes_archived(): void
    {
        $cape = $this->type(NpcEquipmentSlotValidator::SLOT_CAPE, 17);
        $hat = $this->type(NpcEquipmentSlotValidator::SLOT_HAT, 16);

        Item::factory()->create([
            'name' => 'Cape Terre 4',
            'level' => '4',
            'state' => Item::STATE_PLAYABLE,
            'item_type_id' => $cape->id,
            'effect' => json_encode(['strength' => 2], JSON_THROW_ON_ERROR),
            'bonus' => null,
        ]);
        Item::factory()->create([
            'name' => 'Cape Feu 4',
            'level' => '4',
            'state' => Item::STATE_PLAYABLE,
            'item_type_id' => $cape->id,
            'effect' => json_encode(['intelligence' => 2], JSON_THROW_ON_ERROR),
            'bonus' => null,
        ]);
        Item::factory()->create([
            'name' => 'Chapeau Neutre 3',
            'level' => '3',
            'state' => Item::STATE_PLAYABLE,
            'item_type_id' => $hat->id,
            'effect' => json_encode(['vitality' => 1], JSON_THROW_ON_ERROR),
            'bonus' => null,
        ]);
        Item::factory()->create([
            'name' => 'Cape Terre 10',
            'level' => '10',
            'state' => Item::STATE_PLAYABLE,
            'item_type_id' => $cape->id,
            'effect' => json_encode(['strength' => 3], JSON_THROW_ON_ERROR),
            'bonus' => null,
        ]);
        Item::factory()->create([
            'name' => 'Cape Archivée',
            'level' => '4',
            'state' => EntityState::Archived->value,
            'item_type_id' => $cape->id,
            'effect' => json_encode(['strength' => 2], JSON_THROW_ON_ERROR),
            'bonus' => null,
        ]);

        $catalog = new NpcKitCatalog;
        $items = $catalog->equipment(4, 'terre');
        $names = array_column($items, 'name');

        $this->assertContains('Cape Terre 4', $names);
        $this->assertContains('Chapeau Neutre 3', $names);
        $this->assertNotContains('Cape Feu 4', $names);
        $this->assertNotContains('Cape Terre 10', $names);
        $this->assertNotContains('Cape Archivée', $names);
        $this->assertLessThanOrEqual(NpcKitCatalog::MAX_ITEMS, count($items));
    }

    public function test_equipment_caps_at_forty(): void
    {
        $hat = $this->type(NpcEquipmentSlotValidator::SLOT_HAT, 16);
        $cape = $this->type(NpcEquipmentSlotValidator::SLOT_CAPE, 17);
        $ring = $this->type(NpcEquipmentSlotValidator::SLOT_RING, 9);
        $belt = $this->type(NpcEquipmentSlotValidator::SLOT_BELT, 10);
        $boots = $this->type(NpcEquipmentSlotValidator::SLOT_BOOTS, 11);
        $amulet = $this->type(NpcEquipmentSlotValidator::SLOT_AMULET, 1);
        $weapon = $this->type(NpcEquipmentSlotValidator::SLOT_WEAPON, 2);
        $shield = $this->type(NpcEquipmentSlotValidator::SLOT_SHIELD, 82);

        foreach ([$hat, $cape, $ring, $belt, $boots, $amulet, $weapon, $shield] as $i => $type) {
            for ($n = 0; $n < 8; $n++) {
                Item::factory()->create([
                    'name' => 'Stuff '.$i.'-'.$n,
                    'level' => '4',
                    'state' => Item::STATE_PLAYABLE,
                    'item_type_id' => $type->id,
                    'effect' => json_encode(['vitality' => 1], JSON_THROW_ON_ERROR),
                    'bonus' => null,
                ]);
            }
        }

        $items = (new NpcKitCatalog)->equipment(4);
        $this->assertLessThanOrEqual(NpcKitCatalog::MAX_ITEMS, count($items));
        $this->assertGreaterThan(10, count($items));
    }

    public function test_spells_respect_character_level(): void
    {
        $breed = Breed::factory()->create(['name' => 'Iop-test-kit', 'state' => Breed::STATE_DRAFT]);
        $low = Spell::factory()->create([
            'name' => 'Pression test',
            'state' => Spell::STATE_PLAYABLE,
            'element' => ElementBitmask::fromSlug('earth'),
            'pa' => '3',
        ]);
        $high = Spell::factory()->create([
            'name' => 'Colère test',
            'state' => Spell::STATE_PLAYABLE,
            'element' => ElementBitmask::fromSlug('earth'),
            'pa' => '4',
        ]);
        $low->breeds()->attach($breed->id, ['character_level' => 1, 'slot_index' => 1, 'choice_order' => 0]);
        $high->breeds()->attach($breed->id, ['character_level' => 8, 'slot_index' => 2, 'choice_order' => 0]);

        $spells = (new NpcKitCatalog)->spells((int) $breed->id, 4);
        $names = array_column($spells, 'name');

        $this->assertContains('Pression test', $names);
        $this->assertNotContains('Colère test', $names);
        $this->assertSame('Terre', $spells[0]['element']);
    }

    public function test_example_ids_resolve_incarnam_official_ids(): void
    {
        $playable = Npc::factory()->create([
            'official_id' => NpcKitCatalog::OFFICIAL_ID_PREFIX.'ganymede',
            'state' => Npc::STATE_PLAYABLE,
        ]);
        Npc::factory()->create([
            'official_id' => 'other:npc',
            'state' => Npc::STATE_PLAYABLE,
        ]);
        Npc::factory()->create([
            'official_id' => NpcKitCatalog::OFFICIAL_ID_PREFIX.'drafty',
            'state' => Npc::STATE_DRAFT,
        ]);

        $this->assertSame([(int) $playable->id], (new NpcKitCatalog)->exampleIds());
    }

    private function type(string $slot, int $dofusdbTypeId): ItemType
    {
        return ItemType::factory()->create([
            'name' => 'Type '.$slot.' '.$dofusdbTypeId,
            'dofusdb_type_id' => $dofusdbTypeId,
            'state' => ItemType::STATE_PLAYABLE,
        ]);
    }
}
