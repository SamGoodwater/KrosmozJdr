<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\GenerationConfigLoader;
use PHPUnit\Framework\TestCase;

final class GenerationConfigLoaderTest extends TestCase
{
    public function test_committed_file_freezes_sourced_stats_and_keeps_spell_effect_writable(): void
    {
        $loader = $this->committedLoader();

        $item = $loader->forEntity('item');
        $this->assertTrue($item->hasDofusSource);
        $this->assertTrue($item->isFieldFrozen('name'));
        $this->assertTrue($item->isCharacteristicFrozen('intelligence_object'));
        $this->assertSame([], $item->exampleIds);

        $spell = $loader->forEntity('spell');
        $this->assertTrue($spell->isFieldFrozen('name'));
        $this->assertFalse($spell->isFieldFrozen('effect'));
        $this->assertTrue($spell->isCharacteristicFrozen('action_points_spell'));

        $npc = $loader->forEntity('npc');
        $this->assertFalse($npc->hasDofusSource);
        $this->assertFalse($npc->isFieldFrozen('name'));
        $this->assertFalse($npc->isCharacteristicFrozen('strength_creature'));

        $this->assertSame(2, $loader->get('generation.max_retries'));
    }

    public function test_writable_characteristic_overrides_wildcard(): void
    {
        $loader = $this->loaderFrom([
            'version' => 1,
            'entities' => $this->minimalEntities([
                'item' => [
                    'has_dofus_source' => true,
                    'frozen_fields' => '*',
                    'writable_fields' => [],
                    'frozen_characteristics' => '*',
                    'writable_characteristics' => ['intelligence_object'],
                    'example_ids' => [12, 44],
                    'tone' => 'dry',
                ],
            ]),
        ]);

        $item = $loader->forEntity('item');
        $this->assertFalse($item->isCharacteristicFrozen('intelligence_object'));
        $this->assertTrue($item->isCharacteristicFrozen('strength_object'));
        $this->assertSame([12, 44], $item->exampleIds);
        $this->assertSame('dry', $item->extra['tone'] ?? null);
    }

    public function test_explicit_frozen_list_does_not_freeze_others(): void
    {
        $loader = $this->loaderFrom([
            'version' => 1,
            'entities' => $this->minimalEntities([
                'monster' => [
                    'has_dofus_source' => true,
                    'frozen_fields' => ['name'],
                    'writable_fields' => [],
                    'frozen_characteristics' => ['vitality'],
                    'writable_characteristics' => [],
                    'example_ids' => [],
                ],
            ]),
        ]);

        $monster = $loader->forEntity('monster');
        $this->assertTrue($monster->isFieldFrozen('name'));
        $this->assertFalse($monster->isFieldFrozen('description'));
        $this->assertTrue($monster->isCharacteristicFrozen('vitality'));
        $this->assertFalse($monster->isCharacteristicFrozen('strength_creature'));
    }

    public function test_get_reads_arbitrary_nested_keys(): void
    {
        $loader = $this->loaderFrom([
            'version' => 1,
            'generation' => ['max_retries' => 1],
            'prompt_pack' => 'v3',
            'entities' => $this->minimalEntities(),
        ]);

        $this->assertSame(1, $loader->get('generation.max_retries'));
        $this->assertSame('v3', $loader->get('prompt_pack'));
        $this->assertSame('fallback', $loader->get('missing.key', 'fallback'));
    }

    public function test_payload_override_skips_the_file(): void
    {
        $payload = json_decode(
            (string) file_get_contents(dirname(__DIR__, 3).'/resources/ia/generation.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $payload['generation']['max_retries'] = 4;

        $loader = new GenerationConfigLoader('/tmp/krosmoz-ia-missing.json', $payload);

        $this->assertSame(4, $loader->get('generation.max_retries'));
        $this->assertTrue($loader->forEntity('item')->isFieldFrozen('name'));
    }

    public function test_unknown_entity_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Type d'entité IA inconnu: panoply");

        $this->committedLoader()->forEntity('panoply');
    }

    public function test_missing_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Config JSON introuvable');

        (new GenerationConfigLoader('/tmp/krosmoz-ia-missing.json'))->raw();
    }

    public function test_invalid_json_throws(): void
    {
        $path = $this->writeTemp('{"version":');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('JSON invalide');

        (new GenerationConfigLoader($path))->raw();
    }

    private function committedLoader(): GenerationConfigLoader
    {
        return new GenerationConfigLoader(dirname(__DIR__, 3).'/resources/ia/generation.json');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function loaderFrom(array $payload): GenerationConfigLoader
    {
        return new GenerationConfigLoader($this->writeTemp(
            json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
        ));
    }

    /**
     * @param  array<string, array<string, mixed>>  $overrides
     * @return array<string, array<string, mixed>>
     */
    private function minimalEntities(array $overrides = []): array
    {
        $blank = [
            'has_dofus_source' => false,
            'frozen_fields' => [],
            'writable_fields' => [],
            'frozen_characteristics' => [],
            'writable_characteristics' => [],
            'example_ids' => [],
        ];

        $entities = [
            'item' => $blank,
            'spell' => $blank,
            'monster' => $blank,
            'npc' => $blank,
        ];

        foreach ($overrides as $type => $row) {
            $entities[$type] = $row;
        }

        return $entities;
    }

    private function writeTemp(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'krosmoz-ia-');
        if ($path === false) {
            $this->fail('Impossible de créer un JSON temporaire.');
        }
        file_put_contents($path, $contents);

        return $path;
    }
}
