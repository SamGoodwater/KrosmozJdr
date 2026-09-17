<?php

declare(strict_types=1);

namespace Tests\Unit\Seeders;

use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie que chaque conversion_formula des définitions JSON sort (groupe spell) est valide et évaluable.
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
        $spellDir = dirname(__DIR__, 3).'/database/seeders/data/characteristic-definitions/spell';
        $paths = glob($spellDir.'/*-definition.json') ?: [];
        self::assertNotEmpty($paths, 'Aucun fichier *-definition.json dans spell/');

        $vars = ['d' => 42, 'level' => 8];

        foreach ($paths as $path) {
            $raw = file_get_contents($path);
            self::assertNotFalse($raw, $path);
            $decoded = json_decode($raw, true);
            self::assertIsArray($decoded, $path);
            $entities = $decoded['entities'] ?? [];
            self::assertIsArray($entities, $path);
            foreach ($entities as $entity => $row) {
                if (! is_array($row)) {
                    continue;
                }
                $key = (string) $entity;
                $formula = $row['conversion_formula'] ?? null;
                if ($formula === null || $formula === '') {
                    continue;
                }
                self::assertIsString($formula, $key);

                $errors = $this->resolver->validateFormula($formula);
                self::assertSame([], $errors, 'Validation échoue pour '.$key.' dans '.basename($path).': '.json_encode($errors));

                $value = $this->resolver->evaluate($formula, $vars);
                self::assertNotNull($value, 'Évaluation null pour '.$key.' dans '.basename($path).' avec vars '.json_encode($vars));
            }
        }
    }
}
