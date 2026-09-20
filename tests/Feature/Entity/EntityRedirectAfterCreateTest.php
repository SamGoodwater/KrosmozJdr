<?php

namespace Tests\Feature\Entity;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Item;
use App\Models\Entity\Npc;
use App\Models\Entity\Resource;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Q9 — redirection post-création via redirect_after_create=edit.
 */
class EntityRedirectAfterCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([CheckRole::class, PreventRequestForgery::class]);
    }

    public function test_resource_store_with_redirect_after_create_goes_to_edit(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->post(route('entities.resources.store'), [
                'name' => 'Ressource Q9',
                'description' => 'Test',
                'redirect_after_create' => 'edit',
            ]);

        $resource = Resource::query()->where('name', 'Ressource Q9')->first();
        $this->assertNotNull($resource);
        $response->assertRedirect(route('entities.resources.edit', $resource));
    }

    public function test_resource_store_without_redirect_goes_to_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->post(route('entities.resources.store'), [
                'name' => 'Ressource index',
                'description' => 'Test',
            ]);

        $response->assertRedirect(route('entities.resources.index'));
    }

    public function test_item_store_with_redirect_after_create_goes_to_edit(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->post(route('entities.items.store'), [
                'name' => 'Objet Q9',
                'description' => 'Test objet',
                'redirect_after_create' => 'edit',
            ]);

        $item = Item::query()->where('name', 'Objet Q9')->first();
        $this->assertNotNull($item);
        $response->assertRedirect(route('entities.items.edit', $item));
    }

    public function test_resource_store_json_returns_id_and_edit_url(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->postJson(route('entities.resources.store'), [
                'name' => 'Ressource JSON',
                'description' => 'Brief IA',
            ]);

        $resource = Resource::query()->where('name', 'Ressource JSON')->first();
        $this->assertNotNull($resource);
        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('id', $resource->id)
            ->assertJsonPath('edit_url', route('entities.resources.edit', $resource));
    }

    public function test_npc_store_json_returns_id_and_edit_url(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->postJson(route('entities.npcs.store'), [
                'name' => 'PNJ JSON',
                'description' => 'Garde d’Astrub',
                'state' => 'draft',
            ]);

        $npc = Npc::query()->whereHas('creature', fn ($q) => $q->where('name', 'PNJ JSON'))->first();
        $this->assertNotNull($npc);
        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('id', $npc->id)
            ->assertJsonPath('edit_url', route('entities.npcs.edit', $npc));
    }
}
