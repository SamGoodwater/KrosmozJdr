<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Creature\Runtime;

use App\Contracts\Characteristic\CharacteristicDefinitionLookup;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Creature\Runtime\CreatureAggregatedItemBonusLimiter;
use PHPUnit\Framework\TestCase;

final class CreatureAggregatedItemBonusLimiterTest extends TestCase
{
    public function test_it_caps_wakfu_recharge_at_three(): void
    {
        $lookup = new class implements CharacteristicDefinitionLookup
        {
            public function getDefinition(string $key, string $entity): ?array
            {
                return null;
            }

            public function getLimits(string $characteristicKey, string $entity, array $variables = []): ?array
            {
                return null;
            }
        };
        $limiter = new CreatureAggregatedItemBonusLimiter(new CharacteristicLimitService($lookup));

        $out = $limiter->apply(['wakfu_recharge' => 8, 'strength' => 2]);

        $this->assertSame(3, $out['wakfu_recharge']);
        $this->assertSame(2, $out['strength']);
    }
}
