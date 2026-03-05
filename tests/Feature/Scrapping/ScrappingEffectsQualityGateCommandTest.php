<?php

declare(strict_types=1);

namespace Tests\Feature\Scrapping;

use App\Models\DofusdbEffectMapping;
use App\Models\Effect;
use App\Models\EffectGroup;
use App\Models\EffectSubEffect;
use App\Models\EffectUsage;
use App\Models\SubEffect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Tests de la commande scrapping:effects:quality-gate.
 */
class ScrappingEffectsQualityGateCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_gate_fails_when_no_expected_rows_and_allow_empty_is_absent(): void
    {
        $code = Artisan::call('scrapping:effects:quality-gate', [
            '--json' => true,
        ]);

        $this->assertSame(1, $code);
        $decoded = json_decode(Artisan::output(), true);
        $this->assertIsArray($decoded);
        $this->assertFalse((bool) ($decoded['ok'] ?? true));
        $this->assertContains(
            'conversion_expected_rows=0 et --allow-empty absent',
            $decoded['violations'] ?? []
        );
    }

    public function test_gate_passes_when_no_expected_rows_and_allow_empty_is_present(): void
    {
        $code = Artisan::call('scrapping:effects:quality-gate', [
            '--allow-empty' => true,
            '--json' => true,
        ]);

        $this->assertSame(0, $code);
        $decoded = json_decode(Artisan::output(), true);
        $this->assertIsArray($decoded);
        $this->assertTrue((bool) ($decoded['ok'] ?? false));
    }

    public function test_gate_fails_when_mapping_missing_exceeds_threshold(): void
    {
        DofusdbEffectMapping::query()->create([
            'dofusdb_effect_id' => 116,
            'sub_effect_slug' => 'booster',
            'characteristic_source' => DofusdbEffectMapping::SOURCE_CHARACTERISTIC,
            'characteristic_key' => null,
        ]);

        $code = Artisan::call('scrapping:effects:quality-gate', [
            '--allow-empty' => true,
            '--json' => true,
        ]);

        $this->assertSame(1, $code);
        $decoded = json_decode(Artisan::output(), true);
        $this->assertIsArray($decoded);
        $this->assertFalse((bool) ($decoded['ok'] ?? true));
        $violations = $decoded['violations'] ?? [];
        $this->assertIsArray($violations);
        $this->assertNotEmpty($violations);
        $this->assertStringContainsString('mapping_missing_characteristic_key=1 > max=0', (string) $violations[0]);
    }

    public function test_gate_passes_when_thresholds_are_respected(): void
    {
        $group = EffectGroup::query()->create([
            'name' => 'Test',
            'slug' => 'test-group',
        ]);
        $effect = Effect::query()->create([
            'name' => 'Test effect',
            'slug' => 'test-effect',
            'effect_group_id' => $group->id,
            'degree' => 1,
        ]);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper',
            'type_slug' => 'damage',
        ]);
        EffectSubEffect::query()->create([
            'effect_id' => $effect->id,
            'sub_effect_id' => $sub->id,
            'order' => 0,
            'params' => [
                'value_formula' => '2d6',
                'value_converted' => '2d6',
                'characteristic_key' => 'strength',
            ],
        ]);
        EffectUsage::query()->create([
            'entity_type' => 'spell',
            'entity_id' => 1,
            'effect_id' => $effect->id,
            'level_min' => 1,
            'level_max' => 1,
        ]);

        $code = Artisan::call('scrapping:effects:quality-gate', [
            '--min-coverage' => 99,
            '--max-missing-mappings' => 0,
            '--max-missing-value-converted' => 0,
            '--json' => true,
        ]);

        $this->assertSame(0, $code);
        $decoded = json_decode(Artisan::output(), true);
        $this->assertIsArray($decoded);
        $this->assertTrue((bool) ($decoded['ok'] ?? false));
    }
}

