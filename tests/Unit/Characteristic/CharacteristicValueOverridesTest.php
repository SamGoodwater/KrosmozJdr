<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Models\Characteristic;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Tests pour le champ value_overrides : stockage, cast JSON, normalisation des icônes.
 */
class CharacteristicValueOverridesTest extends TestCase
{
    use RefreshDatabase;

    public function test_value_overrides_is_stored_and_cast_as_array(): void
    {
        if (! Schema::hasColumn('characteristics', 'value_overrides')) {
            $this->markTestSkipped('Migration value_overrides non exécutée.');
        }

        $characteristic = Characteristic::create([
            'key' => 'test_override',
            'name' => 'Test Override',
            'type' => 'int',
            'value_overrides' => [
                ['value' => 1, 'icon' => 'cac.webp', 'color' => '#e53935', 'subtitle' => 'Corps à corps'],
                ['value' => 0, 'subtitle' => 'Auto-cible'],
            ],
        ]);

        $fresh = Characteristic::find($characteristic->id);
        $this->assertIsArray($fresh->value_overrides);
        $this->assertCount(2, $fresh->value_overrides);
        $this->assertSame(1, $fresh->value_overrides[0]['value']);
        $this->assertSame('cac.webp', $fresh->value_overrides[0]['icon']);
        $this->assertSame('Auto-cible', $fresh->value_overrides[1]['subtitle']);
    }

    public function test_value_overrides_null_when_empty(): void
    {
        if (! Schema::hasColumn('characteristics', 'value_overrides')) {
            $this->markTestSkipped('Migration value_overrides non exécutée.');
        }

        $characteristic = Characteristic::create([
            'key' => 'test_no_override',
            'name' => 'Test No Override',
            'type' => 'int',
            'value_overrides' => null,
        ]);

        $fresh = Characteristic::find($characteristic->id);
        $this->assertNull($fresh->value_overrides);
    }

    public function test_meta_service_normalizes_value_override_icons(): void
    {
        if (! Schema::hasColumn('characteristics', 'value_overrides')) {
            $this->markTestSkipped('Migration value_overrides non exécutée.');
        }

        $service = new CharacteristicMetaByDbColumnService();

        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('normalizeValueOverridesIcons');
        $method->setAccessible(true);

        $overrides = [
            ['value' => 1, 'icon' => 'cac.webp'],
            ['value' => false, 'icon' => 'icons/caracteristics/noSightLine.webp'],
            ['value' => true, 'icon' => 'fa-solid fa-eye'],
            ['value' => 0],
        ];

        $result = $method->invoke($service, $overrides);

        $this->assertSame('icons/caracteristics/cac.webp', $result[0]['icon']);
        $this->assertSame('icons/caracteristics/noSightLine.webp', $result[1]['icon']);
        $this->assertSame('fa-solid fa-eye', $result[2]['icon']);
        $this->assertArrayNotHasKey('icon', $result[3]);
    }

    public function test_meta_service_returns_null_for_empty_overrides(): void
    {
        $service = new CharacteristicMetaByDbColumnService();

        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('normalizeValueOverridesIcons');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($service, null));
        $this->assertNull($method->invoke($service, []));
    }
}
