<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Entity;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Accessor area : utilise la relation déjà eager-loadée (pas de N+1).
 */
final class SpellAreaAttributeTest extends TestCase
{
    use RefreshDatabase;

    public function test_area_uses_eager_loaded_effects_without_extra_query(): void
    {
        $spell = Spell::factory()->create([
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => 0,
        ]);
        $effect = Effect::create([
            'name' => 'Zone test',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $spell->effects()->attach($effect->id);
        EffectDegree::create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'area' => 'circle-2',
        ]);

        $loaded = Spell::query()->with('effects.degrees')->findOrFail($spell->id);

        DB::enableQueryLog();
        DB::flushQueryLog();
        $area = $loaded->area;
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertSame('circle-2', $area);
        $this->assertSame([], $queries);
    }
}
