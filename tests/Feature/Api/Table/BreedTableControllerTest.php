<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Breed;
use App\Models\Entity\Capability;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour BreedTableController (TanStack Table — Classes).
 *
 * @description
 * Vérifie notamment le format `entities` avec sorts et capacités pour les aperçus riches.
 */
class BreedTableControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    public function test_format_entities_includes_spells_capabilities_and_passive_flag(): void
    {
        $user = User::factory()->create();
        $breed = Breed::factory()->create([
            'name' => 'Classe Test UX',
            'created_by' => $user->id,
        ]);

        $spell = Spell::factory()->create([
            'name' => 'Sort Pivot',
            'created_by' => $user->id,
        ]);
        $breed->spells()->attach($spell->id, [
            'character_level' => 2,
            'slot_index' => 3,
            'choice_order' => 0,
        ]);

        $cap = Capability::factory()->create([
            'name' => 'Passif test',
            'created_by' => $user->id,
            'is_passive' => true,
        ]);
        $breed->capabilities()->attach($cap->id);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/breeds?format=entities&limit=10');

        $response->assertOk()
            ->assertJsonPath('meta.entityType', 'breeds')
            ->assertJsonPath('meta.format', 'entities');

        $data = $response->json();
        $this->assertArrayHasKey('entities', $data);
        $this->assertCount(1, $data['entities']);

        $entity = $data['entities'][0];
        $this->assertSame('Classe Test UX', $entity['name']);
        $this->assertArrayHasKey('spells', $entity);
        $this->assertArrayHasKey('capabilities', $entity);
        $this->assertArrayHasKey('element_orientations', $entity);
        $this->assertCount(1, $entity['spells']);
        $this->assertSame('Sort Pivot', $entity['spells'][0]['name']);
        $this->assertCount(1, $entity['capabilities']);
        $this->assertTrue($entity['capabilities'][0]['is_passive']);
        $this->assertSame('Passif test', $entity['capabilities'][0]['name']);
    }

    public function test_format_cells_returns_rows_without_entities_payload(): void
    {
        $user = User::factory()->create();
        Breed::factory()->create([
            'name' => 'Classe Cells',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/breeds?limit=10');

        $response->assertOk()
            ->assertJsonStructure([
                'meta' => ['entityType', 'query', 'capabilities'],
                'rows' => [
                    '*' => [
                        'id',
                        'cells' => ['name'],
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('rows', $data);
        $this->assertArrayNotHasKey('entities', $data);
    }
}
