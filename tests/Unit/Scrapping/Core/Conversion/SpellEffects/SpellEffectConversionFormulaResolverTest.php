<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion\SpellEffects;

use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectConversionFormulaResolver;
use PHPUnit\Framework\TestCase;

/**
 * Tests du resolver action → characteristic_key (groupe spell) pour la conversion des valeurs d'effet.
 *
 * @see SpellEffectConversionFormulaResolver
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md
 */
class SpellEffectConversionFormulaResolverTest extends TestCase
{
    private SpellEffectConversionFormulaResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new SpellEffectConversionFormulaResolver;
    }

    public function test_frapper_returns_dommages_spell(): void
    {
        $key = $this->resolver->resolveCharacteristicKeyForConversion('frapper', []);
        $this->assertSame('dommages_spell', $key);
    }

    public function test_soigner_returns_soin_spell(): void
    {
        $this->assertSame('soin_spell', $this->resolver->resolveCharacteristicKeyForConversion('soigner', []));
    }

    public function test_life_steal_formula_returns_vol_vie_spell_for_conversion(): void
    {
        $this->assertSame(
            'vol_vie_spell',
            $this->resolver->resolveLifeStealCharacteristicKeyForConversion(['life_steal_formula' => '[dgt]/2'])
        );
        $this->assertNull($this->resolver->resolveLifeStealCharacteristicKeyForConversion([]));
        $this->assertNull($this->resolver->resolveLifeStealCharacteristicKeyForConversion(['life_steal_formula' => '']));
    }

    public function test_proteger_returns_bouclier_spell(): void
    {
        $this->assertSame('bouclier_spell', $this->resolver->resolveCharacteristicKeyForConversion('protéger', []));
    }

    public function test_booster_with_pa_returns_action_points_variation_spell(): void
    {
        $key = $this->resolver->resolveCharacteristicKeyForConversion('booster', ['characteristic' => 'pa']);
        $this->assertSame('action_points_variation_spell', $key);
    }

    public function test_booster_with_po_returns_range_spell(): void
    {
        $this->assertSame('range_spell', $this->resolver->resolveCharacteristicKeyForConversion('booster', ['characteristic' => 'po']));
    }

    public function test_booster_with_strong_returns_strong_spell(): void
    {
        $this->assertSame('strong_spell', $this->resolver->resolveCharacteristicKeyForConversion('booster', ['characteristic' => 'strong']));
    }

    public function test_booster_without_characteristic_returns_null(): void
    {
        $this->assertNull($this->resolver->resolveCharacteristicKeyForConversion('booster', []));
        $this->assertNull($this->resolver->resolveCharacteristicKeyForConversion('booster', ['characteristic' => '']));
    }

    public function test_retirer_with_pa_returns_action_points_variation_spell(): void
    {
        $this->assertSame('action_points_variation_spell', $this->resolver->resolveCharacteristicKeyForConversion('retirer', ['characteristic' => 'pa']));
    }

    public function test_voler_caracteristiques_with_pm_returns_movement_points_spell(): void
    {
        $this->assertSame('movement_points_spell', $this->resolver->resolveCharacteristicKeyForConversion('voler-caracteristiques', ['characteristic' => 'pm']));
    }

    public function test_deplacer_without_params_returns_movement_distance_spell(): void
    {
        $this->assertSame(
            'movement_distance_spell',
            $this->resolver->resolveCharacteristicKeyForConversion('déplacer', [])
        );
    }

    public function test_invoquer_returns_null(): void
    {
        $this->assertNull($this->resolver->resolveCharacteristicKeyForConversion('invoquer', []));
    }

    public function test_autre_returns_null(): void
    {
        $this->assertNull($this->resolver->resolveCharacteristicKeyForConversion('autre', ['value_formula' => '10']));
    }
}
