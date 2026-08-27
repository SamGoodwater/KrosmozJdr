<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Listes visibles : alignement SQL sur la policy (dont tables sans created_by).
 */
class EntityDisplayVisibilityQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_monster_visible_to_user_query_does_not_reference_missing_created_by(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        Monster::factory()->create([
            'state' => 'draft',
            'write_level' => User::ROLE_GAME_MASTER,
            'read_level' => User::ROLE_GUEST,
        ]);

        $count = Monster::query()->visibleToUser($user)->count();

        $this->assertSame(0, $count);
    }

    public function test_draft_spells_are_listed_for_super_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        Spell::factory()->count(3)->create([
            'state' => Spell::STATE_DRAFT,
            'write_level' => User::ROLE_GAME_MASTER,
            'read_level' => User::ROLE_GUEST,
            'created_by' => $author->id,
        ]);

        $this->assertSame(3, Spell::query()->visibleToUser($admin)->count());
    }

    public function test_monster_visibility_sql_stays_valid_after_joining_creatures(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $visible = Creature::factory()->create();
        $hidden = Creature::factory()->create();
        Monster::factory()->create([
            'creature_id' => $visible->id,
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        Monster::factory()->create([
            'creature_id' => $hidden->id,
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $sql = Monster::query()
            ->visibleToUser($user)
            ->join('creatures', 'monsters.creature_id', '=', 'creatures.id')
            ->select('monsters.*')
            ->toSql();

        $this->assertStringContainsString('`monsters`.`state`', $sql);
        $this->assertStringContainsString('`monsters`.`read_level`', $sql);
        $this->assertDoesNotMatchRegularExpression('/where `state`/i', $sql);

        $ids = Monster::query()
            ->visibleToUser($user)
            ->join('creatures', 'monsters.creature_id', '=', 'creatures.id')
            ->select('monsters.*')
            ->pluck('monsters.id');

        $this->assertCount(1, $ids);
    }

    public function test_auto_spells_are_hidden_from_player_and_listed_for_game_master(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        Spell::factory()->create([
            'state' => Spell::STATE_AUTO,
            'write_level' => User::ROLE_GAME_MASTER,
            'read_level' => User::ROLE_GUEST,
            'created_by' => $author->id,
        ]);

        $this->assertSame(0, Spell::query()->visibleToUser($player)->count());
        $this->assertSame(1, Spell::query()->visibleToUser($gm)->count());
    }
}
