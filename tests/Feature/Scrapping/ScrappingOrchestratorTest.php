<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\CreatesSystemUser;

/**
 * Tests d'intégration pour l'orchestrateur de scrapping.
 *
 * @package Tests\Feature\Scrapping
 */
class ScrappingOrchestratorTest extends TestCase
{
    use RefreshDatabase, CreatesSystemUser;

    private Orchestrator $orchestrator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createSystemUser();
        $this->orchestrator = app(Orchestrator::class);
    }

    private function pipelineOpts(): array
    {
        return ['integrate' => true, 'dry_run' => false, 'force_update' => false];
    }

    private function toArray($r): array
    {
        return [
            'success' => $r->isSuccess(),
            'data' => $r->getIntegrationResult()?->getData() ?? [],
            'message' => $r->getMessage(),
        ];
    }

    /**
     * Test d'import complet d'une classe
     */
    public function test_import_class_complete_workflow(): void
    {
        $mockData = [
            'id' => 1,
            'name' => ['fr' => 'Iop'],
            'description' => ['fr' => 'Description de la classe Iop'],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force',
        ];

        Http::fake([
            'api.dofusdb.fr/breeds/1*' => Http::response($mockData, 200),
        ]);

        $result = $this->toArray($this->orchestrator->runOne('dofusdb', 'breed', 1, $this->pipelineOpts()));

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');
        $this->assertArrayHasKey('data', $result);

        // Vérifier que le breed (classe) a été créé en base
        $breed = Breed::where('name', 'like', '%Iop%')->orWhere('name', 'like', '%Classe%')->first();
        $this->assertNotNull($breed);
    }

    /**
     * Test d'import complet d'un monstre
     */
    public function test_import_monster_complete_workflow(): void
    {
        $mockData = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'level' => 5,
            'lifePoints' => 100,
            'grades' => [
                [
                    'level' => 5,
                    'lifePoints' => 100,
                    'strength' => 10,
                    'intelligence' => 5,
                    'agility' => 8,
                    'wisdom' => 3,
                    'chance' => 2
                ]
            ],
            'size' => 'medium'
        ];

        Http::fake([
            'api.dofusdb.fr/monsters/31*' => Http::response($mockData, 200),
        ]);

        $result = $this->toArray($this->orchestrator->runOneWithRaw('dofusdb', 'monster', $mockData, array_merge($this->pipelineOpts(), ['convert' => true, 'validate' => true])));

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');

        // Vérifier que le monstre a été créé
        $creature = Creature::where('name', 'Bouftou')->first();
        $this->assertNotNull($creature);
        
        $monster = Monster::where('creature_id', $creature->id)->first();
        $this->assertNotNull($monster);
    }

    /**
     * Test d'import complet d'un objet (resource)
     */
    public function test_import_item_complete_workflow(): void
    {
        $mockData = [
            'id' => 15,
            'name' => ['fr' => 'Purée pique-fêle'],
            'description' => ['fr' => 'Description de la ressource'],
            'typeId' => 15,
            'level' => 1,
            'rarity' => 'common',
            'price' => 10
        ];

        Http::fake([
            'api.dofusdb.fr/items/15*' => Http::response($mockData, 200),
        ]);

        $result = $this->toArray($this->orchestrator->runOne('dofusdb', 'item', 15, $this->pipelineOpts()));

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');

        // Vérifier que la ressource a été créée dans la bonne table
        $resource = Resource::where('name', 'Purée pique-fêle')->first();
        $this->assertNotNull($resource);

        // Vérifier qu'elle n'est pas dans items
        $item = Item::where('name', 'Purée pique-fêle')->first();
        $this->assertNull($item);
    }

    /**
     * Test d'import complet d'un sort
     */
    public function test_import_spell_complete_workflow(): void
    {
        $spellList = [
            'data' => [
                [
                    'id' => 201,
                    'name' => ['fr' => 'Béco du Tofu'],
                    'description' => ['fr' => 'Description du sort'],
                    'cost' => 3,
                    'range' => 1,
                    'area' => 1
                ]
            ],
            'total' => 1,
            'limit' => 100,
            'skip' => 0
        ];

        $levelsList = [
            'data' => []
        ];

        Http::fake([
            'api.dofusdb.fr/spells/201*' => Http::response($spellList['data'][0], 200),
            'api.dofusdb.fr/spells*' => Http::response($spellList, 200),
            'api.dofusdb.fr/spell-levels*' => Http::response($levelsList, 200),
        ]);

        $result = $this->toArray($this->orchestrator->runOne('dofusdb', 'spell', 201, $this->pipelineOpts()));

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');

        // Vérifier que le sort a été créé
        $spell = Spell::where('name', 'Béco du Tofu')->first();
        $this->assertNotNull($spell);
    }

    /**
     * Test d'import en lot
     */
    public function test_import_batch_complete_workflow(): void
    {
        $entities = [
            ['type' => 'class', 'id' => 1],
            ['type' => 'item', 'id' => 15],
        ];

        Http::fake([
            'api.dofusdb.fr/breeds/1*' => Http::response([
                'id' => 1,
                'description' => ['fr' => 'Description classe'],
                'life' => 50,
                'life_dice' => '1d6',
                'specificity' => 'Force'
            ], 200),
            'api.dofusdb.fr/items/15*' => Http::response([
                'id' => 15,
                'name' => ['fr' => 'Purée pique-fêle'],
                'description' => ['fr' => 'Description'],
                'typeId' => 15,
                'level' => 1,
                'rarity' => 'common',
                'price' => 10
            ], 200),
        ]);

        $results = [];
        $errors = 0;
        foreach ($entities as $e) {
            $type = $e['type'];
            $id = (int) $e['id'];
            $entity = $type === 'class' ? 'breed' : $type;
            $r = $this->orchestrator->runOne('dofusdb', $entity, $id, $this->pipelineOpts());
            $results[] = ['success' => $r->isSuccess()];
            if (!$r->isSuccess()) {
                $errors++;
            }
        }
        $result = [
            'success' => $errors === 0,
            'summary' => ['total' => count($entities), 'success' => count($entities) - $errors, 'errors' => $errors],
            'results' => $results,
        ];

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('summary', $result);
        $this->assertEquals(2, $result['summary']['total']);
        $this->assertEquals(2, $result['summary']['success']);
        $this->assertEquals(0, $result['summary']['errors']);
    }

    /**
     * Test d'import avec option skip_cache
     */
    public function test_import_with_skip_cache_option(): void
    {
        $mockData = [
            'id' => 1,
            'description' => ['fr' => 'Description'],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force'
        ];

        Http::fake([
            'api.dofusdb.fr/breeds/1*' => Http::response($mockData, 200),
        ]);

        $result = $this->toArray($this->orchestrator->runOne('dofusdb', 'breed', 1, $this->pipelineOpts()));

        $this->assertTrue($result['success'], $result['message'] ?? '');
    }

    /**
     * Test d'import avec erreur de collecte
     */
    public function test_import_handles_collection_error(): void
    {
        Http::fake([
            'api.dofusdb.fr/breeds/999' => Http::response([], 404),
        ]);

        $r = $this->orchestrator->runOne('dofusdb', 'breed', 999, $this->pipelineOpts());
        $result = $this->toArray($r);
        $result['error'] = $r->getMessage();

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    /**
     * Test d'import d'une classe avec sorts associés
     */
    public function test_import_class_with_spells(): void
    {
        $classData = [
            'id' => 1,
            'description' => ['fr' => 'Description'],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force'
        ];

        $spellLevelsData = [
            'data' => [
                [
                    'id' => 1,
                    'spellId' => 201,
                    'spellBreed' => 1
                ]
            ]
        ];

        $spellData = [
            'data' => [
                [
                    'id' => 201,
                    'name' => ['fr' => 'Sort de classe'],
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

        Http::fake(function ($request) use ($classData, $spellLevelsData, $spellData, $levelsList) {
            $url = $request->url();
            if (str_contains($url, '/breeds/1')) {
                return Http::response($classData, 200);
            }
            if (str_contains($url, '/spell-levels')) {
                return Http::response($spellLevelsData, 200);
            }
            if (str_contains($url, '/spells')) {
                return Http::response($spellData, 200);
            }
            return Http::response([], 404);
        });

        $result = $this->toArray($this->orchestrator->runOne('dofusdb', 'breed', 1, $this->pipelineOpts()));

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test d'import d'un monstre avec relations (runOneWithRaw + RelationResolutionService)
     */
    public function test_import_monster_with_relations(): void
    {
        $monsterData = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'level' => 5,
            'lifePoints' => 100,
            'grades' => [
                [
                    'level' => 5,
                    'lifePoints' => 100,
                    'strength' => 10,
                    'intelligence' => 5,
                    'agility' => 8,
                    'wisdom' => 3,
                    'chance' => 2
                ]
            ],
            'size' => 'medium',
            'spells' => [['id' => 201]],
            'drops' => [['id' => 15]]
        ];

        $spellData = [
            'data' => [
                [
                    'id' => 201,
                    'name' => ['fr' => 'Sort'],
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

        $r = $this->orchestrator->runOneWithRaw('dofusdb', 'monster', $monsterData, array_merge($this->pipelineOpts(), ['convert' => true, 'validate' => true]));
        $this->assertTrue($r->isSuccess(), $r->getMessage());
        $creatureId = $r->getIntegrationResult()?->getCreatureId();
        $relationOut = app(\App\Services\Scrapping\Core\Relation\RelationResolutionService::class)
            ->resolveAndSyncMonsterRelations($monsterData, $creatureId, ['integrate' => true, 'dry_run' => false]);
        $result = [
            'success' => true,
            'data' => ['creature_id' => $creatureId, 'monster_id' => $r->getIntegrationResult()?->getMonsterId()],
            'related' => $relationOut['related_results'],
        ];

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('related', $result);
    }

    /**
     * runMany avec integrate + include_relations : vérifie que la variable de boucle ($raw) est bien
     * passée à resolveRelationsAndDrain (régression si on utilisait $rawItem par erreur).
     * Monster sans spells/drops pour limiter les appels HTTP du drain.
     */
    public function test_run_many_monster_with_include_relations_passes_raw_to_relations(): void
    {
        $monsterData = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'grades' => [
                [
                    'level' => 5,
                    'lifePoints' => 100,
                    'strength' => 10,
                    'intelligence' => 5,
                    'agility' => 8,
                    'wisdom' => 3,
                    'chance' => 2,
                ],
            ],
            'size' => 'medium',
            'race' => 1,
            'spells' => [],
            'drops' => [],
        ];

        $fetchManyResponse = [
            'data' => [$monsterData],
            'total' => 1,
            'limit' => 1,
        ];

        Http::fake(function ($request) use ($fetchManyResponse) {
            $url = $request->url();
            if (str_contains($url, 'monsters')) {
                return Http::response($fetchManyResponse, 200);
            }
            return Http::response([], 404);
        });

        $opts = [
            'limit' => 1,
            'convert' => true,
            'validate' => true,
            'integrate' => true,
            'dry_run' => false,
            'include_relations' => true,
            'lang' => 'fr',
        ];

        $result = $this->orchestrator->runMany('dofusdb', 'monster', [], $opts);

        $this->assertTrue($result->isSuccess(), $result->getMessage());
        $convertedList = $result->getConverted();
        $this->assertIsArray($convertedList);
        $this->assertCount(1, $convertedList);
    }

    /**
     * Test d'import sans relations
     */
    public function test_import_without_relations(): void
    {
        $mockData = [
            'id' => 1,
            'description' => ['fr' => 'Description'],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force'
        ];

        Http::fake([
            'api.dofusdb.fr/breeds/1*' => Http::response($mockData, 200),
        ]);

        $result = $this->toArray($this->orchestrator->runOne('dofusdb', 'breed', 1, $this->pipelineOpts()));

        $this->assertTrue($result['success'], $result['message'] ?? '');
    }

    /**
     * Panoplie cosmétique : rejetée par l'orchestrateur (seules les panoplies à bonus sont importables).
     */
    public function test_panoply_cosmetic_rejected(): void
    {
        $rawCosmetic = [
            'id' => 999,
            'name' => ['fr' => 'Panoplie cosmétique'],
            'description' => ['fr' => 'Description'],
            'effects' => [],
            'items' => [],
            'isCosmetic' => true,
        ];

        $r = $this->orchestrator->runOneWithRaw('dofusdb', 'panoply', $rawCosmetic, array_merge($this->pipelineOpts(), ['convert' => true, 'validate' => true]));

        $this->assertFalse($r->isSuccess());
        $this->assertStringContainsString('cosmétique', $r->getMessage());
    }

    /**
     * Test d'import complet d'une panoplie (stats, pas cosmétique).
     */
    public function test_import_panoply_complete_workflow(): void
    {
        $mockPanoply = [
            'id' => 10,
            'name' => ['fr' => 'Panoplie du Bouftou'],
            'description' => ['fr' => 'Bonus de panoplie'],
            'effects' => [
                ['effectId' => 1, 'min' => 10, 'max' => 20],
            ],
            'items' => [
                ['id' => 101],
                ['id' => 102],
            ],
            'isCosmetic' => false,
        ];

        Http::fake([
            'api.dofusdb.fr/item-sets/10*' => Http::response($mockPanoply, 200),
        ]);

        $result = $this->toArray($this->orchestrator->runOne('dofusdb', 'panoply', 10, $this->pipelineOpts()));

        $this->assertIsArray($result);
        $this->assertTrue($result['success'], $result['message'] ?? '');

        $panoply = Panoply::where('dofusdb_id', '10')->first();
        $this->assertNotNull($panoply);
        $this->assertSame('Panoplie du Bouftou', $panoply->name);
        $this->assertNotNull($panoply->bonus);
    }
}

