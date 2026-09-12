<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Npc;
use App\Models\Entity\Shop;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Support\Npc\NpcRole;
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
        $npc = Npc::factory()->create($this->playableAttrs(['age' => '25', 'size' => 2]));

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

    public function test_non_admin_can_sort_playable_npcs_by_creature_name(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $creatureZ = Creature::factory()->create(['name' => 'Zibouya']);
        $creatureA = Creature::factory()->create(['name' => 'Abraknyde']);
        Npc::factory()->create($this->playableAttrs(['creature_id' => $creatureZ->id]));
        Npc::factory()->create($this->playableAttrs(['creature_id' => $creatureA->id]));

        $response = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&sort=creature_name&order=asc&limit=10');

        $response->assertOk();
        $names = collect($response->json('entities'))->pluck('creature.name')->all();
        $this->assertSame(['Abraknyde', 'Zibouya'], $names);
    }

    public function test_creature_hostility_filter_and_sort(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $friendly = Creature::factory()->create(['name' => 'Ami', 'hostility' => 0]);
        $hostile = Creature::factory()->create(['name' => 'Ennemi', 'hostility' => 3]);
        Npc::factory()->create($this->playableAttrs(['creature_id' => $friendly->id]));
        Npc::factory()->create($this->playableAttrs(['creature_id' => $hostile->id]));

        $filterResponse = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=20&filters[creature_hostility][]=3');
        $filterResponse->assertOk();
        $filterNames = collect($filterResponse->json('entities'))->pluck('creature.name')->all();
        $this->assertSame(['Ennemi'], $filterNames);

        $sortResponse = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=20&sort=creature_hostility&order=asc');
        $sortResponse->assertOk();
        $sortNames = collect($sortResponse->json('entities'))->pluck('creature.name')->all();
        $this->assertSame(['Ami', 'Ennemi'], $sortNames);
    }

    public function test_filter_options_include_creature_combat_bounds(): void
    {
        $user = User::factory()->create();
        Npc::factory()->create($this->playableAttrs());

        $response = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=1');

        $response->assertOk();
        $options = $response->json('meta.filterOptions');
        $this->assertArrayHasKey('creature_hostility', $options);
        $this->assertArrayHasKey('creature_pa', $options);
        $this->assertArrayHasKey('creature_life', $options);
    }

    public function test_creature_level_range_and_npc_role_filters(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $low = Creature::factory()->create(['name' => 'Low', 'level' => '1']);
        $mid = Creature::factory()->create(['name' => 'Mid', 'level' => '50']);
        $high = Creature::factory()->create(['name' => 'High', 'level' => '200']);
        Npc::factory()->create($this->playableAttrs([
            'creature_id' => $low->id,
            'npc_role' => NpcRole::GUARD,
        ]));
        Npc::factory()->create($this->playableAttrs([
            'creature_id' => $mid->id,
            'npc_role' => NpcRole::MERCHANT,
        ]));
        Npc::factory()->create($this->playableAttrs([
            'creature_id' => $high->id,
            'npc_role' => NpcRole::GUARD,
            'state' => Npc::STATE_DRAFT,
        ]));

        $rangeResponse = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=20&filters[creature_level][min]=1&filters[creature_level][max]=50');
        $rangeResponse->assertOk();
        $rangeNames = collect($rangeResponse->json('entities'))->pluck('creature.name')->sort()->values()->all();
        $this->assertSame(['Low', 'Mid'], $rangeNames);

        $roleResponse = $this->actingAs($user)
            ->getJson('/api/tables/npcs?format=entities&limit=20&filters[npc_role][]=merchant');
        $roleResponse->assertOk();
        $roleNames = collect($roleResponse->json('entities'))->pluck('creature.name')->all();
        $this->assertSame(['Mid'], $roleNames);

        $bounds = $rangeResponse->json('meta.filterOptions.creature_level');
        $this->assertIsArray($bounds);
        $this->assertArrayHasKey('min', $bounds);
        $this->assertArrayHasKey('max', $bounds);
    }

    public function test_nested_spells_hide_foreign_draft_from_player(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $playableSpell = Spell::factory()->create([
            'name' => 'Coup Public',
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpell = Spell::factory()->create([
            'name' => 'Mécanique Secrète',
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $creature->spells()->attach([$playableSpell->id, $draftSpell->id]);
        Npc::factory()->create($this->playableAttrs(['creature_id' => $creature->id]));

        $response = $this->actingAs($player)
            ->getJson('/api/tables/npcs?format=entities&limit=10');

        $response->assertOk();
        $spellIds = collect($response->json('entities.0.creature.spells'))->pluck('id')->all();
        $this->assertContains($playableSpell->id, $spellIds);
        $this->assertNotContains($draftSpell->id, $spellIds);
    }

    /**
     * Un joueur qui voit un PNJ jouable ne doit pas recevoir les traits brouillon liés.
     */
    public function test_nested_creature_traits_hide_foreign_draft_from_player(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $playableTrait = CreatureTrait::factory()->create([
            'name' => 'Trait Public PNJ',
            'state' => CreatureTrait::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftTrait = CreatureTrait::factory()->create([
            'name' => 'Trait Secret PNJ XYZ',
            'state' => CreatureTrait::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $creature->creatureTraits()->attach([$playableTrait->id, $draftTrait->id]);
        Npc::factory()->create($this->playableAttrs(['creature_id' => $creature->id]));

        $response = $this->actingAs($player)
            ->getJson('/api/tables/npcs?format=entities&limit=10');

        $response->assertOk();
        $traitIds = collect($response->json('entities.0.creature.creatureTraits'))->pluck('id')->all();
        $this->assertContains($playableTrait->id, $traitIds);
        $this->assertNotContains($draftTrait->id, $traitIds);
    }

    public function test_has_shop_ignores_foreign_draft_shop_for_player(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $npc = Npc::factory()->create($this->playableAttrs(['creature_id' => $creature->id]));
        Shop::factory()->create([
            'name' => 'Réserve secrète',
            'npc_id' => $npc->id,
            'state' => Shop::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);

        $response = $this->actingAs($player)
            ->getJson('/api/tables/npcs?format=entities&limit=10');

        $response->assertOk();
        $this->assertFalse((bool) $response->json('entities.0.has_shop'));
    }

    public function test_nested_breed_and_specialization_hide_foreign_draft_from_player(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $creature = Creature::factory()->create(['created_by' => $author->id, 'name' => 'Garde public']);
        $draftBreed = Breed::factory()->create([
            'name' => 'Classe Catalogue Secrète',
            'state' => Breed::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpec = Specialization::factory()->create([
            'name' => 'Spé Catalogue Secrète',
            'state' => Specialization::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        Npc::factory()->create($this->playableAttrs([
            'creature_id' => $creature->id,
            'breed_id' => $draftBreed->id,
            'specialization_id' => $draftSpec->id,
        ]));

        $playerResponse = $this->actingAs($player)
            ->getJson('/api/tables/npcs?format=entities&limit=10');
        $playerResponse->assertOk();
        $playerEntity = $playerResponse->json('entities.0');
        $this->assertNull($playerEntity['breed']);
        $this->assertNull($playerEntity['specialization']);

        $authorResponse = $this->actingAs($author)
            ->getJson('/api/tables/npcs?format=entities&limit=10');
        $authorResponse->assertOk();
        $authorEntity = $authorResponse->json('entities.0');
        $this->assertSame($draftBreed->id, $authorEntity['breed']['id']);
        $this->assertSame('Classe Catalogue Secrète', $authorEntity['breed']['name']);
        $this->assertSame($draftSpec->id, $authorEntity['specialization']['id']);
        $this->assertSame('Spé Catalogue Secrète', $authorEntity['specialization']['name']);
    }
}
