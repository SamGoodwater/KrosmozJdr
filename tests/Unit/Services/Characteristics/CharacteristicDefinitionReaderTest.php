<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Characteristics;

use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionJson;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Tests\TestCaseNoDatabase;

final class CharacteristicDefinitionReaderTest extends TestCaseNoDatabase
{
    public function test_load_returns_characteristic_and_entities(): void
    {
        $path = base_path(CharacteristicDefinitionNaming::definitionRelativePath('action_points', 'creature'));
        self::assertFileExists($path);

        $def = CharacteristicDefinitionReader::load($path);

        self::assertSame('action_points_creature', $def['characteristic']['key']);
        self::assertArrayHasKey('*', $def['entities']);
        self::assertIsArray($def['entities']['*']);
    }

    public function test_all_definition_paths_include_imported_files(): void
    {
        $paths = CharacteristicDefinitionReader::allDefinitionAbsolutePaths();
        self::assertNotEmpty($paths);
        $names = array_map(basename(...), $paths);
        self::assertContains('action_points-creature-definition.json', $names);
    }

    public function test_load_rejects_path_outside_definitions_root(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CharacteristicDefinitionReader::load(base_path('composer.json'));
    }

    public function test_strip_underscore_keys_removes_meta_keys(): void
    {
        $data = [
            '_meta' => 'x',
            'characteristic' => [
                'key' => 'k',
                '_note' => 'n',
                'name' => 'N',
            ],
        ];

        $stripped = CharacteristicDefinitionJson::stripUnderscoreKeys($data);

        self::assertArrayNotHasKey('_meta', $stripped);
        self::assertSame('k', $stripped['characteristic']['key']);
        self::assertArrayNotHasKey('_note', $stripped['characteristic']);
    }
}
