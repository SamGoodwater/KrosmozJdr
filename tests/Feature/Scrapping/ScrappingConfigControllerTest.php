<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SeedsScrappingPipeline;
use Tests\TestCase;

class ScrappingConfigControllerTest extends TestCase
{
    use RefreshDatabase, SeedsScrappingPipeline;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedScrappingPipeline();
    }

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

        $firstEntity = $response->json('data.entities.0');
        $this->assertIsArray($firstEntity);
        $this->assertArrayHasKey('mappingDiagnostics', $firstEntity);
        $this->assertIsArray($firstEntity['mappingDiagnostics']);
        $this->assertArrayHasKey('coveragePct', $firstEntity['mappingDiagnostics']);
        $this->assertArrayHasKey('total', $firstEntity['mappingDiagnostics']);
        $this->assertArrayHasKey('valid', $firstEntity['mappingDiagnostics']);
        $this->assertArrayHasKey('invalid', $firstEntity['mappingDiagnostics']);
        $this->assertArrayHasKey('blocking', $firstEntity['mappingDiagnostics']);
        $this->assertArrayHasKey('improvable', $firstEntity['mappingDiagnostics']);
        $this->assertArrayHasKey('warnings', $firstEntity['mappingDiagnostics']);
        $this->assertIsArray($firstEntity['mappingDiagnostics']['warnings']);
    }
}

