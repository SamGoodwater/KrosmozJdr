<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Creature\Runtime;

use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Creature\Runtime\CreatureAggregatedItemBonusLimiter;
use PHPUnit\Framework\TestCase;

final class CreatureAggregatedItemBonusLimiterTest extends TestCase
{
    public function test_it_caps_wakfu_recharge_at_three(): void
    {
        $getter = $this->createMock(CharacteristicGetterService::class);
        $getter->method('getLimits')->willReturn(null);
        $limiter = new CreatureAggregatedItemBonusLimiter(new CharacteristicLimitService($getter));

        $out = $limiter->apply(['wakfu_recharge' => 8, 'strength' => 2]);

        $this->assertSame(3, $out['wakfu_recharge']);
        $this->assertSame(2, $out['strength']);
    }
}
