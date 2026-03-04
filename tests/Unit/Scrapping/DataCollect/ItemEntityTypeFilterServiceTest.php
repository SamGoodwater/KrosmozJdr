<?php

namespace Tests\Unit\Scrapping\DataCollect;

use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use App\Services\Scrapping\DataCollect\ItemEntityTypeFilterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ItemEntityTypeFilterServiceTest extends TestCase
{
    use RefreshDatabase;

    private ItemEntityTypeFilterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(ItemEntityTypeFilterService::class);
    }

    public function test_resource_defaults_exclude_overlapping_equipment_and_consumable_type_ids(): void
    {
        ItemType::factory()->create([
            'dofusdb_type_id' => 6,
            'decision' => ItemType::DECISION_ALLOWED,
        ]);
        ConsumableType::factory()->create([
            'dofusdb_type_id' => 12,
            'decision' => ConsumableType::DECISION_ALLOWED,
        ]);
        ResourceType::factory()->create([
            'dofusdb_type_id' => 6,
            'decision' => ResourceType::DECISION_ALLOWED,
        ]);
        ResourceType::factory()->create([
            'dofusdb_type_id' => 12,
            'decision' => ResourceType::DECISION_ALLOWED,
        ]);
        ResourceType::factory()->create([
            'dofusdb_type_id' => 51,
            'decision' => ResourceType::DECISION_ALLOWED,
        ]);

        $filters = $this->service->applyDefaults('resource', [], ItemEntityTypeFilterService::TYPE_MODE_ALLOWED);

        $this->assertSame([51], $filters['typeIds'] ?? []);
        $this->assertFalse($this->service->isTypeIdAllowedForEntity('resource', 6, ItemEntityTypeFilterService::TYPE_MODE_ALLOWED));
        $this->assertFalse($this->service->isTypeIdAllowedForEntity('resource', 12, ItemEntityTypeFilterService::TYPE_MODE_ALLOWED));
        $this->assertTrue($this->service->isTypeIdAllowedForEntity('resource', 51, ItemEntityTypeFilterService::TYPE_MODE_ALLOWED));
    }
}

