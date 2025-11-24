<?php

namespace Tests\Unit\Scrapping;

use App\Models\User;
use App\Models\Entity\Classe;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\Scrapping\DataIntegration\DataIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests unitaires pour le service DataIntegration
 * 
 * @package Tests\Unit\Scrapping
 */
class DataIntegrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private DataIntegrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataIntegrationService();
        
        // Créer un utilisateur système pour les tests
        User::factory()->create();
    }

    /**
     * Test d'intégration d'une classe
     */
    public function test_integrate_class_creates_new_class(): void
    {
        $convertedData = [
            'name' => 'Iop',
            'description' => 'Description de la classe Iop',
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force'
        ];

        $result = $this->service->integrateClass($convertedData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('action', $result);
        $this->assertEquals('created', $result['action']);

        $class = Classe::find($result['id']);
        $this->assertNotNull($class);
        $this->assertEquals('Iop', $class->name);
    }

    /**
     * Test d'intégration d'une classe existante (mise à jour)
     */
    public function test_integrate_class_updates_existing(): void
    {
        $existingClass = Classe::factory()->create([
            'name' => 'Iop',
            'description' => 'Ancienne description'
        ]);

        $convertedData = [
            'name' => 'Iop',
            'description' => 'Nouvelle description',
            'life' => 60,
            'life_dice' => '1d6',
            'specificity' => 'Force'
        ];

        $result = $this->service->integrateClass($convertedData);

        $this->assertEquals('updated', $result['action']);
        $this->assertEquals($existingClass->id, $result['id']);

        $existingClass->refresh();
        $this->assertEquals('Nouvelle description', $existingClass->description);
    }

    /**
     * Test d'intégration d'un monstre
     */
    public function test_integrate_monster_creates_creature_and_monster(): void
    {
        $convertedData = [
            'creatures' => [
                'name' => 'Bouftou',
                'level' => 5,
                'life' => 100,
                'strength' => 10,
                'intelligence' => 5,
                'agility' => 8,
                'luck' => 0,
                'wisdom' => 3,
                'chance' => 2
            ],
            'monsters' => [
                'size' => 2, // medium
                'monster_race_id' => null
            ]
        ];

        $result = $this->service->integrateMonster($convertedData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('creature_id', $result);
        $this->assertArrayHasKey('monster_id', $result);

        $creature = Creature::find($result['creature_id']);
        $monster = Monster::find($result['monster_id']);

        $this->assertNotNull($creature);
        $this->assertNotNull($monster);
        $this->assertEquals('Bouftou', $creature->name);
        $this->assertEquals(2, $monster->size);
    }

    /**
     * Test d'intégration d'un objet (resource)
     */
    public function test_integrate_item_creates_resource(): void
    {
        $convertedData = [
            'name' => 'Purée pique-fêle',
            'description' => 'Description de la ressource',
            'level' => 1,
            'type' => 'resource',
            'category' => 'resource',
            'type_id' => 15,
            'rarity' => 'common',
            'price' => 10
        ];

        $result = $this->service->integrateItem($convertedData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('table', $result);
        $this->assertEquals('resources', $result['table']);

        $resource = Resource::where('name', 'Purée pique-fêle')->first();
        $this->assertNotNull($resource);
    }

    /**
     * Test d'intégration d'un objet (consumable)
     */
    public function test_integrate_item_creates_consumable(): void
    {
        $convertedData = [
            'name' => 'Potion de soin',
            'description' => 'Description de la potion',
            'level' => 1,
            'type' => 'potion',
            'category' => 'potion',
            'type_id' => 12,
            'rarity' => 'common',
            'price' => 10
        ];

        $result = $this->service->integrateItem($convertedData);

        $this->assertEquals('consumables', $result['table']);

        $consumable = Consumable::where('name', 'Potion de soin')->first();
        $this->assertNotNull($consumable);
    }

    /**
     * Test d'intégration d'un objet (item générique)
     */
    public function test_integrate_item_creates_generic_item(): void
    {
        $convertedData = [
            'name' => 'Épée',
            'description' => 'Description de l\'épée',
            'level' => 10,
            'type' => 'weapon',
            'category' => 'weapon',
            'type_id' => 6,
            'rarity' => 'rare',
            'price' => 100
        ];

        $result = $this->service->integrateItem($convertedData);

        $this->assertEquals('items', $result['table']);

        $item = Item::where('name', 'Épée')->first();
        $this->assertNotNull($item);
    }

    /**
     * Test de prévention des doublons
     */
    public function test_integrate_item_prevents_duplicates(): void
    {
        // Créer un objet dans la mauvaise table
        Item::factory()->create([
            'name' => 'Purée pique-fêle',
            'description' => 'Ancienne description'
        ]);

        $convertedData = [
            'name' => 'Purée pique-fêle',
            'description' => 'Nouvelle description',
            'level' => 1,
            'type' => 'resource',
            'category' => 'resource',
            'type_id' => 15,
            'rarity' => 'common',
            'price' => 10
        ];

        $result = $this->service->integrateItem($convertedData);

        // L'objet devrait être dans resources, pas dans items
        $this->assertEquals('resources', $result['table']);

        // L'ancien objet dans items devrait être supprimé
        $oldItem = Item::where('name', 'Purée pique-fêle')->first();
        $this->assertNull($oldItem);

        // Le nouvel objet devrait être dans resources
        $resource = Resource::where('name', 'Purée pique-fêle')->first();
        $this->assertNotNull($resource);
    }

    /**
     * Test d'intégration d'un sort
     */
    public function test_integrate_spell_creates_spell(): void
    {
        $convertedData = [
            'name' => 'Béco du Tofu',
            'description' => 'Description du sort',
            'class' => '',
            'cost' => 3,
            'range' => 1,
            'area' => 1,
            'critical_hit' => 0,
            'failure' => 0
        ];

        $result = $this->service->integrateSpell($convertedData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('created', $result['action']);

        $spell = Spell::find($result['id']);
        $this->assertNotNull($spell);
        $this->assertEquals('Béco du Tofu', $spell->name);
        $this->assertEquals('3', $spell->pa); // cost -> pa
        $this->assertEquals('1', $spell->po); // range -> po
    }
}

