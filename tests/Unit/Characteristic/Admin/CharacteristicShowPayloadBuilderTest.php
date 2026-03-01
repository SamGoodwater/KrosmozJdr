<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic\Admin;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Services\Characteristic\Admin\CharacteristicShowPayloadBuilder;
use App\Services\Characteristic\Conversion\ConversionFunctionRegistry;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Config\ScrappingMappingService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests unitaires pour CharacteristicShowPayloadBuilder.
 *
 * @see App\Services\Characteristic\Admin\CharacteristicShowPayloadBuilder
 */
class CharacteristicShowPayloadBuilderTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    private CharacteristicShowPayloadBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seedMinimalCharacteristicsIfEmpty();
        $this->app->make(CharacteristicGetterService::class)->clearCache();
        $this->builder = new CharacteristicShowPayloadBuilder(
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(ScrappingMappingService::class),
            $this->app->make(ConversionFunctionRegistry::class)
        );
    }

    public function test_build_returns_selected_with_entities_and_group(): void
    {
        $characteristic = Characteristic::where('key', 'life_points_creature')->first();
        $this->assertNotNull($characteristic);

        $payload = $this->builder->build($characteristic);

        $this->assertArrayHasKey('selected', $payload);
        $this->assertArrayHasKey('scrappingMappingsUsingThis', $payload);
        $this->assertArrayHasKey('characteristicsForConvertToLinked', $payload);
        $selected = $payload['selected'];
        $this->assertSame('life_points_creature', $selected['id']);
        $this->assertArrayHasKey('name', $selected);
        $this->assertArrayHasKey('entities', $selected);
        $this->assertArrayHasKey('group', $selected);
        $this->assertArrayHasKey('conversion_formulas', $selected);
        $this->assertIsArray($payload['scrappingMappingsUsingThis']);
        $this->assertIsArray($payload['characteristicsForConvertToLinked']);
    }

    public function test_build_includes_scrapping_mappings_when_rule_linked_to_characteristic(): void
    {
        $characteristic = Characteristic::where('key', 'life_points_creature')->first();
        $this->assertNotNull($characteristic);
        $mapping = \App\Models\Scrapping\ScrappingEntityMapping::create([
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping_key' => 'life',
            'from_path' => 'grades.0.lifePoints',
            'from_lang_aware' => false,
            'characteristic_id' => $characteristic->id,
            'sort_order' => 0,
        ]);
        $mapping->targets()->create([
            'target_model' => 'creatures',
            'target_field' => 'life',
            'sort_order' => 0,
        ]);

        $payload = $this->builder->build($characteristic);

        $this->assertCount(1, $payload['scrappingMappingsUsingThis']);
        $this->assertSame('life', $payload['scrappingMappingsUsingThis'][0]['mapping_key']);
        $this->assertSame('dofusdb', $payload['scrappingMappingsUsingThis'][0]['source']);
    }
}
