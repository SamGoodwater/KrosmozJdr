<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\NpcStatGabarit;
use App\Support\Npc\NpcRole;
use PHPUnit\Framework\TestCase;

final class NpcStatGabaritTest extends TestCase
{
    public function test_social_level_four_stays_in_low_band(): void
    {
        $stats = (new NpcStatGabarit)->forLevelAndRole(4, NpcRole::SOCIAL);

        $life = (int) $stats['life'];
        $this->assertSame('1-5', $stats['band']);
        $this->assertGreaterThanOrEqual(20, $life);
        $this->assertLessThanOrEqual(50, $life);
        $this->assertLessThan(40, $life);
        $this->assertSame('4', $stats['pa']);
        $this->assertSame('1d6', $stats['damage_dice']);
        $this->assertSame('12', $stats['chance']);
    }

    public function test_guard_is_stronger_than_merchant(): void
    {
        $gabarit = new NpcStatGabarit;
        $guard = $gabarit->forLevelAndRole(5, NpcRole::GUARD);
        $merchant = $gabarit->forLevelAndRole(5, NpcRole::MERCHANT);

        $this->assertGreaterThan((int) $merchant['life'], (int) $guard['life']);
        $this->assertSame('6', $guard['pa']);
        $this->assertSame('14', $guard['strong']);
        $this->assertSame('2d6', $guard['damage_dice']);
    }

    public function test_validate_stats_rejects_life_outside_band(): void
    {
        $errors = (new NpcStatGabarit)->validateStats(
            ['life' => '999', 'pa' => '6'],
            4,
            NpcRole::GUARD
        );

        $this->assertNotSame([], $errors);
        $this->assertStringContainsString('PV hors gabarit', $errors[0]);
    }

    public function test_validate_stats_accepts_band_and_pa(): void
    {
        $errors = (new NpcStatGabarit)->validateStats(
            ['life' => '22', 'pa' => '6'],
            4,
            NpcRole::GUARD
        );

        $this->assertSame([], $errors);
    }
}
