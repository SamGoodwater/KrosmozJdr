<?php

declare(strict_types=1);

namespace Tests\Feature\Type;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypeRegistryAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_master_is_forbidden_from_item_type_registry(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('entities.item-types.index'))
            ->assertForbidden();
    }

    public function test_game_master_is_forbidden_from_resource_type_registry_index(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('entities.resource-types.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_item_type_registry(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('entities.item-types.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Pages/entity/item-type/Index'));
    }

    public function test_admin_can_view_monster_race_registry(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('entities.monster-races.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Pages/entity/monster-race/Index'));
    }
}
