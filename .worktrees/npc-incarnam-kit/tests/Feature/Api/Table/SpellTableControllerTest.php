<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour SpellTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions / visibilité par ligne sont respectées
 * - La structure des données est correcte
 */
class SpellTableControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    /**
     * @return array<string, mixed>
     */
    private function playableAttrs(array $overrides = []): array
    {
        return array_merge([
            'state' => Spell::STATE_PLAYABLE,
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
        Spell::factory()->create($this->playableAttrs([
            'name' => 'Test Spell',
            'level' => '10',
            'pa' => '3',
            'po_min' => '2',
            'po_max' => '2',
        ]));

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
        Spell::factory()->create($this->playableAttrs([
            'name' => 'Test Spell',
            'level' => '10',
        ]));

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
        Spell::factory()->create($this->playableAttrs([
            'created_by' => $user->id,
        ]));

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
        Spell::factory()->create($this->playableAttrs());

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertArrayHasKey('capabilities', $data['meta']);
        $this->assertIsBool($data['meta']['capabilities']['viewAny']);
        $this->assertIsBool($data['meta']['capabilities']['updateAny']);
    }

    /**
     * Invité / user : brouillon d’autrui masqué ; playable visible.
     */
    public function test_table_hides_foreign_draft_from_guest_and_user(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $viewer = User::factory()->create(['role' => User::ROLE_USER]);

        $draft = Spell::factory()->create([
            'name' => 'Draft Secret',
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $playable = Spell::factory()->create($this->playableAttrs([
            'name' => 'Playable Public',
            'created_by' => $author->id,
        ]));

        $guestResponse = $this->getJson('/api/tables/spells?format=entities&limit=50');
        $guestResponse->assertOk();
        $guestIds = collect($guestResponse->json('entities'))->pluck('id')->all();
        $this->assertNotContains($draft->id, $guestIds);
        $this->assertContains($playable->id, $guestIds);

        $userResponse = $this->actingAs($viewer)
            ->getJson('/api/tables/spells?format=entities&limit=50');
        $userResponse->assertOk();
        $userIds = collect($userResponse->json('entities'))->pluck('id')->all();
        $this->assertNotContains($draft->id, $userIds);
        $this->assertContains($playable->id, $userIds);
    }

    /**
     * Auteur : voit son propre brouillon dans la table.
     */
    public function test_table_shows_own_draft_to_author(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_USER]);
        $draft = Spell::factory()->create([
            'name' => 'Mon brouillon',
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);

        $response = $this->actingAs($author)
            ->getJson('/api/tables/spells?format=entities&limit=50');

        $response->assertOk();
        $ids = collect($response->json('entities'))->pluck('id')->all();
        $this->assertContains($draft->id, $ids);
    }

    /**
     * Test : Le format `entities` gère la pagination/limite
     */
    public function test_entities_format_respects_limit(): void
    {
        $user = User::factory()->create();
        Spell::factory()->count(15)->create($this->playableAttrs());

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
        Spell::factory()->create($this->playableAttrs(['name' => 'Fireball']));
        Spell::factory()->create($this->playableAttrs(['name' => 'Ice Bolt']));
        Spell::factory()->create($this->playableAttrs(['name' => 'Lightning']));

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
        Spell::factory()->create($this->playableAttrs(['name' => 'Z Spell', 'level' => '1']));
        Spell::factory()->create($this->playableAttrs(['name' => 'A Spell', 'level' => '10']));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&sort=name&order=asc&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertCount(2, $data['entities']);
        $this->assertEquals('A Spell', $data['entities'][0]['name']);
        $this->assertEquals('Z Spell', $data['entities'][1]['name']);
    }

    /**
     * Tri multi : paramètres sorts[i][field] + sorts[i][dir]
     */
    public function test_multi_sort_sorts_parameter_applies_order(): void
    {
        $user = User::factory()->create();
        Spell::factory()->create($this->playableAttrs(['name' => 'B', 'level' => '5']));
        Spell::factory()->create($this->playableAttrs(['name' => 'A', 'level' => '5']));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&limit=10&sorts[0][field]=level&sorts[0][dir]=asc&sorts[1][field]=name&sorts[1][dir]=asc');

        $response->assertOk();
        $data = $response->json();
        $this->assertCount(2, $data['entities']);
        $this->assertEquals('A', $data['entities'][0]['name']);
        $this->assertEquals('B', $data['entities'][1]['name']);
    }

    /**
     * Tri `po` : mappe vers po_min / po_max (pas d’erreur SQL sur accessor).
     */
    public function test_sort_by_po_uses_po_min_max_columns(): void
    {
        $user = User::factory()->create();
        Spell::factory()->create($this->playableAttrs([
            'name' => 'Far',
            'po_min' => '5',
            'po_max' => '8',
        ]));
        Spell::factory()->create($this->playableAttrs([
            'name' => 'Near',
            'po_min' => '0',
            'po_max' => '1',
        ]));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&sort=po&order=asc&limit=10');

        $response->assertOk();
        $names = collect($response->json('entities'))->pluck('name')->all();
        $this->assertSame(['Near', 'Far'], $names);
    }

    public function test_types_filter_matches_related_spell_types(): void
    {
        $user = User::factory()->create();
        $type = \App\Models\Type\SpellType::factory()->create([
            'name' => 'FilterTypeUnique',
            'state' => \App\Models\Type\SpellType::STATE_PLAYABLE,
        ]);
        $with = Spell::factory()->create($this->playableAttrs(['name' => 'Typed']));
        Spell::factory()->create($this->playableAttrs(['name' => 'Untyped']));
        $with->spellTypes()->attach($type->id);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&limit=20&filters[types][]='.$type->id);

        $response->assertOk();
        $names = collect($response->json('entities'))->pluck('name')->all();
        $this->assertSame(['Typed'], $names);
    }

    public function test_whitelist_returns_only_requested_spell(): void
    {
        $user = User::factory()->create();
        $keep = Spell::factory()->create($this->playableAttrs(['name' => 'Keep Spell']));
        Spell::factory()->create($this->playableAttrs(['name' => 'Other Spell']));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/spells?format=entities&limit=20&whitelist[]='.$keep->id);

        $response->assertOk();
        $this->assertCount(1, $response->json('entities'));
        $this->assertSame($keep->id, $response->json('entities.0.id'));
        $this->assertArrayHasKey('effects_definitions', $response->json('entities.0'));
    }
}
