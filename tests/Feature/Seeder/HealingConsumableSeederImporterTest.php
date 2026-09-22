<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Type\ConsumableType;
use App\Services\Seeder\Consumable\HealingConsumableCatalog;
use App\Services\Seeder\Consumable\HealingConsumableSeederImporter;
use Tests\TestCase;

final class HealingConsumableSeederImporterTest extends TestCase
{
    public function test_imports_ladder_with_recipe_prices_and_is_idempotent(): void
    {
        $catalog = HealingConsumableCatalog::load();
        $this->seedTypes($catalog);
        $this->seedResources($catalog);

        $importer = app(HealingConsumableSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(44, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);
        $this->assertSame(44, Consumable::query()->where('state', Consumable::STATE_AUTO)->count());

        $pain = Consumable::query()->where('dofusdb_id', '468')->first();
        $this->assertNotNull($pain);
        $this->assertSame('Pain d\'Incarnam', $pain->name);
        $this->assertSame(HealingConsumableCatalog::effectText(1), $pain->effect);
        $this->assertSame(HealingConsumableCatalog::bonusJson(1), $pain->bonus);
        $this->assertFalse($pain->auto_update);
        $this->assertSame(20, $pain->totalPriceKamas());
        $this->assertSame(1, $pain->resources()->count());
        $this->assertSame(10, (int) $pain->resources->first()->pivot->quantity);
        $this->assertSame('2', $pain->resources->first()->price);

        $jdrPotion = Consumable::query()->where('official_id', 'jdr:heal:potion:11')->first();
        $this->assertNotNull($jdrPotion);
        $this->assertSame('Potion de Soin divine', $jdrPotion->name);
        $this->assertSame(10000, $jdrPotion->totalPriceKamas());
        $this->assertSame('1000', $jdrPotion->resources->first()->price);

        $wheat = Resource::query()->where('dofusdb_id', '289')->first();
        $this->assertSame(Resource::STATE_PLAYABLE, $wheat?->state);
        $this->assertFalse((bool) $wheat?->auto_update);

        $second = $importer->import($catalog);
        $this->assertSame([], $second['created']);
        $this->assertCount(44, $second['updated']);
        $this->assertSame(44, Consumable::query()->where('state', Consumable::STATE_AUTO)->count());
        $this->assertSame(20, $pain->fresh(['resources'])->totalPriceKamas());
    }

    public function test_falls_back_to_custom_price_when_resource_is_missing(): void
    {
        $catalog = HealingConsumableCatalog::load();
        $this->seedTypes($catalog);

        $result = app(HealingConsumableSeederImporter::class)->import($catalog);

        $this->assertCount(44, $result['created']);
        $this->assertCount(44, $result['skipped']);

        $pain = Consumable::query()->where('dofusdb_id', '468')->first();
        $this->assertNotNull($pain);
        $this->assertSame(20, $pain->totalPriceKamas());
        $this->assertSame(20, $pain->price_custom);
        $this->assertSame(0, $pain->resources()->count());
    }

    private function seedTypes(HealingConsumableCatalog $catalog): void
    {
        foreach ($catalog->kinds() as $kind) {
            ConsumableType::factory()->create([
                'name' => $kind['consumable_type_name'],
                'dofusdb_type_id' => $kind['consumable_type_dofus_id'],
                'state' => ConsumableType::STATE_DRAFT,
                'show_in_catalog' => true,
                'read_level' => 0,
                'write_level' => 3,
                'created_by' => null,
            ]);
        }
    }

    private function seedResources(HealingConsumableCatalog $catalog): void
    {
        $seen = [];
        foreach ($catalog->entries() as $entry) {
            $id = $entry['resource_dofusdb_id'];
            if (isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            Resource::factory()->create([
                'dofusdb_id' => $id,
                'name' => $entry['resource_name'],
                'price' => '0',
                'state' => Resource::STATE_DRAFT,
                'auto_update' => true,
                'created_by' => null,
            ]);
        }
    }
}
