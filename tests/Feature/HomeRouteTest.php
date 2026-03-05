<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_accueil_page_when_available_and_visible(): void
    {
        Page::factory()->create([
            'title' => 'Accueil',
            'slug' => 'accueil',
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'in_menu' => true,
            'menu_order' => 0,
        ]);

        $response = $this->get(route('home'));

        $response->assertRedirect(route('pages.show', 'accueil'));
    }

    public function test_home_falls_back_to_static_home_when_accueil_page_missing(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }
}

