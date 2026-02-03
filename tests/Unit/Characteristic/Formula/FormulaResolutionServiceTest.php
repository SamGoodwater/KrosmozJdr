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
}
