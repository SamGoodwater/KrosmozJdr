<?php

namespace Tests\Unit\Creature;

use App\Models\Entity\Creature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Défauts applicatifs des résistances fixes (MySQL refuse DEFAULT sur TEXT).
 */
class CreatureFixedResistanceDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixed_resistances_default_to_zero_without_sql_text_default(): void
    {
        $creature = Creature::query()->create([
            'name' => 'Res Fixe Default',
            'created_by' => User::factory()->create()->id,
        ]);

        $fresh = $creature->fresh();
        $this->assertSame('0', $fresh->res_fixe_neutre);
        $this->assertSame('0', $fresh->res_fixe_terre);
        $this->assertSame('0', $fresh->res_fixe_feu);
        $this->assertSame('0', $fresh->res_fixe_air);
        $this->assertSame('0', $fresh->res_fixe_eau);
    }
}
