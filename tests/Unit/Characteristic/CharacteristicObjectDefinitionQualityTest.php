<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Garantit la cohérence numérique des définitions de caractéristiques objet.
 */
class CharacteristicObjectDefinitionQualityTest extends TestCase
{
    private CharacteristicFormulaService $formulaService;

    protected function setUp(): void
    {
        $this->formulaService = new CharacteristicFormulaService(
            new FormulaResolutionService(new SafeExpressionEvaluator)
        );
    }

    public function test_object_grids_and_samples_stay_inside_declared_limits(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            $row = $definition['entities']['*'] ?? null;
            if (! is_array($row) || ! is_numeric($row['min'] ?? null) || ! is_numeric($row['max'] ?? null)) {
                continue;
            }

            $min = (float) $row['min'];
            $max = (float) $row['max'];
            foreach ($row['norms_grid'] ?? [] as $values) {
                if (! is_array($values)) {
                    continue;
                }
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

    public function test_object_conversion_samples_match_their_formulas(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            $row = $definition['entities']['*'] ?? null;
            if (! is_array($row)) {
                continue;
            }

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

    public function test_object_zero_stays_zero_when_formula_present(): void
    {
        foreach ($this->definitions() as $path => $definition) {
            $row = $definition['entities']['*'] ?? null;
            $formula = is_array($row) ? ($row['conversion_formula'] ?? null) : null;
            if (($definition['characteristic']['type'] ?? null) !== 'int' || ! is_string($formula) || $formula === '') {
                continue;
            }

            $converted = $this->formulaService->evaluate($formula, ['d' => 0, 'level' => 1]);
            $this->assertNotNull($converted, $path);
            $this->assertSame(0, $this->clamp((int) round($converted), $row), $path);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function definitions(): array
    {
        $root = dirname(__DIR__, 3).'/database/seeders/data/characteristic-definitions/object';
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
