<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Spell\SpellIntensificationCran;
use PHPUnit\Framework\TestCase;

final class SpellIntensificationCranTest extends TestCase
{
    public function test_default_cran_bumps_attack_dice_then_stops_at_five(): void
    {
        $cran = new SpellIntensificationCran;
        $tiers = $cran->defaultTiers([
            'effect' => '4d6 + Agilité (Air) en petite zone. Magique, 5 PA, 1×/tour.',
            'sub_effects' => [
                [
                    'slug' => 'frapper',
                    'params' => [
                        'value_formula' => '4d6+[agi]',
                        'value_formula_crit' => '5d6+[agi]',
                    ],
                ],
            ],
        ]);

        $this->assertSame('5d6 + Agilité (Air) en petite zone. Magique, 5 PA, 1×/tour.', $tiers[0]['effect']);
        $this->assertSame('5d6+[agi]', $tiers[0]['sub_effects'][0]['params']['value_formula']);
        $this->assertSame('—', $tiers[1]['effect']);
        $this->assertSame([], $tiers[1]['sub_effects']);
        $this->assertSame('—', $tiers[2]['effect']);
    }

    public function test_utility_without_dice_gains_one_cell(): void
    {
        $cran = new SpellIntensificationCran;
        $tiers = $cran->defaultTiers([
            'effect' => 'Attirance de 2 cases, pas de dégâts. Physique, 3 PA, 1×/tour.',
            'sub_effects' => [
                [
                    'slug' => 'déplacer',
                    'params' => [
                        'cells_formula' => '2',
                        'movement_kind' => 'pull',
                    ],
                ],
            ],
        ]);

        $this->assertSame('Attirance de 3 cases, pas de dégâts. Physique, 3 PA, 1×/tour.', $tiers[0]['effect']);
        $this->assertSame('3', $tiers[0]['sub_effects'][0]['params']['cells_formula']);
        $this->assertSame('Attirance de 5 cases, pas de dégâts. Physique, 3 PA, 1×/tour.', $tiers[2]['effect']);
    }

    public function test_effect_with_tiers_keeps_dash_lines(): void
    {
        $cran = new SpellIntensificationCran;
        $text = $cran->effectWithTiers([
            'effect' => '+2 aux jets d’attaque jusqu’à la fin de ton prochain tour.',
            'sub_effects' => [
                ['slug' => 'booster', 'params' => ['value' => '2']],
            ],
        ]);

        $this->assertStringContainsString('+2 aux jets d’attaque', $text);
        $this->assertStringContainsString('Palier I (perso 13) : —', $text);
        $this->assertStringContainsString('Palier III (perso 20) : —', $text);
    }
}
