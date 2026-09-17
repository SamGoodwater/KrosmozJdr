<?php

declare(strict_types=1);

namespace Tests\Feature\Effects;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Support\ElementBitmask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class SpellsSyncElementsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_sets_air_from_effect_characteristic(): void
    {
        $spell = Spell::factory()->create([
            'name' => 'Bec',
            'element' => 3,
        ]);
        $effect = Effect::query()->create([
            'name' => 'Bec',
            'slug' => 'bec-sync-test',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $degree = EffectDegree::query()->create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'slug' => 'bec-sync-test-1',
        ]);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper-sync-test',
            'type_slug' => 'frapper',
            'template_text' => 'Frappe.',
            'variables_allowed' => [],
            'param_schema' => [],
        ]);
        EffectSubEffect::query()->create([
            'effect_degree_id' => $degree->id,
            'sub_effect_id' => $sub->id,
            'order' => 0,
            'scope' => Effect::SCOPE_GENERAL,
            'params' => ['characteristic' => 'air', 'value' => '1d4'],
            'crit_only' => false,
        ]);
        $spell->effects()->sync([$effect->id]);

        $code = Artisan::call('spells:sync-elements');

        $this->assertSame(0, $code);
        $this->assertSame(ElementBitmask::fromSlug('air'), (int) $spell->fresh()->element);
    }
}
