<?php

declare(strict_types=1);

namespace Tests\Feature\Scrapping;

use App\Models\DofusdbEffectMapping;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Services\Scrapping\Core\Conversion\SpellEffects\DofusdbEffectMappingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Réapplication des mappings sur sous-effets « autre » déjà importés.
 */
final class ScrappingEffectsReapplyMappingsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_does_not_update_rows(): void
    {
        [$pivotId] = $this->seedAutreTeleportPivot();

        $code = Artisan::call('scrapping:effects:reapply-mappings', [
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $code);
        $autreId = (int) SubEffect::query()->where('slug', 'autre')->value('id');
        $this->assertDatabaseHas('effect_sub_effect', [
            'id' => $pivotId,
            'sub_effect_id' => $autreId,
        ]);
    }

    public function test_reapply_moves_teleport_from_autre_to_deplacer(): void
    {
        [$pivotId] = $this->seedAutreTeleportPivot();

        $code = Artisan::call('scrapping:effects:reapply-mappings');

        $this->assertSame(0, $code);
        $deplacerId = (int) SubEffect::query()->where('slug', 'déplacer')->value('id');
        $this->assertDatabaseHas('effect_sub_effect', [
            'id' => $pivotId,
            'sub_effect_id' => $deplacerId,
        ]);

        $params = EffectSubEffect::query()->whereKey($pivotId)->value('params');
        $this->assertIsArray($params);
        $this->assertSame('teleport', $params['movement_kind'] ?? null);
        $this->assertTrue(($params['teleport'] ?? false) === true);
    }

    /**
     * @return array{0: int}
     */
    private function seedAutreTeleportPivot(): array
    {
        SubEffect::query()->firstOrCreate(
            ['slug' => 'autre'],
            ['type_slug' => 'autre', 'template_text' => null, 'variables_allowed' => [], 'param_schema' => []]
        );
        SubEffect::query()->firstOrCreate(
            ['slug' => 'déplacer'],
            ['type_slug' => 'déplacer', 'template_text' => null, 'variables_allowed' => [], 'param_schema' => []]
        );

        DofusdbEffectMapping::query()->create([
            'dofusdb_effect_id' => 1100,
            'sub_effect_slug' => 'déplacer',
            'characteristic_source' => DofusdbEffectMapping::SOURCE_NONE,
            'characteristic_key' => null,
        ]);
        app(DofusdbEffectMappingService::class)->clearCache();

        $spell = Spell::factory()->create();
        $effect = Effect::create([
            'name' => 'Téléport test',
            'slug' => 'teleport-test',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $spell->effects()->attach($effect->id);
        $degree = EffectDegree::create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'slug' => 'teleport-test-1',
        ]);

        $autreId = (int) SubEffect::query()->where('slug', 'autre')->value('id');
        $pivot = EffectSubEffect::create([
            'effect_degree_id' => $degree->id,
            'sub_effect_id' => $autreId,
            'order' => 0,
            'scope' => Effect::SCOPE_GENERAL,
            'params' => [
                'value' => 'Téléporte à la position précédente',
                'value_formula' => '0',
                'dofus_effect_id' => 1100,
            ],
            'crit_only' => false,
        ]);

        return [(int) $pivot->id];
    }
}
