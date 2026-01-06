<?php

namespace Tests\Feature\Api\Table;

use App\Models\User;
use App\Models\Entity\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ScenarioTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions sont respectées
 */
class ScenarioTableControllerTest extends TestCase
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
        $scenario = Scenario::factory()->create([
            'name' => 'Test Scenario',
            'state' => 1,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/scenarios?format=entities&limit=10');

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
                        'name',
                        'state',
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertEquals('entities', $data['meta']['format']);
        $this->assertArrayHasKey('entities', $data);
        $this->assertArrayNotHasKey('rows', $data);
        $this->assertCount(1, $data['entities']);
        $this->assertEquals('Test Scenario', $data['entities'][0]['name']);
    }

    /**
     * Test : Le format par défaut (`cells`) retourne les cellules formatées
     */
    public function test_format_cells_returns_formatted_cells(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::factory()->create([
            'name' => 'Test Scenario',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/scenarios?limit=10');

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
                        'cells' => [
                            'name',
                        ],
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('rows', $data);
        $this->assertArrayNotHasKey('entities', $data);
        $this->assertArrayHasKey('cells', $data['rows'][0]);
        // Le type peut être 'route' ou autre selon l'implémentation
        $this->assertArrayHasKey('type', $data['rows'][0]['cells']['name']);
    }

    /**
     * Test : Le format `entities` inclut les relations
     */
    public function test_entities_format_includes_relations(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/scenarios?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $entity = $data['entities'][0];
        $this->assertArrayHasKey('createdBy', $entity);
        $this->assertNotNull($entity['createdBy']);
        $this->assertEquals($user->id, $entity['createdBy']['id']);
    }

    /**
     * Test : Le format `entities` respecte les permissions
     */
    public function test_entities_format_respects_permissions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Scenario::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/scenarios?format=entities&limit=10');

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
        Scenario::factory()->count(15)->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/scenarios?format=entities&limit=5');

        $response->assertOk();

        $data = $response->json();
        $this->assertCount(5, $data['entities']);
        $this->assertEquals(5, $data['meta']['query']['limit']);
    }
}
