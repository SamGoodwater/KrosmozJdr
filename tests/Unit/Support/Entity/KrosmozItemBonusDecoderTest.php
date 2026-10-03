<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Entity;

use App\Support\Entity\KrosmozItemBonusDecoder;
use PHPUnit\Framework\TestCase;

final class KrosmozItemBonusDecoderTest extends TestCase
{
    private KrosmozItemBonusDecoder $decoder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoder = new KrosmozItemBonusDecoder;
    }

    public function test_prefers_effect_over_bonus(): void
    {
        $map = $this->decoder->decode('{"strength":2}', '{"strength":9}');

        $this->assertSame(2.0, $map['strength']);
    }

    public function test_falls_back_to_bonus_when_effect_empty(): void
    {
        $map = $this->decoder->decode(null, '{"wakfu_recharge":1}');

        $this->assertSame(1.0, $map['wakfu_recharge']);
    }
}
