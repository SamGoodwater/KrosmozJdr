<?php

namespace Tests\Unit\Scrapping\V2;

use App\Services\Scrapping\V2\Config\CollectAliasResolver;
use Tests\TestCase;

/**
 * Tests unitaires pour CollectAliasResolver (alias CLI → source/entity).
 */
class CollectAliasResolverTest extends TestCase
{
    private CollectAliasResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = CollectAliasResolver::default();
    }

    public function test_resolve_monster_returns_dofusdb_monster(): void
    {
        $config = $this->resolver->resolve('monster');

        $this->assertNotNull($config);
        $this->assertSame('dofusdb', $config['source']);
        $this->assertSame('monster', $config['entity']);
        $this->assertArrayHasKey('label', $config);
    }

    public function test_resolve_spell_returns_dofusdb_spell(): void
    {
        $config = $this->resolver->resolve('spell');

        $this->assertNotNull($config);
        $this->assertSame('dofusdb', $config['source']);
        $this->assertSame('spell', $config['entity']);
    }

    public function test_resolve_ressource_returns_item_with_default_filter(): void
    {
        $config = $this->resolver->resolve('ressource');

        $this->assertNotNull($config);
        $this->assertSame('dofusdb', $config['source']);
        $this->assertSame('item', $config['entity']);
        $this->assertArrayHasKey('defaultFilter', $config);
        $this->assertSame('resource', $config['defaultFilter']['superTypeGroup'] ?? null);
    }

    public function test_resolve_classe_returns_breed(): void
    {
        $config = $this->resolver->resolve('classe');

        $this->assertNotNull($config);
        $this->assertSame('dofusdb', $config['source']);
        $this->assertSame('breed', $config['entity']);
    }

    public function test_resolve_item_returns_item(): void
    {
        $config = $this->resolver->resolve('item');

        $this->assertNotNull($config);
        $this->assertSame('item', $config['entity']);
    }

    public function test_resolve_consumable_returns_item_with_filter(): void
    {
        $config = $this->resolver->resolve('consumable');

        $this->assertNotNull($config);
        $this->assertSame('item', $config['entity']);
        $this->assertSame('consumable', $config['defaultFilter']['superTypeGroup'] ?? null);
    }

    public function test_resolve_unknown_returns_null(): void
    {
        $this->assertNull($this->resolver->resolve('unknown'));
        $this->assertNull($this->resolver->resolve(''));
    }

    public function test_resolve_is_case_insensitive(): void
    {
        $lower = $this->resolver->resolve('monster');
        $upper = $this->resolver->resolve('MONSTER');

        $this->assertNotNull($lower);
        $this->assertNotNull($upper);
        $this->assertSame($lower['entity'], $upper['entity']);
    }

    public function test_list_aliases_returns_non_empty_sorted_list(): void
    {
        $aliases = $this->resolver->listAliases();

        $this->assertIsArray($aliases);
        $this->assertNotEmpty($aliases);
        $this->assertContains('monster', $aliases);
        $this->assertContains('spell', $aliases);
        $this->assertContains('ressource', $aliases);
        $sorted = $aliases;
        sort($sorted);
        $this->assertSame($sorted, $aliases);
    }
}
