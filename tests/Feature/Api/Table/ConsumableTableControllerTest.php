<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Consumable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Filtre bonus (picked-range) sur le catalogue consommables.
 */
final class ConsumableTableControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function playableAttrs(array $overrides = []): array
    {
        return array_merge([
            'state' => Consumable::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ], $overrides);
    }

    public function test_bonus_filter_keeps_consumables_with_characteristic_in_range(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $strong = Consumable::factory()->create($this->playableAttrs([
            'name' => 'Potion Force',
            'bonus' => json_encode(['life_points_restore' => 10], JSON_THROW_ON_ERROR),
            'effect' => 'Restaure 10 PV. Hors combat uniquement.',
        ]));
        $weak = Consumable::factory()->create($this->playableAttrs([
            'name' => 'Potion Faible',
            'bonus' => json_encode(['life_points_restore' => 2], JSON_THROW_ON_ERROR),
            'effect' => 'Restaure 2 PV. Hors combat uniquement.',
        ]));
        $other = Consumable::factory()->create($this->playableAttrs([
            'name' => 'Potion Bouclier',
            'bonus' => json_encode(['shield_points' => 10], JSON_THROW_ON_ERROR),
            'effect' => '+10 points de bouclier.',
        ]));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/consumables?format=entities&limit=20&filters[bonus][life_points_restore][min]=5');

        $response->assertOk();
        $ids = collect($response->json('entities'))->pluck('id')->all();
        $this->assertContains($strong->id, $ids);
        $this->assertNotContains($weak->id, $ids);
        $this->assertNotContains($other->id, $ids);
        $this->assertIsArray($response->json('meta.filterOptions.bonus'));
    }
}
