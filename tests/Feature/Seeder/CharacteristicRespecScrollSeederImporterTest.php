<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Type\ConsumableType;
use App\Services\Seeder\Consumable\CharacteristicRespecScrollCatalog;
use App\Services\Seeder\Consumable\CharacteristicRespecScrollSeederImporter;
use Tests\TestCase;

final class CharacteristicRespecScrollSeederImporterTest extends TestCase
{
    public function test_imports_scrolls_with_custom_prices_without_recipe_and_is_idempotent(): void
    {
        $catalog = CharacteristicRespecScrollCatalog::load();
        $this->seedType($catalog);

        $importer = app(CharacteristicRespecScrollSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(24, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);
        $this->assertSame(24, Consumable::query()->where('state', Consumable::STATE_AUTO)->count());

        $petitChance = Consumable::query()->where('dofusdb_id', '809')->first();
        $this->assertNotNull($petitChance);
        $this->assertSame('Petit Parchemin de Chance', $petitChance->name);
        $this->assertSame(
            CharacteristicRespecScrollCatalog::effectText(1, 'de Chance'),
            $petitChance->effect
        );
        $this->assertFalse($petitChance->auto_update);
        $this->assertSame(1000, $petitChance->totalPriceKamas());
        $this->assertSame(1000, $petitChance->price_custom);
        $this->assertSame(0, $petitChance->price_calculated);
        $this->assertSame(0, $petitChance->resources()->count());
        $this->assertSame('3', $petitChance->level);
        $this->assertSame(0, $petitChance->rarity);

        $puissantInt = Consumable::query()->where('dofusdb_id', '817')->first();
        $this->assertNotNull($puissantInt);
        $this->assertSame('Puissant Parchemin d\'Intelligence', $puissantInt->name);
        $this->assertSame(10000, $puissantInt->totalPriceKamas());
        $this->assertSame('15', $puissantInt->level);
        $this->assertSame(3, $puissantInt->rarity);

        $type = ConsumableType::query()->where('dofusdb_type_id', 76)->first();
        $this->assertSame(ConsumableType::STATE_PLAYABLE, $type?->state);
        $this->assertTrue((bool) $type?->show_in_catalog);

        $second = $importer->import($catalog);
        $this->assertSame([], $second['created']);
        $this->assertCount(24, $second['updated']);
        $this->assertSame(24, Consumable::query()->where('state', Consumable::STATE_AUTO)->count());
        $this->assertSame(1000, $petitChance->fresh()->totalPriceKamas());
    }

    public function test_clears_existing_recipe_on_update(): void
    {
        $catalog = CharacteristicRespecScrollCatalog::load();
        $this->seedType($catalog);
        $resource = Resource::factory()->create(['price' => '50']);
        $existing = Consumable::factory()->create([
            'dofusdb_id' => '809',
            'name' => 'Ancien parchemin',
            'state' => Consumable::STATE_DRAFT,
            'auto_update' => true,
            'price_calculated' => 50,
            'price_custom' => null,
        ]);
        $existing->resources()->attach($resource->id, ['quantity' => 1]);

        app(CharacteristicRespecScrollSeederImporter::class)->import($catalog);

        $existing->refresh();
        $this->assertSame(Consumable::STATE_AUTO, $existing->state);
        $this->assertFalse((bool) $existing->auto_update);
        $this->assertSame(0, $existing->resources()->count());
        $this->assertSame(1000, $existing->totalPriceKamas());
    }

    private function seedType(CharacteristicRespecScrollCatalog $catalog): void
    {
        ConsumableType::factory()->create([
            'name' => $catalog->consumableTypeName(),
            'dofusdb_type_id' => $catalog->consumableTypeDofusId(),
            'state' => ConsumableType::STATE_DRAFT,
            'show_in_catalog' => false,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
    }
}
