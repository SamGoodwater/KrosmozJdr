<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Condition;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\Entity\Condition;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Services\Condition\ConditionCanonicalMapper;
use App\Services\Condition\ConditionSpellCanonicalRemapper;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Recollement des liaisons sort / params vers les états JDR de base.
 */
final class ConditionSpellCanonicalRemapperTest extends TestCase
{
    public function test_remap_moves_spell_link_and_params_to_playable(): void
    {
        $playable = Condition::query()->create([
            'name' => 'Pesanteur',
            'state' => Condition::STATE_PLAYABLE,
            'read_level' => 0,
            'write_level' => 4,
        ]);
        $raw = Condition::query()->create([
            'name' => 'Lourd',
            'state' => Condition::STATE_RAW,
            'dofusdb_id' => 68,
            'read_level' => 0,
            'write_level' => 4,
            'cant_switch_position' => true,
        ]);
        $unmapped = Condition::query()->create([
            'name' => 'Invisible',
            'state' => Condition::STATE_RAW,
            'dofusdb_id' => 250,
            'read_level' => 0,
            'write_level' => 4,
            'invulnerable' => true,
        ]);

        $spell = Spell::factory()->create();
        $now = now();
        DB::table('condition_spell')->insert([
            [
                'spell_id' => $spell->id,
                'condition_id' => $raw->id,
                'application_mode' => 'target',
                'dofus_effect_id' => 950,
                'duration' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'spell_id' => $spell->id,
                'condition_id' => $unmapped->id,
                'application_mode' => 'target',
                'dofus_effect_id' => 950,
                'duration' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $effect = Effect::query()->create([
            'name' => 'Remap état',
            'slug' => 'remap-etat-test',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $degree = EffectDegree::query()->create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'slug' => 'remap-etat-test-1',
        ]);
        $subEffect = SubEffect::query()->create([
            'slug' => 'appliquer-etat-remap',
            'type_slug' => 'appliquer-etat',
            'template_text' => 'Applique [condition_name].',
            'variables_allowed' => [],
            'param_schema' => [],
        ]);
        $mappedPivot = EffectSubEffect::query()->create([
            'effect_degree_id' => $degree->id,
            'sub_effect_id' => $subEffect->id,
            'order' => 0,
            'scope' => Effect::SCOPE_GENERAL,
            'params' => [
                'condition_id' => $raw->id,
                'condition_dofusdb_id' => 68,
                'condition_name' => 'Lourd',
            ],
            'crit_only' => false,
        ]);
        $unmappedPivot = EffectSubEffect::query()->create([
            'effect_degree_id' => $degree->id,
            'sub_effect_id' => $subEffect->id,
            'order' => 1,
            'scope' => Effect::SCOPE_GENERAL,
            'params' => [
                'condition_id' => $unmapped->id,
                'condition_dofusdb_id' => 250,
                'condition_name' => 'Invisible',
            ],
            'crit_only' => false,
        ]);

        $stats = (new ConditionSpellCanonicalRemapper(new ConditionCanonicalMapper))->remapAll();

        $this->assertSame(1, $stats['aliases']);
        $this->assertSame(1, $stats['spell_links']);
        $this->assertSame(1, $stats['unlinked']);
        $this->assertSame(2, $stats['effect_params']);

        $raw->refresh();
        $this->assertSame($playable->id, $raw->canonical_condition_id);
        $unmapped->refresh();
        $this->assertNull($unmapped->canonical_condition_id);

        $this->assertDatabaseHas('condition_spell', [
            'spell_id' => $spell->id,
            'condition_id' => $playable->id,
            'application_mode' => 'target',
            'dofus_effect_id' => 950,
        ]);
        $this->assertDatabaseMissing('condition_spell', [
            'spell_id' => $spell->id,
            'condition_id' => $raw->id,
        ]);
        $this->assertDatabaseMissing('condition_spell', [
            'spell_id' => $spell->id,
            'condition_id' => $unmapped->id,
        ]);

        $mappedPivot->refresh();
        $this->assertSame($playable->id, $mappedPivot->params['condition_id'] ?? null);
        $this->assertSame('Pesanteur', $mappedPivot->params['condition_name'] ?? null);
        $this->assertSame(68, $mappedPivot->params['condition_dofusdb_id'] ?? null);

        $unmappedPivot->refresh();
        $this->assertArrayNotHasKey('condition_id', $unmappedPivot->params ?? []);
        $this->assertSame(250, $unmappedPivot->params['condition_dofusdb_id'] ?? null);
        $this->assertSame('Invisible', $unmappedPivot->params['condition_name'] ?? null);
    }
}
