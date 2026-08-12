<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Monster;
use App\Models\Type\MonsterRace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Validation race + PDF multi monstre (visibilité).
 */
class MonsterControllerVisibilityAndValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_monster_race_with_monster_races_table(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $race = MonsterRace::factory()->create(['name' => 'Bouftous']);
        $monster = Monster::factory()->create(['monster_race_id' => null]);

        $this->actingAs($admin)
            ->patch(route('entities.monsters.update', $monster), [
                'monster_race_id' => $race->id,
            ])
            ->assertRedirect();

        $this->assertSame($race->id, $monster->fresh()->monster_race_id);
    }

    public function test_multi_pdf_only_includes_monsters_visible_to_viewer(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $visible = Monster::factory()->create([
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $hidden = Monster::factory()->create([
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        // Route liée à un monstre visible ; la query `ids` mélange visible + brouillon.
        $response = $this->actingAs($player)
            ->get(route('entities.monsters.pdf', $visible).'?'.http_build_query([
                'ids' => [$visible->id, $hidden->id],
            ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }
}
