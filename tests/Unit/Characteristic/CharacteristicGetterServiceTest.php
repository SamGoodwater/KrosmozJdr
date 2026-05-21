<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Database\Seeders\Type\ItemTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests unitaires pour CharacteristicGetterService.
 *
 * @see App\Services\Characteristic\Getter\CharacteristicGetterService
 */
class CharacteristicGetterServiceTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    private CharacteristicGetterService $getter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemTypeSeeder::class);
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seedMinimalCharacteristicsIfEmpty();
        $this->getter = $this->app->make(CharacteristicGetterService::class);
        // Singleton : vider le mémo entre tests (RefreshDatabase recrée les pivots item_types).
        $this->getter->clearCache();
    }

    public function test_get_definition_returns_null_for_unknown_key(): void
    {
        $this->assertNull($this->getter->getDefinition('unknown_key', 'monster'));
    }

    public function test_get_definition_returns_null_for_unknown_entity(): void
    {
        $this->assertNull($this->getter->getDefinition('life_points_creature', 'unknown_entity'));
    }

    public function test_get_definition_returns_merged_definition_for_creature(): void
    {
        $def = $this->getter->getDefinition('life_points_creature', 'monster');
        $this->assertNotNull($def);
        $this->assertSame('life_points_creature', $def['key']);
        $this->assertArrayHasKey('name', $def);
        $this->assertArrayHasKey('min', $def);
        $this->assertArrayHasKey('max', $def);
        $this->assertArrayHasKey('db_column', $def);
        $this->assertArrayHasKey('conversion_formula', $def);
    }

    public function test_get_limits_returns_null_for_unknown_key(): void
    {
        $this->assertNull($this->getter->getLimits('unknown', 'monster'));
    }

    public function test_get_limits_returns_min_max_for_known_characteristic(): void
    {
        $limits = $this->getter->getLimits('level_creature', 'monster');
        $this->assertNotNull($limits);
        $this->assertArrayHasKey('min', $limits);
        $this->assertArrayHasKey('max', $limits);
        $this->assertIsInt($limits['min']);
        $this->assertIsInt($limits['max']);
    }

    public function test_get_limits_by_field_resolves_key(): void
    {
        $limits = $this->getter->getLimitsByField('level', 'monster');
        $this->assertNotNull($limits);
        $this->assertArrayHasKey('min', $limits);
        $this->assertArrayHasKey('max', $limits);
    }

    public function test_get_limits_by_field_resolves_short_name_to_full_key(): void
    {
        $limitsByShort = $this->getter->getLimitsByField('level', 'monster');
        $limitsByFull = $this->getter->getLimits('level_creature', 'monster');
        $this->assertNotNull($limitsByShort);
        $this->assertNotNull($limitsByFull);
        $this->assertSame($limitsByFull['min'], $limitsByShort['min']);
        $this->assertSame($limitsByFull['max'], $limitsByShort['max']);
    }

    public function test_get_group_for_entity(): void
    {
        $this->assertSame('creature', $this->getter->getGroupForEntity('monster'));
        $this->assertSame('creature', $this->getter->getGroupForEntity('class'));
        $this->assertSame('object', $this->getter->getGroupForEntity('item'));
        $this->assertSame('object', $this->getter->getGroupForEntity('resource'));
        $this->assertSame('spell', $this->getter->getGroupForEntity('spell'));
    }

    public function test_clear_cache_does_not_throw(): void
    {
        $this->getter->clearCache();
        $this->expectNotToPerformAssertions();
    }

    public function test_object_definition_includes_equipment_type_restriction_for_pdf_scoped_stat(): void
    {
        $def = $this->getter->getDefinition('hit_bonus_object', 'item');
        $this->assertNotNull($def);
        $this->assertArrayHasKey('allowed_item_type_restricted', $def);
        $this->assertTrue($def['allowed_item_type_restricted']);
        $this->assertIsArray($def['allowed_item_type_ids']);
        $this->assertNotSame([], $def['allowed_item_type_ids']);
    }

    public function test_failure_hit_object_shares_amulet_slot_with_critical(): void
    {
        $def = $this->getter->getDefinition('failure_hit_object', 'item');
        $this->assertNotNull($def);
        $this->assertTrue($def['allowed_item_type_restricted']);
        $crit = $this->getter->getDefinition('critical_hit_object', 'item');
        $this->assertNotNull($crit);
        $this->assertSame($crit['allowed_item_type_ids'], $def['allowed_item_type_ids']);
    }

    public function test_object_definition_without_slot_allowlist_marks_unrestricted(): void
    {
        $def = $this->getter->getDefinition('level_object', 'item');
        $this->assertNotNull($def);
        $this->assertArrayHasKey('allowed_item_type_restricted', $def);
        $this->assertFalse($def['allowed_item_type_restricted']);
        $this->assertSame([], $def['allowed_item_type_ids']);
    }

    public function test_object_name_and_description_have_group_definition_and_db_column(): void
    {
        $nameDef = $this->getter->getDefinition('name_object', 'item');
        $this->assertNotNull($nameDef);
        $this->assertSame('name', $nameDef['db_column']);
        $this->assertSame('string', $nameDef['type']);

        $descDef = $this->getter->getDefinition('description_object', 'consumable');
        $this->assertNotNull($descDef);
        $this->assertSame('description', $descDef['db_column']);
        $this->assertSame('string', $descDef['type']);
    }
}
