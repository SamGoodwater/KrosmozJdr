<?php

namespace Tests\Unit\Scrapping;

use App\Services\Scrapping\Config\ScrappingConfigLoader;
use App\Services\Scrapping\DataCollect\ConfigDrivenDofusDbCollector;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConfigDrivenDofusDbCollectorTest extends TestCase
{
    public function test_fetch_many_paginates_and_merges_pages(): void
    {
        $loader = new ScrappingConfigLoader();
        $client = new DofusDbClient([]);
        $collector = new ConfigDrivenDofusDbCollector($loader, $client);

        Http::fake(function ($request) {
            $url = $request->url();

            if (str_contains($url, '%24skip=0') && str_contains($url, '%24limit=2')) {
                return Http::response([
                    'data' => [
                        ['id' => 1],
                        ['id' => 2],
                    ],
                    'total' => 3,
                    'limit' => 2,
                    'skip' => 0,
                ], 200);
            }

            if (str_contains($url, '%24skip=2') && str_contains($url, '%24limit=2')) {
                return Http::response([
                    'data' => [
                        ['id' => 3],
                    ],
                    'total' => 3,
                    'limit' => 2,
                    'skip' => 2,
                ], 200);
            }

            return Http::response([], 404);
        });

        $items = $collector->fetchMany('monster', [], ['limit' => 2, 'max_pages' => 10]);

        $this->assertCount(3, $items);
        $this->assertEquals(1, $items[0]['id'] ?? null);
        $this->assertEquals(3, $items[2]['id'] ?? null);
    }

    public function test_fetch_many_builds_safe_filters_query(): void
    {
        $loader = new ScrappingConfigLoader();
        $client = new DofusDbClient([]);
        $collector = new ConfigDrivenDofusDbCollector($loader, $client);

        $seenUrl = null;

        Http::fake(function ($request) use (&$seenUrl) {
            $seenUrl = $request->url();
            return Http::response([
                'data' => [],
                'total' => 0,
                'limit' => 100,
                'skip' => 0,
            ], 200);
        });

        $collector->fetchMany('monster', [
            'name' => 'Bouftou',
            'ids' => [1, '2', 'nope', 3],
            'unknown' => 'ignored',
        ], ['limit' => 100]);

        $this->assertNotNull($seenUrl);
        $this->assertStringContainsString('name%5B%24search%5D=Bouftou', (string) $seenUrl);
        $this->assertStringContainsString('id%5B%24in%5D', (string) $seenUrl);
        $this->assertStringContainsString('1', (string) $seenUrl);
        $this->assertStringNotContainsString('unknown', (string) $seenUrl);
    }
}

