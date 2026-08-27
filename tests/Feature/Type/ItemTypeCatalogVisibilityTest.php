<?php

declare(strict_types=1);

namespace Tests\Feature\Type;

use App\Models\Type\ItemType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTypeCatalogVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_show_in_catalog(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $type = ItemType::factory()->create([
            'dofusdb_type_id' => 1,
            'decision' => ItemType::DECISION_ALLOWED,
            'show_in_catalog' => false,
        ]);

        $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->patchJson("/api/dofusdb/item-types/{$type->id}/catalog", [
                'show_in_catalog' => true,
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('show_in_catalog', true);

        $this->assertTrue($type->fresh()->show_in_catalog);
    }

    public function test_game_master_cannot_toggle_show_in_catalog(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $type = ItemType::factory()->create([
            'dofusdb_type_id' => 1,
            'show_in_catalog' => false,
        ]);

        $this->actingAs($gm)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->patchJson("/api/dofusdb/item-types/{$type->id}/catalog", [
                'show_in_catalog' => true,
            ])
            ->assertForbidden();

        $this->assertFalse((bool) $type->fresh()->show_in_catalog);
    }

    public function test_bulk_catalog_updates_selected_types(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $visible = ItemType::factory()->create([
            'dofusdb_type_id' => 1,
            'show_in_catalog' => false,
        ]);
        $hidden = ItemType::factory()->create([
            'dofusdb_type_id' => 199,
            'show_in_catalog' => true,
        ]);

        $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->patchJson('/api/dofusdb/item-types/bulk', [
                'ids' => [$visible->id, $hidden->id],
                'show_in_catalog' => true,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertTrue($visible->fresh()->show_in_catalog);
        $this->assertTrue($hidden->fresh()->show_in_catalog);
    }
}
