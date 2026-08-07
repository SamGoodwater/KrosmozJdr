<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic\Domain;

use App\Services\Characteristic\Domain\LevelDomainResolver;
use App\Services\Characteristic\Formula\FormulaExpressionParser;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Domaine de niveau (nombre, fourchette, dé) → liste de niveaux possibles.
 */
class LevelDomainResolverTest extends TestCase
{
    private LevelDomainResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new LevelDomainResolver(
            new FormulaExpressionParser(new SafeExpressionEvaluator)
        );
    }

    public function test_it_resolves_fixed_and_legacy_syntaxes(): void
    {
        $this->assertSame([7], $this->resolver->resolve('7'));
        $this->assertSame([5, 6, 7, 8], $this->resolver->resolve('{[5-8]}'));
        $this->assertSame([5, 6, 7, 8], $this->resolver->resolve('[5-8]'));
        $this->assertSame([1, 2, 3, 4], $this->resolver->resolve('1d4'));
        $this->assertSame([9, 10, 11, 12], $this->resolver->resolve('{8 + [1d4]}'));
    }

    public function test_default_and_variable_helpers(): void
    {
        $this->assertSame(9, $this->resolver->defaultLevel('{8 + [1d4]}'));
        $this->assertTrue($this->resolver->isVariable('1d4'));
        $this->assertFalse($this->resolver->isVariable('12'));
        $this->assertSame([1], $this->resolver->resolve(''));
    }
}
