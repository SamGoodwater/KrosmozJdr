<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

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
}
