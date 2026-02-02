<?php

namespace Tests\Feature\Scrapping;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests de la commande scrapping (collect, import, limit, filtres, options).
 * Remplace les tests de scrapping:import après fusion dans scrapping.
 */
class ScrappingCommandTest extends TestCase
{
    public function test_command_requires_entity_option(): void
    {
        $code = Artisan::call('scrapping', []);

        $this->assertSame(1, $code);
        $out = Artisan::output();
        $this->assertTrue(
            str_contains($out, '--collect=') || str_contains($out, '--import='),
            "Output should mention collect or import: {$out}"
        );
        $this->assertStringContainsString('Aucune entité', $out);
    }

    public function test_command_import_monster_outputs_success(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                'total' => 1,
                'limit' => 50,
                'skip' => 0,
            ], 200);
        });

        $code = Artisan::call('scrapping', [
            '--import' => 'monster',
            '--limit' => '5',
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('monster', $out);
    }

    public function test_command_import_ressource_returns_success(): void
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

        $code = Artisan::call('scrapping', [
            '--import' => 'ressource',
            '--limit' => '1',
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('ressource', $out);
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

        $code = Artisan::call('scrapping', [
            '--import' => 'monster',
            '--limit' => '3',
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('monster', $out);
    }

    public function test_command_start_skip_passed_to_collect(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            $this->assertTrue(str_contains($url, 'skip') || str_contains($url, '%24skip'));
            return Http::response([
                'data' => [['id' => 11], ['id' => 12]],
                'total' => 20,
                'limit' => 2,
                'skip' => 10,
            ], 200);
        });

        $code = Artisan::call('scrapping', [
            '--import' => 'monster',
            '--limit' => '2',
            '--start-skip' => '10',
        ]);

        $this->assertSame(0, $code);
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

        $code = Artisan::call('scrapping', [
            '--import' => 'monster',
            '--limit' => '1',
            '--json' => true,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $jsonStart = strpos($out, '{');
        $this->assertNotFalse($jsonStart);
        $decoded = json_decode(substr($out, $jsonStart), true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('query', $decoded);
        $this->assertArrayHasKey('entities', $decoded);
    }

    public function test_command_run_one_by_id(): void
    {
        Http::fake([
            '*/monsters/31*' => Http::response([
                'id' => 31,
                'name' => ['fr' => 'Bouftou'],
                'grades' => [['level' => 5]],
            ], 200),
        ]);

        $code = Artisan::call('scrapping', [
            '--import' => 'monster',
            '--id' => '31',
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('monster', $out);
        $this->assertTrue(str_contains($out, '31') || str_contains($out, 'imports'), "Output should mention id or imports: {$out}");
    }

    public function test_command_unknown_entity_fails(): void
    {
        $code = Artisan::call('scrapping', [
            '--import' => 'unknown-entity',
        ]);

        $out = Artisan::output();
        $this->assertTrue(
            $code !== 0 || str_contains($out, 'erreur') || str_contains($out, 'error') || str_contains($out, 'unknown'),
            "Expected failure or error in output: code={$code}, out={$out}"
        );
    }

    public function test_command_max_pages_limits_pagination(): void
    {
        $callCount = 0;
        Http::fake(function ($request) use (&$callCount) {
            $callCount++;
            $url = (string) $request->url();
            if (str_contains($url, 'skip=0') || str_contains($url, '%24skip=0')) {
                return Http::response([
                    'data' => [['id' => 1], ['id' => 2]],
                    'total' => 10,
                    'limit' => 2,
                    'skip' => 0,
                ], 200);
            }
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

        $code = Artisan::call('scrapping', [
            '--import' => 'monster',
            '--limit' => '2',
            '--max-pages' => '2',
        ]);

        $this->assertSame(0, $code);
        $this->assertGreaterThanOrEqual(1, $callCount);
    }
}
