<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentBonusTableControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_forbidden(): void
    {
        $this->getJson('/api/characteristics/equipment-bonus-table')
            ->assertForbidden();
    }

    public function test_player_is_forbidden(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $this->actingAs($player)
            ->getJson('/api/characteristics/equipment-bonus-table')
            ->assertForbidden();
    }

    public function test_game_master_receives_table_payload(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $response = $this->actingAs($gm)
            ->getJson('/api/characteristics/equipment-bonus-table');

        $response->assertOk();
        $response->assertJsonStructure([
            'bands' => [
                ['from', 'to', 'label'],
            ],
            'groups',
        ]);
        $this->assertCount(10, $response->json('bands'));
    }
}
