<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic\Formula;

use App\Services\Characteristic\Formula\CreatureFormulaPlaceholderValidator;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Services\Characteristic\Formula\CreatureFormulaPlaceholderValidator
 */
final class CreatureFormulaPlaceholderValidatorTest extends TestCase
{
    public function test_validate_creature_definition_file_detects_unknown_placeholder(): void
    {
        $formulas = new FormulaResolutionService(new SafeExpressionEvaluator);
        $validator = new CreatureFormulaPlaceholderValidator($formulas);

        $tmp = sys_get_temp_dir().'/krosmoz_creature_formula_test_'.uniqid().'.json';
        file_put_contents($tmp, <<<'JSON'
{
    "_schema_version": "1",
    "characteristic": { "key": "fixture_test_creature" },
    "entities": {
        "*": {
            "formula": "[this_placeholder_does_not_exist_in_allowlist]+1",
            "conversion_formula": null
        }
    }
}
JSON);

        try {
            $errors = $validator->validateCreatureDefinitionFile($tmp, ['level_creature' => true]);
            $this->assertNotEmpty($errors);
            $this->assertSame('this_placeholder_does_not_exist_in_allowlist', $errors[0]['unknown']);
        } finally {
            @unlink($tmp);
        }
    }

    public function test_validate_creature_definition_file_passes_with_full_allowlist(): void
    {
        $formulas = new FormulaResolutionService(new SafeExpressionEvaluator);
        $validator = new CreatureFormulaPlaceholderValidator($formulas);

        $tmp = sys_get_temp_dir().'/krosmoz_creature_formula_test_ok_'.uniqid().'.json';
        file_put_contents($tmp, <<<'JSON'
{
    "_schema_version": "1",
    "characteristic": { "key": "fixture_ok_creature" },
    "entities": {
        "*": {
            "formula": "[modifier_agility_creature]+[acrobatics_object]",
            "conversion_formula": null
        }
    }
}
JSON);

        try {
            $allowed = [
                'modifier_agility_creature' => true,
                'acrobatics_object' => true,
            ];
            $errors = $validator->validateCreatureDefinitionFile($tmp, $allowed);
            $this->assertSame([], $errors);
        } finally {
            @unlink($tmp);
        }
    }
}
