<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Services\Seeder\Resource\MarkPlayableItemRecipeResources;
use Tests\TestCase;

final class MarkPlayableItemRecipeResourcesTest extends TestCase
{
    public function test_marks_recipe_resources_of_playable_items_only(): void
    {
        $playableItem = Item::factory()->create(['state' => Item::STATE_PLAYABLE]);
        $draftItem = Item::factory()->create(['state' => Item::STATE_DRAFT]);

        $recipeResource = Resource::factory()->create([
            'state' => Resource::STATE_DRAFT,
            'name' => 'Patte d\'Arakne',
            'created_by' => null,
        ]);
        $unusedResource = Resource::factory()->create([
            'state' => Resource::STATE_DRAFT,
            'name' => 'Clef des Champs',
            'created_by' => null,
        ]);
        $draftOnlyResource = Resource::factory()->create([
            'state' => Resource::STATE_DRAFT,
            'name' => 'Essence hors recette jouable',
            'created_by' => null,
        ]);

        $playableItem->resources()->attach($recipeResource->id, ['quantity' => 3]);
        $draftItem->resources()->attach($draftOnlyResource->id, ['quantity' => 1]);

        $count = app(MarkPlayableItemRecipeResources::class)->mark();

        $this->assertSame(1, $count);
        $this->assertSame(Resource::STATE_PLAYABLE, $recipeResource->fresh()->state);
        $this->assertSame(Resource::STATE_DRAFT, $unusedResource->fresh()->state);
        $this->assertSame(Resource::STATE_DRAFT, $draftOnlyResource->fresh()->state);
        $this->assertSame('Patte d\'Arakne', $recipeResource->fresh()->name);
        $this->assertSame(0, app(MarkPlayableItemRecipeResources::class)->mark());
    }
}
