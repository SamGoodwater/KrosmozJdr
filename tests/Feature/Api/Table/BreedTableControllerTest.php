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

    /**
     * Un joueur ne doit pas voir les sorts / capacités brouillon via une classe jouable.
     */
    public function test_format_entities_hides_draft_nested_spells_and_capabilities_from_players(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $breed = Breed::factory()->create([
            'name' => 'Classe Jouable',
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);

        $playableSpell = Spell::factory()->create([
            'name' => 'Sort public',
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpell = Spell::factory()->create([
            'name' => 'Sort secret WIP',
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $breed->spells()->attach($playableSpell->id, [
            'character_level' => 1,
            'slot_index' => 1,
            'choice_order' => 0,
        ]);
        $breed->spells()->attach($draftSpell->id, [
            'character_level' => 1,
            'slot_index' => 2,
            'choice_order' => 0,
        ]);

        $playableCap = Capability::factory()->create([
            'name' => 'Passif public',
            'state' => Capability::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftCap = Capability::factory()->create([
            'name' => 'Passif brouillon',
            'state' => Capability::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $breed->capabilities()->attach([$playableCap->id, $draftCap->id]);

        $playerResponse = $this->actingAs($player)
            ->getJson('/api/tables/breeds?format=entities&limit=10');

        $playerResponse->assertOk();
        $playerEntity = collect($playerResponse->json('entities'))->firstWhere('id', $breed->id);
        $this->assertNotNull($playerEntity);
        $this->assertSame(1, $playerEntity['spells_count']);
        $this->assertEqualsCanonicalizing(['Sort public'], collect($playerEntity['spells'])->pluck('name')->all());
        $this->assertEqualsCanonicalizing(['Passif public'], collect($playerEntity['capabilities'])->pluck('name')->all());

        $adminResponse = $this->actingAs($author)
            ->getJson('/api/tables/breeds?format=entities&limit=10');

        $adminResponse->assertOk();
        $adminEntity = collect($adminResponse->json('entities'))->firstWhere('id', $breed->id);
        $this->assertNotNull($adminEntity);
        $this->assertSame(2, $adminEntity['spells_count']);
        $this->assertEqualsCanonicalizing(
            ['Sort public', 'Sort secret WIP'],
            collect($adminEntity['spells'])->pluck('name')->all()
        );
        $this->assertEqualsCanonicalizing(
            ['Passif public', 'Passif brouillon'],
            collect($adminEntity['capabilities'])->pluck('name')->all()
        );
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
