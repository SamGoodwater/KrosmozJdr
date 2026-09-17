<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GameDownloadCatalogControllerTest extends TestCase
{
    public function test_guest_receives_catalog_groups(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('downloads/generated/krosmoz-jdr-regles.pdf', '%PDF-fake');

        $response = $this->getJson('/api/game-downloads');

        $response->assertOk();
        $response->assertJsonStructure([
            'groups' => [
                ['key', 'label', 'items'],
            ],
            'generated' => ['generated_at', 'available', 'missing'],
        ]);
        $keys = collect($response->json('groups'))
            ->pluck('items')
            ->flatten(1)
            ->pluck('key')
            ->all();
        $this->assertContains('rules-pdf', $keys);
        $this->assertContains('character-sheet-pdf', $keys);
        $this->assertContains('logo-png', $keys);

        $pdf = collect($response->json('groups'))
            ->pluck('items')
            ->flatten(1)
            ->firstWhere('key', 'rules-pdf');
        $this->assertTrue($pdf['available']);
        $this->assertSame('/telechargements/rules-pdf', parse_url((string) $pdf['download_url'], PHP_URL_PATH));
    }
}
