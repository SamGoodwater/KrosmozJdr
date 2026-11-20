<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Http\Middleware\PreventRequestForgery;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\Entity\Creature;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase; // pragma: allowlist secret
use Tests\TestCase;

/**
 * Enregistrement d’un groupe d’effets : conditions / trigger_turn / creature_id
 * ne doivent pas disparaître de validated().
 */
final class EffectGroupUpdateParamsTest extends TestCase
{
    use RefreshDatabase; // pragma: allowlist secret

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_admin_group_update_persists_conditions_trigger_turn_and_creature_id(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        [$effect, $degree, $sub, $creature] = $this->makeEffectGroup();

        $this->actingAsConfirmed($admin)
            ->patchJson(route('admin.effects.group-update', $effect), $this->groupPayload($degree, $sub, $creature))
            ->assertOk();

        $this->assertPersistedParams($degree, $sub, $creature);
    }

    public function test_spell_effect_group_update_persists_the_same_params(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['created_by' => $gm->id, 'write_level' => 0]);
        [$effect, $degree, $sub, $creature] = $this->makeEffectGroup();
        $spell->effects()->syncWithoutDetaching([$effect->id]);

        $this->actingAs($gm)
            ->patchJson(route('entities.spells.updateEffectGroup', [$spell, $effect]), $this->groupPayload($degree, $sub, $creature))
            ->assertOk();

        $this->assertPersistedParams($degree, $sub, $creature);
    }

    /**
     * @return array{0: Effect, 1: EffectDegree, 2: SubEffect, 3: Creature}
     */
    private function makeEffectGroup(): array
    {
        $effect = Effect::query()->create([
            'name' => 'Groupe params',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $degree = EffectDegree::query()->create([
            'effect_id' => $effect->id,
            'degree' => 1,
        ]);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper-group-params-'.uniqid(),
            'type_slug' => 'frapper',
            'template_text' => 'Dégâts [value].',
            'variables_allowed' => ['value'],
            'param_schema' => [],
        ]);
        $creature = Creature::factory()->create();

        return [$effect, $degree, $sub, $creature];
    }

    /**
     * @return array<string, mixed>
     */
    private function groupPayload(EffectDegree $degree, SubEffect $sub, Creature $creature): array
    {
        return [
            'common' => [
                'name' => 'Groupe params',
                'description' => null,
                'target_type' => Effect::TARGET_DIRECT,
            ],
            'degrees' => [[
                'id' => $degree->id,
                'slug' => null,
                'area' => 'point',
                'required_creature_level' => null,
                'effect_sub_effects' => [[
                    'sub_effect_id' => $sub->id,
                    'order' => 0,
                    'scope' => 'general',
                    'params' => [
                        'value_formula' => '2d6+[intel]',
                        'characteristic' => 'fire',
                        'creature_id' => $creature->id,
                        'applies_to' => 'caster',
                        'trigger_turn' => 1,
                        'conditions' => [
                            ['left' => '[agi]', 'op' => '>=', 'right' => '3'],
                        ],
                        'value_rules' => [
                            ['action' => 'add', 'formula' => '1', 'conditions' => []],
                        ],
                    ],
                ]],
            ]],
        ];
    }

    private function assertPersistedParams(EffectDegree $degree, SubEffect $sub, Creature $creature): void
    {
        $pivot = EffectSubEffect::query()
            ->where('effect_degree_id', $degree->id)
            ->where('sub_effect_id', $sub->id)
            ->first();
        $this->assertNotNull($pivot);
        $params = $pivot->params ?? [];
        $this->assertSame('2d6+[intel]', $params['value_formula'] ?? null);
        $this->assertSame('fire', $params['characteristic'] ?? null);
        $this->assertSame($creature->id, $params['creature_id'] ?? null);
        $this->assertSame(1, $params['trigger_turn'] ?? null);
        $this->assertSame('caster', $params['applies_to'] ?? null);
        $this->assertSame(
            [['left' => '[agi]', 'op' => '>=', 'right' => '3']],
            $params['conditions'] ?? null,
        );
        $this->assertSame(
            [['conditions' => [], 'action' => 'add', 'formula' => '1']],
            $params['value_rules'] ?? null,
        );
    }
}
