<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentAreaLegacyRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_characteristics_url_redirects_permanently(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get('/admin/characteristics')
            ->assertRedirect('/admin/content/characteristics');
    }

    public function test_legacy_effects_url_keeps_path_suffix(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get('/admin/effects/create')
            ->assertRedirect('/admin/content/effects/create');
    }

    public function test_named_content_routes_use_admin_content_prefix(): void
    {
        $this->assertSame('/admin/content/characteristics', route('admin.characteristics.index', absolute: false));
        $this->assertSame('/admin/content/effects', route('admin.effects.index', absolute: false));
        $this->assertSame('/admin/content/languages', route('admin.languages.index', absolute: false));
        $this->assertSame('/admin/content/scrapping-mappings', route('admin.scrapping-mappings.index', absolute: false));
        $this->assertSame('/admin/content/dofusdb-effect-mappings', route('admin.dofusdb-effect-mappings.index', absolute: false));
        $this->assertSame('/admin/content/sub-effects', route('admin.sub-effects.index', absolute: false));
    }
}
