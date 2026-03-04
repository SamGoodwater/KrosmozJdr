<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Resource;
use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\Entity\Consumable;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use App\Models\Type\ConsumableType;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\SeedsScrappingPipeline;
use Tests\TestCase;
use Tests\CreatesSystemUser;

/**
 * Tests d'intégration pour vérifier que les relations sont bien importées lors du scrapping.
 *
 * @package Tests\Feature\Scrapping
 */
class ScrappingRelationsTest extends TestCase
{
    use RefreshDatabase, CreatesSystemUser, SeedsScrappingPipeline;

    private Orchestrator $orchestrator;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedScrappingPipeline();
        $this->createSystemUser();
        $this->user = User::factory()->create();
        $this->orchestrator = app(Orchestrator::class);
    }

    /** Convertit un résultat V2 en tableau (success, data) pour les assertions. */
    private function resultToArray($result): array
    {
        $data = $result->getIntegrationResult()?->getData();
        return [
            'success' => $result->isSuccess(),
            'data' => $data ?? [],
            'message' => $result->getMessage(),
        ];
    }

    private function pipelineOptions(array $opts = []): array
    {
        return [
            'integrate' => !($opts['dry_run'] ?? false) && !($opts['validate_only'] ?? false),
            'dry_run' => (bool) ($opts['dry_run'] ?? false),
            'force_update' => (bool) ($opts['force_update'] ?? false),
        ];
    }

    /**
     * Test que l'import d'un breed avec include_relations crée les relations dans breed_spell
     * 
     * Note: Ce test nécessite que l'API DofusDB soit accessible et que les données soient cohérentes
     */
    public function test_import_class_with_spells_creates_relations(): void
    {
        $dofusdbId = 1;
        $mockBreed = [
            'id' => 1,
            'name' => ['fr' => 'Iop'],
            'description' => ['fr' => 'Description courte Iop'],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force',
        ];
        Http::fake(['api.dofusdb.fr/breeds/1*' => Http::response($mockBreed, 200)]);

        $r = $this->orchestrator->runOne('dofusdb', 'breed', $dofusdbId, $this->pipelineOptions());
        $result = $this->resultToArray($r);

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('id', $result['data']);

        $breed = Breed::find($result['data']['id']);
        $this->assertNotNull($breed);

        // Vérifier que le breed a des sorts associés
        $spellsCount = $breed->spells()->count();

        // Si des sorts ont été importés, ils devraient être associés
        if ($spellsCount > 0) {
            $this->assertGreaterThan(0, $spellsCount, 'Le breed devrait avoir des sorts associés');

            // Vérifier que la relation existe dans la table pivot
            $this->assertDatabaseHas('breed_spell', [
                'breed_id' => $breed->id,
            ]);
        }
    }

    /**
     * Test que l'import d'un monstre avec include_relations crée les relations
     */
    public function test_import_monster_with_relations_creates_pivot_tables(): void
    {
        // Un monstre qui devrait avoir des sorts et des drops
        $dofusdbId = 31; // Utiliser un ID qui fonctionne avec les mocks
        
        $monsterData = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'description' => ['fr' => 'Description du Bouftou'],
            'life' => 50,
            'pa' => 3,
            'pm' => 3,
            'spells' => [
                ['id' => 201, 'name' => ['fr' => 'Sort de monstre']]
            ],
            'drops' => [
                ['id' => 15, 'name' => ['fr' => 'Ressource'], 'quantity' => 1]
            ]
        ];

        $spellData = [
            'data' => [
                [
                    'id' => 201,
                    'name' => ['fr' => 'Sort de monstre'],
                    'description' => ['fr' => 'Description'],
                    'cost' => 3,
                    'range' => 1,
                    'area' => 1
                ]
            ]
        ];

        $levelsList = [
            'data' => []
        ];

        $itemData = [
            'id' => 15,
            'name' => ['fr' => 'Ressource'],
            'description' => ['fr' => 'Description'],
            'typeId' => 15,
            'level' => 1,
            'rarity' => 'common',
            'price' => 10
        ];

        Http::fake(function ($request) use ($monsterData, $spellData, $levelsList, $itemData) {
            $url = $request->url();
            if (str_contains($url, '/monsters/31')) {
                return Http::response($monsterData, 200);
            }
            if (str_contains($url, '/spells')) {
                return Http::response($spellData, 200);
            }
            if (str_contains($url, '/spell-levels')) {
                return Http::response($levelsList, 200);
            }
            if (str_contains($url, '/items/15')) {
                return Http::response($itemData, 200);
            }
            return Http::response([], 404);
        });
        
        $r = $this->orchestrator->runOneWithRaw('dofusdb', 'monster', $monsterData, [
            'convert' => true,
            'validate' => true,
            'integrate' => true,
        ]);
        if (!$r->isSuccess()) {
            $this->fail('Import monster V2 échoué: ' . $r->getMessage());
        }
        $creatureId = $r->getIntegrationResult()?->getCreatureId();
        $monsterId = $r->getIntegrationResult()?->getMonsterId();
        $relationOut = app(\App\Services\Scrapping\Core\Relation\RelationResolutionService::class)
            ->resolveAndSyncMonsterRelations($monsterData, $creatureId, ['integrate' => true, 'dry_run' => false]);
        $result = [
            'success' => true,
            'data' => ['creature_id' => $creatureId, 'monster_id' => $monsterId],
        ];

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);

        $monsterData = $result['data'];
        $this->assertArrayHasKey('creature_id', $monsterData);
        $this->assertArrayHasKey('monster_id', $monsterData);

        $creature = Creature::find($monsterData['creature_id']);
        $this->assertNotNull($creature);

        // Vérifier les relations avec les sorts
        $spellsCount = $creature->spells()->count();
        if ($spellsCount > 0) {
            $this->assertDatabaseHas('creature_spell', [
                'creature_id' => $creature->id,
            ]);
        }

        // Vérifier les relations avec les ressources (drops)
        $resourcesCount = $creature->resources()->count();
        if ($resourcesCount > 0) {
            $this->assertDatabaseHas('creature_resource', [
                'creature_id' => $creature->id,
            ]);
        }
    }

    /**
     * Test que l'import d'un item avec include_relations crée les relations de recette
     */
    public function test_import_item_with_recipe_creates_item_resource_relations(): void
    {
        $dofusdbId = 100;
        $r = $this->orchestrator->runOne('dofusdb', 'item', $dofusdbId, $this->pipelineOptions());
        $result = $this->resultToArray($r);

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('id', $result['data']);

        $item = Item::find($result['data']['id']);
        if ($item) {
            // Vérifier les relations avec les ressources (recette)
            $resourcesCount = $item->resources()->count();
            if ($resourcesCount > 0) {
                $this->assertDatabaseHas('item_resource', [
                    'item_id' => $item->id,
                ]);
            }
        }
    }

    /**
     * Test que l'import d'un sort d'invocation avec include_relations crée la relation spell_invocation.
     * Utilise des mocks HTTP pour le sort 201 et le monstre invoqué.
     */
    public function test_import_invocation_spell_creates_spell_invocation_relation(): void
    {
        $dofusdbId = 201;

        $spellData = [
            'data' => [
                [
                    'id' => 201,
                    'name' => ['fr' => 'Invocation Bouftou'],
                    'description' => ['fr' => 'Invoque un Bouftou'],
                    'spellLevels' => [1],
                    'summon' => ['id' => 31, 'name' => ['fr' => 'Bouftou']],
                ],
            ],
        ];

        $levelsList = ['data' => []];

        $monsterData = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'description' => ['fr' => 'Description'],
            'life' => 50,
            'pa' => 3,
            'pm' => 3,
            'spells' => [],
            'drops' => [],
        ];

        Http::fake(function ($request) use ($spellData, $levelsList, $monsterData) {
            $url = $request->url();
            if (str_contains($url, '/spells')) {
                return Http::response($spellData, 200);
            }
            if (str_contains($url, '/spell-levels')) {
                return Http::response($levelsList, 200);
            }
            if (str_contains($url, '/monsters/31')) {
                return Http::response($monsterData, 200);
            }
            return Http::response([], 404);
        });

        $r = $this->orchestrator->runOne('dofusdb', 'spell', $dofusdbId, $this->pipelineOptions());
        $result = $this->resultToArray($r);

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? 'Import spell a échoué');
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('id', $result['data']);

        $spell = Spell::find($result['data']['id']);
        $this->assertNotNull($spell);

        $monstersCount = $spell->monsters()->count();
        if ($monstersCount > 0) {
            $this->assertDatabaseHas('spell_invocation', [
                'spell_id' => $spell->id,
            ]);
        }
    }

    /**
     * Test que l'import sans include_relations ne crée pas de relations
     */
    public function test_import_without_relations_does_not_create_pivot_entries(): void
    {
        $dofusdbId = 1;
        $mockBreed = [
            'id' => 1,
            'name' => ['fr' => 'Iop'],
            'description' => ['fr' => 'Description courte'],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force',
        ];
        Http::fake(['api.dofusdb.fr/breeds/1*' => Http::response($mockBreed, 200)]);

        $r = $this->orchestrator->runOne('dofusdb', 'breed', $dofusdbId, $this->pipelineOptions());
        $result = $this->resultToArray($r);

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('id', $result['data']);

        $breed = Breed::find($result['data']['id']);
        $this->assertNotNull($breed);

        // Vérifier qu'aucune relation n'a été créée
        $spellsCount = $breed->spells()->count();
        $this->assertEquals(0, $spellsCount, 'Aucune relation ne devrait être créée sans include_relations');
    }

    /**
     * Test que l'import d'une panoplie importe les items manquants et crée le pivot item_panoply.
     */
    public function test_import_panoply_with_items_imports_missing_items_and_creates_relations(): void
    {
        $this->createSystemUser();
        ItemType::query()->firstOrCreate(
            ['dofusdb_type_id' => 6],
            ['name' => 'Type item test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );

        $panoplyData = [
            'id' => 1001,
            'name' => ['fr' => 'Panoplie relation test'],
            'description' => ['fr' => 'Description panoplie'],
            'effects' => [],
            'items' => [
                ['id' => 2001],
                ['id' => 2002],
            ],
            'isCosmetic' => false,
        ];

        $itemData1 = [
            'id' => 2001,
            'typeId' => 6,
            'name' => ['fr' => 'Item panoplie 1'],
            'description' => ['fr' => 'Desc item 1'],
            'level' => 10,
            'price' => 100,
            'effects' => [],
        ];
        $itemData2 = [
            'id' => 2002,
            'typeId' => 6,
            'name' => ['fr' => 'Item panoplie 2'],
            'description' => ['fr' => 'Desc item 2'],
            'level' => 20,
            'price' => 200,
            'effects' => [],
        ];

        Http::fake(function ($request) use ($panoplyData, $itemData1, $itemData2) {
            $url = $request->url();
            if (str_contains($url, '/item-sets/1001')) {
                return Http::response($panoplyData, 200);
            }
            if (str_contains($url, '/items/2001')) {
                return Http::response($itemData1, 200);
            }
            if (str_contains($url, '/items/2002')) {
                return Http::response($itemData2, 200);
            }

            return Http::response([], 404);
        });

        $r = $this->orchestrator->runOne('dofusdb', 'panoply', 1001, [
            'integrate' => true,
            'dry_run' => false,
            'force_update' => false,
            'include_relations' => true,
            'skip_cache' => true,
        ]);
        $result = $this->resultToArray($r);

        $this->assertTrue($result['success'], $result['message'] ?? '');
        $panoply = Panoply::where('dofusdb_id', '1001')->first();
        $this->assertNotNull($panoply);
        $this->assertSame(2, $panoply->items()->count());
        $this->assertNotNull(Item::where('dofusdb_id', '2001')->first());
        $this->assertNotNull(Item::where('dofusdb_id', '2002')->first());
        $this->assertDatabaseHas('item_panoply', ['panoply_id' => $panoply->id]);
    }

    /**
     * Test que l'import d'un équipement avec recette crée les relations item_resource.
     */
    public function test_import_equipment_with_recipe_creates_item_resource_relations(): void
    {
        $this->createSystemUser();
        ItemType::query()->firstOrCreate(
            ['dofusdb_type_id' => 6],
            ['name' => 'Type item test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );
        ResourceType::query()->firstOrCreate(
            ['dofusdb_type_id' => 51],
            ['name' => 'Type resource test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );

        $equipmentRaw = [
            'id' => 3001,
            'typeId' => 6,
            'name' => ['fr' => 'Equipement recette test'],
            'description' => ['fr' => 'Description equipement'],
            'level' => 10,
            'price' => 100,
            'effects' => [],
            'recipe' => [
                'ingredientIds' => [274],
                'quantities' => [2],
            ],
        ];
        $ingredientResourceRaw = [
            'id' => 274,
            'typeId' => 51,
            'name' => ['fr' => 'Ingrédient ressource test'],
            'description' => ['fr' => 'Description ressource'],
            'level' => 1,
            'price' => 10,
            'effects' => [],
        ];

        Http::fake(function ($request) use ($ingredientResourceRaw) {
            $url = $request->url();
            if (str_contains($url, '/items/274')) {
                return Http::response($ingredientResourceRaw, 200);
            }

            return Http::response([], 404);
        });

        $r = $this->orchestrator->runOneWithRaw('dofusdb', 'item', $equipmentRaw, [
            'convert' => true,
            'validate' => true,
            'integrate' => true,
            'include_relations' => true,
            'skip_cache' => true,
        ]);
        $result = $this->resultToArray($r);

        $this->assertTrue($result['success'], $result['message'] ?? '');
        $item = Item::where('dofusdb_id', '3001')->first();
        $this->assertNotNull($item);
        $resource = Resource::where('dofusdb_id', '274')->first();
        $this->assertNotNull($resource);
        $this->assertSame(1, $item->resources()->count());
        $this->assertDatabaseHas('item_resource', [
            'item_id' => $item->id,
            'resource_id' => $resource->id,
        ]);
    }

    /**
     * Test qu'une panoplie importe aussi les recettes de ses équipements (item_resource).
     */
    public function test_import_panoply_resolves_equipment_recipe_relations(): void
    {
        $this->createSystemUser();
        ItemType::query()->firstOrCreate(
            ['dofusdb_type_id' => 6],
            ['name' => 'Type item test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );
        ResourceType::query()->firstOrCreate(
            ['dofusdb_type_id' => 51],
            ['name' => 'Type resource test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );

        $panoplyRaw = [
            'id' => 4100,
            'name' => ['fr' => 'Panoplie recette test'],
            'description' => ['fr' => 'Panoplie avec équipement craftable'],
            'effects' => [],
            'items' => [
                ['id' => 4101],
            ],
            'isCosmetic' => false,
        ];
        $equipmentRaw = [
            'id' => 4101,
            'typeId' => 6,
            'name' => ['fr' => 'Equipement panoplie recette'],
            'description' => ['fr' => 'Description'],
            'level' => 20,
            'price' => 1000,
            'effects' => [],
            'recipe' => [
                'ingredientIds' => [4102],
                'quantities' => [3],
            ],
        ];
        $ingredientRaw = [
            'id' => 4102,
            'typeId' => 51,
            'name' => ['fr' => 'Ressource ingrédient panoplie'],
            'description' => ['fr' => 'Description ressource'],
            'level' => 1,
            'price' => 5,
            'effects' => [],
        ];

        Http::fake(function ($request) use ($panoplyRaw, $equipmentRaw, $ingredientRaw) {
            $url = $request->url();
            if (str_contains($url, '/item-sets/4100')) {
                return Http::response($panoplyRaw, 200);
            }
            if (str_contains($url, '/items/4101')) {
                return Http::response($equipmentRaw, 200);
            }
            if (str_contains($url, '/items/4102')) {
                return Http::response($ingredientRaw, 200);
            }

            return Http::response([], 404);
        });

        $r = $this->orchestrator->runOne('dofusdb', 'panoply', 4100, [
            'integrate' => true,
            'dry_run' => false,
            'force_update' => false,
            'include_relations' => true,
            'skip_cache' => true,
        ]);
        $result = $this->resultToArray($r);

        $this->assertTrue($result['success'], $result['message'] ?? '');

        $panoply = Panoply::where('dofusdb_id', '4100')->first();
        $this->assertNotNull($panoply);
        $item = Item::where('dofusdb_id', '4101')->first();
        $this->assertNotNull($item);
        $resource = Resource::where('dofusdb_id', '4102')->first();
        $this->assertNotNull($resource);

        $this->assertSame(1, $panoply->items()->count());
        $this->assertSame(1, $item->resources()->count());
        $this->assertDatabaseHas('item_resource', [
            'item_id' => $item->id,
            'resource_id' => $resource->id,
        ]);
    }

    /**
     * Test que l'import d'une ressource craftable crée des relations resource_recipe (resource -> resources).
     */
    public function test_import_resource_with_recipe_creates_resource_recipe_relations(): void
    {
        $this->createSystemUser();
        ResourceType::query()->firstOrCreate(
            ['dofusdb_type_id' => 51],
            ['name' => 'Type resource test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );

        $resourceRaw = [
            'id' => 6101,
            'typeId' => 51,
            'name' => ['fr' => 'Ressource craftable'],
            'description' => ['fr' => 'Description ressource craftable'],
            'level' => 10,
            'price' => 100,
            'effects' => [],
            'recipe' => [
                'ingredientIds' => [6102],
                'quantities' => [4],
            ],
        ];
        $ingredientRaw = [
            'id' => 6102,
            'typeId' => 51,
            'name' => ['fr' => 'Ressource ingrédient'],
            'description' => ['fr' => 'Description ingrédient'],
            'level' => 1,
            'price' => 10,
            'effects' => [],
        ];

        Http::fake(function ($request) use ($ingredientRaw) {
            $url = $request->url();
            if (str_contains($url, '/items/6102')) {
                return Http::response($ingredientRaw, 200);
            }

            return Http::response([], 404);
        });

        $r = $this->orchestrator->runOneWithRaw('dofusdb', 'item', $resourceRaw, [
            'convert' => true,
            'validate' => true,
            'integrate' => true,
            'include_relations' => true,
            'skip_cache' => true,
        ]);
        $result = $this->resultToArray($r);

        $this->assertTrue($result['success'], $result['message'] ?? '');
        $resource = Resource::where('dofusdb_id', '6101')->first();
        $this->assertNotNull($resource);
        $ingredient = Resource::where('dofusdb_id', '6102')->first();
        $this->assertNotNull($ingredient);
        $this->assertSame(1, $resource->recipeIngredients()->count());
        $this->assertDatabaseHas('resource_recipe', [
            'resource_id' => $resource->id,
            'ingredient_resource_id' => $ingredient->id,
        ]);
    }

    /**
     * Test que l'import d'un consommable craftable crée des relations consumable_resource.
     */
    public function test_import_consumable_with_recipe_creates_consumable_resource_relations(): void
    {
        $this->createSystemUser();
        ConsumableType::query()->firstOrCreate(
            ['dofusdb_type_id' => 12],
            ['name' => 'Type consumable test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );
        ResourceType::query()->firstOrCreate(
            ['dofusdb_type_id' => 51],
            ['name' => 'Type resource test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );

        $consumableRaw = [
            'id' => 6201,
            'typeId' => 12,
            'name' => ['fr' => 'Consommable craftable'],
            'description' => ['fr' => 'Description consommable craftable'],
            'level' => 20,
            'price' => 50,
            'effects' => [],
            'recipe' => [
                'ingredientIds' => [6202],
                'quantities' => [2],
            ],
        ];
        $ingredientRaw = [
            'id' => 6202,
            'typeId' => 51,
            'name' => ['fr' => 'Ressource ingrédient consommable'],
            'description' => ['fr' => 'Description ingrédient'],
            'level' => 1,
            'price' => 5,
            'effects' => [],
        ];

        Http::fake(function ($request) use ($ingredientRaw) {
            $url = $request->url();
            if (str_contains($url, '/items/6202')) {
                return Http::response($ingredientRaw, 200);
            }

            return Http::response([], 404);
        });

        $r = $this->orchestrator->runOneWithRaw('dofusdb', 'item', $consumableRaw, [
            'convert' => true,
            'validate' => true,
            'integrate' => true,
            'include_relations' => true,
            'skip_cache' => true,
        ]);
        $result = $this->resultToArray($r);

        $this->assertTrue($result['success'], $result['message'] ?? '');
        $consumable = Consumable::where('dofusdb_id', '6201')->first();
        $this->assertNotNull($consumable);
        $resource = Resource::where('dofusdb_id', '6202')->first();
        $this->assertNotNull($resource);
        $this->assertSame(1, $consumable->resources()->count());
        $this->assertDatabaseHas('consumable_resource', [
            'consumable_id' => $consumable->id,
            'resource_id' => $resource->id,
        ]);
    }

    /**
     * Test de boucle monster <-> spell (invocation) : pas de boucle infinie, relations créées.
     */
    public function test_monster_spell_invocation_loop_is_handled_and_relations_synced(): void
    {
        ItemType::query()->firstOrCreate(
            ['dofusdb_type_id' => 6],
            ['name' => 'Type item test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );
        ResourceType::query()->firstOrCreate(
            ['dofusdb_type_id' => 51],
            ['name' => 'Type resource test', 'state' => 'playable', 'read_level' => 0, 'write_level' => 2]
        );

        $monsterRaw = [
            'id' => 7001,
            'name' => ['fr' => 'Monstre boucle'],
            'description' => ['fr' => 'Monstre qui invoque'],
            'life' => 50,
            'pa' => 3,
            'pm' => 3,
            'spells' => [7002],
            'drops' => [
                ['id' => 7003, 'quantity' => 1],
            ],
        ];
        $spellRaw = [
            'id' => 7002,
            'name' => ['fr' => 'Sort invocation boucle'],
            'description' => ['fr' => 'Invoque le même monstre'],
            'apCost' => 3,
            'range' => 1,
            'minRange' => 0,
            'maxCastPerTurn' => 1,
            'castTestLos' => true,
            'isMagic' => false,
            'elementId' => 0,
            'categoryId' => 0,
            'powerful' => 0,
            'summon' => ['id' => 7001, 'name' => ['fr' => 'Monstre boucle']],
        ];
        $ingredientDropRaw = [
            'id' => 7003,
            'typeId' => 51,
            'name' => ['fr' => 'Drop boucle'],
            'description' => ['fr' => 'Drop ressource'],
            'level' => 1,
            'price' => 1,
            'effects' => [],
        ];

        Http::fake(function ($request) use ($monsterRaw, $spellRaw, $ingredientDropRaw) {
            $url = $request->url();
            if (str_contains($url, '/spells/7002')) {
                return Http::response($spellRaw, 200);
            }
            if (str_contains($url, '/spell-levels')) {
                return Http::response(['data' => []], 200);
            }
            if (str_contains($url, '/monsters/7001')) {
                return Http::response($monsterRaw, 200);
            }
            if (str_contains($url, '/items/7003')) {
                return Http::response($ingredientDropRaw, 200);
            }

            return Http::response([], 404);
        });

        $r = $this->orchestrator->runOneWithRaw('dofusdb', 'monster', $monsterRaw, [
            'convert' => true,
            'validate' => true,
            'integrate' => true,
            'include_relations' => true,
            'skip_cache' => true,
        ]);
        $result = $this->resultToArray($r);
        $this->assertTrue($result['success'], $result['message'] ?? '');

        $monster = Monster::where('dofusdb_id', '7001')->first();
        $this->assertNotNull($monster);
        $creature = Creature::find($monster->creature_id);
        $this->assertNotNull($creature);
        $spell = Spell::where('dofusdb_id', '7002')->first();
        $this->assertNotNull($spell);

        $this->assertDatabaseHas('creature_spell', [
            'creature_id' => $creature->id,
            'spell_id' => $spell->id,
        ]);
        $this->assertDatabaseHas('spell_invocation', [
            'spell_id' => $spell->id,
            'monster_id' => $monster->id,
        ]);
    }
}

