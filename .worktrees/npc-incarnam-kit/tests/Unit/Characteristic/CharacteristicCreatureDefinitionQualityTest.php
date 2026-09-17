<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Garantit la cohérence numérique des définitions de caractéristiques créature / monstre.
 */
class CharacteristicCreatureDefinitionQualityTest extends TestCase
{
    private CharacteristicFormulaService $formulaService;

    protected function setUp(): void
    {
        $this->formulaService = new CharacteristicFormulaService(
            new FormulaResolutionService(new SafeExpressionEvaluator)
        );
    }

    public function test_creature_grids_and_samples_stay_inside_declared_limits(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            $hasMonster = isset($definition['entities']['monster']) && is_array($definition['entities']['monster']);
            foreach ($definition['entities'] ?? [] as $entity => $row) {
                if (! is_array($row) || ! is_numeric($row['min'] ?? null) || ! is_numeric($row['max'] ?? null)) {
                    continue;
                }

                $min = (float) $row['min'];
                $max = (float) $row['max'];
                // La grille `*` peut rester éditoriale (PJ) alors que l’import scrap utilise `monster`.
                $checkGrid = $entity !== '*' || ! $hasMonster;
                if ($checkGrid) {
                    foreach ($row['norms_grid'] ?? [] as $values) {
                        if (! is_array($values)) {
                            continue;
                        }
                        foreach ($values as $value) {
                            $this->assertGreaterThanOrEqual($min, $value, "{$path} [{$entity}]");
                            $this->assertLessThanOrEqual($max, $value, "{$path} [{$entity}]");
                        }
                    }
                }
                foreach ($row['conversion_krosmoz_sample'] ?? [] as $value) {
                    $this->assertGreaterThanOrEqual($min, $value, "{$path} [{$entity}]");
                    $this->assertLessThanOrEqual($max, $value, "{$path} [{$entity}]");
                }
            }
        }
    }

    public function test_creature_conversion_samples_match_their_formulas(): void
    {
        foreach ($this->entityRows() as [$path, $entity, $row]) {
            $formula = $row['conversion_formula'] ?? null;
            $dofus = $row['conversion_dofus_sample'] ?? null;
            $krosmoz = $row['conversion_krosmoz_sample'] ?? null;
            if (! is_string($formula) || ! is_array($dofus) || ! is_array($krosmoz)
                || count($dofus) !== count($krosmoz)) {
                continue;
            }

            $keys = array_keys($dofus);
            $actual = [];
            foreach (array_values($dofus) as $index => $value) {
                $level = is_numeric($keys[$index] ?? null) ? (float) $keys[$index] : 1.0;
                $converted = $this->formulaService->evaluate($formula, [
                    'd' => (float) $value,
                    'level' => $level,
                ]);
                $this->assertNotNull($converted, "{$path} [{$entity}]");
                $actual[] = $this->clamp((int) round($converted), $row);
            }

            $this->assertSame(array_values($krosmoz), $actual, "{$path} [{$entity}]");
        }
    }

    public function test_monster_override_owns_samples_when_star_formula_differs(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            $star = $definition['entities']['*'] ?? null;
            $monster = $definition['entities']['monster'] ?? null;
            if (! is_array($star) || ! is_array($monster)) {
                continue;
            }

            $starFormula = (string) ($star['conversion_formula'] ?? '');
            $monsterFormula = (string) ($monster['conversion_formula'] ?? '');
            if ($monsterFormula === '' || $monsterFormula === $starFormula) {
                continue;
            }

            $starHasSamples = is_array($star['conversion_dofus_sample'] ?? null)
                || is_array($star['conversion_krosmoz_sample'] ?? null);
            $this->assertFalse(
                $starHasSamples,
                basename($path).' : samples * trompeurs alors que monster porte la courbe'
            );
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function definitions(): array
    {
        $root = dirname(__DIR__, 3).'/database/seeders/data/characteristic-definitions/creature';
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
     * @return list<array{0:string,1:string,2:array<string,mixed>}>
     */
    private function entityRows(): array
    {
        $rows = [];
        foreach ($this->definitions() as $path => $definition) {
            foreach ($definition['entities'] ?? [] as $entity => $row) {
                if (is_array($row)) {
                    $rows[] = [$path, (string) $entity, $row];
                }
            }
        }

        return $rows;
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
