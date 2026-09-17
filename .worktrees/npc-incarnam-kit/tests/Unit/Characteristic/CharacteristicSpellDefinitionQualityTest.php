<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Garantit la cohérence numérique des définitions de caractéristiques de sorts.
 */
class CharacteristicSpellDefinitionQualityTest extends TestCase
{
    private CharacteristicFormulaService $formulaService;

    protected function setUp(): void
    {
        $this->formulaService = new CharacteristicFormulaService(
            new FormulaResolutionService(new SafeExpressionEvaluator)
        );
    }

    public function test_spell_grids_and_samples_stay_inside_declared_limits(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            $row = $definition['entities']['*'];
            if (! is_numeric($row['min'] ?? null) || ! is_numeric($row['max'] ?? null)) {
                continue;
            }

            $min = (float) $row['min'];
            $max = (float) $row['max'];
            foreach ($row['norms_grid'] ?? [] as $values) {
                foreach ($values as $value) {
                    $this->assertGreaterThanOrEqual($min, $value, $path);
                    $this->assertLessThanOrEqual($max, $value, $path);
                }
            }
            foreach ($row['conversion_krosmoz_sample'] ?? [] as $value) {
                $this->assertGreaterThanOrEqual($min, $value, $path);
                $this->assertLessThanOrEqual($max, $value, $path);
            }
        }
    }

    public function test_spell_conversion_samples_match_their_formulas(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            $row = $definition['entities']['*'];
            $formula = $row['conversion_formula'] ?? null;
            $dofus = $row['conversion_dofus_sample'] ?? null;
            $krosmoz = $row['conversion_krosmoz_sample'] ?? null;
            if (! is_string($formula) || ! is_array($dofus) || ! is_array($krosmoz)
                || count($dofus) !== count($krosmoz)) {
                continue;
            }

            $actual = [];
            foreach (array_values($dofus) as $value) {
                $converted = $this->formulaService->evaluate($formula, ['d' => (float) $value, 'level' => 1]);
                $this->assertNotNull($converted, $path);
                $actual[] = $this->clamp((int) round($converted), $row);
            }

            $this->assertSame(array_values($krosmoz), $actual, $path);
        }
    }

    public function test_spell_sample_rows_are_complete_and_zero_stays_zero(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            $row = $definition['entities']['*'];
            foreach ($row['conversion_sample_rows'] ?? [] as $sample) {
                $this->assertNotContains(null, $sample, $path);
            }

            $formula = $row['conversion_formula'] ?? null;
            if (($definition['characteristic']['type'] ?? null) !== 'int' || ! is_string($formula)) {
                continue;
            }
            $converted = $this->formulaService->evaluate($formula, ['d' => 0, 'level' => 1]);
            $this->assertNotNull($converted, $path);
            $expected = ($definition['characteristic']['key'] ?? '') === 'level_spell' ? 1 : 0;
            $this->assertSame($expected, $this->clamp((int) round($converted), $row), $path);
        }
    }

    public function test_relative_resistance_grids_only_use_krosmoz_tiers(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            if (! str_starts_with((string) $definition['characteristic']['key'], 'res_')) {
                continue;
            }
            foreach ($definition['entities']['*']['norms_grid'] ?? [] as $values) {
                foreach ($values as $value) {
                    $this->assertContains($value, [-100, -50, 0, 50, 100], $path);
                }
            }
        }
    }

    public function test_spell_level_mapping_uses_minimum_player_level_and_the_conversion_formula(): void
    {
        $root = dirname(__DIR__, 3);
        $config = json_decode(
            (string) file_get_contents($root.'/resources/scrapping/config/sources/dofusdb/entities/spell.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $rules = array_values(array_filter(
            $config['mapping'],
            static fn (array $rule): bool => ($rule['key'] ?? null) === 'level'
        ));

        $this->assertCount(1, $rules);
        $this->assertSame('spell_global.minPlayerLevel', $rules[0]['from']['path']);
        $this->assertSame('level_spell', $rules[0]['characteristic_key']);
        $this->assertContains('convertCharacteristic', array_column($rules[0]['formatters'], 'name'));
        $this->assertNotContains('po', array_column($config['mapping'], 'key'));

        $snapshot = require $root.'/database/seeders/data/scrapping_entity_mappings.php';
        $spellRows = array_values(array_filter(
            $snapshot,
            static fn (array $row): bool => ($row['entity'] ?? null) === 'spell'
        ));
        $snapshotLevels = array_values(array_filter(
            $spellRows,
            static fn (array $row): bool => ($row['mapping_key'] ?? null) === 'level'
        ));
        $this->assertCount(1, $snapshotLevels);
        $this->assertSame('spell_global.minPlayerLevel', $snapshotLevels[0]['from_path']);
        $this->assertNotContains('po', array_column($spellRows, 'mapping_key'));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function definitions(): array
    {
        $root = dirname(__DIR__, 3).'/database/seeders/data/characteristic-definitions/spell';
        $definitions = [];
        foreach (glob($root.'/*.json') ?: [] as $path) {
            $definitions[$path] = json_decode(
                (string) file_get_contents($path),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        return $definitions;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function clamp(int $value, array $row): int
    {
        if (is_numeric($row['min'] ?? null)) {
            $value = max((int) ceil((float) $row['min']), $value);
        }
        if (is_numeric($row['max'] ?? null)) {
            $value = min((int) floor((float) $row['max']), $value);
        }

        return $value;
    }
}
