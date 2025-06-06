<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class InertiaPropsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_resource_contains_expected_props()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user');
        $response->assertInertia(
            fn($page) =>
            $page->where('user.data.id', $user->id)
                ->where('user.data.name', $user->name)
                ->where('user.data.email', $user->email)
                ->where('user.data.role', $user->role)
                ->where('user.data.avatar', $user->avatarPath())
                ->where('user.data.notifications_enabled', $user->notifications_enabled)
                ->where('user.data.notification_channels', $user->notification_channels)
        );
    }

    public function test_inertia_page_contains_can_permissions()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user');
        $response->assertInertia(
            fn($page) =>
            $page->where('user.data.can.update', true)
                ->where('user.data.can.delete', true)
                ->where('user.data.can.forceDelete', false)
                ->where('user.data.can.restore', false)
        );
    }

    public function test_inertia_page_relations_are_loaded()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user');
        $response->assertInertia(
            fn($page) =>
            $page->has('user.data.scenarios')
                ->has('user.data.campaigns')
                ->has('user.data.pages')
                ->has('user.data.sections')
        );
    }
}
