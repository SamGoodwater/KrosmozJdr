<?php

namespace Tests\Feature\Scrapping;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests de la commande scrapping:v2 (collect, limit, offset, filtres, options).
 */
class ScrappingV2CommandTest extends TestCase
{
    public function test_command_requires_collect_option(): void
    {
        $code = Artisan::call('scrapping:v2', []);

        $this->assertSame(1, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('--collect=', $out);
        $this->assertStringContainsString('obligatoire', $out);
    }

    public function test_command_collect_monster_outputs_success(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                'total' => 1,
                'limit' => 50,
                'skip' => 0,
            ], 200);
        });

        $code = Artisan::call('scrapping:v2', [
            '--collect' => 'monster',
            '--limit' => 5,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('dofusdb/monster', $out);
        $this->assertStringContainsString('objet(s)', $out);
        $this->assertStringContainsString('offset=', $out);
        $this->assertStringContainsString('limit=', $out);
    }

    public function test_command_collect_ressource_returns_success(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, '/item-types')) {
                return Http::response([
                    'data' => [['id' => 15, 'superTypeId' => 1, 'name' => ['fr' => 'Ressource']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            if (str_contains($url, '/items')) {
                return Http::response([
                    'data' => [['id' => 1, 'typeId' => 15]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping:v2', [
            '--collect' => 'ressource',
            '--limit' => 1,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('objet(s)', $out);
    }

    public function test_command_limit_respects_max_items(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [
                    ['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4], ['id' => 5],
                ],
                'total' => 100,
                'limit' => 5,
                'skip' => 0,
            ], 200);
        });

        $code = Artisan::call('scrapping:v2', [
            '--collect' => 'monster',
            '--limit' => 3,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('limit=3', $out);
    }

    public function test_command_offset_passed_to_collect(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            $this->assertStringContainsString('skip', $url);
            return Http::response([
                'data' => [['id' => 11], ['id' => 12]],
                'total' => 20,
                'limit' => 2,
                'skip' => 10,
            ], 200);
        });

        $code = Artisan::call('scrapping:v2', [
            '--collect' => 'monster',
            '--limit' => 2,
            '--offset' => 10,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('offset=10', $out);
    }

    public function test_command_json_outputs_valid_json(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                'total' => 1,
                'limit' => 50,
                'skip' => 0,
            ], 200);
        });

        $code = Artisan::call('scrapping:v2', [
            '--collect' => 'monster',
            '--limit' => 1,
            '--json' => true,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $jsonStart = strpos($out, '{');
        $this->assertNotFalse($jsonStart);
        $decoded = json_decode(substr($out, $jsonStart), true);
        $this->assertIsArray($decoded);
        $this->assertTrue($decoded['success'] ?? false);
        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('meta', $decoded);
        $this->assertSame(1, ($decoded['meta']['collected'] ?? 0));
    }

    public function test_command_run_one_by_id(): void
    {
        Http::fake([
            '*/monsters/31*' => Http::response([
                'id' => 31,
                'name' => ['fr' => 'Bouftou'],
                'level' => 5,
            ], 200),
        ]);

        $code = Artisan::call('scrapping:v2', [
            '--collect' => 'monster',
            '--id' => 31,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('Run un objet', $out);
        $this->assertStringContainsString('31', $out);
    }

    public function test_command_unknown_collect_fails(): void
    {
        $code = Artisan::call('scrapping:v2', [
            '--collect' => 'unknown-entity',
        ]);

        $this->assertSame(1, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('inconnue', $out);
    }

    public function test_command_limit_zero_collects_all_available(): void
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

        $code = Artisan::call('scrapping:v2', [
            '--collect' => 'monster',
            '--limit' => 0,
        ]);

        $this->assertSame(0, $code);
        $this->assertSame(2, $callCount);
        $out = Artisan::output();
        $this->assertStringContainsString('limit=tout', $out);
        $this->assertStringContainsString('3 objet(s)', $out);
    }
}
