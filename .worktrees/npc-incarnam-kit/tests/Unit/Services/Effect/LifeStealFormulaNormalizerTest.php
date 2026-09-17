<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Effect;

use App\Services\Effect\LifeStealFormulaNormalizer;
use PHPUnit\Framework\TestCase;

final class LifeStealFormulaNormalizerTest extends TestCase
{
    public function test_empty_returns_null(): void
    {
        $this->assertNull(LifeStealFormulaNormalizer::normalize(null));
        $this->assertNull(LifeStealFormulaNormalizer::normalize(''));
        $this->assertNull(LifeStealFormulaNormalizer::normalize('   '));
    }

    public function test_percent_expands_to_dgt_fraction(): void
    {
        $this->assertSame(
            '[dgt]*(50/100)',
            LifeStealFormulaNormalizer::normalize('50%')
        );
        $this->assertSame(
            '[dgt]*(12.5/100)',
            LifeStealFormulaNormalizer::normalize('12.5 %')
        );
    }

    public function test_mixed_formula_replaces_each_percent_token(): void
    {
        $this->assertSame(
            '[dgt]/2+[dgt]*(25/100)',
            LifeStealFormulaNormalizer::normalize('[dgt]/2+25%')
        );
    }

    public function test_plain_formula_unchanged(): void
    {
        $this->assertSame(
            '[dgt]/2',
            LifeStealFormulaNormalizer::normalize('[dgt]/2')
        );
    }
}
