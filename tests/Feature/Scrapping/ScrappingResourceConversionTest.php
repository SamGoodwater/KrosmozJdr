<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use App\Models\Entity\Resource;
use App\Models\Type\ResourceType;
use App\Services\Scrapping\DataConversion\DataConversionService;
use App\Services\Scrapping\DataIntegration\DataIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration : item DofusDB "ressource" -> conversion -> intégration en base.
 *
 * Objectif : garantir qu'un item DofusDB de type ressource (typeId 15/35)
 * termine bien dans la table `resources` (et pas `items`) avec un `resource_type_id`.
 */
class ScrappingResourceConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_type_resource_is_converted_and_integrated_as_resource_model(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $this->actingAs($admin);

        // Registry DB: typeId 15 doit exister et être autorisé pour être assigné à resource_type_id
        $resourceType = ResourceType::factory()->create([
            'name' => 'Minerai',
            'dofusdb_type_id' => 15,
            'decision' => 'allowed',
            'created_by' => $admin->id,
        ]);

        $rawItem = [
            'id' => 123,
            'typeId' => 15, // resource
            'name' => ['fr' => 'Fer'],
            'description' => ['fr' => 'Minerai brut.'],
            'level' => 10,
            'rarity' => 'rare',
            'price' => 1000,
            'img' => 'https://example.test/iron.png',
        ];

        $conversion = new DataConversionService();
        $converted = $conversion->convertItem($rawItem);

        $this->assertSame('resource', $converted['type']);
        $this->assertSame('resource', $converted['category']);
        $this->assertSame(15, $converted['type_id']);

        $integration = new DataIntegrationService();
        $result = $integration->integrateItem($converted);

        $this->assertSame('resources', $result['table'] ?? null);

        $this->assertDatabaseHas('resources', [
            'dofusdb_id' => '123',
            'name' => 'Fer',
            'resource_type_id' => $resourceType->id,
            'created_by' => $admin->id,
        ]);

        $this->assertDatabaseCount('resources', 1);
        $this->assertDatabaseCount('items', 0);

        $resource = Resource::firstOrFail();
        $this->assertSame($resourceType->id, $resource->resource_type_id);
    }
}


