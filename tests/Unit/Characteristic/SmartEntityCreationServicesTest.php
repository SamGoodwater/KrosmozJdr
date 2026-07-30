<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Norms\PowerCoefficientAssigner;
use App\Services\Characteristic\Pricing\EquipmentPriceCalculator;
use App\Services\Entity\Equipment\DuplicateEquipmentSignatureChecker;
use PHPUnit\Framework\TestCase;

final class SmartEntityCreationServicesTest extends TestCase
{
    public function test_power_assignment_is_seedable(): void
    {
        $assigner = new PowerCoefficientAssigner();

        $this->assertSame($assigner->assign('item:42'), $assigner->assign('item:42'));
        $this->assertContains($assigner->assign('item:42'), [0, 1, 2, 3, 4]);
    }

    public function test_price_uses_units_level_and_power(): void
    {
        $price = (new EquipmentPriceCalculator())->calculate(
            ['strength' => 2, 'initiative' => 3],
            ['strength' => 500, 'initiative' => 100],
            10,
            3
        );

        $this->assertGreaterThan(1300, $price);
    }

    public function test_equipment_signature_is_order_independent(): void
    {
        $checker = new DuplicateEquipmentSignatureChecker();

        $a = $checker->signature(['strength' => 2, 'initiative' => 1], 8, 10);
        $b = $checker->signature(['initiative' => 1, 'strength' => 2], 8, 10);

        $this->assertSame($a, $b);
    }
}
