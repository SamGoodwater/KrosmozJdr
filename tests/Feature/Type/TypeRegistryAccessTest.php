<?php

declare(strict_types=1);

namespace Tests\Feature\Type;

use App\Models\Type\ResourceType;
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

    public function test_game_master_is_forbidden_from_unified_type_registry(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.content.types.show', ['kind' => 'equipment']))
            ->assertForbidden();
    }

    public function test_game_master_is_forbidden_from_resource_type_registry_index(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('entities.resource-types.index'))
            ->assertForbidden();
    }

    public function test_admin_is_redirected_from_legacy_item_type_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('entities.item-types.index'))
            ->assertRedirect(route('admin.content.types.show', ['kind' => 'equipment']));
    }

    public function test_admin_can_view_unified_type_registry(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.content.types.show', ['kind' => 'race']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Content/Types/Index')
                ->where('kind', 'race'));
    }

    public function test_game_master_is_forbidden_from_resource_type_show(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $type = ResourceType::factory()->create();

        $this->actingAs($gm)
            ->get(route('entities.resource-types.show', $type))
            ->assertForbidden();
    }

    public function test_admin_resource_type_show_and_edit_redirect_to_unified_registry(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $type = ResourceType::factory()->create();

        $this->actingAs($admin)
            ->get(route('entities.resource-types.show', $type))
            ->assertRedirect(route('admin.content.types.show', ['kind' => 'resource']));

        $this->actingAs($admin)
            ->get(route('entities.resource-types.edit', $type))
            ->assertRedirect(route('admin.content.types.show', ['kind' => 'resource']));
    }
}
