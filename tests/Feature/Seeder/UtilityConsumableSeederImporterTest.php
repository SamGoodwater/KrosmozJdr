<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Consumable;
use App\Models\Type\ConsumableType;
use App\Services\Seeder\Consumable\UtilityConsumableCatalog;
use App\Services\Seeder\Consumable\UtilityConsumableSeederImporter;
use Tests\TestCase;

final class UtilityConsumableSeederImporterTest extends TestCase
{
    public function test_imports_utility_consumables_with_custom_prices_and_is_idempotent(): void
    {
        $catalog = UtilityConsumableCatalog::load();
        $this->seedTypes($catalog);

        $importer = app(UtilityConsumableSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(10, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);
        $this->assertSame(10, Consumable::query()->where('state', Consumable::STATE_AUTO)->count());

        $rappel = Consumable::query()->where('dofusdb_id', '548')->first();
        $this->assertNotNull($rappel);
        $this->assertSame('Potion de Rappel', $rappel->name);
        $this->assertSame(800, $rappel->totalPriceKamas());
        $this->assertSame(1, $rappel->rarity);
        $this->assertSame('6', $rappel->level);
        $this->assertFalse($rappel->auto_update);
        $this->assertSame(0, $rappel->resources()->count());

        $antidote = Consumable::query()->where('official_id', 'jdr:antidote')->first();
        $this->assertNotNull($antidote);
        $this->assertSame(1500, $antidote->totalPriceKamas());
        $this->assertSame('8', $antidote->level);

        $candy = Consumable::query()->where('dofusdb_id', '12196')->first();
        $this->assertNotNull($candy);
        $this->assertSame(10000, $candy->totalPriceKamas());
        $this->assertSame(3, $candy->rarity);
        $this->assertSame('12', $candy->level);

        $wakfu = Consumable::query()->where('official_id', 'jdr:wakfu-elixir')->first();
        $this->assertNotNull($wakfu);
        $this->assertSame(3500, $wakfu->totalPriceKamas());
        $this->assertSame(2, $wakfu->rarity);

        $second = $importer->import($catalog);
        $this->assertSame([], $second['created']);
        $this->assertCount(10, $second['updated']);
        $this->assertSame(800, $rappel->fresh()->totalPriceKamas());
    }

    private function seedTypes(UtilityConsumableCatalog $catalog): void
    {
        foreach ($catalog->types() as $type) {
            ConsumableType::factory()->create([
                'name' => $type['name'],
                'dofusdb_type_id' => $type['dofusdb_type_id'],
                'state' => ConsumableType::STATE_DRAFT,
                'show_in_catalog' => false,
                'read_level' => 0,
                'write_level' => 3,
                'created_by' => null,
            ]);
        }
    }
}
