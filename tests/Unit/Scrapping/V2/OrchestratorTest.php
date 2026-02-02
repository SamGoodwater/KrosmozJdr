<?php

namespace Tests\Unit\Scrapping\V2;

use App\Services\Scrapping\V2\Orchestrator\Orchestrator;
use App\Services\Scrapping\V2\Orchestrator\OrchestratorResult;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests unitaires pour l'orchestrateur V2 (runOne, runMany, options).
 */
class OrchestratorTest extends TestCase
{
    private Orchestrator $orchestrator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = Orchestrator::default();
    }

    public function test_run_one_returns_raw_when_no_convert_validate_integrate(): void
    {
        Http::fake([
            '*/monsters/31*' => Http::response([
                'id' => 31,
                'name' => ['fr' => 'Bouftou'],
                'level' => 5,
            ], 200),
        ]);

        $result = $this->orchestrator->runOne('dofusdb', 'monster', 31, []);

        $this->assertInstanceOf(OrchestratorResult::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNotNull($result->getRaw());
        $this->assertSame(31, $result->getRaw()['id'] ?? null);
        $this->assertSame('Bouftou', $result->getRaw()['name']['fr'] ?? null);
        $this->assertNull($result->getConverted());
    }

    public function test_run_one_fails_when_http_error(): void
    {
        Http::fake([
            '*' => Http::response([], 404),
        ]);

        $result = $this->orchestrator->runOne('dofusdb', 'monster', 999, []);

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('404', $result->getMessage());
    }

    public function test_run_many_returns_items_and_meta_with_limit_offset(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [['id' => 1], ['id' => 2], ['id' => 3]],
                'total' => 10,
                'limit' => 3,
                'skip' => 0,
            ], 200);
        });

        $result = $this->orchestrator->runMany('dofusdb', 'monster', [], [
            'limit' => 3,
            'offset' => 0,
        ]);

        $this->assertTrue($result->isSuccess());
        $converted = $result->getConverted();
        $this->assertIsArray($converted);
        $this->assertCount(3, $converted);
        $meta = $result->getMeta();
        $this->assertNotNull($meta);
        $this->assertSame(10, $meta['total'] ?? null);
        $this->assertSame(3, $meta['limit'] ?? null);
        $this->assertSame(0, $meta['offset'] ?? null);
        $this->assertSame(3, $meta['collected'] ?? null);
        $this->assertStringContainsString('offset=', $result->getMessage());
        $this->assertStringContainsString('limit=', $result->getMessage());
    }

    public function test_run_many_limit_zero_collects_until_exhausted(): void
    {
        $callCount = 0;
        Http::fake(function ($request) use (&$callCount) {
            $url = (string) $request->url();
            $callCount++;
            if (str_contains($url, 'skip=0') || str_contains($url, '%24skip=0')) {
                return Http::response([
                    'data' => [['id' => 1], ['id' => 2]],
                    'total' => 3,
                    'limit' => 2,
                    'skip' => 0,
                ], 200);
            }
            if (str_contains($url, 'skip=2') || str_contains($url, '%24skip=2')) {
                return Http::response([
                    'data' => [['id' => 3]],
                    'total' => 3,
                    'limit' => 2,
                    'skip' => 2,
                ], 200);
            }
            return Http::response([], 404);
        });

        $result = $this->orchestrator->runMany('dofusdb', 'monster', [], ['limit' => 0, 'offset' => 0]);

        $this->assertTrue($result->isSuccess());
        $this->assertCount(3, $result->getConverted());
        $this->assertSame(2, $callCount);
        $this->assertStringContainsString('limit=tout', $result->getMessage());
    }

    public function test_run_many_with_convert_applies_conversion(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [
                    [
                        'id' => 31,
                        'name' => ['fr' => 'Bouftou'],
                        'level' => 5,
                        'raceId' => 1,
                    ],
                ],
                'total' => 1,
                'limit' => 1,
                'skip' => 0,
            ], 200);
        });

        $result = $this->orchestrator->runMany('dofusdb', 'monster', [], [
            'limit' => 1,
            'convert' => true,
        ]);

        $this->assertTrue($result->isSuccess());
        $converted = $result->getConverted();
        $this->assertIsArray($converted);
        $this->assertCount(1, $converted);
        $first = $converted[0] ?? [];
        $this->assertIsArray($first);
        $this->assertArrayHasKey('creatures', $first);
        $this->assertArrayHasKey('monsters', $first);
    }

    public function test_run_many_catalog_only_skips_integration(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [['id' => 1, 'name' => ['fr' => 'Race A']]],
                'total' => 1,
                'limit' => 1,
                'skip' => 0,
            ], 200);
        });

        $result = $this->orchestrator->runMany('dofusdb', 'monster-race', [], [
            'limit' => 1,
            'convert' => true,
            'integrate' => true,
        ]);

        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getIntegrationResults());
    }

    public function test_run_one_with_convert_returns_converted_structure(): void
    {
        Http::fake([
            '*/monsters/31*' => Http::response([
                'id' => 31,
                'name' => ['fr' => 'Bouftou'],
                'level' => 5,
                'raceId' => 1,
                'grades' => [['level' => 5, 'lifePoints' => 100]],
            ], 200),
        ]);

        $result = $this->orchestrator->runOne('dofusdb', 'monster', 31, ['convert' => true]);

        $this->assertTrue($result->isSuccess());
        $converted = $result->getConverted();
        $this->assertIsArray($converted);
        $this->assertArrayHasKey('creatures', $converted);
        $this->assertArrayHasKey('monsters', $converted);

        $creatures = $converted['creatures'] ?? [];
        $this->assertArrayHasKey('level', $creatures);
        $this->assertArrayHasKey('life', $creatures);
        $this->assertSame(1, $creatures['level'], 'Level Dofus 5 → JDR 1 (formule BDD k = d/10)');
        $this->assertSame(6, $creatures['life'], 'Life Dofus 100 + level JDR 1 → formule BDD k = d/200 + level*5');
    }

    public function test_run_one_with_raw_skips_collect_and_uses_provided_raw(): void
    {
        $raw = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'raceId' => 1,
            'grades' => [
                ['level' => 50, 'lifePoints' => 800, 'strength' => 100, 'intelligence' => 50, 'agility' => 80, 'chance' => 60],
            ],
        ];
        $result = $this->orchestrator->runOneWithRaw('dofusdb', 'monster', $raw, [
            'convert' => true,
            'validate' => true,
            'integrate' => false,
        ]);

        $this->assertTrue($result->isSuccess());
        $converted = $result->getConverted();
        $this->assertIsArray($converted);
        $creatures = $converted['creatures'] ?? [];
        $this->assertSame(5, $creatures['level'] ?? null, 'Level Dofus 50 → JDR 5');
        $this->assertArrayHasKey('life', $creatures);
    }
}
