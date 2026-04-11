<?php

declare(strict_types=1);

namespace Tests\Unit\Seeders;

use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie que chaque conversion_formula du seeder characteristic_spell est valide et évaluable.
 *
 * Test sans bootstrap Laravel (pas de BDD) : pure logique de formules.
 */
class CharacteristicSpellConversionFormulasTest extends TestCase
{
    private FormulaResolutionService $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new FormulaResolutionService(new SafeExpressionEvaluator);
    }

    public function test_all_conversion_formulas_validate_and_evaluate(): void
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = require dirname(__DIR__, 3).'/database/seeders/data/characteristic_spell.php';

        $vars = ['d' => 42, 'level' => 8];

        foreach ($rows as $row) {
            $key = $row['characteristic_key'] ?? 'unknown';
            $formula = $row['conversion_formula'] ?? null;
            $this->assertIsString($formula, $key);

            $errors = $this->resolver->validateFormula($formula);
            $this->assertSame([], $errors, 'Validation échoue pour '.$key.': '.json_encode($errors));

            $value = $this->resolver->evaluate($formula, $vars);
            $this->assertNotNull($value, 'Évaluation null pour '.$key.' avec vars '.json_encode($vars));
        }
    }
}
