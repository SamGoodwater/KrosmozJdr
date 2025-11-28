<?php

namespace Tests\Feature\Type;

use App\Models\User;
use App\Models\Type\ResourceType;
use App\Models\Entity\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle ResourceType
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Type
 */
class ResourceTypeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un type de ressource via factory
     */
    public function test_resource_type_factory_creates_valid_resource_type(): void
    {
        $user = User::factory()->create();
        
        $resourceType = ResourceType::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($resourceType);
        $this->assertNotNull($resourceType->id);
        $this->assertNotNull($resourceType->name);
        $this->assertEquals($user->id, $resourceType->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_resource_type_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $resourceType = ResourceType::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($resourceType->createdBy);
        $this->assertEquals($user->id, $resourceType->createdBy->id);
    }

    /**
     * Test de la relation resources (hasMany)
     */
    public function test_resource_type_has_resources_relation(): void
    {
        $user = User::factory()->create();
        $resourceType = ResourceType::factory()->create([
            'created_by' => $user->id,
        ]);

        $resource1 = Resource::factory()->create([
            'created_by' => $user->id,
            'resource_type_id' => $resourceType->id,
        ]);
        $resource2 = Resource::factory()->create([
            'created_by' => $user->id,
            'resource_type_id' => $resourceType->id,
        ]);

        $resourceType->refresh();
        $this->assertCount(2, $resourceType->resources);
        $this->assertTrue($resourceType->resources->contains($resource1));
        $this->assertTrue($resourceType->resources->contains($resource2));
    }
}

