<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\EquipmentCharacteristicService;
use Database\Seeders\CharacteristicConfigSeeder;
use Database\Seeders\EquipmentCharacteristicConfigSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Tests unitaires pour EquipmentCharacteristicService.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/PLAN_MIGRATION_CHARACTERISTICS_DB.md
 */
class EquipmentCharacteristicServiceTest extends TestCase
{
    use RefreshDatabase;

    private EquipmentCharacteristicService $service;

    protected function setUp(): void
    {
        parent::setUp();
        (new CharacteristicConfigSeeder)->run();
        $this->service = $this->app->make(EquipmentCharacteristicService::class);
        (new EquipmentCharacteristicConfigSeeder)->run();
        $this->service->clearCache();
    }

    public function test_get_slots_returns_name_and_characteristics_per_slot(): void
    {
        $slots = $this->service->getSlots();

        $this->assertIsArray($slots);
        $this->assertArrayHasKey('weapon', $slots);
        $weapon = $slots['weapon'];
        $this->assertArrayHasKey('name', $weapon);
        $this->assertArrayHasKey('characteristics', $weapon);
        $this->assertSame('Arme', $weapon['name']);
        $this->assertIsArray($weapon['characteristics']);
    }

    public function test_slot_characteristic_has_bracket_max_and_forgemagie_max(): void
    {
        $slots = $this->service->getSlots();
        $this->assertArrayHasKey('weapon', $slots);
        $chars = $slots['weapon']['characteristics'];
        $this->assertArrayHasKey('touch', $chars);
        $touch = $chars['touch'];
        $this->assertArrayHasKey('bracket_max', $touch);
        $this->assertArrayHasKey('forgemagie_max', $touch);
        $this->assertIsArray($touch['bracket_max']);
    }

    public function test_get_slot_returns_null_for_unknown_id(): void
    {
        $this->assertNull($this->service->getSlot('unknown'));
    }

    public function test_get_slot_returns_slot_for_known_id(): void
    {
        $slot = $this->service->getSlot('weapon');
        $this->assertNotNull($slot);
        $this->assertArrayHasKey('name', $slot);
        $this->assertArrayHasKey('characteristics', $slot);
    }

    public function test_slots_are_cached(): void
    {
        $this->service->getSlots();
        $this->assertTrue(Cache::has('equipment_characteristics.slots'));
    }

    public function test_clear_cache_removes_cache(): void
    {
        $this->service->getSlots();
        $this->service->clearCache();
        $this->assertFalse(Cache::has('equipment_characteristics.slots'));
    }
}
