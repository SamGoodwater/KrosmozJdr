<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic\Formula;

use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use Tests\TestCase;

/**
 * Tests unitaires pour FormulaResolutionService.
 *
 * @see App\Services\Characteristic\Formula\FormulaResolutionService
 */
class FormulaResolutionServiceTest extends TestCase
{
    private FormulaResolutionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FormulaResolutionService(new SafeExpressionEvaluator());
    }

    public function test_evaluate_simple_formula(): void
    {
        $result = $this->service->evaluate('[level] * 2', ['level' => 10]);
        $this->assertSame(20.0, $result);
    }

    public function test_evaluate_returns_null_for_empty_formula(): void
    {
        $this->assertNull($this->service->evaluate(null, ['level' => 10]));
        $this->assertNull($this->service->evaluate('', ['level' => 10]));
    }

    public function test_evaluate_for_variable_range_returns_all_values(): void
    {
        $formula = '[level] * 2';
        $result = $this->service->evaluateForVariableRange($formula, 'level', 1, 5, []);

        $this->assertSame([
            1 => 2.0,
            2 => 4.0,
            3 => 6.0,
            4 => 8.0,
            5 => 10.0,
        ], $result);
    }

    public function test_evaluate_for_variable_range_with_base_variables(): void
    {
        $formula = '[vitality] * 10 + [level] * 2';
        $result = $this->service->evaluateForVariableRange(
            $formula,
            'level',
            1,
            3,
            ['vitality' => 5]
        );

        $this->assertSame(50.0 + 2.0, $result[1]); // 52
        $this->assertSame(50.0 + 4.0, $result[2]); // 54
        $this->assertSame(50.0 + 6.0, $result[3]); // 56
    }

    public function test_evaluate_for_variable_range_swapped_min_max(): void
    {
        $result = $this->service->evaluateForVariableRange('[level]', 'level', 5, 1, []);
        $this->assertSame([1 => 1.0, 2 => 2.0, 3 => 3.0, 4 => 4.0, 5 => 5.0], $result);
    }

    public function test_validate_formula_empty_returns_no_errors(): void
    {
        $this->assertSame([], $this->service->validateFormula(null));
        $this->assertSame([], $this->service->validateFormula(''));
    }

    public function test_validate_formula_valid_expression_returns_no_errors(): void
    {
        $this->assertSame([], $this->service->validateFormula('[level] * 2 + floor([vitality]/10)'));
    }

    public function test_validate_formula_invalid_chars_returns_errors(): void
    {
        $errors = $this->service->validateFormula('[level]; system("id");');
        $this->assertNotEmpty($errors);
    }

    public function test_validate_formula_table_valid(): void
    {
        $json = '{"characteristic":"level","1":0,"7":2,"14":4}';
        $this->assertSame([], $this->service->validateFormula($json));
    }

    public function test_evaluate_dice_notation_literal_1d8(): void
    {
        $result = $this->service->evaluate('1d8', []);
        $this->assertNotNull($result);
        $this->assertGreaterThanOrEqual(1.0, $result);
        $this->assertLessThanOrEqual(8.0, $result);
        $this->assertSame((float) (int) $result, $result);
    }

    public function test_evaluate_dice_notation_literal_2d6(): void
    {
        $result = $this->service->evaluate('2d6', []);
        $this->assertNotNull($result);
        $this->assertGreaterThanOrEqual(2.0, $result);
        $this->assertLessThanOrEqual(12.0, $result);
        $this->assertSame((float) (int) $result, $result);
    }

    public function test_evaluate_dice_notation_with_variables(): void
    {
        $result = $this->service->evaluate('[level]d[life_dice]', ['level' => 3, 'life_dice' => 6]);
        $this->assertNotNull($result);
        $this->assertGreaterThanOrEqual(3.0, $result);
        $this->assertLessThanOrEqual(18.0, $result);
    }

    public function test_evaluate_formula_with_dice_and_arithmetic(): void
    {
        $formula = '[vitality]*([level]-1) + [life_dice] + [level]d[life_dice]';
        $result = $this->service->evaluate($formula, [
            'vitality' => 5,
            'level' => 3,
            'life_dice' => 8,
        ]);
        $this->assertNotNull($result);
        $expectedFixed = 5 * (3 - 1) + 8; // 10 + 8 = 18
        $diceMin = 3;
        $diceMax = 3 * 8;
        $this->assertGreaterThanOrEqual($expectedFixed + $diceMin, $result);
        $this->assertLessThanOrEqual($expectedFixed + $diceMax, $result);
    }

    public function test_validate_formula_accepts_dice_notation(): void
    {
        $this->assertSame([], $this->service->validateFormula('[level]d[life_dice]'));
        $this->assertSame([], $this->service->validateFormula('2d6 + [vitality]'));
    }

    public function test_evaluate_power_operator(): void
    {
        $this->assertSame(8.0, $this->service->evaluate('2**3', []));
        $this->assertSame(9.0, $this->service->evaluate('[level]**2', ['level' => 3]));
        $this->assertSame(512.0, $this->service->evaluate('2**3**2', []));
    }

    public function test_validate_formula_accepts_power_operator(): void
    {
        $this->assertSame([], $this->service->validateFormula('[level]**2'));
        $this->assertSame([], $this->service->validateFormula('2**([level]-1)'));
    }
}
