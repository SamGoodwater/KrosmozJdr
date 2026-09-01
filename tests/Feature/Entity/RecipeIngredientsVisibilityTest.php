<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Recettes d’objets / consommables / ressources : pas de fuite d’ingrédients brouillon.
 */
class RecipeIngredientsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function playableAttrs(array $overrides = []): array
    {
        return array_merge([
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function draftAttrs(array $overrides = []): array
    {
        return array_merge([
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ], $overrides);
    }

    /**
     * @return array{0: User, 1: Item, 2: resource, 3: resource}
     */
    private function playableItemWithMixedRecipe(): array
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create($this->playableAttrs([
            'name' => 'Gelano public',
            'created_by' => $gm->id,
        ]));
        $visible = Resource::factory()->create($this->playableAttrs([
            'name' => 'Or public',
            'created_by' => $gm->id,
        ]));
        $hidden = Resource::factory()->create($this->draftAttrs([
            'name' => 'Lingot secret',
            'created_by' => $gm->id,
        ]));
        $item->resources()->attach([
            $visible->id => ['quantity' => 2],
            $hidden->id => ['quantity' => 1],
        ]);

        return [$gm, $item, $visible, $hidden];
    }

    public function test_player_item_table_omits_draft_recipe_ingredients(): void
    {
        [, $item, $visible, $hidden] = $this->playableItemWithMixedRecipe();
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $response = $this->actingAs($player)
            ->getJson('/api/tables/items?format=entities&limit=10');

        $response->assertOk();
        $entity = collect($response->json('entities'))->firstWhere('id', $item->id);
        $this->assertIsArray($entity);
        $ids = collect($entity['resources'] ?? [])->pluck('id')->all();
        $this->assertContains($visible->id, $ids);
        $this->assertNotContains($hidden->id, $ids);
        $this->assertSame(1, $entity['resources_count']);
        $this->assertStringNotContainsString('Lingot secret', $response->getContent());
    }

    public function test_guest_item_show_omits_draft_recipe_ingredients(): void
    {
        [, $item, $visible] = $this->playableItemWithMixedRecipe();

        $this->get(route('entities.items.show', $item))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Pages/entity/item/Show')
                ->has('item.data.resources', 1)
                ->where('item.data.resources.0.id', $visible->id)
                ->where('item.data.resources.0.name', 'Or public')
            );
    }

    public function test_gm_item_edit_still_loads_draft_recipe_ingredients(): void
    {
        [$gm, $item, $visible, $hidden] = $this->playableItemWithMixedRecipe();

        $this->actingAs($gm)
            ->get(route('entities.items.edit', $item))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Pages/entity/item/Edit')
                ->where('item.data.resources', function ($resources) use ($visible, $hidden) {
                    $ids = collect($resources)->pluck('id')->all();

                    return in_array($visible->id, $ids, true)
                        && in_array($hidden->id, $ids, true);
                })
            );
    }

    public function test_player_consumable_table_omits_draft_recipe_ingredients(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $consumable = Consumable::factory()->create($this->playableAttrs([
            'name' => 'Potion publique',
            'created_by' => $gm->id,
        ]));
        $visible = Resource::factory()->create($this->playableAttrs([
            'name' => 'Eau publique',
            'created_by' => $gm->id,
        ]));
        $hidden = Resource::factory()->create($this->draftAttrs([
            'name' => 'Essence secrète',
            'created_by' => $gm->id,
        ]));
        $consumable->resources()->attach([
            $visible->id => ['quantity' => 1],
            $hidden->id => ['quantity' => 1],
        ]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $response = $this->actingAs($player)
            ->getJson('/api/tables/consumables?format=entities&limit=10');

        $response->assertOk();
        $entity = collect($response->json('entities'))->firstWhere('id', $consumable->id);
        $this->assertIsArray($entity);
        $ids = collect($entity['resources'] ?? [])->pluck('id')->all();
        $this->assertContains($visible->id, $ids);
        $this->assertNotContains($hidden->id, $ids);
        $this->assertSame(1, $entity['resources_count']);
        $this->assertStringNotContainsString('Essence secrète', $response->getContent());
    }

    public function test_guest_consumable_show_omits_draft_recipe_ingredients(): void
    {
        $consumable = Consumable::factory()->create($this->playableAttrs(['name' => 'Pain public']));
        $visible = Resource::factory()->create($this->playableAttrs(['name' => 'Farine publique']));
        $hidden = Resource::factory()->create($this->draftAttrs(['name' => 'Levure secrète']));
        $consumable->resources()->attach([
            $visible->id => ['quantity' => 3],
            $hidden->id => ['quantity' => 1],
        ]);

        $this->get(route('entities.consumables.show', $consumable))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Pages/entity/consumable/Show')
                ->has('consumable.data.resources', 1)
                ->where('consumable.data.resources.0.id', $visible->id)
            );
    }

    public function test_player_resource_table_omits_draft_recipe_ingredients(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $crafted = Resource::factory()->create($this->playableAttrs([
            'name' => 'Planche publique',
            'created_by' => $gm->id,
        ]));
        $visible = Resource::factory()->create($this->playableAttrs([
            'name' => 'Bois public',
            'created_by' => $gm->id,
        ]));
        $hidden = Resource::factory()->create($this->draftAttrs([
            'name' => 'Écorce secrète',
            'created_by' => $gm->id,
        ]));
        $crafted->recipeIngredients()->attach([
            $visible->id => ['quantity' => 4],
            $hidden->id => ['quantity' => 1],
        ]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $response = $this->actingAs($player)
            ->getJson('/api/tables/resources?format=entities&limit=20');

        $response->assertOk();
        $entity = collect($response->json('entities'))->firstWhere('id', $crafted->id);
        $this->assertIsArray($entity);
        $ids = collect($entity['recipe_ingredients'] ?? [])->pluck('id')->all();
        $this->assertContains($visible->id, $ids);
        $this->assertNotContains($hidden->id, $ids);
        $this->assertSame(1, $entity['recipe_ingredients_count']);
        $this->assertStringNotContainsString('Écorce secrète', $response->getContent());
    }

    public function test_guest_resource_show_omits_draft_recipe_ingredients(): void
    {
        $crafted = Resource::factory()->create($this->playableAttrs(['name' => 'Lingot public']));
        $visible = Resource::factory()->create($this->playableAttrs(['name' => 'Minerai public']));
        $hidden = Resource::factory()->create($this->draftAttrs(['name' => 'Poudre secrète']));
        $crafted->recipeIngredients()->attach([
            $visible->id => ['quantity' => 2],
            $hidden->id => ['quantity' => 1],
        ]);

        $this->get(route('entities.resources.show', $crafted))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Pages/entity/resource/Show')
                ->has('resource.data.recipeIngredients', 1)
                ->where('resource.data.recipeIngredients.0.id', $visible->id)
            );
    }

    public function test_gm_resource_edit_still_loads_draft_recipe_ingredients(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $crafted = Resource::factory()->create($this->playableAttrs([
            'name' => 'Tissu public',
            'created_by' => $gm->id,
        ]));
        $visible = Resource::factory()->create($this->playableAttrs([
            'name' => 'Fil public',
            'created_by' => $gm->id,
        ]));
        $hidden = Resource::factory()->create($this->draftAttrs([
            'name' => 'Soie secrète',
            'created_by' => $gm->id,
        ]));
        $crafted->recipeIngredients()->attach([
            $visible->id => ['quantity' => 2],
            $hidden->id => ['quantity' => 1],
        ]);

        $this->actingAs($gm)
            ->get(route('entities.resources.edit', $crafted))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Pages/entity/resource/Edit')
                ->where('resource.data.recipeIngredients', function ($ingredients) use ($visible, $hidden) {
                    $ids = collect($ingredients)->pluck('id')->all();

                    return in_array($visible->id, $ids, true)
                        && in_array($hidden->id, $ids, true);
                })
            );
    }
}
