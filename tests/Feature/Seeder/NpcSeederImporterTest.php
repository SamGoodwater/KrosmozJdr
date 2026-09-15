<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Breed;
use App\Models\Entity\Item;
use App\Models\Entity\Language;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\Type\ItemType;
use App\Services\Npc\NpcEquipmentSlotValidator;
use App\Services\Seeder\Npc\IncarnamNpcCatalog;
use App\Services\Seeder\Npc\NpcSeederImporter;
use App\Support\ElementBitmask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NpcSeederImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_imports_incarnam_npcs_and_is_idempotent(): void
    {
        Language::factory()->create(['name' => 'Commun']);
        $this->seedCatalogDependencies();

        $legacy = Npc::factory()->create([
            'official_id' => null,
            'state' => Npc::STATE_PLAYABLE,
        ]);
        $legacy->creature?->update(['name' => 'Marchande de Bonta']);

        $importer = app(NpcSeederImporter::class);
        $first = $importer->import();

        $this->assertCount(5, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertContains('Marchande de Bonta', $first['retired']);
        $this->assertTrue($legacy->fresh()?->trashed());

        $milicien = Npc::query()->where('official_id', 'jdr:npc:incarnam:milicien')->with([
            'creature.items',
            'creature.spells',
            'breed',
            'specialization',
        ])->first();
        $this->assertNotNull($milicien);
        $this->assertSame(Npc::STATE_PLAYABLE, $milicien->state);
        $this->assertFalse($milicien->auto_update);
        $this->assertSame('Iop', $milicien->breed?->name);
        $this->assertSame('Milicien·ne', $milicien->specialization?->name);
        $this->assertSame('guard', $milicien->npc_role);
        $this->assertGreaterThanOrEqual(1, $milicien->creature?->items->count());
        $this->assertTrue($milicien->creature?->spells->contains('name', 'Pression'));
        $life = (int) $milicien->creature?->life;
        $this->assertGreaterThanOrEqual(20, $life);
        $this->assertLessThanOrEqual(50, $life);

        $ganymede = Npc::query()->where('official_id', 'jdr:npc:incarnam:ganymede')->with('specialization')->first();
        $this->assertSame('Érudit', $ganymede?->specialization?->name);
        $this->assertSame('social', $ganymede?->npc_role);

        $fouduglen = Npc::query()->where('official_id', 'jdr:npc:incarnam:fouduglen')->with('creature.items')->first();
        $this->assertGreaterThanOrEqual(1, $fouduglen?->creature?->items->count());

        $second = $importer->import();
        $this->assertSame([], $second['created']);
        $this->assertCount(5, $second['updated']);
        $this->assertSame(
            5,
            Npc::query()->where('official_id', 'like', 'jdr:npc:incarnam:%')->count()
        );
        $this->assertCount(5, IncarnamNpcCatalog::load()->entries());
    }

    private function seedCatalogDependencies(): void
    {
        $types = [
            NpcEquipmentSlotValidator::SLOT_HAT => $this->itemType(16, 'Chapeau'),
            NpcEquipmentSlotValidator::SLOT_CAPE => $this->itemType(17, 'Cape'),
            NpcEquipmentSlotValidator::SLOT_AMULET => $this->itemType(1, 'Amulette'),
            NpcEquipmentSlotValidator::SLOT_WEAPON => $this->itemType(2, 'Arme'),
            NpcEquipmentSlotValidator::SLOT_SHIELD => $this->itemType(82, 'Bouclier'),
            NpcEquipmentSlotValidator::SLOT_RING => $this->itemType(9, 'Anneau'),
        ];

        foreach (IncarnamNpcCatalog::load()->entries() as $entry) {
            Breed::factory()->create([
                'name' => $entry['breed'],
                'state' => Breed::STATE_DRAFT,
            ]);
            if (! Specialization::query()->where('name', $entry['specialization'])->exists()) {
                Specialization::factory()->create([
                    'name' => $entry['specialization'],
                    'state' => Specialization::STATE_PLAYABLE,
                ]);
            }
            foreach ($entry['items'] as $itemName) {
                if (Item::query()->where('name', $itemName)->exists()) {
                    continue;
                }
                Item::factory()->create([
                    'name' => $itemName,
                    'level' => '1',
                    'state' => Item::STATE_PLAYABLE,
                    'item_type_id' => $this->typeForItemName($itemName, $types)->id,
                    'effect' => json_encode(['vitality' => 1], JSON_THROW_ON_ERROR),
                    'bonus' => null,
                ]);
            }
            foreach ($entry['spells'] as $spellName) {
                if (Spell::query()->where('name', $spellName)->exists()) {
                    continue;
                }
                $spell = Spell::factory()->create([
                    'name' => $spellName,
                    'state' => Spell::STATE_PLAYABLE,
                    'element' => ElementBitmask::fromSlug('earth'),
                    'pa' => '3',
                ]);
                $breedId = Breed::query()->where('name', $entry['breed'])->value('id');
                $spell->breeds()->attach($breedId, [
                    'character_level' => 1,
                    'slot_index' => 1,
                    'choice_order' => 0,
                ]);
            }
            foreach ($entry['panoplies'] as $panoplyName) {
                if (Panoply::query()->where('name', $panoplyName)->exists()) {
                    continue;
                }
                Panoply::factory()->create([
                    'name' => $panoplyName,
                    'state' => Panoply::STATE_PLAYABLE,
                ]);
            }
        }
    }

    /**
     * @param  array<string, ItemType>  $types
     */
    private function typeForItemName(string $name, array $types): ItemType
    {
        return match (true) {
            str_contains($name, 'Anneau') => $types[NpcEquipmentSlotValidator::SLOT_RING],
            str_contains($name, 'Amulette') => $types[NpcEquipmentSlotValidator::SLOT_AMULET],
            str_contains($name, 'Bouclier') => $types[NpcEquipmentSlotValidator::SLOT_SHIELD],
            str_contains($name, 'Marteau'), str_contains($name, 'Dagues') => $types[NpcEquipmentSlotValidator::SLOT_WEAPON],
            str_contains($name, 'Cape') => $types[NpcEquipmentSlotValidator::SLOT_CAPE],
            default => $types[NpcEquipmentSlotValidator::SLOT_HAT],
        };
    }

    private function itemType(int $dofusdbTypeId, string $name): ItemType
    {
        return ItemType::factory()->create([
            'name' => $name,
            'dofusdb_type_id' => $dofusdbTypeId,
            'state' => ItemType::STATE_PLAYABLE,
        ]);
    }
}
