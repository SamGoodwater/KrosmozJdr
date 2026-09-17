<?php

namespace Tests\Unit\Support;

use App\Support\EntityPermissions\EntityPermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EntityPermissionServiceGuestCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_permissions_are_cached_with_revision_key(): void
    {
        Cache::flush();
        Cache::forever(EntityPermissionService::PERMISSIONS_CACHE_REVISION_KEY, 3);

        $service = app(EntityPermissionService::class);
        $first = $service->forUser(null);
        $second = $service->forUser(null);

        $this->assertSame($first, $second);
        $this->assertTrue(Cache::has('permissions.entities.guest.r3'));
    }
}
