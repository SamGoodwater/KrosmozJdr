<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\CostEstimator;
use PHPUnit\Framework\TestCase;

final class CostEstimatorTest extends TestCase
{
    public function test_npc_is_an_order_of_magnitude_above_item_update(): void
    {
        $estimator = new CostEstimator;
        $npc = $estimator->forAction('npc');
        $encounter = $estimator->forAction('encounter');
        $spell = $estimator->forAction('spell');
        $item = $estimator->forAction('item');

        $this->assertNotNull($npc);
        $this->assertNotNull($encounter);
        $this->assertNotNull($spell);
        $this->assertNotNull($item);
        $this->assertGreaterThan($encounter['usd'], $npc['usd']);
        $this->assertGreaterThan($spell['usd'], $encounter['usd']);
        $this->assertGreaterThan($item['usd'], $spell['usd']);
        $this->assertSame('encounter', $estimator->actionForEntityType('monsters'));
        $this->assertStringContainsString('$', $npc['formatted']);
        $this->assertSame('≈ 10 rencontres ou 2 PNJ', $estimator->remainingHint(1.0));
        $this->assertNull($estimator->remainingHint(null));
    }
}
