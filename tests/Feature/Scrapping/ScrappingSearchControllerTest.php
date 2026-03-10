<?php

namespace Tests\Feature\Scrapping;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\SeedsScrappingPipeline;

class ScrappingSearchControllerTest extends TestCase
{
    use SeedsScrappingPipeline;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedScrappingPipeline();
        $this->withoutMiddleware('password.confirm');
    }

    public function test_search_endpoint_returns_items_and_meta(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $seenUrl = null;

        Http::fake(function ($request) use (&$seenUrl) {
            $seenUrl = $request->url();
            return Http::response([
                'data' => [
                    ['id' => 31, 'name' => ['fr' => 'Bouftou']],
                ],
                'total' => 1,
                'limit' => 2,
                'skip' => 0,
            ], 200);
        });

        $res = $this->actingAs($admin)->getJson('/api/scrapping/search/monster?name=Bouftou&limit=2&max_pages=1&skip_cache=true');

        $res->assertOk();
        $res->assertJsonPath('success', true);
        $res->assertJsonCount(1, 'data.items');
        $res->assertJsonPath('data.items.0.id', 31);
        $res->assertJsonPath('data.items.0.exists', false);
        $res->assertJsonPath('data.items.0.existing', null);
        $res->assertJsonPath('data.meta.total', 1);
        $res->assertJsonPath('data.meta.limit', 2);

        $this->assertNotNull($seenUrl);
        $this->assertStringContainsString('/monsters?', (string) $seenUrl);
        $this->assertStringContainsString('name%5B%24search%5D=Bouftou', (string) $seenUrl);
        $this->assertStringContainsString('%24limit=2', (string) $seenUrl);
        $this->assertStringContainsString('%24skip=0', (string) $seenUrl);
    }

    public function test_search_supports_id_range_filters(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $seenUrl = null;

        Http::fake(function ($request) use (&$seenUrl) {
            $seenUrl = $request->url();
            return Http::response([
                'data' => [
                    ['id' => 10, 'name' => ['fr' => 'Test']],
                ],
                'total' => 1,
                'limit' => 2,
                'skip' => 0,
            ], 200);
        });

        $res = $this->actingAs($admin)->getJson('/api/scrapping/search/monster?idMin=10&idMax=12&limit=2&max_pages=1');

        $res->assertOk();
        $res->assertJsonPath('success', true);
        $this->assertNotNull($seenUrl);
        $this->assertStringContainsString('id%5B%24gte%5D=10', (string) $seenUrl);
        $this->assertStringContainsString('id%5B%24lte%5D=12', (string) $seenUrl);
    }

    public function test_search_paginates_using_api_limit_when_capped(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $seenUrls = [];

        Http::fake(function ($request) use (&$seenUrls) {
            $url = (string) $request->url();
            $seenUrls[] = $url;

            $skip = 0;
            if (preg_match('/[\$%]24skip=(\d+)/', $url, $m)) {
                $skip = (int) $m[1];
            } elseif (preg_match('/[?&]skip=(\d+)/', $url, $m)) {
                $skip = (int) $m[1];
            }

            $data = [];
            for ($i = 0; $i < 50; $i++) {
                $data[] = ['id' => $skip + $i + 1, 'name' => ['fr' => 'X']];
            }

            return Http::response([
                'data' => $data,
                'total' => 120,
                // API cap: renvoie 50 même si on a demandé 200
                'limit' => 50,
                'skip' => $skip,
            ], 200);
        });

        $res = $this->actingAs($admin)->getJson('/api/scrapping/search/monster?limit=200&max_pages=2&skip_cache=true');

        $res->assertOk();
        $res->assertJsonPath('success', true);
        // 2 pages x 50
        $res->assertJsonCount(100, 'data.items');
        $this->assertCount(2, $seenUrls);
        // Le 2e appel doit être à skip=50 (et pas 200)
        $this->assertStringContainsString('%24skip=50', (string) $seenUrls[1]);
    }

    public function test_search_unknown_entity_returns_404(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $res = $this->actingAs($admin)->getJson('/api/scrapping/search/not-a-real-entity');
        $res->assertStatus(404);
        $res->assertJsonPath('success', false);
    }
}

