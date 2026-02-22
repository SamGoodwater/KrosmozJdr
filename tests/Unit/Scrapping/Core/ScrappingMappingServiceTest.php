<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core;

use App\Models\Characteristic;
use App\Models\Scrapping\ScrappingEntityMapping;
use App\Services\Scrapping\Core\Config\ScrappingMappingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests unitaires pour ScrappingMappingService.
 *
 * @see App\Services\Scrapping\Core\Config\ScrappingMappingService
 */
class ScrappingMappingServiceTest extends TestCase
{
    use RefreshDatabase;

    private ScrappingMappingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ScrappingMappingService::class);
    }

    public function test_list_mappings_for_characteristic_returns_empty_when_none(): void
    {
        $char = Characteristic::create([
            'key' => 'test_char_creature',
            'name' => 'Test',
            'type' => 'int',
            'sort_order' => 0,
            'group' => 'creature',
        ]);

        $list = $this->service->listMappingsForCharacteristic($char->id);

        $this->assertIsArray($list);
        $this->assertSame([], $list);
    }

    public function test_list_mappings_for_characteristic_returns_summary_array(): void
    {
        $key = 'level_creature_test_' . uniqid();
        $char = Characteristic::create([
            'key' => $key,
            'name' => 'Niveau',
            'type' => 'int',
            'sort_order' => 0,
            'group' => 'creature',
        ]);
        $mapping = ScrappingEntityMapping::create([
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping_key' => 'level',
            'from_path' => 'grades.0.level',
            'from_lang_aware' => false,
            'characteristic_id' => $char->id,
            'sort_order' => 0,
        ]);
        $mapping->targets()->create([
            'target_model' => 'creatures',
            'target_field' => 'level',
            'sort_order' => 0,
        ]);

        $list = $this->service->listMappingsForCharacteristic($char->id);

        $this->assertCount(1, $list);
        $this->assertArrayHasKey('id', $list[0]);
        $this->assertArrayHasKey('source', $list[0]);
        $this->assertArrayHasKey('entity', $list[0]);
        $this->assertArrayHasKey('mapping_key', $list[0]);
        $this->assertArrayHasKey('from_path', $list[0]);
        $this->assertArrayHasKey('targets', $list[0]);
        $this->assertSame('level', $list[0]['mapping_key']);
        $this->assertSame('grades.0.level', $list[0]['from_path']);
        $this->assertCount(1, $list[0]['targets']);
        $this->assertSame('creatures', $list[0]['targets'][0]['model']);
        $this->assertSame('level', $list[0]['targets'][0]['field']);
    }

    public function test_get_mapping_for_entity_returns_null_when_no_bdd_mappings(): void
    {
        $out = $this->service->getMappingForEntity('dofusdb', 'entity_sans_mapping_xyz');

        $this->assertNull($out);
    }

    public function test_has_mapping_for_entity_returns_false_when_no_bdd_mappings(): void
    {
        $this->assertFalse($this->service->hasMappingForEntity('dofusdb', 'entity_sans_mapping_xyz'));
    }
}
