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

    public function test_describe_in_french(): void
    {
        $this->assertSame('', AreaNotation::describeInFrench(null));
        $this->assertSame('', AreaNotation::describeInFrench(''));
        $this->assertSame('cible unique', AreaNotation::describeInFrench('point'));
        $this->assertSame('ligne de 1 case', AreaNotation::describeInFrench('line-1x1'));
        $this->assertSame('ligne de 9 cases', AreaNotation::describeInFrench('line-1x9'));
        $this->assertSame('rectangle de 3 par 4 cases', AreaNotation::describeInFrench('rect-3x4'));
        $this->assertSame('carré de 2 × 2 cases', AreaNotation::describeInFrench('rect-2x2'));
        $this->assertSame('croix de 0 à 2 cases', AreaNotation::describeInFrench('cross-0-2'));
        $this->assertSame('croix de 1 à 2 cases', AreaNotation::describeInFrench('cross-1-2'));
        $this->assertSame("disque : jusqu'à 2 cases du centre", AreaNotation::describeInFrench('circle-0-2'));
        $this->assertSame('anneau circulaire : de 1 à 2 cases du centre', AreaNotation::describeInFrench('circle-1-2'));
        $this->assertSame('cercle (contour) à 2 cases du centre', AreaNotation::describeInFrench('circle-2-2'));
        $this->assertSame('zone spéciale (forme 99)', AreaNotation::describeInFrench('shape-99'));
        $this->assertSame('zone brute-inconnue', AreaNotation::describeInFrench('brute-inconnue'));
    }
}
