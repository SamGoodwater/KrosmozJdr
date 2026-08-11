<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Effect;

use App\Http\Middleware\CheckRole;
use App\Models\Effect;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Index API effets : pagination / recherche (plus de dump massif).
 */
final class EffectIndexPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_index_is_paginated_and_filterable(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PLAYER]);
        Effect::create(['name' => 'Alpha Feu', 'slug' => 'alpha-feu', 'target_type' => Effect::TARGET_DIRECT]);
        Effect::create(['name' => 'Beta Eau', 'slug' => 'beta-eau', 'target_type' => Effect::TARGET_DIRECT]);

        $response = $this->actingAs($user)
            ->getJson('/api/effects/effects?q=Alpha&per_page=10');

        $response->assertOk();
        $this->assertSame(1, $response->json('meta.total'));
        $this->assertSame('Alpha Feu', $response->json('data.0.name'));
    }
}
