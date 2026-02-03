<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\FormulaEvaluator;
use Tests\TestCase;

/**
 * Tests unitaires pour FormulaEvaluator.
 *
 * @see App\Services\Characteristic\FormulaEvaluator
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 */
class FormulaEvaluatorTest extends TestCase
{
    private FormulaEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = $this->app->make(FormulaEvaluator::class);
    }

    public function test_evaluate_returns_null_for_empty_formula(): void
    {
        $this->assertNull($this->evaluator->evaluate('', ['level' => 10]));
    }

    public function test_evaluate_simple_expression(): void
    {
        $result = $this->evaluator->evaluate('[level] * 2', ['level' => 10]);
        $this->assertSame(20.0, $result);
    }

    public function test_evaluate_multiple_variables(): void
    {
        $result = $this->evaluator->evaluate('[vitality] * 10 + [level] * 2', [
            'vitality' => 5,
            'level' => 10,
        ]);
        $this->assertSame(70.0, $result); // 50 + 20
    }

    public function test_evaluate_unknown_variable_replaced_by_zero(): void
    {
        $result = $this->evaluator->evaluate('[level] + [unknown]', ['level' => 10]);
        $this->assertSame(10.0, $result);
    }

    public function test_evaluate_floor(): void
    {
        $result = $this->evaluator->evaluate('floor([level] / 3)', ['level' => 10]);
        $this->assertSame(3.0, $result);
    }

    public function test_evaluate_ceil(): void
    {
        $result = $this->evaluator->evaluate('ceil([level] / 3)', ['level' => 10]);
        $this->assertSame(4.0, $result);
    }

    public function test_evaluate_compound_with_floor(): void
    {
        $result = $this->evaluator->evaluate('6 + floor([level] / 3)', ['level' => 9]);
        $this->assertSame(9.0, $result); // 6 + 3
    }

    public function test_evaluate_invalid_expression_returns_null(): void
    {
        $this->assertNull($this->evaluator->evaluate('echo "bad";', ['level' => 10]));
    }

    public function test_evaluate_disallowed_chars_returns_null(): void
    {
        $this->assertNull($this->evaluator->evaluate('[level]; system("id");', ['level' => 10]));
    }

    public function test_evaluate_division(): void
    {
        $result = $this->evaluator->evaluate('[level] / 2', ['level' => 10]);
        $this->assertSame(5.0, $result);
    }

    public function test_evaluate_float_variables(): void
    {
        $result = $this->evaluator->evaluate('[a] + [b]', ['a' => 1.5, 'b' => 2.5]);
        $this->assertSame(4.0, $result);
    }

    public function test_evaluate_formula_or_table_simple_formula(): void
    {
        $result = $this->evaluator->evaluateFormulaOrTable('[level] * 2', ['level' => 10]);
        $this->assertSame(20.0, $result);
    }

    public function test_evaluate_formula_or_table_table_fixed_values(): void
    {
        $json = '{"characteristic":"level","1":0,"7":2,"14":4}';
        $this->assertSame(0.0, $this->evaluator->evaluateFormulaOrTable($json, ['level' => 5]));
        $this->assertSame(2.0, $this->evaluator->evaluateFormulaOrTable($json, ['level' => 10]));
        $this->assertSame(4.0, $this->evaluator->evaluateFormulaOrTable($json, ['level' => 18]));
    }

    public function test_evaluate_formula_or_table_table_with_formula_in_bracket(): void
    {
        $json = '{"characteristic":"level","1":0,"7":"[level]*2","14":4}';
        $this->assertSame(0.0, $this->evaluator->evaluateFormulaOrTable($json, ['level' => 5]));
        $this->assertSame(20.0, $this->evaluator->evaluateFormulaOrTable($json, ['level' => 10]));
        $this->assertSame(4.0, $this->evaluator->evaluateFormulaOrTable($json, ['level' => 18]));
    }

    public function test_evaluate_formula_or_table_value_below_smallest_from_uses_first_entry(): void
    {
        $json = '{"characteristic":"level","1":0,"7":2,"14":4}';
        $this->assertSame(0.0, $this->evaluator->evaluateFormulaOrTable($json, ['level' => 0]));
    }
}
