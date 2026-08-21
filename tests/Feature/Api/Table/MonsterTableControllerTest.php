<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\User;
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
        $this->withoutMiddleware(CheckRole::class);
    }

    /**
     * Test : Le format `entities` retourne les données brutes
     */
    public function test_format_entities_returns_raw_data(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
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
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
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
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create(['created_by' => $user->id]);
        $monster = Monster::factory()->create(['creature_id' => $creature->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $entity = $data['entities'][0];
        // Monster n'a pas createdBy directement, mais via creature
        $this->assertArrayHasKey('creature', $entity);
        $this->assertNotNull($entity['creature']);
        $this->assertArrayHasKey('spells', $entity['creature']);
        $this->assertIsArray($entity['creature']['spells']);
        $this->assertArrayHasKey('items', $entity['creature']);
        $this->assertIsArray($entity['creature']['items']);
        // Payload allégé : pas d’arbre effects / usages dans la table.
        foreach ($entity['creature']['spells'] as $spellRow) {
            $this->assertArrayNotHasKey('effects', $spellRow);
            $this->assertArrayNotHasKey('effect_usages_chips', $spellRow);
            $this->assertArrayNotHasKey('effect_usages_summary', $spellRow);
        }
        $this->assertArrayHasKey('acrobatie_mastery', $entity['creature']);
        $this->assertArrayHasKey('athletisme_mastery', $entity['creature']);
        $this->assertIsInt($entity['creature']['athletisme_mastery']);
    }

    /**
     * Test : le format `entities` inclut les équipements de créature (payload allégé).
     */
    public function test_entities_format_includes_creature_items(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create(['created_by' => $user->id]);
        $monster = Monster::factory()->create(['creature_id' => $creature->id]);
        $item = Item::factory()->create([
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'name' => 'Coiffe de test',
        ]);
        $creature->items()->attach($item->id, ['quantity' => 2]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=10');

        $response->assertOk();

        $entity = collect($response->json('entities'))->firstWhere('id', $monster->id);
        $this->assertIsArray($entity);
        $this->assertArrayHasKey('items', $entity['creature']);
        $this->assertCount(1, $entity['creature']['items']);
        $this->assertSame($item->id, $entity['creature']['items'][0]['id']);
        $this->assertSame('Coiffe de test', $entity['creature']['items'][0]['name']);
        $this->assertSame(2, (int) $entity['creature']['items'][0]['pivot']['quantity']);
        $this->assertArrayHasKey('image', $entity['creature']['items'][0]);
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
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
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
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature1 = Creature::factory()->create(['name' => 'Dragon']);
        $creature2 = Creature::factory()->create(['name' => 'Goblin']);
        $creature3 = Creature::factory()->create(['name' => 'Orc']);
        Monster::factory()->create(['creature_id' => $creature1->id]);
        Monster::factory()->create(['creature_id' => $creature2->id]);
        Monster::factory()->create(['creature_id' => $creature3->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&search=Dragon&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, count($data['entities']));
        $this->assertTrue(
            collect($data['entities'])->contains(fn ($e) => $e['creature'] && str_contains($e['creature']['name'], 'Dragon')
            )
        );
    }

    /**
     * Test : Le format `entities` gère le tri
     */
    public function test_entities_format_supports_sorting(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature1 = Creature::factory()->create(['name' => 'Z Monster']);
        $creature2 = Creature::factory()->create(['name' => 'A Monster']);
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

    public function test_non_admin_can_sort_playable_monsters_by_creature_name(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $creatureZ = Creature::factory()->create(['name' => 'Zibouya']);
        $creatureA = Creature::factory()->create(['name' => 'Abraknyde']);
        Monster::factory()->create([
            'creature_id' => $creatureZ->id,
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        Monster::factory()->create([
            'creature_id' => $creatureA->id,
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&sort=creature_name&order=asc&limit=10');

        $response->assertOk();
        $names = collect($response->json('entities'))->pluck('creature.name')->all();
        $this->assertSame(['Abraknyde', 'Zibouya'], $names);
    }
}
