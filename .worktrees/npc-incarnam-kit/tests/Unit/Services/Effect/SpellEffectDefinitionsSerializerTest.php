<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Effect;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\Entity\Condition;
use App\Models\SubEffect;
use App\Services\Effect\SpellEffectDefinitionsSerializer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpellEffectDefinitionsSerializerTest extends TestCase
{
    use RefreshDatabase;

    public function test_serializer_resolves_condition_from_dofusdb_id_when_local_id_is_missing(): void
    {
        $state = Condition::create([
            'dofusdb_id' => 123456,
            'name' => 'Pesanteur',
            'icon' => 'icons/states/pesanteur.webp',
        ]);
        $effect = Effect::create([
            'name' => 'Appliquer état',
            'slug' => 'appliquer-etat-test',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $degree = EffectDegree::create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'slug' => 'appliquer-etat-test-1',
        ]);
        $subEffect = SubEffect::create([
            'slug' => 'appliquer-etat',
            'type_slug' => 'appliquer-etat',
            'template_text' => 'Applique [condition_name].',
            'variables_allowed' => [],
            'param_schema' => [],
        ]);
        EffectSubEffect::create([
            'effect_degree_id' => $degree->id,
            'sub_effect_id' => $subEffect->id,
            'order' => 0,
            'scope' => Effect::SCOPE_GENERAL,
            'params' => [
                'condition_dofusdb_id' => 123456,
                'condition_name' => 'Ancien nom',
            ],
            'crit_only' => false,
        ]);

        $serialized = (new SpellEffectDefinitionsSerializer)->serialize(collect([$effect]));

        $row = $serialized[0]['degrees'][0]['rows'][0];
        $this->assertSame($state->id, $row['condition']['id']);
        $this->assertSame(123456, $row['condition']['dofusdb_id']);
        $this->assertSame('Pesanteur', $row['condition']['name']);
        $this->assertSame('icons/states/pesanteur.webp', $row['condition']['icon']);
    }

    public function test_serializer_follows_canonical_condition_from_raw_id(): void
    {
        $playable = Condition::query()->create([
            'name' => 'Pesanteur',
            'state' => Condition::STATE_PLAYABLE,
            'icon' => 'icons/states/pesanteur.webp',
            'read_level' => 0,
            'write_level' => 4,
        ]);
        $raw = Condition::query()->create([
            'name' => 'Lourd',
            'state' => Condition::STATE_RAW,
            'dofusdb_id' => 68,
            'canonical_condition_id' => $playable->id,
            'read_level' => 0,
            'write_level' => 4,
        ]);
        $effect = Effect::create([
            'name' => 'Appliquer état canon',
            'slug' => 'appliquer-etat-canon-test',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $degree = EffectDegree::create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'slug' => 'appliquer-etat-canon-test-1',
        ]);
        $subEffect = SubEffect::create([
            'slug' => 'appliquer-etat-canon',
            'type_slug' => 'appliquer-etat',
            'template_text' => 'Applique [condition_name].',
            'variables_allowed' => [],
            'param_schema' => [],
        ]);
        EffectSubEffect::create([
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

        $serialized = (new SpellEffectDefinitionsSerializer)->serialize(collect([$effect]));

        $row = $serialized[0]['degrees'][0]['rows'][0];
        $this->assertSame($playable->id, $row['condition']['id']);
        $this->assertSame('Pesanteur', $row['condition']['name']);
        $this->assertSame('icons/states/pesanteur.webp', $row['condition']['icon']);
    }

    public function test_serializer_hides_raw_condition_without_canonical(): void
    {
        Condition::query()->create([
            'name' => 'Barrière',
            'state' => Condition::STATE_RAW,
            'dofusdb_id' => 717,
            'read_level' => 0,
            'write_level' => 4,
        ]);
        $effect = Effect::create([
            'name' => 'Jeton brut',
            'slug' => 'jeton-brut-test',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $degree = EffectDegree::create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'slug' => 'jeton-brut-test-1',
        ]);
        $subEffect = SubEffect::create([
            'slug' => 'appliquer-etat-jeton',
            'type_slug' => 'appliquer-etat',
            'template_text' => 'Applique [condition_name].',
            'variables_allowed' => [],
            'param_schema' => [],
        ]);
        EffectSubEffect::create([
            'effect_degree_id' => $degree->id,
            'sub_effect_id' => $subEffect->id,
            'order' => 0,
            'scope' => Effect::SCOPE_GENERAL,
            'params' => [
                'condition_dofusdb_id' => 717,
                'condition_name' => 'Barrière',
            ],
            'crit_only' => false,
        ]);

        $serialized = (new SpellEffectDefinitionsSerializer)->serialize(collect([$effect]));

        $row = $serialized[0]['degrees'][0]['rows'][0];
        $this->assertNull($row['condition']);
    }
}
