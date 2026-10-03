<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\EntityPermissions\EntityPermissionService;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Matrice Contenu (MJ+) / pipeline & Admin (admin+) avec password.confirm sans élévation.
 */
class ContentAreaAccessTest extends TestCase
{
    public function test_game_master_without_password_is_redirected_from_content_dashboard(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.content.dashboard.index'))
            ->assertRedirect(route('password.confirm'));
    }

    public function test_game_master_with_password_can_view_content_but_not_pipeline_props(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAsConfirmed($gm)
            ->get(route('admin.content.dashboard.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Content/Dashboard/Index')
                ->where('canPipeline', false)
                ->where('rulesDownloads', null)
                ->where('permissions.access.contentManagement', true)
                ->where('permissions.access.contentPipeline', false)
                ->where('permissions.access.adminPanel', false)
                ->where('permissions.access.effectsAdmin', true));
    }

    public function test_game_master_with_password_can_view_languages_types_effects(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAsConfirmed($gm)
            ->get(route('admin.languages.index'))
            ->assertOk();

        $this->actingAsConfirmed($gm)
            ->get(route('admin.content.types.index'))
            ->assertRedirect(route('admin.content.types.show', ['kind' => 'equipment']));

        $this->actingAsConfirmed($gm)
            ->get(route('admin.content.types.show', ['kind' => 'equipment']))
            ->assertOk();

        $this->actingAsConfirmed($gm)
            ->get(route('admin.effects.index'))
            ->assertOk();

        $this->actingAsConfirmed($gm)
            ->get(route('admin.characteristics.index'))
            ->assertOk();
    }

    public function test_game_master_with_password_forbidden_from_pipeline_and_admin_app(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAsConfirmed($gm)
            ->get(route('admin.content.ia-generation.edit'))
            ->assertForbidden();

        $this->actingAsConfirmed($gm)
            ->get(route('admin.scrapping-mappings.index'))
            ->assertForbidden();

        $this->actingAsConfirmed($gm)
            ->get(route('admin.content.dofusdb.index'))
            ->assertForbidden();

        $this->actingAsConfirmed($gm)
            ->get(route('admin.loading-tips.index'))
            ->assertForbidden();

        $this->actingAsConfirmed($gm)
            ->get(route('admin.recap.index'))
            ->assertForbidden();
    }

    public function test_admin_with_password_sees_pipeline_and_admin_pages(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->get(route('admin.content.dashboard.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('canPipeline', true)
                ->has('rulesDownloads')
                ->where('permissions.access.contentManagement', true)
                ->where('permissions.access.contentPipeline', true)
                ->where('permissions.access.adminPanel', true));

        $this->actingAsConfirmed($admin)
            ->get(route('admin.loading-tips.index'))
            ->assertOk();

        $this->actingAsConfirmed($admin)
            ->get(route('admin.content.dofusdb.index'))
            ->assertOk();
    }

    public function test_legacy_loading_tips_url_redirects_to_admin_area(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->get('/admin/content/loading-tips')
            ->assertRedirect('/admin/loading-tips');
    }

    public function test_permission_service_exposes_content_keys_for_game_master(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $perms = app(EntityPermissionService::class)->forUser($gm);

        $this->assertTrue($perms['access']['contentManagement']);
        $this->assertTrue($perms['access']['effectsAdmin']);
        $this->assertFalse($perms['access']['contentPipeline']);
        $this->assertFalse($perms['access']['adminPanel']);
    }
}
