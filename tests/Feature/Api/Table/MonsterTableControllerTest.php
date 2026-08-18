<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
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
        // Payload sorts liés : chips d’effets pour la vue minimale, pas l’arbre `effects`.
        foreach ($entity['creature']['spells'] as $spellRow) {
            $this->assertArrayNotHasKey('effects', $spellRow);
            $this->assertArrayHasKey('effect_usages_chips', $spellRow);
            $this->assertArrayHasKey('effect_usages_summary', $spellRow);
        }
        $this->assertArrayHasKey('acrobatie_mastery', $entity['creature']);
        $this->assertArrayHasKey('athletisme_mastery', $entity['creature']);
        $this->assertIsInt($entity['creature']['athletisme_mastery']);
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

    public function test_creature_level_filter_and_sort(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $low = Creature::factory()->create(['name' => 'Low', 'level' => '1']);
        $high = Creature::factory()->create(['name' => 'High', 'level' => '50']);
        Monster::factory()->create(['creature_id' => $low->id]);
        Monster::factory()->create(['creature_id' => $high->id]);

        $filterResponse = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=20&filters[creature_level][]=50');
        $filterResponse->assertOk();
        $filterNames = collect($filterResponse->json('entities'))->pluck('creature.name')->all();
        $this->assertSame(['High'], $filterNames);

        $sortResponse = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=20&sort=creature_level&order=asc');
        $sortResponse->assertOk();
        $sortNames = collect($sortResponse->json('entities'))->pluck('creature.name')->all();
        $this->assertSame(['Low', 'High'], $sortNames);
    }

    public function test_entities_format_nested_spells_include_effect_chips(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create(['created_by' => $user->id]);
        $spell = Spell::factory()->create([
            'name' => 'Maxquale',
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'created_by' => $user->id,
        ]);
        $creature->spells()->attach($spell->id);
        Monster::factory()->create(['creature_id' => $creature->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/monsters?format=entities&limit=10');

        $response->assertOk();
        $spellRow = collect($response->json('entities.0.creature.spells'))
            ->firstWhere('id', $spell->id);
        $this->assertIsArray($spellRow);
        $this->assertSame('Maxquale', $spellRow['name']);
        $this->assertArrayHasKey('effect_usages_chips', $spellRow);
        $this->assertIsArray($spellRow['effect_usages_chips']);
        $this->assertArrayHasKey('effect_usages_summary', $spellRow);
        $this->assertArrayNotHasKey('effects', $spellRow);
        $this->assertArrayHasKey('resolution_mode', $spellRow);
    }
}
