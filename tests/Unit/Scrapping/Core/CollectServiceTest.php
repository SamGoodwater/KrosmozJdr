<?php

namespace Tests\Unit\Scrapping\Core;

use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests unitaires pour CollectService (limit, offset, fetchMany, fetchOne).
 */
class CollectServiceTest extends TestCase
{
    private CollectService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CollectService(ConfigLoader::default());
    }

    public function test_fetch_many_limit_zero_returns_all_available(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
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

        $result = $this->service->fetchMany('dofusdb', 'monster', [], ['limit' => 0, 'offset' => 0]);

        $this->assertCount(3, $result['items']);
        $this->assertSame(3, $result['meta']['total']);
        $this->assertSame(0, $result['meta']['limit']);
        $this->assertSame(0, $result['meta']['offset']);
        $this->assertSame(3, $result['meta']['collected']);
        $this->assertSame(1, ($result['items'][0]['id'] ?? null));
        $this->assertSame(3, ($result['items'][2]['id'] ?? null));
    }

    public function test_fetch_many_limit_stops_at_requested_count(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4], ['id' => 5]],
                'total' => 100,
                'limit' => 5,
                'skip' => 0,
            ], 200);
        });

        $result = $this->service->fetchMany('dofusdb', 'monster', [], ['limit' => 3, 'offset' => 0]);

        $this->assertCount(3, $result['items']);
        $this->assertSame(100, $result['meta']['total']);
        $this->assertSame(3, $result['meta']['limit']);
        $this->assertSame(3, $result['meta']['collected']);
    }

    public function test_fetch_many_offset_skips_items(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, 'skip=2') || str_contains($url, '%24skip=2')) {
                return Http::response([
                    'data' => [['id' => 3], ['id' => 4]],
                    'total' => 10,
                    'limit' => 2,
                    'skip' => 2,
                ], 200);
            }
            return Http::response([], 404);
        });

        $result = $this->service->fetchMany('dofusdb', 'monster', [], ['limit' => 2, 'offset' => 2]);

        $this->assertCount(2, $result['items']);
        $this->assertSame(2, $result['meta']['offset']);
        $this->assertSame(10, $result['meta']['total']);
        $this->assertSame(3, ($result['items'][0]['id'] ?? null));
        $this->assertSame(4, ($result['items'][1]['id'] ?? null));
    }

    public function test_fetch_many_limit_and_offset_combined(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, 'skip=1') || str_contains($url, '%24skip=1')) {
                return Http::response([
                    'data' => [['id' => 2], ['id' => 3], ['id' => 4]],
                    'total' => 10,
                    'limit' => 3,
                    'skip' => 1,
                ], 200);
            }
            return Http::response([], 404);
        });

        $result = $this->service->fetchMany('dofusdb', 'monster', [], ['limit' => 2, 'offset' => 1]);

        $this->assertCount(2, $result['items']);
        $this->assertSame(1, $result['meta']['offset']);
        $this->assertSame(2, $result['meta']['limit']);
        $this->assertSame(2, ($result['items'][0]['id'] ?? null));
        $this->assertSame(3, ($result['items'][1]['id'] ?? null));
    }

    public function test_fetch_many_empty_response(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => [],
                'total' => 0,
                'limit' => 50,
                'skip' => 0,
            ], 200),
        ]);

        $result = $this->service->fetchMany('dofusdb', 'monster', [], ['limit' => 0]);

        $this->assertCount(0, $result['items']);
        $this->assertSame(0, $result['meta']['collected']);
        $this->assertArrayHasKey('total', $result['meta']);
    }

    public function test_fetch_many_applies_filters_to_query(): void
    {
        $seenUrl = null;
        Http::fake(function ($request) use (&$seenUrl) {
            $seenUrl = $request->url();
            return Http::response([
                'data' => [],
                'total' => 0,
                'limit' => 50,
                'skip' => 0,
            ], 200);
        });

        $this->service->fetchMany('dofusdb', 'monster', [
            'name' => 'Bouftou',
            'levelMin' => 5,
            'levelMax' => 10,
        ], ['limit' => 0]);

        $this->assertNotNull($seenUrl);
        $this->assertStringContainsString('name', (string) $seenUrl);
        $this->assertStringContainsString('Bouftou', (string) $seenUrl);
        $this->assertStringContainsString('level', (string) $seenUrl);
    }

    public function test_fetch_one_returns_single_object(): void
    {
        Http::fake([
            '*/monsters/31*' => Http::response([
                'id' => 31,
                'name' => ['fr' => 'Bouftou'],
                'level' => 5,
            ], 200),
        ]);

        $result = $this->service->fetchOne('dofusdb', 'monster', 31);

        $this->assertIsArray($result);
        $this->assertSame(31, $result['id'] ?? null);
        $this->assertSame('Bouftou', $result['name']['fr'] ?? null);
    }

    public function test_fetch_one_throws_on_404(): void
    {
        Http::fake([
            '*' => Http::response([], 404),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('404');

        $this->service->fetchOne('dofusdb', 'monster', 999);
    }

    public function test_fetch_many_uses_page_size_for_api_requests(): void
    {
        $seenUrl = null;
        Http::fake(function ($request) use (&$seenUrl) {
            $seenUrl = $request->url();
            return Http::response([
                'data' => [['id' => 1]],
                'total' => 1,
                'limit' => 10,
                'skip' => 0,
            ], 200);
        });

        $this->service->fetchMany('dofusdb', 'monster', [], ['limit' => 1, 'page_size' => 10]);

        $this->assertNotNull($seenUrl);
        $this->assertStringContainsString('10', (string) $seenUrl);
    }
}
