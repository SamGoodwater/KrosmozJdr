<?php

namespace Tests\Unit\Scrapping\Core;

use App\Services\Scrapping\Core\Config\ConfigLoader;
use Tests\TestCase;

/**
 * Tests unitaires pour ConfigLoader (resources/scrapping/config).
 */
class ConfigLoaderTest extends TestCase
{
    private ConfigLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = ConfigLoader::default();
    }

    public function test_loads_dofusdb_source(): void
    {
        $source = $this->loader->loadSource('dofusdb');

        $this->assertIsArray($source);
        $this->assertSame('dofusdb', $source['source'] ?? null);
        $this->assertArrayHasKey('baseUrl', $source);
    }

    public function test_list_entities_returns_sorted_entity_names(): void
    {
        $entities = $this->loader->listEntities('dofusdb');

        $this->assertIsArray($entities);
        $this->assertContains('monster', $entities);
        $this->assertContains('spell', $entities);
        $this->assertContains('item', $entities);
        $this->assertContains('breed', $entities);
        $this->assertContains('monster-race', $entities);
        $this->assertContains('item-type', $entities);
        $this->assertContains('item-super-type', $entities);
        $sorted = $entities;
        sort($sorted);
        $this->assertSame($sorted, $entities);
    }

    public function test_load_entity_monster_has_endpoints_and_mapping(): void
    {
        $entity = $this->loader->loadEntity('dofusdb', 'monster');

        $this->assertSame('monster', $entity['entity']);
        $this->assertSame('dofusdb', $entity['source']);
        $this->assertIsArray($entity['endpoints']);
        $this->assertArrayHasKey('fetchOne', $entity['endpoints']);
        $this->assertArrayHasKey('fetchMany', $entity['endpoints']);
        $this->assertIsArray($entity['mapping']);
        $this->assertArrayHasKey('filters', $entity);
    }

    public function test_load_entity_spell(): void
    {
        $entity = $this->loader->loadEntity('dofusdb', 'spell');

        $this->assertSame('spell', $entity['entity']);
        $this->assertNotEmpty($entity['endpoints']['fetchMany']['path'] ?? '');
    }

    public function test_load_entity_item(): void
    {
        $entity = $this->loader->loadEntity('dofusdb', 'item');

        $this->assertSame('item', $entity['entity']);
        $this->assertIsArray($entity['filters']['supported'] ?? []);
    }

    public function test_rejects_unknown_source(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Config JSON introuvable');

        $this->loader->loadSource('unknown-source');
    }

    public function test_rejects_unknown_entity(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Config JSON introuvable');

        $this->loader->loadEntity('dofusdb', 'unknown-entity');
    }

    public function test_default_uses_config_base_path(): void
    {
        $loader = ConfigLoader::default();
        $source = $loader->loadSource('dofusdb');

        $this->assertSame('dofusdb', $source['source']);
    }
}
