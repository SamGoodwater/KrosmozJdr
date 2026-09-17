<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\EntityDisplay\EntityDisplayVisibilityService;
use App\Support\EntityPermissions\EntityPermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * @description
 * Phase A — accès à la page « Gérer l’affichage » et cohérence cache permissions après PATCH.
 */
class EntityDisplayVisibilityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirected(): void
    {
        $this->get(route('admin.entity-display-visibility.index'))
            ->assertRedirect();
    }

    public function test_game_master_is_forbidden(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)->get(route('admin.entity-display-visibility.index'))
            ->assertForbidden();
    }

    public function test_admin_patch_bumps_permission_cache_revision(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Cache::forever(EntityPermissionService::PERMISSIONS_CACHE_REVISION_KEY, 41);

        $visibilityService = app(EntityDisplayVisibilityService::class);

        $response = $this->actingAs($admin)->patch(
            route('admin.entity-display-visibility.update'),
            ['rules' => $visibilityService->matrixForManageableEntities()],
        );

        $response->assertRedirect(route('admin.entity-display-visibility.index'));
        $this->assertSame(
            42,
            (int) Cache::get(EntityPermissionService::PERMISSIONS_CACHE_REVISION_KEY),
        );
    }
}
