<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GET /entities/creatures/{id}/resolved-stats suit la visibilité monstre/PNJ.
 */
class CreatureResolvedStatsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_read_resolved_stats_of_playable_monster(): void
    {
        $monster = Monster::factory()->create([
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->get(route('entities.creatures.resolvedStats', $monster->creature_id))
            ->assertOk()
            ->assertJsonPath('entity', 'monster');
    }

    public function test_guest_cannot_read_resolved_stats_of_draft_monster(): void
    {
        $monster = Monster::factory()->create([
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->get(route('entities.creatures.resolvedStats', $monster->creature_id))
            ->assertForbidden();
    }

    public function test_player_cannot_enumerate_draft_monster_stats_by_creature_id(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $monster = Monster::factory()->create([
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($player)
            ->get(route('entities.creatures.resolvedStats', $monster->creature_id).'?entity=monster')
            ->assertForbidden();
    }

    public function test_game_master_can_read_resolved_stats_of_draft_monster(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $monster = Monster::factory()->create([
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($gm)
            ->get(route('entities.creatures.resolvedStats', $monster->creature_id))
            ->assertOk();
    }

    public function test_admin_can_read_resolved_stats_of_draft_monster(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create([
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($admin)
            ->get(route('entities.creatures.resolvedStats', $monster->creature_id))
            ->assertOk();
    }

    public function test_guest_cannot_read_resolved_stats_of_draft_npc(): void
    {
        $creature = Creature::factory()->create();
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->get(route('entities.creatures.resolvedStats', $npc->creature_id).'?entity=npc')
            ->assertForbidden();
    }
}
