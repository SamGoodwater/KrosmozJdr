<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Jdr;

use App\Services\Jdr\DiceFormulaService;
use App\Services\Jdr\DiceNotationService;
use App\Services\Jdr\LifeDiceCalculator;
use PHPUnit\Framework\TestCase;

/**
 * @see LifeDiceCalculator
 * @see DiceFormulaService
 */
class LifeDiceCalculatorTest extends TestCase
{
    private LifeDiceCalculator $calculator;

    private DiceFormulaService $dice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dice = new DiceFormulaService(new DiceNotationService);
        $this->calculator = new LifeDiceCalculator($this->dice);
    }

    public function test_fixed_formula_5_plus_2d6_over_2_with_d6_fixed_to_3(): void
    {
        $result = $this->dice->roll('5+2d6/2', [
            'fixed_by_sides' => [6 => 3],
        ]);
        $this->assertTrue($result->isValid);
        $this->assertSame(8, $result->result);
    }

    public function test_reroll_ones_never_keeps_a_one_when_sides_gt_1(): void
    {
        $sequence = [1, 1, 4, 2];
        $index = 0;
        $randomInt = static function (int $min, int $max) use (&$sequence, &$index): int {
            $value = $sequence[$index] ?? $max;
            $index++;

            return max($min, min($max, $value));
        };

        $result = $this->dice->roll('1d6', [
            'reroll_ones' => true,
            'random_int' => $randomInt,
        ]);
        $this->assertTrue($result->isValid);
        $this->assertSame(4, $result->result);
        $this->assertStringContainsString('4', $result->breakdown[0] ?? '');
    }

    public function test_class_level_one_d10_is_max(): void
    {
        $total = $this->calculator->totalForClass(10, 1, LifeDiceCalculator::METHOD_RANDOM);
        $this->assertTrue($total['ok']);
        $this->assertSame(10, $total['life_dice_total']);
    }

    public function test_class_level_eight_d8_stable_base_without_bonus_die(): void
    {
        $sequence = [2, 1, 2, 1, 2, 1, 2];
        $index = 0;
        $randomInt = static function (int $min, int $max) use (&$sequence, &$index): int {
            $value = $sequence[$index] ?? $max;
            $index++;

            return max($min, min($max, $value));
        };

        $total = $this->calculator->totalForClass(8, 8, LifeDiceCalculator::METHOD_STABLE, [
            'random_int' => $randomInt,
        ]);
        $this->assertTrue($total['ok']);
        // 8 + 7×6 + 7d2 with sequence 2,1,2,1,2,1,2 → +11 = 8+42+11 = 61
        $this->assertSame(61, $total['life_dice_total']);
    }

    public function test_preview_clamps_negative_vitality_modifier(): void
    {
        $this->assertSame(20, $this->calculator->previewLifePoints(20, 5, -3));
        $this->assertSame(30, $this->calculator->previewLifePoints(20, 5, 2));
    }

    public function test_formula_total_level_one_uses_max(): void
    {
        $total = $this->calculator->totalForFormula('5+2d6/2', 1, LifeDiceCalculator::METHOD_STABLE);
        $this->assertTrue($total['ok']);
        // max = 5 + 12/2 = 11
        $this->assertSame(11, $total['life_dice_total']);
    }

    public function test_default_fixed_by_sides_half_faces(): void
    {
        $this->assertSame([6 => 3], $this->dice->defaultFixedBySides('5+2d6/2'));
    }
}
