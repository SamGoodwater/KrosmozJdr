<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\AreaNotation;
use PHPUnit\Framework\TestCase;

final class AreaNotationTest extends TestCase
{
    public function test_empty_is_valid(): void
    {
        $this->assertTrue(AreaNotation::isValid(null));
        $this->assertTrue(AreaNotation::isValid(''));
        $this->assertTrue(AreaNotation::isValid('   '));
    }

    public function test_documented_shapes_are_valid(): void
    {
        $this->assertTrue(AreaNotation::isValid('point'));
        $this->assertTrue(AreaNotation::isValid('line-1x1'));
        $this->assertTrue(AreaNotation::isValid('cross-0-2'));
        $this->assertTrue(AreaNotation::isValid('circle-2-2'));
        $this->assertTrue(AreaNotation::isValid('rect-3x4'));
        $this->assertTrue(AreaNotation::isValid('shape-99'));
        $this->assertTrue(AreaNotation::isValid('shape-12-0-5'));
    }

    public function test_invalid_notations(): void
    {
        $this->assertFalse(AreaNotation::isValid('line-2x3'));
        $this->assertFalse(AreaNotation::isValid('cross-2-1'));
        $this->assertFalse(AreaNotation::isValid('shape-0'));
        $this->assertFalse(AreaNotation::isValid('shape-1-2'));
    }
}
