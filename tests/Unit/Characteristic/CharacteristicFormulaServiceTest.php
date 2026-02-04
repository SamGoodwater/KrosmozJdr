<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use Tests\TestCase;

/**
 * Tests unitaires pour CharacteristicFormulaService (wrapper FormulaResolutionService).
 *
 * @see App\Services\Characteristic\Formula\CharacteristicFormulaService
 */
class CharacteristicFormulaServiceTest extends TestCase
{
    private CharacteristicFormulaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CharacteristicFormulaService(
            new FormulaResolutionService(new SafeExpressionEvaluator())
        );
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

    public function test_evaluate_table_json(): void
    {
        $table = '{"characteristic":"level","1":0,"7":2,"14":4}';
        $this->assertSame(0.0, $this->service->evaluate($table, ['level' => 1]));
        $this->assertSame(2.0, $this->service->evaluate($table, ['level' => 7]));
    }

    public function test_validate_formula_returns_empty_for_valid(): void
    {
        $errors = $this->service->validateFormula('[level] * 2');
        $this->assertSame([], $errors);
    }

    public function test_validate_formula_returns_errors_for_invalid(): void
    {
        $errors = $this->service->validateFormula('[level] * 2 + shell_exec("id")');
        $this->assertNotEmpty($errors);
    }

    public function test_evaluate_for_variable_range(): void
    {
        $result = $this->service->evaluateForVariableRange('[level] * 2', 'level', 1, 3, []);
        $this->assertSame([1 => 2.0, 2 => 4.0, 3 => 6.0], $result);
    }
}
