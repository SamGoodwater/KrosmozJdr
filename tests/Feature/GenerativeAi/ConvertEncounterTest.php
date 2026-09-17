<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Enums\EntityState;
use App\Models\AiGenerationRun;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\GenerativeAi\GenerativeAiClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class ConvertEncounterTest extends TestCase
{
    public function test_admin_converts_encounter_packet_to_auto_with_faked_http(): void
    {
        $this->fakeAnthropicEncounter();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableEtalon();
        $monster = $this->sourceMonster();

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
                'brief' => 'chef Bouftou niveau 10',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('queued', false)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS)
            ->assertJsonPath('diff.source', 'ia');

        $monster->refresh();
        $this->assertSame(EntityState::Auto->value, $monster->state);
        $this->assertFalse((bool) $monster->auto_update);

        $creature = $monster->creature()->first();
        $this->assertNotNull($creature);
        $this->assertSame(EntityState::Auto->value, $creature->state);
        $this->assertCount(2, $creature->spells);

        foreach ($creature->spells as $spell) {
            $this->assertSame(EntityState::Auto->value, $spell->state);
            $this->assertFalse((bool) $spell->auto_update);
            $this->assertSame(Spell::CATEGORY_CREATURE, $spell->category);
            $this->assertStringStartsWith('ia:encounter:'.$monster->id.':', (string) $spell->official_id);
        }

        $run = AiGenerationRun::query()->first();
        $this->assertNotNull($run);
        $this->assertSame('encounter', $run->action);
        $this->assertSame(120, $run->input_tokens);
        $this->assertSame(40, $run->output_tokens);
        $this->assertSame('claude-sonnet-5', $run->model);
        $this->assertNotNull($run->ai_generated_at);

        Http::assertSentCount(1);
    }

    public function test_missing_api_key_does_not_pretend_success(): void
    {
        config(['services.anthropic.api_key' => '']);
        Http::fake();
        Http::preventStrayRequests();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = $this->sourceMonster();

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Clé Anthropic absente : aucun appel n’a été lancé.');

        $this->assertDatabaseCount('ai_generation_runs', 0);
        $this->assertSame(EntityState::Raw->value, $monster->fresh()->state);
        Http::assertSentCount(0);
    }

    public function test_http_convert_runs_inline_when_queue_is_database(): void
    {
        config(['queue.default' => 'database']);
        $this->fakeAnthropicEncounter();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableEtalon();
        $monster = $this->sourceMonster();

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
                'brief' => 'chef Bouftou niveau 10',
            ])
            ->assertOk()
            ->assertJsonPath('queued', false)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $this->assertSame(EntityState::Auto->value, $monster->fresh()->state);
        Http::assertSentCount(1);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_restore_reverts_ia_snapshot(): void
    {
        $this->fakeAnthropicEncounter();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableEtalon();
        $monster = $this->sourceMonster();
        $beforeState = $monster->state;

        $payload = $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertOk()
            ->json();

        $this->assertSame(EntityState::Auto->value, $monster->fresh()->state);
        $snapshotId = $payload['diff']['snapshot_id'] ?? null;
        $this->assertIsString($snapshotId);

        $this->actingAs($admin)
            ->postJson(route('api.entities.update-diff.restore', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'snapshot_id' => $snapshotId,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame($beforeState, $monster->fresh()->state);
        $this->assertCount(0, $monster->fresh()->creature?->spells ?? collect());
    }

    public function test_game_master_cannot_convert(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $monster = $this->sourceMonster();

        $this->actingAs($gm)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertForbidden();
    }

    public function test_artisan_convert_encounter_persists_auto_packet(): void
    {
        $this->fakeAnthropicEncounter();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableEtalon();
        $monster = $this->sourceMonster();

        $this->artisan('ia:convert-encounter', [
            '--id' => $monster->id,
            '--user' => $admin->id,
            '--brief' => 'chef Bouftou niveau 10',
        ])->assertSuccessful();

        $this->assertSame(EntityState::Auto->value, $monster->fresh()->state);
        $this->assertDatabaseHas('ai_generation_runs', [
            'action' => 'encounter',
            'status' => AiGenerationRun::STATUS_SUCCESS,
            'entity_id' => $monster->id,
        ]);
    }

    public function test_ia_status_is_graceful_without_api_key(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->getJson(route('api.ia.status'))
            ->assertOk()
            ->assertJsonPath('usage.available', false)
            ->assertJsonPath('usage.local_input_tokens', 0)
            ->assertJsonPath('usage.has_api_key', false)
            ->assertJsonPath('estimates.0.action', 'npc');
    }

    private function fakeAnthropicEncounter(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::preventStrayRequests();
        Http::fake([
            '*anthropic.com/v1/messages' => Http::response([
                'model' => 'claude-sonnet-5',
                'content' => [[
                    'type' => 'tool_use',
                    'name' => GenerativeAiClient::TOOL_NAME,
                    'input' => [
                        'monster' => [],
                        'spells' => [
                            ['name' => 'Bec', 'effect' => '1d6 Air', 'pa' => '3', 'element' => 'air'],
                            ['name' => 'Picore', 'effect' => '1d4 Air', 'pa' => '2', 'element' => 'air'],
                        ],
                    ],
                ]],
                'usage' => [
                    'input_tokens' => 120,
                    'output_tokens' => 40,
                    'cache_read_input_tokens' => 10,
                ],
            ], 200),
        ]);
    }

    private function seedPlayableEtalon(): Monster
    {
        return Monster::factory()->create([
            'official_id' => 'jdr:bestiary:piou-vert',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
    }

    private function sourceMonster(): Monster
    {
        $monster = Monster::factory()->create([
            'official_id' => 'dofus:encounter-source',
            'state' => EntityState::Raw->value,
            'auto_update' => true,
        ]);
        $monster->creature?->update([
            'pa' => '6',
            'agi' => '8',
            'strong' => '2',
            'state' => EntityState::Raw->value,
        ]);

        return $monster->fresh(['creature']);
    }
}
