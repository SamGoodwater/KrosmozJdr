<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_routes()
    {
        $page = Page::factory()->create();

        $routes = [
            route('pages.create'),
            route('pages.store'),
            route('pages.edit', $page),
            route('pages.update', $page),
            route('pages.delete', $page)
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(route('login'));
        }
    }

    public function test_admin_can_force_delete_page()
    {
        $admin = User::factory()->create(['role' => User::ROLES['admin']]);
        $page = Page::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.pages.forceDelete', $page));

        $response->assertRedirect(route('pages.index'));
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_guest_can_view_public_page()
    {
        $page = Page::factory()->create(['is_public' => true]);

        $response = $this->get(route('pages.show', $page));

        $response->assertStatus(200);
    }

    public function test_guest_cannot_view_private_page()
    {
        $page = Page::factory()->create(['is_public' => false]);

        $response = $this->get(route('pages.show', $page));

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_view_private_page()
    {
        $user = User::factory()->create(['role' => User::ROLES['user']]);
        $page = Page::factory()->create(['is_public' => false]);

        $response = $this->actingAs($user)
            ->get(route('pages.show', $page));

        $response->assertStatus(200);
    }

    public function test_contributor_can_create_page()
    {
        $user = User::factory()->create(['role' => User::ROLES['contributor']]);

        $response = $this->actingAs($user)
            ->get(route('pages.create'));

        $response->assertStatus(200);
    }

    public function test_non_contributor_cannot_create_page()
    {
        $user = User::factory()->create(['role' => User::ROLES['user']]);

        $response = $this->actingAs($user)
            ->get(route('pages.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_restore_page()
    {
        $admin = User::factory()->create(['role' => User::ROLES['admin']]);
        $page = Page::factory()->create(['deleted_at' => now()]);

        $response = $this->actingAs($admin)
            ->post(route('admin.pages.restore', $page));

        $response->assertRedirect(route('pages.index'));
        $this->assertDatabaseHas('pages', ['id' => $page->id, 'deleted_at' => null]);
    }
}
