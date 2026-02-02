<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use App\Models\Entity\Breed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests d'intégration pour le contrôleur de scrapping
 * 
 * @package Tests\Feature\Scrapping
 */
class ScrappingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur système
        User::factory()->create();
    }

    /**
     * Test de l'endpoint POST /api/scrapping/import/class/{id} (pipeline).
     */
    public function test_import_class_endpoint_succeeds(): void
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

        $response = $this->postJson('/api/scrapping/import/class/1');

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'timestamp',
            ]);

        $breed = Breed::where('name', 'Iop')->orWhere('dofusdb_id', '1')->first();
        $this->assertNotNull($breed);
    }

    /**
     * Test de l'endpoint POST /api/scrapping/import/class/{id} avec erreur
     */
    public function test_import_class_endpoint_handles_error(): void
    {
        // Simuler une erreur HTTP 404 depuis DofusDB
        Http::fake(function ($request) {
            return Http::response(['error' => 'Not Found'], 404);
        });

        // Utiliser un ID valide selon la contrainte de route (1-19)
        // mais qui provoque une erreur lors de la collecte
        $response = $this->postJson('/api/scrapping/import/class/19');

        // Le contrôleur retourne 400 si l'orchestrateur retourne success=false
        // ou 500 si une exception est levée
        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'error'
            ]);
    }

    /**
     * Test de l'endpoint POST /api/scrapping/import/monster/{id}
     */
    public function test_import_monster_endpoint_succeeds(): void
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

        $response = $this->postJson('/api/scrapping/import/monster/31');

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test de l'endpoint POST /api/scrapping/import/item/{id}
     */
    public function test_import_item_endpoint_succeeds(): void
    {
        $mockData = [
            'id' => 15,
            'name' => ['fr' => 'Purée pique-fêle'],
            'description' => ['fr' => 'Description'],
            'typeId' => 15,
            'level' => 1,
            'rarity' => 'common',
            'price' => 10
        ];

        Http::fake([
            'api.dofusdb.fr/items/15*' => Http::response($mockData, 200),
        ]);

        $response = $this->postJson('/api/scrapping/import/item/15');

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test de l'endpoint POST /api/scrapping/import/spell/{id} (pipeline).
     * fetchOne GET /spells/{id} attend un objet unique.
     */
    public function test_import_spell_endpoint_succeeds(): void
    {
        $spellObject = [
            'id' => 201,
            'name' => ['fr' => 'Béco du Tofu'],
            'description' => ['fr' => 'Description du sort'],
            'img' => null,
            'levels' => [
                ['apCost' => 3, 'range' => 1, 'maxCastPerTurn' => 2],
            ],
        ];

        Http::fake([
            'api.dofusdb.fr/spells/201*' => Http::response($spellObject, 200),
        ]);

        $response = $this->postJson('/api/scrapping/import/spell/201');

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'timestamp',
            ]);
    }

    /**
     * Test de l'endpoint POST /api/scrapping/import/batch
     */
    public function test_import_batch_endpoint_succeeds(): void
    {
        $entities = [
            ['type' => 'class', 'id' => 1],
            ['type' => 'item', 'id' => 15],
        ];

        Http::fake([
            'api.dofusdb.fr/breeds/1*' => Http::response([
                'id' => 1,
                'description' => ['fr' => 'Description'],
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

        $response = $this->postJson('/api/scrapping/import/batch', [
            'entities' => $entities
        ]);

        // Le contrôleur retourne 201 pour les batch imports réussis
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'results',
                'summary' => [
                    'total',
                    'success',
                    'errors'
                ]
            ]);
    }

    /**
     * Test de validation de l'endpoint batch
     */
    public function test_import_batch_endpoint_validates_input(): void
    {
        $response = $this->postJson('/api/scrapping/import/batch', [
            'entities' => [
                ['type' => 'invalid', 'id' => 1]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['entities.0.type']);
    }
}

