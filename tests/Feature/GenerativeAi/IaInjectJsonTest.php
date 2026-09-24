<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Enums\EntityState;
use App\Models\AiGenerationRun;
use App\Models\Entity\Breed;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Support\ElementBitmask;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Injection JSON manuelle (même validate + persist que l’IA, sans LLM).
 *
 * Couvre les 5 actions convertibles : spell, item, consumable, encounter, npc.
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

    public function test_admin_injects_unique_item_writable_fields(): void
    {
        config(['services.anthropic.api_key' => null]);
        Http::fake();
        Http::preventStrayRequests();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'name' => 'Anneau brut',
            'description' => 'brouillon',
            'bonus' => null,
            'effect' => null,
            'dofusdb_id' => null,
            'official_id' => 'jdr:item:inject-unique',
            'state' => EntityState::Draft->value,
            'auto_update' => true,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'items', 'id' => $item->id]), [
                'action' => 'item',
                'payload' => [
                    'name' => 'Anneau injecté',
                    'description' => 'Unique de quête.',
                    'bonus' => '+2 Force',
                    'effect' => '{"strength":2}',
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $item->refresh();
        $this->assertSame(EntityState::Auto->value, $item->state);
        $this->assertFalse((bool) $item->auto_update);
        $this->assertSame('Anneau injecté', $item->name);
        $this->assertSame('+2 Force', $item->bonus);
        Http::assertSentCount(0);
    }

    public function test_admin_injects_consumable_effect_only(): void
    {
        config(['services.anthropic.api_key' => null]);
        Http::fake();
        Http::preventStrayRequests();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $consumable = Consumable::factory()->create([
            'name' => 'Pain brut',
            'effect' => 'soin opaque',
            'dofusdb_id' => '468',
            'state' => EntityState::Raw->value,
            'auto_update' => true,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'consumables', 'id' => $consumable->id]), [
                'action' => 'consumable',
                'payload' => ['effect' => 'Soigne 5 PV hors combat.'],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $consumable->refresh();
        $this->assertSame(EntityState::Auto->value, $consumable->state);
        $this->assertSame('Pain brut', $consumable->name);
        $this->assertSame('Soigne 5 PV hors combat.', $consumable->effect);
        Http::assertSentCount(0);
    }

    public function test_admin_injects_encounter_packet(): void
    {
        config(['services.anthropic.api_key' => null]);
        Http::fake();
        Http::preventStrayRequests();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create([
            'official_id' => 'dofus:encounter-inject',
            'state' => EntityState::Raw->value,
            'auto_update' => true,
        ]);
        $monster->creature?->update([
            'pa' => '6',
            'agi' => '8',
            'strong' => '2',
            'state' => EntityState::Raw->value,
        ]);
        $monster = $monster->fresh(['creature']);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
                'payload' => [
                    'monster' => [],
                    'spells' => [
                        ['name' => 'Bec', 'effect' => '1d6 Air', 'pa' => '3', 'element' => 'air'],
                        ['name' => 'Picore', 'effect' => '1d4 Air', 'pa' => '2', 'element' => 'air'],
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $monster->refresh();
        $this->assertSame(EntityState::Auto->value, $monster->state);
        $this->assertFalse((bool) $monster->auto_update);
        $creature = $monster->creature()->first();
        $this->assertNotNull($creature);
        $this->assertSame(EntityState::Auto->value, $creature->state);
        $this->assertCount(2, $creature->spells);
        $this->assertDatabaseHas('ai_generation_runs', [
            'action' => 'encounter',
            'entity_id' => $monster->id,
            'model' => 'manual-json',
            'status' => AiGenerationRun::STATUS_SUCCESS,
        ]);
        Http::assertSentCount(0);
    }

    public function test_admin_injects_npc_kit(): void
    {
        config(['services.anthropic.api_key' => null]);
        Http::fake();
        Http::preventStrayRequests();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Npc::factory()->create([
            'official_id' => 'jdr:npc:incarnam:ganymede',
            'state' => Npc::STATE_PLAYABLE,
            'npc_role' => 'social',
        ]);
        $breed = Breed::factory()->create([
            'name' => 'Iop-inject',
            'state' => Breed::STATE_PLAYABLE,
        ]);
        $spell = Spell::factory()->create([
            'name' => 'Pression inject',
            'state' => Spell::STATE_PLAYABLE,
            'element' => ElementBitmask::fromSlug('earth'),
            'pa' => '3',
        ]);
        $spell->breeds()->attach($breed->id, ['character_level' => 1, 'slot_index' => 1, 'choice_order' => 0]);

        $npc = Npc::factory()->create([
            'official_id' => 'jdr:npc:inject:source',
            'state' => EntityState::Draft->value,
            'npc_role' => 'guard',
            'breed_id' => $breed->id,
            'auto_update' => true,
        ]);
        $npc->creature?->update([
            'name' => 'Garde brut',
            'level' => '4',
            'state' => EntityState::Draft->value,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'npcs', 'id' => $npc->id]), [
                'action' => 'npc',
                'payload' => [
                    'npc' => [
                        'name' => 'Garde injecté',
                        'concept' => 'Factionnaire Iop',
                        'story' => 'Il tient la porte.',
                        'level' => 4,
                        'breed_id' => (int) $breed->id,
                        'npc_role' => 'guard',
                    ],
                    'stats' => ['life' => '22', 'pa' => '6', 'strong' => '8'],
                    'item_ids' => [],
                    'spell_ids' => [(int) $spell->id],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $npc->refresh();
        $creature = $npc->creature()->first();
        $this->assertSame(EntityState::Auto->value, $npc->state);
        $this->assertFalse((bool) $npc->auto_update);
        $this->assertSame('Garde injecté', $creature?->name);
        $this->assertSame('Il tient la porte.', $npc->story);
        $this->assertTrue($creature?->spells->contains('id', $spell->id));
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

    public function test_schema_endpoint_returns_example_for_each_convertible_type(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach ([
            'spells' => 'spell',
            'items' => 'item',
            'consumables' => 'consumable',
            'monsters' => 'encounter',
            'npcs' => 'npc',
        ] as $entityType => $action) {
            $this->actingAsConfirmed($admin)
                ->getJson(route('api.ia.schema', ['entityType' => $entityType]))
                ->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('action', $action)
                ->assertJsonPath('mode', 'specialization')
                ->assertJsonStructure(['schema', 'example']);
        }
    }

    public function test_admin_injects_generic_campaign_fillable_fields(): void
    {
        config(['services.anthropic.api_key' => null]);
        Http::fake();
        Http::preventStrayRequests();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $campaign = \App\Models\Entity\Campaign::factory()->create([
            'name' => 'Campagne brute',
            'description' => 'ancien',
            'state' => EntityState::Draft->value,
            'created_by' => $admin->id,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'campaigns', 'id' => $campaign->id]), [
                'payload' => [
                    'name' => 'Incarnam injecté',
                    'description' => 'Tutoriel des Douze.',
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $campaign->refresh();
        $this->assertSame(EntityState::Auto->value, $campaign->state);
        $this->assertSame('Incarnam injecté', $campaign->name);
        $this->assertSame('Tutoriel des Douze.', $campaign->description);
        $this->assertDatabaseHas('ai_generation_runs', [
            'action' => 'inject',
            'entity_type' => 'campaigns',
            'entity_id' => $campaign->id,
            'model' => 'manual-json',
            'status' => AiGenerationRun::STATUS_SUCCESS,
        ]);
        Http::assertSentCount(0);
    }

    public function test_schema_endpoint_returns_generic_example_for_campaigns(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->getJson(route('api.ia.schema', ['entityType' => 'campaigns']))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('action', 'inject')
            ->assertJsonPath('mode', 'generic')
            ->assertJsonStructure(['schema', 'example']);
    }

    public function test_generic_inject_rejects_state_key(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $campaign = \App\Models\Entity\Campaign::factory()->create([
            'name' => 'Garde',
            'state' => EntityState::Draft->value,
            'created_by' => $admin->id,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-inject', ['entityType' => 'campaigns', 'id' => $campaign->id]), [
                'payload' => [
                    'name' => 'Hacked',
                    'state' => 'playable',
                ],
            ])
            ->assertStatus(422);

        $this->assertSame('Garde', $campaign->fresh()->name);
        $this->assertSame(EntityState::Draft->value, $campaign->fresh()->state);
        Http::assertSentCount(0);
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
