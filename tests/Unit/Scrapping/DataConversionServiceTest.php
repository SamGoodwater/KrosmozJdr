<?php

namespace Tests\Unit\Scrapping;

use App\Services\Scrapping\DataConversion\DataConversionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests unitaires pour le service DataConversion
 * 
 * @package Tests\Unit\Scrapping
 */
class DataConversionServiceTest extends TestCase
{
    use RefreshDatabase;

    private DataConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataConversionService();
    }

    /**
     * Test de conversion d'une classe
     */
    public function test_convert_class_succeeds(): void
    {
        $rawData = [
            'id' => 1,
            'description' => [
                'fr' => 'Description de la classe Iop'
            ],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force'
        ];

        $result = $this->service->convertClass($rawData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('life', $result);
        $this->assertEquals(50, $result['life']);
    }

    /**
     * Test de conversion d'une classe avec description tronquée
     */
    public function test_convert_class_truncates_long_description(): void
    {
        $longDescription = str_repeat('a', 300); // 300 caractères
        $rawData = [
            'id' => 1,
            'description' => ['fr' => $longDescription],
            'life' => 50,
            'life_dice' => '1d6',
            'specificity' => 'Force'
        ];

        $result = $this->service->convertClass($rawData);

        $this->assertLessThanOrEqual(255, mb_strlen($result['description']));
    }

    /**
     * Test de conversion d'un monstre
     */
    public function test_convert_monster_succeeds(): void
    {
        $rawData = [
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

        $result = $this->service->convertMonster($rawData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('creatures', $result);
        $this->assertArrayHasKey('monsters', $result);
        $this->assertEquals('Bouftou', $result['creatures']['name']);
        $this->assertEquals(5, $result['creatures']['level']);
    }

    /**
     * Test de conversion d'un objet
     */
    public function test_convert_item_succeeds(): void
    {
        $rawData = [
            'id' => 15,
            'name' => ['fr' => 'Purée pique-fêle'],
            'description' => ['fr' => 'Description de la ressource'],
            'typeId' => 15,
            'level' => 1,
            'rarity' => 'common',
            'price' => 10
        ];

        $result = $this->service->convertItem($rawData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertEquals('resource', $result['type']);
        $this->assertEquals('resource', $result['category']);
    }

    /**
     * Test de conversion d'un objet avec mapping de type
     */
    public function test_convert_item_maps_type_correctly(): void
    {
        $rawData = [
            'id' => 12,
            'name' => ['fr' => 'Potion'],
            'description' => ['fr' => 'Description'],
            'typeId' => 12, // Potion
            'level' => 1,
            'rarity' => 'common',
            'price' => 10
        ];

        $result = $this->service->convertItem($rawData);

        $this->assertEquals('potion', $result['type']);
        $this->assertEquals('potion', $result['category']);
    }

    /**
     * Test de conversion d'un sort
     */
    public function test_convert_spell_succeeds(): void
    {
        $rawData = [
            'id' => 201,
            'name' => ['fr' => 'Béco du Tofu'],
            'description' => ['fr' => 'Description du sort'],
            'cost' => 3,
            'range' => 1,
            'area' => 1
        ];

        $result = $this->service->convertSpell($rawData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertEquals('Béco du Tofu', $result['name']);
        $this->assertEquals(3, $result['cost']);
    }

    /**
     * Test de conversion avec valeurs limites
     */
    public function test_convert_respects_limits(): void
    {
        $rawData = [
            'id' => 1,
            'description' => ['fr' => 'Test'],
            'life' => 9999, // Valeur très élevée
            'life_dice' => '1d6',
            'specificity' => 'Test'
        ];

        $result = $this->service->convertClass($rawData);

        // La vie devrait être limitée selon la configuration
        $this->assertLessThanOrEqual(1000, $result['life']);
    }

    /**
     * Test de conversion avec valeur zéro (doit être acceptée mais limitée au minimum)
     */
    public function test_convert_accepts_zero_values(): void
    {
        $rawData = [
            'id' => 31,
            'name' => ['fr' => 'Monstre'],
            'level' => 0,
            'lifePoints' => 0,
            'grades' => [
                [
                    'level' => 0,
                    'lifePoints' => 0,
                    'strength' => 0,
                    'intelligence' => 0,
                    'agility' => 0,
                    'wisdom' => 0,
                    'chance' => 0
                ]
            ],
            'size' => 'medium'
        ];

        $result = $this->service->convertMonster($rawData);

        $this->assertIsArray($result);
        // Le niveau 0 est converti au minimum (1) selon les limites
        $this->assertEquals(1, $result['creatures']['level']);
        // La vie 0 est convertie au minimum (1) selon les limites
        $this->assertEquals(1, $result['creatures']['life']);
    }
}

