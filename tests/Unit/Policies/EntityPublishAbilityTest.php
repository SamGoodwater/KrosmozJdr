<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Ability `publish` alignée sur `updateAny` du type d’entité.
 *
 * Item : MJ OK. Spell / Monster : admin-only (MJ KO, admin OK).
 */
class EntityPublishAbilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_master_can_publish_item(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create();

        $this->actingAs($gm);

        $this->assertTrue(Gate::allows('publish', $item));
    }

    public function test_game_master_cannot_publish_spell(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create();

        $this->actingAs($gm);

        $this->assertFalse(Gate::allows('publish', $spell));
    }

    public function test_admin_can_publish_spell(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create();

        $this->actingAs($admin);

        $this->assertTrue(Gate::allows('publish', $spell));
    }

    public function test_game_master_cannot_publish_monster(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $monster = Monster::factory()->create();

        $this->actingAs($gm);

        $this->assertFalse(Gate::allows('publish', $monster));
    }

    public function test_admin_can_publish_monster(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();

        $this->actingAs($admin);

        $this->assertTrue(Gate::allows('publish', $monster));
    }
}
