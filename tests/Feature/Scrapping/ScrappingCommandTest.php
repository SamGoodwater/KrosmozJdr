<?php

namespace Tests\Feature\Scrapping;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScrappingCommandTest extends TestCase
{
    public function test_scrapping_collect_outputs_json(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [
                    ['id' => 31, 'name' => ['fr' => 'Bouftou']],
                ],
                'total' => 1,
                'limit' => 50,
                'skip' => 0,
            ], 200);
        });

        $code = Artisan::call('scrapping', [
            '--collect' => 'monster',
            '--name' => 'Bouftou',
            '--json' => true,
            '--skip-cache' => true,
            '--limit' => 50,
            '--max-pages' => 1,
            '--max-items' => 10,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('"entities"', $out);
        $this->assertStringContainsString('"monster"', $out);
        $this->assertStringContainsString('"id": 31', $out);
    }
}

