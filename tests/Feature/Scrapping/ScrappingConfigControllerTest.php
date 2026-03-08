<?php

namespace Tests\Feature\Scrapping;

use App\Models\Scrapping\ScrappingEntityMapping;
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
        // Routes scrapping protégées par password.confirm ; en tests on contourne pour éviter 423
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\RequirePassword::class);
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
                    'health' => ['status', 'entityCount', 'healthyCount', 'errorCount'],
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

    public function test_config_endpoint_survives_missing_bdd_mapping_for_entity(): void
    {
        ScrappingEntityMapping::where('source', 'dofusdb')
            ->where('entity', 'panoply')
            ->delete();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $response = $this->actingAs($admin)->getJson('/api/scrapping/config');

        $response->assertStatus(200)->assertJson(['success' => true]);

        $entities = $response->json('data.entities');
        $this->assertIsArray($entities);
        $panoply = collect($entities)->first(fn ($e) => ($e['entity'] ?? null) === 'panoply');
        $this->assertIsArray($panoply);
        $this->assertNotNull($panoply['configError'] ?? null);
        $this->assertIsArray($panoply['mappingDiagnostics'] ?? null);
        $this->assertGreaterThanOrEqual(1, (int) ($panoply['mappingDiagnostics']['blocking'] ?? 0));

        $monster = collect($entities)->first(fn ($e) => ($e['entity'] ?? null) === 'monster');
        $this->assertIsArray($monster);
        $this->assertSame('', (string) ($monster['configError'] ?? ''));

        $health = $response->json('data.health');
        $this->assertIsArray($health);
        $this->assertSame('partial', (string) ($health['status'] ?? ''));
        $this->assertGreaterThanOrEqual(1, (int) ($health['errorCount'] ?? 0));
    }
}

