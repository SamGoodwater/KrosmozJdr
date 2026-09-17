<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\KamasAmount;
use PHPUnit\Framework\TestCase;

final class KamasAmountTest extends TestCase
{
    public function test_parse_strips_label_and_spaces(): void
    {
        $this->assertSame(1200, KamasAmount::parse('1 200 kamas'));
        $this->assertSame(50, KamasAmount::parse('50'));
        $this->assertSame(0, KamasAmount::parse(null));
        $this->assertSame(0, KamasAmount::parse('abc'));
    }
}
