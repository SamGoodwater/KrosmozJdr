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
 * Tests de la commande scrapping:effects:audit-quality.
 */
class ScrappingEffectsQualityAuditCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_json_reports_mapping_missing_characteristic_key(): void
    {
        DofusdbEffectMapping::query()->create([
            'dofusdb_effect_id' => 116,
            'sub_effect_slug' => 'booster',
            'characteristic_source' => DofusdbEffectMapping::SOURCE_CHARACTERISTIC,
            'characteristic_key' => null,
        ]);

        $code = Artisan::call('scrapping:effects:audit-quality', [
            '--json' => true,
        ]);

        $this->assertSame(0, $code);
        $decoded = json_decode(Artisan::output(), true);
        $this->assertIsArray($decoded);
        $this->assertSame(1, $decoded['mapping']['missing_characteristic_key_count'] ?? null);
    }

    public function test_command_json_reports_missing_value_converted_for_spell_effect_sub_effect(): void
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
            ],
        ]);
        EffectUsage::query()->create([
            'entity_type' => 'spell',
            'entity_id' => 1,
            'effect_id' => $effect->id,
            'level_min' => 1,
            'level_max' => 1,
        ]);

        $code = Artisan::call('scrapping:effects:audit-quality', [
            '--json' => true,
        ]);

        $this->assertSame(0, $code);
        $decoded = json_decode(Artisan::output(), true);
        $this->assertIsArray($decoded);
        $this->assertSame(1, $decoded['conversion']['expected_rows'] ?? null);
        $this->assertSame(1, $decoded['conversion']['missing_value_converted_rows'] ?? null);
    }
}

