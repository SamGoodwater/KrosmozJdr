<?php

declare(strict_types=1);

namespace Tests\Feature\Scrapping;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DofusdbApiLegacyRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_named_routes_use_dofusdb_prefix(): void
    {
        $this->assertSame('/api/dofusdb/config', route('scrapping.config', absolute: false));
        $this->assertSame('/api/dofusdb/item-types', route('scrapping.item-types.index', absolute: false));
        $this->assertSame('/api/dofusdb/resource-types', route('scrapping.resource-types.index', absolute: false));
    }

    public function test_legacy_scrapping_prefix_redirects_to_dofusdb(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->getJson('/api/scrapping/config')
            ->assertStatus(307)
            ->assertRedirect('/api/dofusdb/config');
    }

    public function test_legacy_scrapping_prefix_keeps_path_and_query(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->getJson('/api/scrapping/search/monster?limit=2')
            ->assertStatus(307)
            ->assertRedirect('/api/dofusdb/search/monster?limit=2');
    }
}
