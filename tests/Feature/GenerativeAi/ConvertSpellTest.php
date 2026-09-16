<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Enums\EntityState;
use App\Models\AiGenerationRun;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class ConvertSpellTest extends TestCase
{
    use FakesAnthropicJson;

    public function test_admin_rewrites_spell_effect_only_to_auto(): void
    {
        $this->fakeAnthropicJson(['effect' => "1d6 Terre.\nMêlée, 2×/tour."]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableEtalon();
        $spell = Spell::factory()->create([
            'name' => 'Sort brut',
            'effect' => 'texte dofus illisible',
            'pa' => '3',
            'state' => EntityState::Raw->value,
            'auto_update' => true,
            'dofusdb_id' => '13106',
        ]);

        $this->actingAs($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
                'brief' => 'effet lisible à table',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $spell->refresh();
        $this->assertSame(EntityState::Auto->value, $spell->state);
        $this->assertFalse((bool) $spell->auto_update);
        $this->assertSame('Sort brut', $spell->name);
        $this->assertSame("1d6 Terre.\nMêlée, 2×/tour.", $spell->effect);
        $this->assertSame('3', $spell->pa);
        Http::assertSentCount(1);
    }

    public function test_game_master_cannot_convert_spell(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['state' => EntityState::Raw->value]);

        $this->actingAs($gm)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
            ])
            ->assertForbidden();
    }

    public function test_artisan_convert_spell_persists_auto_effect(): void
    {
        $this->fakeAnthropicJson(['effect' => '1d8 Terre']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableEtalon();
        $spell = Spell::factory()->create([
            'name' => 'Sort CLI',
            'effect' => 'ancien',
            'state' => EntityState::Draft->value,
            'auto_update' => true,
        ]);

        $this->artisan('ia:convert', [
            'type' => 'spell',
            '--id' => $spell->id,
            '--user' => $admin->id,
        ])->assertSuccessful();

        $this->assertSame('1d8 Terre', $spell->fresh()->effect);
        $this->assertSame(EntityState::Auto->value, $spell->fresh()->state);
        $this->assertDatabaseHas('ai_generation_runs', [
            'action' => 'spell',
            'status' => AiGenerationRun::STATUS_SUCCESS,
            'entity_id' => $spell->id,
        ]);
    }

    private function seedPlayableEtalon(): Spell
    {
        return Spell::factory()->create([
            'name' => 'Pression',
            'official_id' => 'jdr:spell:pression',
            'effect' => '1d6 + Force (Terre).',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
    }
}
