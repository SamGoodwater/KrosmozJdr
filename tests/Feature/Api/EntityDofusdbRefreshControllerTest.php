<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use App\Models\User;
use App\Services\Entity\EntityDofusdbRefreshService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Maj DofusDB unitaire : id local, policy update, MJ+ ; masse reste admin.
 */
class EntityDofusdbRefreshControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $gm;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_game_master_can_preview_owned_draft_item(): void
    {
        $item = Item::factory()->create([
            'created_by' => $this->gm->id,
            'state' => Item::STATE_DRAFT,
            'dofusdb_id' => '42',
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $this->mockOrchestratorPreview();

        $this->actingAs($this->gm)
            ->postJson("/api/entities/items/{$item->id}/dofusdb-refresh", ['mode' => 'preview'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.dofusdb_id', 42)
            ->assertJsonPath('data.mode', 'preview');
    }

    public function test_game_master_forbidden_from_mass_scrapping_jobs(): void
    {
        $this->actingAs($this->gm)
            ->withSession($this->passwordConfirmedSession())
            ->postJson('/api/scrapping/jobs', [
                'kind' => 'import_batch',
                'entities' => [['type' => 'item', 'id' => 1]],
            ])
            ->assertForbidden();
    }

    public function test_game_master_cannot_apply_playable_without_admin_force(): void
    {
        $item = Item::factory()->create([
            'created_by' => $this->gm->id,
            'state' => Item::STATE_PLAYABLE,
            'dofusdb_id' => '42',
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($this->gm)
            ->postJson("/api/entities/items/{$item->id}/dofusdb-refresh", ['mode' => 'full'])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_admin_can_force_playable_refresh(): void
    {
        $item = Item::factory()->create([
            'created_by' => $this->admin->id,
            'state' => Item::STATE_PLAYABLE,
            'dofusdb_id' => '42',
        ]);
        $this->mockOrchestratorApply();

        $this->actingAs($this->admin)
            ->postJson("/api/entities/items/{$item->id}/dofusdb-refresh", [
                'mode' => 'full',
                'force' => true,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_non_scrappable_type_is_rejected(): void
    {
        $this->actingAs($this->gm)
            ->postJson('/api/entities/npcs/1/dofusdb-refresh', ['mode' => 'full'])
            ->assertStatus(422);
    }

    public function test_missing_dofusdb_id_is_rejected(): void
    {
        $item = Item::factory()->create([
            'created_by' => $this->gm->id,
            'state' => Item::STATE_DRAFT,
            'dofusdb_id' => null,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($this->gm)
            ->postJson("/api/entities/items/{$item->id}/dofusdb-refresh", ['mode' => 'preview'])
            ->assertStatus(422);
    }

    public function test_player_is_forbidden(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $item = Item::factory()->create([
            'created_by' => $player->id,
            'state' => Item::STATE_DRAFT,
            'dofusdb_id' => '42',
        ]);

        $this->actingAs($player)
            ->postJson("/api/entities/items/{$item->id}/dofusdb-refresh", ['mode' => 'preview'])
            ->assertForbidden();
    }

    public function test_refresh_blocked_when_item_type_disallows_scrap(): void
    {
        $type = ItemType::factory()->create([
            'name' => 'Costume',
            'dofusdb_type_id' => 199,
            'decision' => ItemType::DECISION_PENDING,
            'allow_scrap' => false,
        ]);
        $item = Item::factory()->create([
            'created_by' => $this->gm->id,
            'state' => Item::STATE_DRAFT,
            'dofusdb_id' => '42',
            'write_level' => User::ROLE_GAME_MASTER,
            'item_type_id' => $type->id,
        ]);

        $this->actingAs($this->gm)
            ->postJson("/api/entities/items/{$item->id}/dofusdb-refresh", ['mode' => 'preview'])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_game_master_cannot_update_item_they_cannot_edit(): void
    {
        $other = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create([
            'created_by' => $other->id,
            'state' => Item::STATE_DRAFT,
            'dofusdb_id' => '42',
            'write_level' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($this->gm)
            ->postJson("/api/entities/items/{$item->id}/dofusdb-refresh", ['mode' => 'preview'])
            ->assertForbidden();
    }

    private function mockOrchestratorPreview(): void
    {
        $service = Mockery::mock(EntityDofusdbRefreshService::class);
        $service->shouldReceive('run')
            ->once()
            ->andReturn([
                'success' => true,
                'message' => 'Aperçu',
                'data' => [
                    'mode' => 'preview',
                    'dofusdb_id' => 42,
                    'raw' => ['id' => 42],
                    'converted' => ['items' => ['name' => 'Test']],
                    'validation_errors' => [],
                    'existing' => null,
                ],
            ]);
        $this->app->instance(EntityDofusdbRefreshService::class, $service);
    }

    private function mockOrchestratorApply(): void
    {
        $service = Mockery::mock(EntityDofusdbRefreshService::class);
        $service->shouldReceive('run')
            ->once()
            ->andReturn([
                'success' => true,
                'message' => 'Mis à jour',
                'data' => [
                    'mode' => 'full',
                    'dofusdb_id' => 42,
                    'raw' => ['id' => 42],
                    'converted' => ['items' => ['name' => 'Test']],
                    'validation_errors' => [],
                    'existing' => null,
                ],
            ]);
        $this->app->instance(EntityDofusdbRefreshService::class, $service);
    }
}
