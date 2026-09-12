<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Norms\PowerCoefficientAssigner;
use App\Services\Characteristic\Pricing\ConsumablePriceCalculator;
use App\Services\Characteristic\Pricing\EquipmentPriceCalculator;
use App\Services\Entity\Equipment\DuplicateEquipmentSignatureChecker;
use PHPUnit\Framework\TestCase;

final class SmartEntityCreationServicesTest extends TestCase
{
    public function test_power_assignment_is_seedable(): void
    {
        $assigner = new PowerCoefficientAssigner;

        $this->assertSame($assigner->assign('item:42'), $assigner->assign('item:42'));
        $this->assertContains($assigner->assign('item:42'), [0, 1, 2, 3, 4]);
    }

    public function test_equipment_price_uses_bonus_level_and_rarity(): void
    {
        $price = (new EquipmentPriceCalculator)->calculate(
            ['strength' => 2, 'initiative' => 3],
            ['strength' => 500, 'initiative' => 100],
            10,
            2
        );

        $this->assertSame(2 * 500 + 3 * 100 + 150 * 10 + 200 * 2, $price);
    }

    public function test_equipment_price_floors_at_zero_with_malus(): void
    {
        $price = (new EquipmentPriceCalculator)->calculate(
            ['strength' => -10],
            ['strength' => 1000],
            0,
            0
        );

        $this->assertSame(0, $price);
    }

    public function test_equipment_signature_is_order_independent(): void
    {
        $checker = new DuplicateEquipmentSignatureChecker;

        $a = $checker->signature(['strength' => 2, 'initiative' => 1], 8, 10);
        $b = $checker->signature(['initiative' => 1, 'strength' => 2], 8, 10);

        $this->assertSame($a, $b);
    }

    public function test_consumable_price_sums_resource_prices_times_quantity(): void
    {
        $resources = [
            (object) ['price' => '100', 'pivot' => (object) ['quantity' => 2]],
            (object) ['price' => '50 kamas', 'pivot' => (object) ['quantity' => 1]],
        ];

        $price = (new ConsumablePriceCalculator)->calculateFromResources($resources);

        $this->assertSame(250, $price);
    }
}
