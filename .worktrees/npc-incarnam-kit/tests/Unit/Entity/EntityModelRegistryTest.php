<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Models\Entity\Spell;
use App\Support\EntityModelRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Registre backend des entités JDR.
 *
 * @example php artisan test --filter=EntityModelRegistryTest
 */
class EntityModelRegistryTest extends TestCase
{
    public function test_registry_normalizes_common_entity_type_aliases(): void
    {
        $this->assertSame('spells', EntityModelRegistry::normalizeType('spell'));
        $this->assertSame('breeds', EntityModelRegistry::normalizeType('class'));
        $this->assertSame('creature-traits', EntityModelRegistry::normalizeType('creature-trait'));
    }

    public function test_registry_contains_main_entity_models(): void
    {
        $map = EntityModelRegistry::modelMap();

        $this->assertSame(Spell::class, $map['spells']);
        $this->assertArrayHasKey('items', $map);
        $this->assertArrayHasKey('monsters', $map);
        $this->assertArrayHasKey('conditions', $map);
        $this->assertArrayHasKey('specializations', $map);
    }
}
