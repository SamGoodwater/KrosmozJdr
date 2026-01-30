<?php

namespace Tests\Unit\Scrapping;

use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DofusDbClientTest extends TestCase
{
    public function test_skip_cache_bypasses_cached_value(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->zeroOrMoreTimes();

        $url = 'https://api.dofusdb.fr/breeds/1';
        $cacheKey = 'dofusdb_' . md5($url);

        Cache::put($cacheKey, ['id' => 1, 'description' => ['fr' => 'A']], 3600);

        Http::fake(['*' => Http::response(['id' => 1, 'description' => ['fr' => 'B']], 200)]);

        $client = new DofusDbClient([
            'timeout' => 30,
            'retry_attempts' => 1,
            'retry_delay' => 1,
            'cache_ttl' => 3600,
        ]);

        $data = $client->getJson($url, ['skip_cache' => true]);
        $this->assertEquals('B', $data['description']['fr'] ?? null);
    }
}

