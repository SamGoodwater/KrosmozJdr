<?php

namespace Tests\Feature\Http;

use App\Models\User;
use App\Services\PageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Props Inertia partagées par HandleInertiaRequests (shareOnce, defer, auth).
 */
class HandleInertiaSharedPropsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_inertia_page_includes_share_once_and_auth_props(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('permissions')
            ->missing('ziggy')
            ->has('ziggy_location')
            ->has('oauth_enabled_providers')
            ->has('auth')
            ->where('auth.isLogged', false)
            ->has('menu_revision')
            ->missing('characteristics')
        );
    }

    public function test_menu_revision_increments_on_cache_clear_and_stays_on_partial_reload(): void
    {
        Cache::forget(PageService::MENU_CACHE_REVISION_KEY);
        $this->assertSame(0, PageService::menuRevision());

        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('menu_revision', 0));

        PageService::clearMenuCache();
        $this->assertSame(1, PageService::menuRevision());

        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('menu_revision', 1)
                ->reloadOnly('auth', fn ($partial) => $partial->where('menu_revision', 1))
            );

        PageService::clearMenuCache();
        $this->assertSame(2, PageService::menuRevision());
    }

    public function test_authenticated_inertia_page_includes_permissions_and_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.show'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('permissions')
            ->missing('ziggy')
            ->has('auth.user')
            ->where('auth.isLogged', true)
            ->where('auth.user.id', $user->id)
        );
    }

    public function test_partial_reload_still_includes_auth_props(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.show', $user));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->reloadOnly('user', fn ($partial) => $partial
                ->has('auth.user')
                ->where('auth.isLogged', true)
                ->where('auth.user.id', $user->id)
            )
        );
    }
}
