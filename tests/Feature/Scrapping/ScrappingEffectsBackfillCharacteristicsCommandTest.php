<?php

declare(strict_types=1);

namespace Tests\Feature\Scrapping;

use App\Models\DofusdbEffectMapping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests de la commande scrapping:effects:backfill-characteristics.
 */
class ScrappingEffectsBackfillCharacteristicsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dry_run_does_not_update_database(): void
    {
        DofusdbEffectMapping::query()->create([
            'dofusdb_effect_id' => 116,
            'sub_effect_slug' => 'booster',
            'characteristic_source' => DofusdbEffectMapping::SOURCE_CHARACTERISTIC,
            'characteristic_key' => null,
        ]);

        Http::fake([
            'https://api.dofusdb.fr/effects/116*' => Http::response([
                'id' => 116,
                'characteristic' => 19,
            ], 200),
        ]);

        $code = Artisan::call('scrapping:effects:backfill-characteristics', [
            '--ids' => '116',
            '--dry-run' => true,
            '--skip-cache' => true,
        ]);

        $this->assertSame(0, $code);
        $this->assertDatabaseHas('dofusdb_effect_mappings', [
            'dofusdb_effect_id' => 116,
            'characteristic_key' => null,
        ]);
        $this->assertStringContainsString('DRY-RUN effect 116', Artisan::output());
    }

    public function test_command_updates_characteristic_key_from_spell_config_mapping(): void
    {
        DofusdbEffectMapping::query()->create([
            'dofusdb_effect_id' => 116,
            'sub_effect_slug' => 'booster',
            'characteristic_source' => DofusdbEffectMapping::SOURCE_CHARACTERISTIC,
            'characteristic_key' => null,
        ]);

        Http::fake([
            'https://api.dofusdb.fr/effects/116*' => Http::response([
                'id' => 116,
                'characteristic' => 19,
            ], 200),
        ]);

        $code = Artisan::call('scrapping:effects:backfill-characteristics', [
            '--ids' => '116',
            '--skip-cache' => true,
        ]);

        $this->assertSame(0, $code);
        $this->assertDatabaseHas('dofusdb_effect_mappings', [
            'dofusdb_effect_id' => 116,
            'characteristic_key' => 'po',
        ]);
        $this->assertStringContainsString('UPDATED effect 116', Artisan::output());
    }
}

