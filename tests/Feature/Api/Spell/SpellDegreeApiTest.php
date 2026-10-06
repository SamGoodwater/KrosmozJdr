<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Spell;

use App\Models\Entity\Creature;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class SpellDegreeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_game_master_can_create_update_and_materialize_degrees(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['created_by' => $gm->id, 'write_level' => 0, 'pa' => '3']);
        $creatureId = DB::table('creatures')->insertGetId([
            'name' => 'Invocation test',
            'res_fixe_neutre' => '0',
            'res_fixe_terre' => '0',
            'res_fixe_feu' => '0',
            'res_fixe_air' => '0',
            'res_fixe_eau' => '0',
            'created_by' => $gm->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $creature = Creature::query()->findOrFail($creatureId);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper-api-'.uniqid(),
            'type_slug' => 'frapper',
            'template_text' => 'Dégâts [value].',
            'variables_allowed' => ['value'],
            'param_schema' => [],
        ]);

        $create = $this->actingAs($gm)->postJson("/api/spells/{$spell->id}/degrees", [
            'required_level' => 1,
            'inherits_effects' => false,
            'pa' => '3',
            'area' => 'point',
            'effects' => [
                [
                    'sub_effect_id' => $sub->id,
                    'params' => [
                        'value_formula' => '2d6',
                        'creature_id' => $creature->id,
                    ],
                ],
            ],
        ]);
        $create->assertCreated();
        $degreeId = (int) $create->json('degree_id');
        $this->assertGreaterThan(0, $degreeId);
        $this->assertCount(1, $create->json('data.degrees'));
        $this->assertSame(
            $creature->id,
            $create->json('data.degrees.0.rows.0.summon_monster.creature_id')
        );

        $second = $this->actingAs($gm)->postJson("/api/spells/{$spell->id}/degrees", [
            'required_level' => 10,
            'pa' => '4',
            'area' => 'circle-1-1',
        ]);
        $second->assertCreated();
        $secondId = (int) $second->json('degree_id');
        $degrees = $second->json('data.degrees');
        $this->assertCount(2, $degrees);
        $this->assertTrue($degrees[1]['inherits_effects']);
        $this->assertCount(1, $degrees[1]['rows']);

        $materialized = $this->actingAs($gm)->postJson(
            "/api/spells/{$spell->id}/degrees/{$secondId}/materialize-effects"
        );
        $materialized->assertOk();
        $this->assertFalse($materialized->json('data.degrees.1.inherits_effects'));

        $this->actingAs($gm)->putJson("/api/spells/{$spell->id}/degrees/{$secondId}/effects", [
            'effects' => [
                [
                    'sub_effect_id' => $sub->id,
                    'params' => ['value_formula' => '4d6'],
                ],
            ],
        ])->assertOk();

        $index = $this->actingAs($gm)->getJson("/api/spells/{$spell->id}/degrees");
        $index->assertOk();
        $this->assertSame('4d6', $index->json('data.degrees.1.rows.0.params.value_formula'));
    }
}
