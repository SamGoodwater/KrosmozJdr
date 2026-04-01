<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Creature\Runtime;

use App\Services\Creature\Runtime\CreatureItemBonusAggregator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test pur (sans base Laravel) : agrégation JSON bonus objets.
 */
final class CreatureItemBonusAggregatorTest extends TestCase
{
    #[Test]
    public function it_sums_bonuses_with_quantities(): void
    {
        $aggregator = new CreatureItemBonusAggregator;

        $a = (object) [
            'id' => 1,
            'name' => 'A',
            'bonus' => '{"strength": 2, "athletics": 1}',
            'pivot' => (object) ['quantity' => 2],
        ];

        $b = (object) [
            'id' => 2,
            'name' => 'B',
            'bonus' => '{"athletics": 3}',
            'pivot' => (object) ['quantity' => 1],
        ];

        $totals = $aggregator->aggregateTotals(new Collection([$a, $b]));

        $this->assertSame(4, $totals['strength']);
        $this->assertSame(5, $totals['athletics']);
    }

    #[Test]
    public function it_returns_empty_for_invalid_json(): void
    {
        $aggregator = new CreatureItemBonusAggregator;
        $item = (object) [
            'id' => 1,
            'name' => 'X',
            'bonus' => 'not-json',
            'pivot' => (object) ['quantity' => 1],
        ];

        $this->assertSame([], $aggregator->aggregateTotals(new Collection([$item])));
    }
}
