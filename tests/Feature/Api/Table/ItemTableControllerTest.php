<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ItemTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions / visibilité sont respectées
 * - La structure des données est correcte
 */
class ItemTableControllerTest extends TestCase
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
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ], $overrides);
    }

    public function test_format_entities_with_page_returns_pagination_meta(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Item::factory()->count(3)->create($this->playableAttrs());

        $response = $this->actingAs($admin)
            ->getJson('/api/tables/items?format=entities&limit=2&page=1');

        $response->assertOk();
        $pagination = $response->json('meta.pagination');
        $this->assertIsArray($pagination);
        $this->assertSame(3, $pagination['total']);
        $this->assertSame(2, $pagination['perPage']);
        $this->assertSame(1, $pagination['currentPage']);
        $this->assertSame(2, $pagination['lastPage']);
        $this->assertCount(2, $response->json('entities'));
    }

    /**
     * Test : Le format `entities` retourne les données brutes
     */
    public function test_format_entities_returns_raw_data(): void
    {
        $user = User::factory()->create();
        Item::factory()->create($this->playableAttrs([
            'name' => 'Test Item',
            'level' => '10',
            'rarity' => 1,
        ]));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/items?format=entities&limit=10');

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
                        'rarity',
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertEquals('entities', $data['meta']['format']);
        $this->assertArrayHasKey('entities', $data);
        $this->assertArrayNotHasKey('rows', $data);
        $this->assertCount(1, $data['entities']);
        $this->assertEquals('Test Item', $data['entities'][0]['name']);
    }

    /**
     * Test : Le format par défaut (`cells`) retourne les cellules formatées
     */
    public function test_format_cells_returns_formatted_cells(): void
    {
        $user = User::factory()->create();
        Item::factory()->create($this->playableAttrs([
            'name' => 'Test Item',
            'level' => '10',
        ]));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/items?limit=10');

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
        Item::factory()->create($this->playableAttrs([
            'created_by' => $user->id,
        ]));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/items?format=entities&limit=10');

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
        Item::factory()->create($this->playableAttrs());

        $response = $this->actingAs($user)
            ->getJson('/api/tables/items?format=entities&limit=10');

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

        $draft = Item::factory()->create([
            'name' => 'Draft Secret',
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $playable = Item::factory()->create($this->playableAttrs([
            'name' => 'Playable Public',
            'created_by' => $author->id,
        ]));

        $guestResponse = $this->getJson('/api/tables/items?format=entities&limit=50');
        $guestResponse->assertOk();
        $guestIds = collect($guestResponse->json('entities'))->pluck('id')->all();
        $this->assertNotContains($draft->id, $guestIds);
        $this->assertContains($playable->id, $guestIds);

        $userResponse = $this->actingAs($viewer)
            ->getJson('/api/tables/items?format=entities&limit=50');
        $userResponse->assertOk();
        $userIds = collect($userResponse->json('entities'))->pluck('id')->all();
        $this->assertNotContains($draft->id, $userIds);
        $this->assertContains($playable->id, $userIds);
    }

    /**
     * Test : Le format `entities` gère la pagination/limite
     */
    public function test_entities_format_respects_limit(): void
    {
        $user = User::factory()->create();
        Item::factory()->count(15)->create($this->playableAttrs());

        $response = $this->actingAs($user)
            ->getJson('/api/tables/items?format=entities&limit=5');

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
        Item::factory()->create($this->playableAttrs(['name' => 'Sword']));
        Item::factory()->create($this->playableAttrs(['name' => 'Shield']));
        Item::factory()->create($this->playableAttrs(['name' => 'Bow']));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/items?format=entities&search=Sword&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, count($data['entities']));
        $this->assertTrue(
            collect($data['entities'])->contains(fn ($e) => str_contains($e['name'], 'Sword'))
        );
    }

    /**
     * Test : Le format `entities` gère le tri
     */
    public function test_entities_format_supports_sorting(): void
    {
        $user = User::factory()->create();
        Item::factory()->create($this->playableAttrs(['name' => 'Z Item', 'level' => '1']));
        Item::factory()->create($this->playableAttrs(['name' => 'A Item', 'level' => '10']));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/items?format=entities&sort=name&order=asc&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertCount(2, $data['entities']);
        $this->assertEquals('A Item', $data['entities'][0]['name']);
        $this->assertEquals('Z Item', $data['entities'][1]['name']);
    }
}
