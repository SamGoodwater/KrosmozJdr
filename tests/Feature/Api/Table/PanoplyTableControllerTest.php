<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\Type\ItemType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour PanoplyTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions sont respectées
 */
class PanoplyTableControllerTest extends TestCase
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
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'name' => 'Test Panoply',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=10');

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
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertEquals('entities', $data['meta']['format']);
        $this->assertArrayHasKey('entities', $data);
        $this->assertArrayNotHasKey('rows', $data);
        $this->assertCount(1, $data['entities']);
        $this->assertEquals('Test Panoply', $data['entities'][0]['name']);
    }

    /**
     * Test : Le format par défaut (`cells`) retourne les cellules formatées
     */
    public function test_format_cells_returns_formatted_cells(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'name' => 'Test Panoply',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?limit=10');

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
        $this->assertEquals('route', $data['rows'][0]['cells']['name']['type']);
    }

    /**
     * Test : Le format `entities` inclut les relations
     */
    public function test_entities_format_includes_relations(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $entity = $data['entities'][0];
        $this->assertArrayHasKey('createdBy', $entity);
        $this->assertNotNull($entity['createdBy']);
        $this->assertEquals($user->id, $entity['createdBy']['id']);
    }

    /**
     * Test : le format `entities` embarque les équipements (id, nom, image) pour la vue texte.
     */
    public function test_entities_format_includes_item_previews(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $item = Item::factory()->create([
            'name' => 'Coiffe du Bouftou',
            'image' => '/images/entity/items/bouftou.png',
            'created_by' => $user->id,
        ]);
        $panoply->items()->attach($item->id);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=10');

        $response->assertOk();

        $entity = collect($response->json('entities'))->firstWhere('id', $panoply->id);
        $this->assertIsArray($entity);
        $this->assertIsArray($entity['items'] ?? null);
        $this->assertCount(1, $entity['items']);
        $this->assertSame($item->id, $entity['items'][0]['id']);
        $this->assertSame('Coiffe du Bouftou', $entity['items'][0]['name']);
        $this->assertSame('/images/entity/items/bouftou.png', $entity['items'][0]['image']);
    }

    /**
     * Test : Le format `entities` respecte les permissions
     */
    public function test_entities_format_respects_permissions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Panoply::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=10');

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
        Panoply::factory()->count(10)->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=5');

        $response->assertOk();

        $data = $response->json();
        $this->assertLessThanOrEqual(5, count($data['entities']));
        $this->assertEquals(5, $data['meta']['query']['limit']);
    }

    public function test_filter_options_include_all_entity_states(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Panoply::factory()->create([
            'created_by' => $user->id,
            'state' => Panoply::STATE_RAW,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=10');

        $response->assertOk();
        $states = collect($response->json('meta.filterOptions.state'))->pluck('value')->all();
        $this->assertEqualsCanonicalizing(
            ['raw', 'draft', 'auto', 'playable', 'archived'],
            $states
        );
    }

    public function test_filters_by_items_count_range(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $small = Panoply::factory()->create([
            'created_by' => $user->id,
            'state' => Panoply::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $large = Panoply::factory()->create([
            'created_by' => $user->id,
            'state' => Panoply::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $itemA = Item::factory()->create([
            'created_by' => $user->id,
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $itemB = Item::factory()->create([
            'created_by' => $user->id,
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $itemC = Item::factory()->create([
            'created_by' => $user->id,
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $small->items()->attach($itemA->id);
        $large->items()->attach([$itemA->id, $itemB->id, $itemC->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=20&filters[items_count][min]=2&filters[items_count][max]=10');

        $response->assertOk();
        $ids = collect($response->json('entities'))->pluck('id')->all();
        $this->assertContains($large->id, $ids);
        $this->assertNotContains($small->id, $ids);
        $bounds = $response->json('meta.filterOptions.items_count');
        $this->assertIsArray($bounds);
        $this->assertArrayHasKey('min', $bounds);
        $this->assertArrayHasKey('max', $bounds);
    }

    public function test_filters_by_item_type_present_in_panoply(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $hatType = ItemType::factory()->create([
            'name' => 'CoiffeFiltrePano',
            'show_in_catalog' => true,
            'created_by' => $user->id,
        ]);
        $capeType = ItemType::factory()->create([
            'name' => 'CapeFiltrePano',
            'show_in_catalog' => true,
            'created_by' => $user->id,
        ]);
        $withHat = Panoply::factory()->create([
            'created_by' => $user->id,
            'state' => Panoply::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $withCape = Panoply::factory()->create([
            'created_by' => $user->id,
            'state' => Panoply::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $hat = Item::factory()->create([
            'created_by' => $user->id,
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'item_type_id' => $hatType->id,
        ]);
        $cape = Item::factory()->create([
            'created_by' => $user->id,
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'item_type_id' => $capeType->id,
        ]);
        $withHat->items()->attach($hat->id);
        $withCape->items()->attach($cape->id);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=20&filters[item_type_id][]='.$hatType->id);

        $response->assertOk();
        $ids = collect($response->json('entities'))->pluck('id')->all();
        $this->assertContains($withHat->id, $ids);
        $this->assertNotContains($withCape->id, $ids);
        $typeValues = collect($response->json('meta.filterOptions.item_type_id'))->pluck('value')->all();
        $this->assertContains((string) $hatType->id, $typeValues);
        $this->assertContains((string) $capeType->id, $typeValues);
    }

    public function test_filters_by_state(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $playable = Panoply::factory()->create([
            'created_by' => $user->id,
            'state' => Panoply::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $raw = Panoply::factory()->create([
            'created_by' => $user->id,
            'state' => Panoply::STATE_RAW,
            'read_level' => User::ROLE_GUEST,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/panoplies?format=entities&limit=20&filters[state][]=playable');

        $response->assertOk();
        $ids = collect($response->json('entities'))->pluck('id')->all();
        $this->assertContains($playable->id, $ids);
        $this->assertNotContains($raw->id, $ids);
    }
}
