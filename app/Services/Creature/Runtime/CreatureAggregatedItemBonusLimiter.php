<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Services\Characteristic\Limit\CharacteristicLimitService;

/**
 * Plafonds runtime sur les bonus objets agrégés avant mapping créature.
 *
 * @example
 *   $limited = $limiter->apply(['wakfu_recharge' => 5, 'strength' => 2]);
 */
final class CreatureAggregatedItemBonusLimiter
{
    private const WAKFU_RECHARGE_OBJECT_CAP = 3;

    public function __construct(
        private readonly CharacteristicLimitService $limitService,
    ) {}

    /**
     * @param  array<string, int>  $itemTotals
     * @return array<string, int>
     */
    public function apply(array $itemTotals): array
    {
        $out = [];
        foreach ($itemTotals as $shortKey => $amount) {
            $key = (string) $shortKey;
            $intAmount = (int) $amount;
            if ($key === 'wakfu_recharge') {
                $intAmount = max(0, min(self::WAKFU_RECHARGE_OBJECT_CAP, $intAmount));
            }
            $objectKey = str_ends_with($key, '_object') ? $key : "{$key}_object";
            $out[$key] = $this->limitService->clamp($objectKey, $intAmount, 'item');
        }

        return $out;
    }
}
