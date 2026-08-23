<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\User;
use App\Support\Entity\ItemPanoplyPayload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le payload panoplie d’un équipement public ne fuit pas les sets / pièces brouillon.
 */
class ItemPanoplyVisibilityTest extends TestCase
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

    public function test_guest_show_hides_draft_panoply_on_playable_item(): void
    {
        $hat = Item::factory()->create($this->playableAttrs(['name' => 'Coiffe publique']));
        $draftPanoply = Panoply::factory()->create($this->draftAttrs([
            'name' => 'Set secret brouillon',
            'bonus' => json_encode(['2' => ['strength' => 99]], JSON_THROW_ON_ERROR),
        ]));
        $playablePanoply = Panoply::factory()->create($this->playableAttrs([
            'name' => 'Set public',
            'bonus' => json_encode(['2' => ['vitality' => 1]], JSON_THROW_ON_ERROR),
        ]));
        $draftPanoply->items()->sync([$hat->id]);
        $playablePanoply->items()->sync([$hat->id]);

        $this->get(route('entities.items.show', $hat))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Pages/entity/item/Show')
                ->has('item.data.panoplies', 1)
                ->where('item.data.panoplies.0.name', 'Set public')
            );
    }

    public function test_guest_show_hides_draft_sibling_items_inside_playable_panoply(): void
    {
        $hat = Item::factory()->create($this->playableAttrs(['name' => 'Coiffe publique']));
        $secretCape = Item::factory()->create($this->draftAttrs(['name' => 'Cape secrète']));
        $panoply = Panoply::factory()->create($this->playableAttrs([
            'name' => 'Set mixte',
            'bonus' => json_encode(['2' => ['chance' => 2]], JSON_THROW_ON_ERROR),
        ]));
        $panoply->items()->sync([$hat->id, $secretCape->id]);

        $this->get(route('entities.items.show', $hat))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Pages/entity/item/Show')
                ->has('item.data.panoplies', 1)
                ->has('item.data.panoplies.0.items', 1)
                ->where('item.data.panoplies.0.items.0.name', 'Coiffe publique')
            );
    }

    public function test_player_table_api_hides_draft_panoply_and_sibling_items(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $hat = Item::factory()->create($this->playableAttrs(['name' => 'Coiffe table']));
        $secretCape = Item::factory()->create($this->draftAttrs(['name' => 'Cape table secrète']));
        $draftPanoply = Panoply::factory()->create($this->draftAttrs([
            'name' => 'Set table brouillon',
            'bonus' => json_encode(['2' => ['intel' => 50]], JSON_THROW_ON_ERROR),
        ]));
        $playablePanoply = Panoply::factory()->create($this->playableAttrs([
            'name' => 'Set table public',
            'bonus' => json_encode(['2' => ['agi' => 3]], JSON_THROW_ON_ERROR),
        ]));
        $draftPanoply->items()->sync([$hat->id]);
        $playablePanoply->items()->sync([$hat->id, $secretCape->id]);

        $response = $this->actingAs($player)
            ->getJson('/api/tables/items?format=entities&limit=20&whitelist[]='.$hat->id);

        $response->assertOk();
        $entity = collect($response->json('entities'))->firstWhere('id', $hat->id);
        $this->assertIsArray($entity);
        $this->assertSame(1, $entity['panoplies_count']);
        $this->assertCount(1, $entity['panoplies']);
        $this->assertSame('Set table public', $entity['panoplies'][0]['name']);
        $this->assertCount(1, $entity['panoplies'][0]['items']);
        $this->assertSame('Coiffe table', $entity['panoplies'][0]['items'][0]['name']);
    }

    public function test_game_master_still_sees_draft_panoply_on_playable_item(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $hat = Item::factory()->create($this->playableAttrs(['name' => 'Coiffe MJ']));
        $draftPanoply = Panoply::factory()->create($this->draftAttrs([
            'name' => 'Set MJ brouillon',
            'bonus' => json_encode(['2' => ['pa' => 1]], JSON_THROW_ON_ERROR),
        ]));
        $draftPanoply->items()->sync([$hat->id]);

        $this->actingAs($gm)
            ->get(route('entities.items.show', $hat))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Pages/entity/item/Show')
                ->has('item.data.panoplies', 1)
                ->where('item.data.panoplies.0.name', 'Set MJ brouillon')
            );
    }

    public function test_from_item_filters_already_loaded_hidden_relations(): void
    {
        $hat = Item::factory()->create($this->playableAttrs(['name' => 'Coiffe déjà chargée']));
        $secretCape = Item::factory()->create($this->draftAttrs(['name' => 'Cape déjà chargée']));
        $draftPanoply = Panoply::factory()->create($this->draftAttrs([
            'name' => 'Set déjà chargé',
            'bonus' => json_encode(['2' => ['po' => 1]], JSON_THROW_ON_ERROR),
        ]));
        $draftPanoply->items()->sync([$hat->id, $secretCape->id]);

        $hat->load('panoplies.items');

        $this->assertSame([], ItemPanoplyPayload::fromItem($hat, null));
    }
}
