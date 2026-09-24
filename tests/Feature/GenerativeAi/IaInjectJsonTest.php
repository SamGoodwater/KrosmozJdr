<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Enums\EntityState;
use App\Models\AiGenerationRun;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Injection JSON manuelle (même validate + persist que l’IA, sans LLM).
 */
final class IaInjectJsonTest extends TestCase
{
    public function test_admin_injects_spell_effect_without_anthropic_key(): void
    {
        config(['services.anthropic.api_key' => null]);
        Http::fake();
        Http::preventStrayRequests();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'name' => 'Sort inject',
            'effect' => 'ancien',
            'pa' => '3',
            'state' => EntityState::Raw->value,
            'auto_update' => true,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
                'payload' => ['effect' => "1d6 Terre.\nMêlée."],
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $spell->refresh();
        $this->assertSame(EntityState::Auto->value, $spell->state);
        $this->assertFalse((bool) $spell->auto_update);
        $this->assertSame("1d6 Terre.\nMêlée.", $spell->effect);
        $this->assertSame('Sort inject', $spell->name);
        $this->assertDatabaseHas('ai_generation_runs', [
            'action' => 'spell',
            'entity_id' => $spell->id,
            'status' => AiGenerationRun::STATUS_SUCCESS,
            'model' => 'manual-json',
            'prompt_version' => 'manual-v1',
        ]);
        Http::assertSentCount(0);
    }

    public function test_inject_rejects_invalid_json_payload(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'effect' => 'inchangé',
            'state' => EntityState::Draft->value,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
                'payload' => '{not-json',
            ])
            ->assertStatus(422);

        $this->assertSame('inchangé', $spell->fresh()->effect);
        $this->assertSame(EntityState::Draft->value, $spell->fresh()->state);
        Http::assertSentCount(0);
    }

    public function test_inject_rejects_unknown_keys(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'name' => 'Garde nom',
            'effect' => 'ancien',
            'state' => EntityState::Draft->value,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
                'payload' => [
                    'effect' => '1d4',
                    'state' => 'playable',
                    'name' => 'Hacked',
                ],
            ])
            ->assertStatus(422)
            ->assertJsonFragment(['success' => false]);

        $spell->refresh();
        $this->assertSame('ancien', $spell->effect);
        $this->assertSame('Garde nom', $spell->name);
        $this->assertSame(EntityState::Draft->value, $spell->state);
        Http::assertSentCount(0);
    }

    public function test_inject_strips_html_from_string_leaves(): void
    {
        config(['services.anthropic.api_key' => null]);
        Http::fake();
        Http::preventStrayRequests();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'effect' => 'ancien',
            'state' => EntityState::Raw->value,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
                'payload' => ['effect' => '<script>alert(1)</script>1d6 Terre'],
            ])
            ->assertOk();

        $this->assertSame('alert(1)1d6 Terre', $spell->fresh()->effect);
        Http::assertSentCount(0);
    }

    public function test_schema_endpoint_returns_example_for_spells(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->getJson(route('api.ia.schema', ['entityType' => 'spells']))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('action', 'spell')
            ->assertJsonStructure(['schema', 'example']);
    }

    public function test_game_master_cannot_inject(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['state' => EntityState::Raw->value]);

        $this->actingAsConfirmed($gm)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
                'payload' => ['effect' => '1d6'],
            ])
            ->assertForbidden();

        Http::assertSentCount(0);
    }
}
