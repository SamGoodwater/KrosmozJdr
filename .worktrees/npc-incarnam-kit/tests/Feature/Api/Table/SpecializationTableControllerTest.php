<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Capability;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour SpecializationTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions sont respectées
 */
class SpecializationTableControllerTest extends TestCase
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
        $specialization = Specialization::factory()->create([
            'name' => 'Test Specialization',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/specializations?format=entities&limit=10');

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
        $this->assertEquals('Test Specialization', $data['entities'][0]['name']);
    }

    /**
     * Test : Le format par défaut (`cells`) retourne les cellules formatées
     */
    public function test_format_cells_returns_formatted_cells(): void
    {
        $user = User::factory()->create();
        $specialization = Specialization::factory()->create([
            'name' => 'Test Specialization',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/specializations?limit=10');

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
        $specialization = Specialization::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/specializations?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $entity = $data['entities'][0];
        $this->assertArrayHasKey('createdBy', $entity);
        $this->assertNotNull($entity['createdBy']);
        $this->assertEquals($user->id, $entity['createdBy']['id']);
    }

    /**
     * Un joueur ne doit pas voir les sorts / capacités brouillon via une spécialisation jouable.
     */
    public function test_format_entities_hides_draft_nested_spells_and_capabilities_from_players(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $spec = Specialization::factory()->create([
            'name' => 'Voie Jouable',
            'state' => Specialization::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);

        $playableSpell = Spell::factory()->create([
            'name' => 'Sort public',
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpell = Spell::factory()->create([
            'name' => 'Sort secret WIP',
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $spec->spells()->attach($playableSpell->id, ['level' => 1]);
        $spec->spells()->attach($draftSpell->id, ['level' => 1]);

        $playableCap = Capability::factory()->create([
            'name' => 'Passif public',
            'state' => Capability::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftCap = Capability::factory()->create([
            'name' => 'Passif brouillon',
            'state' => Capability::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $spec->capabilities()->attach([
            $playableCap->id => ['level' => 1],
            $draftCap->id => ['level' => 1],
        ]);

        $playerResponse = $this->actingAs($player)
            ->getJson('/api/tables/specializations?format=entities&limit=10');

        $playerResponse->assertOk();
        $playerEntity = collect($playerResponse->json('entities'))->firstWhere('id', $spec->id);
        $this->assertNotNull($playerEntity);
        $this->assertSame(1, $playerEntity['spells_count']);
        $this->assertEqualsCanonicalizing(['Sort public'], collect($playerEntity['spells'])->pluck('name')->all());
        $this->assertEqualsCanonicalizing(['Passif public'], collect($playerEntity['capabilities'])->pluck('name')->all());

        $adminResponse = $this->actingAs($author)
            ->getJson('/api/tables/specializations?format=entities&limit=10');

        $adminResponse->assertOk();
        $adminEntity = collect($adminResponse->json('entities'))->firstWhere('id', $spec->id);
        $this->assertNotNull($adminEntity);
        $this->assertSame(2, $adminEntity['spells_count']);
        $this->assertEqualsCanonicalizing(
            ['Sort public', 'Sort secret WIP'],
            collect($adminEntity['spells'])->pluck('name')->all()
        );
        $this->assertEqualsCanonicalizing(
            ['Passif public', 'Passif brouillon'],
            collect($adminEntity['capabilities'])->pluck('name')->all()
        );
    }

    /**
     * Test : Le format `entities` respecte les permissions
     */
    public function test_entities_format_respects_permissions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Specialization::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/specializations?format=entities&limit=10');

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
        // Créer seulement 7 entités pour éviter les collisions de valeurs uniques
        for ($i = 0; $i < 7; $i++) {
            Specialization::factory()->create([
                'created_by' => $user->id,
                'name' => "Specialization Test {$i}",
            ]);
        }

        $response = $this->actingAs($user)
            ->getJson('/api/tables/specializations?format=entities&limit=5');

        $response->assertOk();

        $data = $response->json();
        $this->assertLessThanOrEqual(5, count($data['entities']));
        $this->assertEquals(5, $data['meta']['query']['limit']);
    }
}
