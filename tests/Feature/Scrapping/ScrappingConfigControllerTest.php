<?php

namespace Tests\Feature\Scrapping;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScrappingConfigControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_config_endpoint_returns_sources_and_entities(): void
    {
        $response = $this->getJson('/api/scrapping/config');

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

