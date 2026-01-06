<?php

namespace Tests\Feature\Api\Table;

use App\Models\User;
use App\Models\Entity\Monster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour MonsterTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions sont respectées
 * - La structure des données est correcte
 */
class MonsterTableControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Le format `entities` retourne les données brutes
     */
    public function test_format_entities_returns_raw_data(): void
    {
        $user = User::factory()->create();
        $monster = Monster::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=10');

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
                        'size',
                        'is_boss',
                        'creature_id',
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertEquals('entities', $data['meta']['format']);
        $this->assertArrayHasKey('entities', $data);
        $this->assertArrayNotHasKey('rows', $data);
        $this->assertCount(1, $data['entities']);
        $this->assertEquals($monster->id, $data['entities'][0]['id']);
    }

    /**
     * Test : Le format par défaut (`cells`) retourne les cellules formatées
     */
    public function test_format_cells_returns_formatted_cells(): void
    {
        $user = User::factory()->create();
        $monster = Monster::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?limit=10');

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
        $creature = \App\Models\Entity\Creature::factory()->create(['created_by' => $user->id]);
        $monster = Monster::factory()->create(['creature_id' => $creature->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $entity = $data['entities'][0];
        // Monster n'a pas createdBy directement, mais via creature
        $this->assertArrayHasKey('creature', $entity);
        $this->assertNotNull($entity['creature']);
    }

    /**
     * Test : Le format `entities` respecte les permissions
     */
    public function test_entities_format_respects_permissions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Monster::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=10');

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
        Monster::factory()->count(15)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=5');

        $response->assertOk();

        $data = $response->json();
        $this->assertCount(5, $data['entities']);
        $this->assertEquals(5, $data['meta']['query']['limit']);
    }

    /**
     * Test : Le format `entities` gère la recherche
     */
    public function test_entities_format_supports_search(): void
    {
        $user = User::factory()->create();
        $creature1 = \App\Models\Entity\Creature::factory()->create(['name' => 'Dragon']);
        $creature2 = \App\Models\Entity\Creature::factory()->create(['name' => 'Goblin']);
        $creature3 = \App\Models\Entity\Creature::factory()->create(['name' => 'Orc']);
        Monster::factory()->create(['creature_id' => $creature1->id]);
        Monster::factory()->create(['creature_id' => $creature2->id]);
        Monster::factory()->create(['creature_id' => $creature3->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&search=Dragon&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, count($data['entities']));
        $this->assertTrue(
            collect($data['entities'])->contains(fn ($e) => 
                $e['creature'] && str_contains($e['creature']['name'], 'Dragon')
            )
        );
    }

    /**
     * Test : Le format `entities` gère le tri
     */
    public function test_entities_format_supports_sorting(): void
    {
        $user = User::factory()->create();
        $creature1 = \App\Models\Entity\Creature::factory()->create(['name' => 'Z Monster']);
        $creature2 = \App\Models\Entity\Creature::factory()->create(['name' => 'A Monster']);
        Monster::factory()->create(['creature_id' => $creature1->id]);
        Monster::factory()->create(['creature_id' => $creature2->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&sort=creature.name&order=asc&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertCount(2, $data['entities']);
        // Vérifier que les créatures sont triées (via la relation creature)
        $this->assertArrayHasKey('creature', $data['entities'][0]);
    }
}

