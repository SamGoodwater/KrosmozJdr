<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Consumable\ConsumableBonusFromEffectBackfiller;
use App\Services\Seeder\Consumable\HealingConsumableCatalog;
use Tests\TestCase;

final class ConsumableBonusFromEffectBackfillerTest extends TestCase
{
    public function test_infer_bonus_from_common_jdr_effect_texts(): void
    {
        $svc = new ConsumableBonusFromEffectBackfiller;

        $this->assertSame(
            HealingConsumableCatalog::bonusJson(7),
            $svc->inferBonus('Restaure 7 PV. Hors combat uniquement.')
        );
        $this->assertSame(
            '{"shield_points":5}',
            $svc->inferBonus('+5 points de bouclier jusqu’au prochain repos long (8 h max) ou jusqu’à absorption. Usage unique.')
        );
        $this->assertSame(
            '{"temporary_life_points":10}',
            $svc->inferBonus('+10 PV temporaires jusqu’au prochain repos long (8 h max) ou jusqu’à absorption. Usage unique.')
        );
        $this->assertSame(
            '{"deception":1}',
            $svc->inferBonus('+1 Supercherie jusqu’au prochain repos long (8 h max). Hors combat pour boire. Usage unique.')
        );
        $this->assertNull($svc->inferBonus('Téléporte vers un zaap.'));
    }
}
