<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Campaign;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Liaisons d’une ressource jouable : pas de fuite des brouillons vers un joueur.
 */
class ResourceNestedVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    /**
     * @return array{
     *     0: User,
     *     1: User,
     *     2: resource,
     *     3: Item,
     *     4: Item,
     *     5: Consumable,
     *     6: Consumable,
     *     7: Campaign,
     *     8: Campaign
     * }
     */
    private function playableResourceWithHiddenLinks(): array
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $resource = Resource::factory()->create([
            'name' => 'Bois de frêne',
            'state' => Resource::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);

        $visibleItem = Item::factory()->create([
            'name' => 'Hache publique',
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $hiddenItem = Item::factory()->create([
            'name' => 'Épée secrète',
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $resource->items()->attach([
            $visibleItem->id => ['quantity' => '1'],
            $hiddenItem->id => ['quantity' => '2'],
        ]);

        $visibleConsumable = Consumable::factory()->create([
            'name' => 'Potion publique',
            'state' => Consumable::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $hiddenConsumable = Consumable::factory()->create([
            'name' => 'Décoction secrète',
            'state' => Consumable::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $resource->consumables()->attach([
            $visibleConsumable->id => ['quantity' => '1'],
            $hiddenConsumable->id => ['quantity' => '3'],
        ]);

        $visibleCampaign = Campaign::factory()->create([
            'name' => 'Campagne publique',
            'state' => Campaign::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $hiddenCampaign = Campaign::factory()->create([
            'name' => 'Campagne secrète',
            'state' => Campaign::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $resource->campaigns()->attach([$visibleCampaign->id, $hiddenCampaign->id]);

        return [
            $gm,
            $player,
            $resource,
            $visibleItem,
            $hiddenItem,
            $visibleConsumable,
            $hiddenConsumable,
            $visibleCampaign,
            $hiddenCampaign,
        ];
    }

    public function test_player_show_page_omits_draft_links_on_playable_resource(): void
    {
        [, $player, $resource, $visibleItem, $hiddenItem, $visibleConsumable, $hiddenConsumable, $visibleCampaign, $hiddenCampaign] = $this->playableResourceWithHiddenLinks();

        $response = $this->actingAs($player)
            ->get(route('entities.resources.show', $resource));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/resource/Show')
            ->where('resource.data.items', function ($items) use ($visibleItem, $hiddenItem) {
                $ids = collect($items)->pluck('id')->all();

                return in_array($visibleItem->id, $ids, true)
                    && ! in_array($hiddenItem->id, $ids, true);
            })
            ->where('resource.data.consumables', function ($consumables) use ($visibleConsumable, $hiddenConsumable) {
                $ids = collect($consumables)->pluck('id')->all();

                return in_array($visibleConsumable->id, $ids, true)
                    && ! in_array($hiddenConsumable->id, $ids, true);
            })
            ->where('resource.data.campaigns', function ($campaigns) use ($visibleCampaign, $hiddenCampaign) {
                $ids = collect($campaigns)->pluck('id')->all();

                return in_array($visibleCampaign->id, $ids, true)
                    && ! in_array($hiddenCampaign->id, $ids, true);
            })
        );
    }

    public function test_guest_show_page_omits_draft_links_on_playable_resource(): void
    {
        [, , $resource, $visibleItem, $hiddenItem, $visibleConsumable, $hiddenConsumable, $visibleCampaign, $hiddenCampaign] = $this->playableResourceWithHiddenLinks();

        $response = $this->get(route('entities.resources.show', $resource));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/resource/Show')
            ->where('resource.data.items', function ($items) use ($visibleItem, $hiddenItem) {
                $ids = collect($items)->pluck('id')->all();

                return in_array($visibleItem->id, $ids, true)
                    && ! in_array($hiddenItem->id, $ids, true);
            })
            ->where('resource.data.consumables', function ($consumables) use ($visibleConsumable, $hiddenConsumable) {
                $ids = collect($consumables)->pluck('id')->all();

                return in_array($visibleConsumable->id, $ids, true)
                    && ! in_array($hiddenConsumable->id, $ids, true);
            })
            ->where('resource.data.campaigns', function ($campaigns) use ($visibleCampaign, $hiddenCampaign) {
                $ids = collect($campaigns)->pluck('id')->all();

                return in_array($visibleCampaign->id, $ids, true)
                    && ! in_array($hiddenCampaign->id, $ids, true);
            })
        );
    }

    public function test_gm_edit_page_still_lists_draft_links(): void
    {
        [$gm, , $resource, $visibleItem, $hiddenItem, $visibleConsumable, $hiddenConsumable, $visibleCampaign, $hiddenCampaign] = $this->playableResourceWithHiddenLinks();

        $response = $this->actingAs($gm)
            ->get(route('entities.resources.edit', $resource));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/resource/Edit')
            ->where('resource.data.items', function ($items) use ($visibleItem, $hiddenItem) {
                $ids = collect($items)->pluck('id')->all();

                return in_array($visibleItem->id, $ids, true)
                    && in_array($hiddenItem->id, $ids, true);
            })
            ->where('resource.data.consumables', function ($consumables) use ($visibleConsumable, $hiddenConsumable) {
                $ids = collect($consumables)->pluck('id')->all();

                return in_array($visibleConsumable->id, $ids, true)
                    && in_array($hiddenConsumable->id, $ids, true);
            })
            ->where('resource.data.campaigns', function ($campaigns) use ($visibleCampaign, $hiddenCampaign) {
                $ids = collect($campaigns)->pluck('id')->all();

                return in_array($visibleCampaign->id, $ids, true)
                    && in_array($hiddenCampaign->id, $ids, true);
            })
        );
    }
}
