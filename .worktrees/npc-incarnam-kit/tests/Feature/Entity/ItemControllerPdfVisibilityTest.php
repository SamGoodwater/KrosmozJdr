<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PDF multi items : filtrage visibleToUser.
 */
class ItemControllerPdfVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_pdf_excludes_draft_items_for_player(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $visible = Item::factory()->create([
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $hidden = Item::factory()->create([
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->actingAs($player)
            ->get(route('entities.items.pdf', $visible).'?'.http_build_query([
                'ids' => [$visible->id, $hidden->id],
            ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_admin_multi_pdf_includes_draft_items(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $draft = Item::factory()->create([
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('entities.items.pdf', $draft).'?'.http_build_query([
                'ids' => [$draft->id],
            ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }
}
