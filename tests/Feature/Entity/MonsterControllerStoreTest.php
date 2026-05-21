<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Création monstre via modal (creature + monster).
 */
class MonsterControllerStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_monster_with_new_creature(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('entities.monsters.store'), [
            'name' => 'Bouftou test',
            'description' => 'Créature de test',
            'level' => '5',
            'redirect_after_create' => 'edit',
        ]);

        $monster = Monster::query()->whereHas('creature', fn ($q) => $q->where('name', 'Bouftou test'))->first();
        $this->assertNotNull($monster);
        $this->assertInstanceOf(Creature::class, $monster->creature);
        $response->assertRedirect(route('entities.monsters.edit', $monster));
    }

    public function test_player_cannot_store_monster(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $this->actingAs($player)
            ->post(route('entities.monsters.store'), [
                'name' => 'Interdit',
            ])
            ->assertForbidden();
    }
}
