<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use App\Models\Entity\Classe;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests d'intégration pour l'orchestrateur de scrapping
 * 
 * @package Tests\Feature\Scrapping
 */
class ScrappingOrchestratorTest extends TestCase
{
    use RefreshDatabase;

    private ScrappingOrchestrator $orchestrator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = app(ScrappingOrchestrator::class);
        
        // Créer un utilisateur système
        User::factory()->create();
    }

    /**
     * Test d'import complet d'une classe
     */
    public function test_import_class_complete_workflow(): void
    {
        $mockData = [
            'id' => 1,
            'description' => [
                'fr' => 'Description de la classe Iop'
            ],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force'
        ];

        Http::fake([
            'api.dofusdb.fr/breeds/1' => Http::response($mockData, 200),
        ]);

        $result = $this->orchestrator->importClass(1);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);

        // Vérifier que la classe a été créée en base
        $class = Classe::where('name', 'like', '%Iop%')->orWhere('name', 'like', '%Classe%')->first();
        $this->assertNotNull($class);
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
            'api.dofusdb.fr/monsters/31' => Http::response($mockData, 200),
        ]);

        $result = $this->orchestrator->importMonster(31);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);

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
            'api.dofusdb.fr/items/15' => Http::response($mockData, 200),
        ]);

        $result = $this->orchestrator->importItem(15);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);

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
            'api.dofusdb.fr/spells*' => Http::response($spellList, 200),
            'api.dofusdb.fr/spell-levels*' => Http::response($levelsList, 200),
        ]);

        $result = $this->orchestrator->importSpell(201);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);

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
            'api.dofusdb.fr/breeds/1' => Http::response([
                'id' => 1,
                'description' => ['fr' => 'Description classe'],
                'life' => 50,
                'life_dice' => '1d6',
                'specificity' => 'Force'
            ], 200),
            'api.dofusdb.fr/items/15' => Http::response([
                'id' => 15,
                'name' => ['fr' => 'Purée pique-fêle'],
                'description' => ['fr' => 'Description'],
                'typeId' => 15,
                'level' => 1,
                'rarity' => 'common',
                'price' => 10
            ], 200),
        ]);

        $result = $this->orchestrator->importBatch($entities);

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
            'api.dofusdb.fr/breeds/1' => Http::response($mockData, 200),
        ]);

        $result = $this->orchestrator->importClass(1, ['skip_cache' => true]);

        $this->assertTrue($result['success']);
    }

    /**
     * Test d'import avec erreur de collecte
     */
    public function test_import_handles_collection_error(): void
    {
        Http::fake([
            'api.dofusdb.fr/breeds/999' => Http::response([], 404),
        ]);

        $result = $this->orchestrator->importClass(999);

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

        $result = $this->orchestrator->importClass(1);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('related', $result);
    }

    /**
     * Test d'import d'un monstre avec relations
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
            'spells' => [201],
            'drops' => [15]
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

        $result = $this->orchestrator->importMonster(31);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('related', $result);
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
            'api.dofusdb.fr/breeds/1' => Http::response($mockData, 200),
        ]);

        $result = $this->orchestrator->importClass(1, ['include_relations' => false]);

        $this->assertTrue($result['success']);
        // Les relations ne devraient pas être importées
    }
}

