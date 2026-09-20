<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Enums\EntityState;
use App\Models\AiGenerationRun;
use App\Models\Entity\Breed;
use App\Models\Entity\Item;
use App\Models\Entity\Npc;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\Type\ItemType;
use App\Models\User;
use App\Services\GenerativeAi\NpcKitCatalog;
use App\Support\ElementBitmask;
use Tests\TestCase;

final class ConvertNpcTest extends TestCase
{
    use FakesAnthropicJson;

    public function test_admin_converts_npc_full_kit_from_catalog_to_auto(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableNpcEtalon();
        $breed = Breed::factory()->create([
            'name' => 'Iop-ia',
            'state' => Breed::STATE_PLAYABLE,
        ]);
        $capeType = ItemType::factory()->create([
            'name' => 'Cape',
            'dofusdb_type_id' => 17,
            'state' => ItemType::STATE_PLAYABLE,
        ]);
        $cape = Item::factory()->create([
            'name' => 'Cape Terre 4',
            'level' => '4',
            'state' => Item::STATE_PLAYABLE,
            'item_type_id' => $capeType->id,
            'effect' => json_encode(['strength' => 2], JSON_THROW_ON_ERROR),
            'bonus' => null,
            'dofusdb_id' => null,
        ]);
        $spell = Spell::factory()->create([
            'name' => 'Pression kit',
            'state' => Spell::STATE_PLAYABLE,
            'element' => ElementBitmask::fromSlug('earth'),
            'pa' => '3',
        ]);
        $spell->breeds()->attach($breed->id, ['character_level' => 1, 'slot_index' => 1, 'choice_order' => 0]);

        $npc = Npc::factory()->create([
            'official_id' => 'jdr:npc:test:source',
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

        $this->fakeAnthropicJson([
            'npc' => [
                'name' => 'Garde d’Astrub',
                'concept' => 'Factionnaire Iop',
                'story' => 'Il tient la porte.',
                'level' => 4,
                'breed_id' => (int) $breed->id,
                'npc_role' => 'guard',
            ],
            'stats' => ['life' => '22', 'pa' => '6', 'strong' => '8'],
            'item_ids' => [(int) $cape->id],
            'spell_ids' => [(int) $spell->id],
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'npcs', 'id' => $npc->id]), [
                'action' => 'npc',
                'brief' => 'garde Iop niveau 4',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $npc->refresh();
        $creature = $npc->creature()->first();
        $this->assertSame(EntityState::Auto->value, $npc->state);
        $this->assertFalse((bool) $npc->auto_update);
        $this->assertSame(EntityState::Auto->value, $creature?->state);
        $this->assertSame('Garde d’Astrub', $creature?->name);
        $this->assertSame('Il tient la porte.', $npc->story);
        $this->assertSame('22', $creature?->life);
        $this->assertSame('12', $creature?->vitality);
        $this->assertTrue($creature?->items->contains('id', $cape->id));
        $this->assertTrue($creature?->spells->contains('id', $spell->id));
    }

    public function test_two_items_on_same_slot_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableNpcEtalon();
        $capeType = ItemType::factory()->create([
            'name' => 'Cape',
            'dofusdb_type_id' => 17,
            'state' => ItemType::STATE_PLAYABLE,
        ]);
        $capeA = Item::factory()->create([
            'name' => 'Cape A',
            'level' => '4',
            'state' => Item::STATE_PLAYABLE,
            'item_type_id' => $capeType->id,
            'effect' => json_encode(['strength' => 2], JSON_THROW_ON_ERROR),
            'bonus' => null,
            'dofusdb_id' => null,
        ]);
        $capeB = Item::factory()->create([
            'name' => 'Cape B',
            'level' => '4',
            'state' => Item::STATE_PLAYABLE,
            'item_type_id' => $capeType->id,
            'effect' => json_encode(['strength' => 1], JSON_THROW_ON_ERROR),
            'bonus' => null,
            'dofusdb_id' => null,
        ]);
        $npc = Npc::factory()->create([
            'official_id' => 'jdr:npc:test:two-capes',
            'state' => EntityState::Draft->value,
            'npc_role' => 'guard',
        ]);
        $npc->creature?->update([
            'name' => 'Garde double cape',
            'level' => '4',
            'state' => EntityState::Draft->value,
        ]);

        $this->fakeAnthropicJson([
            'npc' => [
                'name' => 'Garde double cape',
                'level' => 4,
                'npc_role' => 'guard',
            ],
            'stats' => ['life' => '22', 'pa' => '6'],
            'item_ids' => [(int) $capeA->id, (int) $capeB->id],
            'spell_ids' => [],
        ]);

        $response = $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'npcs', 'id' => $npc->id]), [
                'action' => 'npc',
            ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('cape', strtolower((string) $response->json('message')));
        $this->assertSame(EntityState::Draft->value, $npc->fresh()->state);
    }

    public function test_artisan_convert_npc_uses_generic_command(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableNpcEtalon();
        $npc = Npc::factory()->create([
            'official_id' => 'jdr:npc:test:cli',
            'state' => EntityState::Draft->value,
            'npc_role' => 'other',
        ]);
        $npc->creature?->update([
            'name' => 'PNJ CLI',
            'level' => '4',
            'state' => EntityState::Draft->value,
        ]);

        $this->fakeAnthropicJson([
            'npc' => [
                'name' => 'Tabach bis',
                'level' => 4,
                'story' => 'Boulanger.',
                'npc_role' => 'other',
            ],
            'stats' => ['life' => '22', 'pa' => '6'],
            'item_ids' => [],
            'spell_ids' => [],
        ]);

        $this->artisan('ia:convert', [
            'type' => 'npc',
            '--official-id' => 'jdr:npc:test:cli',
            '--user' => $admin->id,
        ])->assertSuccessful();

        $this->assertSame(EntityState::Auto->value, $npc->fresh()->state);
        $this->assertSame('Tabach bis', $npc->fresh()->creature?->name);
    }

    public function test_admin_converts_npc_without_source_breed(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableNpcEtalon();
        $kit = $this->playableKit();
        $npc = Npc::factory()->create([
            'official_id' => 'jdr:npc:test:no-breed',
            'state' => EntityState::Draft->value,
            'npc_role' => 'guard',
            'breed_id' => null,
            'auto_update' => true,
        ]);
        $npc->creature?->update([
            'name' => 'Sans classe',
            'level' => '4',
            'state' => EntityState::Draft->value,
        ]);

        $this->fakeAnthropicJson([
            'npc' => [
                'name' => 'Garde Iopette',
                'concept' => 'Factionnaire Iop terre',
                'story' => 'Il tient la porte.',
                'level' => 4,
                'breed_id' => (int) $kit['breed']->id,
                'npc_role' => 'guard',
            ],
            'stats' => ['life' => '22', 'pa' => '6'],
            'item_ids' => [(int) $kit['cape']->id],
            'spell_ids' => [(int) $kit['spell']->id],
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'npcs', 'id' => $npc->id]), [
                'action' => 'npc',
                'brief' => 'garde Iop niveau 4',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $npc->refresh();
        $this->assertSame((int) $kit['breed']->id, (int) $npc->breed_id);
        $this->assertTrue($npc->creature?->spells->contains('id', $kit['spell']->id));
    }

    public function test_unknown_breed_and_specialization_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableNpcEtalon();
        $npc = Npc::factory()->create([
            'official_id' => 'jdr:npc:test:bad-ids',
            'state' => EntityState::Draft->value,
            'npc_role' => 'guard',
        ]);
        $npc->creature?->update(['name' => 'Ids faux', 'level' => '4', 'state' => EntityState::Draft->value]);

        $this->fakeAnthropicJson([
            'npc' => [
                'name' => 'Ids faux',
                'level' => 4,
                'breed_id' => 999999,
                'specialization_id' => 888888,
                'npc_role' => 'guard',
            ],
            'stats' => ['life' => '22', 'pa' => '6'],
            'item_ids' => [],
            'spell_ids' => [],
        ]);

        $response = $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'npcs', 'id' => $npc->id]), [
                'action' => 'npc',
            ]);

        $response->assertStatus(422);
        $message = (string) $response->json('message');
        $this->assertStringContainsString('Classe #', $message);
        $this->assertStringContainsString('Spécialisation #', $message);
        $this->assertSame(EntityState::Draft->value, $npc->fresh()->state);
    }

    public function test_draft_specialization_is_accepted(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableNpcEtalon();
        $kit = $this->playableKit();
        $spec = Specialization::factory()->create([
            'name' => 'Milicien draft ia',
            'state' => Specialization::STATE_DRAFT,
        ]);
        $npc = Npc::factory()->create([
            'official_id' => 'jdr:npc:test:draft-spec',
            'state' => EntityState::Draft->value,
            'npc_role' => 'guard',
            'breed_id' => $kit['breed']->id,
        ]);
        $npc->creature?->update(['name' => 'Garde spe', 'level' => '4', 'state' => EntityState::Draft->value]);

        $this->fakeAnthropicJson([
            'npc' => [
                'name' => 'Garde spe',
                'level' => 4,
                'breed_id' => (int) $kit['breed']->id,
                'specialization_id' => (int) $spec->id,
                'npc_role' => 'guard',
            ],
            'stats' => ['life' => '22', 'pa' => '6'],
            'item_ids' => [],
            'spell_ids' => [(int) $kit['spell']->id],
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'npcs', 'id' => $npc->id]), [
                'action' => 'npc',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame((int) $spec->id, (int) $npc->fresh()->specialization_id);
        $this->assertSame(EntityState::Auto->value, $npc->fresh()->state);
    }

    public function test_omitted_primary_stat_follows_spell_element(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableNpcEtalon();
        $kit = $this->playableKit();
        $npc = Npc::factory()->create([
            'official_id' => 'jdr:npc:test:stats-element',
            'state' => EntityState::Draft->value,
            'npc_role' => 'social',
            'breed_id' => $kit['breed']->id,
        ]);
        $npc->creature?->update(['name' => 'Social terre', 'level' => '4', 'state' => EntityState::Draft->value]);

        $this->fakeAnthropicJson([
            'npc' => [
                'name' => 'Social terre',
                'level' => 4,
                'breed_id' => (int) $kit['breed']->id,
                'npc_role' => 'social',
            ],
            'stats' => ['life' => '22', 'pa' => '4'],
            'item_ids' => [],
            'spell_ids' => [(int) $kit['spell']->id],
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'npcs', 'id' => $npc->id]), [
                'action' => 'npc',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $creature = $npc->fresh()->creature;
        $this->assertSame('12', $creature?->strong);
        $this->assertSame('8', $creature?->chance);
    }

    /**
     * @return array{breed: Breed, cape: Item, spell: Spell}
     */
    private function playableKit(): array
    {
        $breed = Breed::factory()->create([
            'name' => 'Iop-ia-kit-'.uniqid(),
            'state' => Breed::STATE_PLAYABLE,
        ]);
        $capeType = ItemType::factory()->create([
            'name' => 'Cape '.uniqid(),
            'dofusdb_type_id' => 17,
            'state' => ItemType::STATE_PLAYABLE,
        ]);
        $cape = Item::factory()->create([
            'name' => 'Cape Terre kit',
            'level' => '4',
            'state' => Item::STATE_PLAYABLE,
            'item_type_id' => $capeType->id,
            'effect' => json_encode(['strength' => 2], JSON_THROW_ON_ERROR),
            'bonus' => null,
            'dofusdb_id' => null,
        ]);
        $spell = Spell::factory()->create([
            'name' => 'Pression kit '.uniqid(),
            'state' => Spell::STATE_PLAYABLE,
            'element' => ElementBitmask::fromSlug('earth'),
            'pa' => '3',
        ]);
        $spell->breeds()->attach($breed->id, ['character_level' => 1, 'slot_index' => 1, 'choice_order' => 0]);

        return ['breed' => $breed, 'cape' => $cape, 'spell' => $spell];
    }

    private function seedPlayableNpcEtalon(): Npc
    {
        return Npc::factory()->create([
            'official_id' => NpcKitCatalog::OFFICIAL_ID_PREFIX.'ganymede',
            'state' => Npc::STATE_PLAYABLE,
            'npc_role' => 'social',
        ]);
    }
}
