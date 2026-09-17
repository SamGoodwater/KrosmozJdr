<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pièces liées d’une panoplie jouable : pas de fuite des brouillons vers un joueur.
 */
class PanoplyNestedItemsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    /**
     * @return array{0: User, 1: User, 2: Panoply, 3: Item, 4: Item}
     */
    private function playablePanoplyWithHiddenPiece(): array
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $panoply = Panoply::factory()->create([
            'name' => 'Panoplie du Gouffre',
            'state' => Panoply::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);

        $visible = Item::factory()->create([
            'name' => 'Coiffe publique',
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $hidden = Item::factory()->create([
            'name' => 'Épée secrète',
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $panoply->items()->attach([$visible->id, $hidden->id]);

        return [$gm, $player, $panoply, $visible, $hidden];
    }

    public function test_player_table_payload_omits_draft_items_on_playable_panoply(): void
    {
        [, $player, $panoply, $visible, $hidden] = $this->playablePanoplyWithHiddenPiece();

        $response = $this->actingAs($player)
            ->getJson('/api/tables/panoplies?format=entities&limit=10');

        $response->assertOk();
        $entity = collect($response->json('entities'))->firstWhere('id', $panoply->id);
        $this->assertIsArray($entity);
        $ids = collect($entity['items'] ?? [])->pluck('id')->all();
        $this->assertContains($visible->id, $ids);
        $this->assertNotContains($hidden->id, $ids);
        $this->assertSame(1, $entity['items_count']);
    }

    public function test_guest_table_payload_omits_draft_items_on_playable_panoply(): void
    {
        [, , $panoply, $visible, $hidden] = $this->playablePanoplyWithHiddenPiece();

        $response = $this->getJson('/api/tables/panoplies?format=entities&limit=10');

        $response->assertOk();
        $entity = collect($response->json('entities'))->firstWhere('id', $panoply->id);
        $this->assertIsArray($entity);
        $ids = collect($entity['items'] ?? [])->pluck('id')->all();
        $this->assertContains($visible->id, $ids);
        $this->assertNotContains($hidden->id, $ids);
    }

    public function test_player_show_page_omits_draft_items_on_playable_panoply(): void
    {
        [, $player, $panoply, $visible, $hidden] = $this->playablePanoplyWithHiddenPiece();

        $response = $this->actingAs($player)
            ->get(route('entities.panoplies.show', $panoply));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/panoply/Show')
            ->where('panoply.data.items', function ($items) use ($visible, $hidden) {
                $ids = collect($items)->pluck('id')->all();

                return in_array($visible->id, $ids, true)
                    && ! in_array($hidden->id, $ids, true);
            })
        );
    }

    public function test_gm_edit_page_still_lists_draft_items(): void
    {
        [$gm, , $panoply, $visible, $hidden] = $this->playablePanoplyWithHiddenPiece();

        $response = $this->actingAs($gm)
            ->get(route('entities.panoplies.edit', $panoply));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/panoply/Edit')
            ->where('panoply.data.items', function ($items) use ($visible, $hidden) {
                $ids = collect($items)->pluck('id')->all();

                return in_array($visible->id, $ids, true)
                    && in_array($hidden->id, $ids, true);
            })
        );
    }

    public function test_gm_table_payload_includes_draft_items(): void
    {
        [$gm, , $panoply, $visible, $hidden] = $this->playablePanoplyWithHiddenPiece();

        $response = $this->actingAs($gm)
            ->getJson('/api/tables/panoplies?format=entities&limit=10');

        $response->assertOk();
        $entity = collect($response->json('entities'))->firstWhere('id', $panoply->id);
        $this->assertIsArray($entity);
        $ids = collect($entity['items'] ?? [])->pluck('id')->all();
        $this->assertContains($visible->id, $ids);
        $this->assertContains($hidden->id, $ids);
        $this->assertSame(2, $entity['items_count']);
    }
}
