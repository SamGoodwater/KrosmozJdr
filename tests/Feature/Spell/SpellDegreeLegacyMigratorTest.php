<?php

declare(strict_types=1);

namespace Tests\Feature\Spell;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use App\Models\SubEffect;
use App\Services\Spell\SpellDegreeLegacyMigrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SpellDegreeLegacyMigratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrates_single_effect_with_degrees_and_is_idempotent(): void
    {
        $spell = Spell::factory()->create(['pa' => '4', 'write_level' => 0]);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper-mig-'.uniqid(),
            'type_slug' => 'frapper',
            'template_text' => 'Dégâts [value].',
            'variables_allowed' => ['value'],
            'param_schema' => [],
        ]);
        $effect = Effect::query()->create(['name' => 'Souffle', 'target_type' => 'direct']);
        $deg1 = EffectDegree::query()->create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'required_creature_level' => 1,
            'area' => 'point',
        ]);
        EffectSubEffect::query()->create([
            'effect_degree_id' => $deg1->id,
            'sub_effect_id' => $sub->id,
            'order' => 0,
            'params' => ['value_formula' => '2d6'],
        ]);
        $deg2 = EffectDegree::query()->create([
            'effect_id' => $effect->id,
            'degree' => 2,
            'required_creature_level' => 10,
            'area' => 'circle-1-1',
        ]);
        EffectSubEffect::query()->create([
            'effect_degree_id' => $deg2->id,
            'sub_effect_id' => $sub->id,
            'order' => 0,
            'params' => ['value_formula' => '3d6'],
        ]);
        $spell->effects()->attach($effect->id);

        $migrator = app(SpellDegreeLegacyMigrator::class);
        $first = $migrator->migrateSpell($spell->fresh());
        $this->assertTrue($first['migrated']);

        $degrees = SpellDegree::query()->where('spell_id', $spell->id)->orderBy('position')->get();
        $this->assertCount(2, $degrees);
        $this->assertSame('4', $degrees[0]->pa);
        $this->assertSame('point', $degrees[0]->area);
        $this->assertFalse($degrees[0]->inherits_effects);
        $this->assertCount(1, $degrees[0]->effects);
        $this->assertSame('circle-1-1', $degrees[1]->area);
        $this->assertFalse($degrees[1]->inherits_effects);

        $second = $migrator->migrateSpell($spell->fresh(['degrees']));
        $this->assertTrue($second['skipped']);
        $this->assertSame(2, SpellDegree::query()->where('spell_id', $spell->id)->count());
    }

    public function test_merges_multiple_effects_at_same_level(): void
    {
        $spell = Spell::factory()->create(['write_level' => 0]);
        $sub = SubEffect::query()->create([
            'slug' => 'soigner-mig-'.uniqid(),
            'type_slug' => 'soigner',
            'template_text' => 'Soin [value].',
            'variables_allowed' => ['value'],
            'param_schema' => [],
        ]);

        $e1 = Effect::query()->create(['name' => 'A', 'target_type' => 'direct']);
        $d1 = EffectDegree::query()->create([
            'effect_id' => $e1->id,
            'degree' => 1,
            'required_creature_level' => 1,
            'area' => 'point',
        ]);
        EffectSubEffect::query()->create([
            'effect_degree_id' => $d1->id,
            'sub_effect_id' => $sub->id,
            'order' => 0,
            'params' => ['value_formula' => '1d6'],
        ]);

        $e2 = Effect::query()->create(['name' => 'B', 'target_type' => 'direct']);
        $d2 = EffectDegree::query()->create([
            'effect_id' => $e2->id,
            'degree' => 1,
            'required_creature_level' => 1,
            'area' => 'line-1x3',
        ]);
        EffectSubEffect::query()->create([
            'effect_degree_id' => $d2->id,
            'sub_effect_id' => $sub->id,
            'order' => 0,
            'params' => ['value_formula' => '2d4'],
        ]);

        $spell->effects()->attach([$e1->id, $e2->id]);

        $report = app(SpellDegreeLegacyMigrator::class)->migrateSpell($spell->fresh());
        $this->assertTrue($report['migrated']);
        $this->assertNotEmpty($report['conflicts']);

        $degree = SpellDegree::query()->where('spell_id', $spell->id)->first();
        $this->assertNotNull($degree);
        $this->assertSame('point', $degree->area);
        $this->assertCount(2, $degree->effects);
    }
}
