<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScrappingConfigControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_config_endpoint_returns_sources_and_entities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $response = $this->actingAs($admin)->getJson('/api/scrapping/config');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'source' => ['source', 'label', 'baseUrl'],
                    'entities',
                ],
                'timestamp',
            ]);
    }
}

