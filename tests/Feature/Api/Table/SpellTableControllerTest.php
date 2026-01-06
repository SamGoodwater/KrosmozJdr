<?php

namespace Tests\Feature\Api\Table;

use App\Models\User;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour SpellTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions sont respectées
 * - La structure des données est correcte
 */
class SpellTableControllerTest extends TestCase
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
        $spell = Spell::factory()->create([
            'name' => 'Test Spell',
            'level' => '10',
            'pa' => '3',
            'po' => '2',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&limit=10');

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
                        'level',
                        'pa',
                        'po',
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertEquals('entities', $data['meta']['format']);
        $this->assertArrayHasKey('entities', $data);
        $this->assertArrayNotHasKey('rows', $data);
        $this->assertCount(1, $data['entities']);
        $this->assertEquals('Test Spell', $data['entities'][0]['name']);
    }

    /**
     * Test : Le format par défaut (`cells`) retourne les cellules formatées
     */
    public function test_format_cells_returns_formatted_cells(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'name' => 'Test Spell',
            'level' => '10',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?limit=10');

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
                            'level',
                        ],
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('rows', $data);
        $this->assertArrayNotHasKey('entities', $data);
        $this->assertArrayHasKey('cells', $data['rows'][0]);
        $this->assertEquals('route', $data['rows'][0]['cells']['name']['type']);
    }

    /**
     * Test : Le format `entities` inclut les relations
     */
    public function test_entities_format_includes_relations(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&limit=10');

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
        Spell::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&limit=10');

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
        Spell::factory()->count(15)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&limit=5');

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
        Spell::factory()->create(['name' => 'Fireball']);
        Spell::factory()->create(['name' => 'Ice Bolt']);
        Spell::factory()->create(['name' => 'Lightning']);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&search=Fire&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, count($data['entities']));
        $this->assertTrue(
            collect($data['entities'])->contains(fn ($e) => str_contains($e['name'], 'Fire'))
        );
    }

    /**
     * Test : Le format `entities` gère le tri
     */
    public function test_entities_format_supports_sorting(): void
    {
        $user = User::factory()->create();
        Spell::factory()->create(['name' => 'Z Spell', 'level' => '1']);
        Spell::factory()->create(['name' => 'A Spell', 'level' => '10']);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&sort=name&order=asc&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertCount(2, $data['entities']);
        $this->assertEquals('A Spell', $data['entities'][0]['name']);
        $this->assertEquals('Z Spell', $data['entities'][1]['name']);
    }
}

