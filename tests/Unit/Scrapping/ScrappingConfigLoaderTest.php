<?php

namespace Tests\Unit\Scrapping;

use App\Services\Scrapping\Config\FormatterRegistry;
use App\Services\Scrapping\Config\ScrappingConfigLoader;
use Tests\TestCase;

class ScrappingConfigLoaderTest extends TestCase
{
    public function test_loads_dofusdb_source_and_entities_from_resources(): void
    {
        $baseDir = base_path('resources/scrapping');
        $registry = FormatterRegistry::fromDefaultPath($baseDir);
        $loader = new ScrappingConfigLoader($baseDir, $registry);

        $source = $loader->loadSource('dofusdb');
        $this->assertEquals('dofusdb', $source['source']);

        $entities = $loader->listEntities('dofusdb');
        $this->assertContains('monster', $entities);
        $this->assertContains('spell', $entities);
        $this->assertContains('item', $entities);

        $monster = $loader->loadEntity('dofusdb', 'monster');
        $this->assertEquals('monster', $monster['entity']);
        $this->assertIsArray($monster['mapping']);

        $spell = $loader->loadEntity('dofusdb', 'spell');
        $this->assertEquals('spell', $spell['entity']);
        $this->assertIsArray($spell['mapping']);

        $item = $loader->loadEntity('dofusdb', 'item');
        $this->assertEquals('item', $item['entity']);
        $this->assertIsArray($item['mapping']);
    }

    public function test_rejects_unknown_formatter_name(): void
    {
        $tmpDir = storage_path('framework/testing/scrapping-fixtures');
        @mkdir($tmpDir . '/formatters', 0777, true);
        @mkdir($tmpDir . '/sources/dofusdb/entities', 0777, true);

        file_put_contents($tmpDir . '/formatters/registry.json', json_encode([
            'version' => 1,
            'formatters' => [
                ['name' => 'toString', 'type' => 'pure', 'argsSchema' => new \stdClass()],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        file_put_contents($tmpDir . '/sources/dofusdb/source.json', json_encode([
            'version' => 1,
            'source' => 'dofusdb',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        file_put_contents($tmpDir . '/sources/dofusdb/entities/monster.json', json_encode([
            'version' => 1,
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping' => [
                [
                    'key' => 'name',
                    'from' => ['path' => 'name'],
                    'to' => [['model' => 'creatures', 'field' => 'name']],
                    'formatters' => [['name' => 'notAllowed', 'args' => new \stdClass()]],
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $registry = new FormatterRegistry($tmpDir . '/formatters/registry.json');
        $loader = new ScrappingConfigLoader($tmpDir, $registry);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("formatter non autorisé");

        $loader->loadEntity('dofusdb', 'monster');
    }
}

