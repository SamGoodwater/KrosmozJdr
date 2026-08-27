<?php

declare(strict_types=1);

namespace Tests\Feature\Type;

use App\Models\Type\ItemType;
use App\Models\Type\MonsterRace;
use App\Models\Type\ResourceType;
use App\Models\Type\SpellType;
use App\Models\User;
use App\Services\Scrapping\DataCollect\MonsterRaceFilterService;
use App\Services\Scrapping\Registry\ItemTypeCategoryMoveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypeRegistryFlagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_allow_scrap_on_item_type(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $type = ItemType::factory()->create([
            'name' => 'Type scrap test',
            'dofusdb_type_id' => 9001,
            'decision' => ItemType::DECISION_PENDING,
            'allow_scrap' => false,
        ]);

        $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->patchJson('/api/dofusdb/item-types/bulk', [
                'ids' => [$type->id],
                'allow_scrap' => true,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $fresh = $type->fresh();
        $this->assertTrue((bool) $fresh->allow_scrap);
        $this->assertSame(ItemType::DECISION_ALLOWED, $fresh->decision);
        $this->assertTrue(ItemType::isDofusdbTypeAllowed(9001));
    }

    public function test_game_master_cannot_toggle_allow_scrap_via_scrapping_api(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $type = ItemType::factory()->create([
            'name' => 'Type GM scrap',
            'dofusdb_type_id' => 9002,
            'allow_scrap' => false,
        ]);

        $this->actingAs($gm)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->patchJson('/api/dofusdb/item-types/bulk', [
                'ids' => [$type->id],
                'allow_scrap' => true,
            ])
            ->assertForbidden();

        $this->assertFalse((bool) $type->fresh()->allow_scrap);
    }

    public function test_admin_can_toggle_race_flags(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $race = MonsterRace::factory()->create([
            'name' => 'Race flags test',
            'dofusdb_race_id' => 42,
            'state' => MonsterRace::STATE_DRAFT,
            'show_in_catalog' => false,
            'allow_scrap' => false,
        ]);

        $this->actingAs($admin)
            ->patchJson('/api/types/monster-races/bulk', [
                'ids' => [$race->id],
                'allow_scrap' => true,
                'show_in_catalog' => true,
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('summary.updated', 1);

        $fresh = $race->fresh();
        $this->assertTrue((bool) $fresh->allow_scrap);
        $this->assertTrue((bool) $fresh->show_in_catalog);
    }

    public function test_admin_can_toggle_spell_type_catalog_flag(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spellType = SpellType::factory()->create([
            'name' => 'Type sort flags',
            'show_in_catalog' => false,
            'allow_scrap' => false,
        ]);

        $this->actingAs($admin)
            ->patchJson("/api/types/spell-types/{$spellType->id}/catalog", [
                'show_in_catalog' => true,
            ])
            ->assertOk()
            ->assertJsonPath('show_in_catalog', true);

        $this->assertTrue((bool) $spellType->fresh()->show_in_catalog);
    }

    public function test_move_copies_registry_flags(): void
    {
        ItemType::factory()->create([
            'name' => 'Équipement à déplacer',
            'dofusdb_type_id' => 9010,
            'allow_scrap' => true,
            'show_in_catalog' => true,
            'decision' => ItemType::DECISION_ALLOWED,
        ]);

        $source = ItemType::query()->where('dofusdb_type_id', 9010)->firstOrFail();

        $result = app(ItemTypeCategoryMoveService::class)->move('equipment', (int) $source->id, 'resource');

        $this->assertTrue($result['success'], $result['message'] ?? '');
        $this->assertNotNull($result['target_id'] ?? null);

        $this->assertNull(ItemType::query()->where('dofusdb_type_id', 9010)->first());

        $target = ResourceType::query()->findOrFail((int) $result['target_id']);
        $this->assertTrue((bool) $target->allow_scrap);
        $this->assertTrue((bool) $target->show_in_catalog);
        $this->assertSame(ResourceType::DECISION_ALLOWED, $target->decision);
    }

    public function test_monster_race_filter_uses_allow_scrap(): void
    {
        MonsterRace::factory()->create([
            'name' => 'Race scrap on',
            'dofusdb_race_id' => 11,
            'allow_scrap' => true,
            'state' => MonsterRace::STATE_DRAFT,
        ]);
        MonsterRace::factory()->create([
            'name' => 'Race scrap off playable',
            'dofusdb_race_id' => 22,
            'allow_scrap' => false,
            'state' => MonsterRace::STATE_PLAYABLE,
        ]);

        $filters = app(MonsterRaceFilterService::class)->applyDefaults([]);

        $this->assertSame([11], $filters['raceIds'] ?? []);
    }

    public function test_monster_race_defaults_are_empty_when_none_allow_scrap(): void
    {
        MonsterRace::factory()->create([
            'name' => 'Race scrap off',
            'dofusdb_race_id' => 33,
            'allow_scrap' => false,
            'state' => MonsterRace::STATE_PLAYABLE,
        ]);

        $filters = app(MonsterRaceFilterService::class)->applyDefaults([]);

        $this->assertSame([], $filters['raceIds'] ?? null);
    }
}
