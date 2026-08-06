<?php

declare(strict_types=1);

namespace Tests\Unit\Effect;

use App\Services\Effect\SpellActionBudgetService;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie la répartition des budgets proportionnels aux PV.
 */
final class SpellActionBudgetServiceTest extends TestCase
{
    private SpellActionBudgetService $service;

    protected function setUp(): void
    {
        $this->service = new SpellActionBudgetService;
    }

    public function test_damage_budget_is_distributed_by_action_point_share(): void
    {
        $this->assertSame(2, $this->service->turnBudget(SpellActionBudgetService::ACTION_DAMAGE, 1));
        $this->assertSame(83, $this->service->turnBudget(SpellActionBudgetService::ACTION_DAMAGE, 20));
        $this->assertSame(1, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 1, 3));
        $this->assertSame(21, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 20, 3));
    }

    public function test_heal_and_life_steal_stay_below_pure_damage(): void
    {
        $damage = $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 20, 4);
        $heal = $this->service->budgetForCast(SpellActionBudgetService::ACTION_HEAL, 20, 4);
        $shield = $this->service->budgetForCast(SpellActionBudgetService::ACTION_SHIELD, 20, 4);
        $tempHp = $this->service->budgetForCast(SpellActionBudgetService::ACTION_TEMP_HP, 20, 4);
        $lifeSteal = $this->service->budgetForCast(SpellActionBudgetService::ACTION_LIFE_STEAL, 20, 4);

        $this->assertSame(28, $damage);
        $this->assertSame(13, $heal);
        $this->assertSame(13, $shield);
        $this->assertSame(13, $tempHp);
        $this->assertSame(14, $lifeSteal);
        $this->assertLessThan($damage, $heal);
        $this->assertSame($heal, $shield);
        $this->assertSame($heal, $tempHp);
        $this->assertLessThan($damage, $lifeSteal);
    }

    public function test_distribution_preserves_budget_and_relative_weight(): void
    {
        $distributed = $this->service->distribute(20, [3, 1]);

        $this->assertSame(20, array_sum($distributed));
        $this->assertSame([15, 5], $distributed);
    }
}
