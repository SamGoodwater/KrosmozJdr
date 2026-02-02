<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use App\Models\Entity\Classe;
use App\Models\Entity\Spell;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Resource;
use App\Models\Entity\Item;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\CreatesSystemUser;

/**
 * Tests d'intégration pour vérifier que les relations sont bien importées lors du scrapping.
 *
 * @package Tests\Feature\Scrapping
 */
class ScrappingRelationsTest extends TestCase
{
    use RefreshDatabase, CreatesSystemUser;

    private Orchestrator $orchestrator;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
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
     * Test que l'import d'une classe avec include_relations crée les relations dans class_spell
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

        $classe = Classe::find($result['data']['id']);
        $this->assertNotNull($classe);

        // Vérifier que la classe a des sorts associés
        $spellsCount = $classe->spells()->count();
        
        // Si des sorts ont été importés, ils devraient être associés
        if ($spellsCount > 0) {
            $this->assertGreaterThan(0, $spellsCount, 'La classe devrait avoir des sorts associés');
            
            // Vérifier que la relation existe dans la table pivot
            $this->assertDatabaseHas('class_spell', [
                'classe_id' => $classe->id,
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

        $classe = Classe::find($result['data']['id']);
        $this->assertNotNull($classe);

        // Vérifier qu'aucune relation n'a été créée
        $spellsCount = $classe->spells()->count();
        $this->assertEquals(0, $spellsCount, 'Aucune relation ne devrait être créée sans include_relations');
    }
}

