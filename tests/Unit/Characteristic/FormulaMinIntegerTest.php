<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Formula\FormulaExpressionParser;
use App\Services\Characteristic\Formula\FormulaMinInteger;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

class FormulaMinIntegerTest extends TestCase
{
    private FormulaMinInteger $mins;

    protected function setUp(): void
    {
        $this->mins = new FormulaMinInteger(new FormulaExpressionParser(new SafeExpressionEvaluator));
    }

    public function test_plain_integers(): void
    {
        $this->assertSame(10, $this->mins->min('10'));
        $this->assertSame(0, $this->mins->min('0'));
        $this->assertNull($this->mins->min(''));
        $this->assertNull($this->mins->min(null));
    }

    public function test_formula_uses_the_lowest_entity_level(): void
    {
        $this->assertSame(1, $this->mins->min('{[niveau]}', 1));
        $this->assertSame(5, $this->mins->min('{[niveau] / 2}', 10));
    }

    public function test_level_domain_uses_the_first_outcome(): void
    {
        $this->assertSame(5, $this->mins->min('{[5-8]}'));
    }
}
