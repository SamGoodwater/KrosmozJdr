<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Npc;
use App\Models\Entity\Creature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour NpcTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions sont respectées
 */
class NpcTableControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    /**
     * Fiches visibles d’un ROLE_USER : playable + lecture guest (comme les autres catalogues).
     *
     * @return array<string, mixed>
     */
    private function playableAttrs(array $overrides = []): array
    {
        return array_merge([
            'state' => Npc::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ], $overrides);
    }

    /**
     * Test : Le format `entities` retourne les données brutes
     */
    public function test_format_entities_returns_raw_data(): void
    {
        $user = User::factory()->create();
        $npc = Npc::factory()->create($this->playableAttrs(['age' => '25', 'size' => 'Moyen']));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=10');

        $response->assertOk()
            ->assertJsonStructure([
                'meta' => [
                    'entityType',
                    'query',
                    'capabilities',
                    'format',
                ],
                'entities' => [
                    '*' => [
                        'id',
                        'age',
                        'size',
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertEquals('entities', $data['meta']['format']);
        $this->assertArrayHasKey('entities', $data);
        $this->assertArrayNotHasKey('rows', $data);
        $this->assertCount(1, $data['entities']);
    }

    /**
     * Test : Le format par défaut (`cells`) retourne les cellules formatées
     */
    public function test_format_cells_returns_formatted_cells(): void
    {
        $user = User::factory()->create();
        $npc = Npc::factory()->create($this->playableAttrs());

        $response = $this->actingAs($user)
            ->getJson('/api/tables/npcs?limit=10');

        $response->assertOk()
            ->assertJsonStructure([
                'meta' => [
                    'entityType',
                    'query',
                    'capabilities',
                ],
                'rows' => [
                    '*' => [
                        'id',
                        'cells',
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('rows', $data);
        $this->assertArrayNotHasKey('entities', $data);
        $this->assertArrayHasKey('cells', $data['rows'][0]);
    }

    /**
     * Test : Le format `entities` inclut les relations
     */
    public function test_entities_format_includes_relations(): void
    {
        $user = User::factory()->create();
        $npc = Npc::factory()->create($this->playableAttrs());

        $response = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $entity = $data['entities'][0];
        $this->assertArrayHasKey('creature', $entity);
    }

    /**
     * Test : Le format `entities` respecte les permissions
     */
    public function test_entities_format_respects_permissions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Npc::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertArrayHasKey('capabilities', $data['meta']);
        $this->assertIsBool($data['meta']['capabilities']['viewAny']);
        $this->assertIsBool($data['meta']['capabilities']['updateAny']);
    }

    /**
     * Test : Le format `entities` gère la pagination/limite
     */
    public function test_entities_format_respects_limit(): void
    {
        $user = User::factory()->create();
        Npc::factory()->count(15)->create($this->playableAttrs());

        $response = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=5');

        $response->assertOk();

        $data = $response->json();
        $this->assertCount(5, $data['entities']);
        $this->assertEquals(5, $data['meta']['query']['limit']);
    }

    /**
     * PA créature : bornes min/max + filtre plage.
     */
    public function test_creature_pa_filter_uses_integer_range(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $low = Creature::factory()->create(['pa' => '3']);
        $high = Creature::factory()->create(['pa' => '10']);
        $lowNpc = Npc::factory()->create($this->playableAttrs(['creature_id' => $low->id]));
        $highNpc = Npc::factory()->create($this->playableAttrs(['creature_id' => $high->id]));

        $optionsResponse = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=20');
        $optionsResponse->assertOk();
        $paBounds = $optionsResponse->json('meta.filterOptions.creature_pa');
        $this->assertIsArray($paBounds);
        $this->assertArrayHasKey('min', $paBounds);
        $this->assertArrayHasKey('max', $paBounds);

        $filtered = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=20&filters[creature_pa][min]=8&filters[creature_pa][max]=12');
        $filtered->assertOk();
        $ids = collect($filtered->json('entities'))->pluck('id')->all();
        $this->assertContains($highNpc->id, $ids);
        $this->assertNotContains($lowNpc->id, $ids);
    }
}
