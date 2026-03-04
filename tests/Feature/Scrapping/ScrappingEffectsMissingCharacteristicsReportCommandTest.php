<?php

declare(strict_types=1);

namespace Tests\Feature\Scrapping;

use App\Models\DofusdbEffectMapping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests de la commande scrapping:effects:report-missing-characteristics.
 */
class ScrappingEffectsMissingCharacteristicsReportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_reports_grouped_counts_by_dofusdb_characteristic(): void
    {
        DofusdbEffectMapping::query()->create([
            'dofusdb_effect_id' => 116,
            'sub_effect_slug' => 'booster',
            'characteristic_source' => DofusdbEffectMapping::SOURCE_CHARACTERISTIC,
            'characteristic_key' => null,
        ]);
        DofusdbEffectMapping::query()->create([
            'dofusdb_effect_id' => 117,
            'sub_effect_slug' => 'booster',
            'characteristic_source' => DofusdbEffectMapping::SOURCE_CHARACTERISTIC,
            'characteristic_key' => null,
        ]);

        Http::fake([
            'https://api.dofusdb.fr/effects/116*' => Http::response(['id' => 116, 'characteristic' => 19], 200),
            'https://api.dofusdb.fr/effects/117*' => Http::response(['id' => 117, 'characteristic' => 19], 200),
        ]);

        $code = Artisan::call('scrapping:effects:report-missing-characteristics', [
            '--skip-cache' => true,
            '--limit' => 5,
        ]);

        $this->assertSame(0, $code);
        $output = Artisan::output();
        $this->assertStringContainsString('Rapport mappings manquants', $output);
        $this->assertStringContainsString('19', $output);
        $this->assertStringContainsString('2', $output);
    }

    public function test_command_json_output_contains_summary_and_groups(): void
    {
        DofusdbEffectMapping::query()->create([
            'dofusdb_effect_id' => 116,
            'sub_effect_slug' => 'booster',
            'characteristic_source' => DofusdbEffectMapping::SOURCE_CHARACTERISTIC,
            'characteristic_key' => null,
        ]);

        Http::fake([
            'https://api.dofusdb.fr/effects/116*' => Http::response(['id' => 116, 'characteristic' => 19], 200),
        ]);

        $code = Artisan::call('scrapping:effects:report-missing-characteristics', [
            '--skip-cache' => true,
            '--json' => true,
        ]);

        $this->assertSame(0, $code);
        $decoded = json_decode(Artisan::output(), true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('summary', $decoded);
        $this->assertArrayHasKey('groups', $decoded);
        $this->assertSame(1, $decoded['summary']['total_missing_rows'] ?? null);
        $this->assertNotEmpty($decoded['groups']);
    }
}

