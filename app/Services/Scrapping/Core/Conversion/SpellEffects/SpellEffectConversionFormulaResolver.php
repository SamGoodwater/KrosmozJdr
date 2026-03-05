<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

/**
 * Résout la characteristic_key (groupe spell) à utiliser pour la conversion de la valeur
 * d'un sous-effet, selon l'action (sub_effect_slug) et les params (ex. characteristic).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md
 */
final class SpellEffectConversionFormulaResolver
{
    /** Actions avec une seule règle de conversion (dommages/soin/bouclier) → power_spell. */
    private const SINGLE_RULE_SLUGS = [
        'frapper',
        'soigner',
        'voler-vie',
        'protéger',
    ];

    /** Actions avec conversion par caractéristique (booster, retirer, voler-caracteristiques). */
    private const PER_CHARACTERISTIC_SLUGS = [
        'booster',
        'retirer',
        'voler-caracteristiques',
    ];

    /** Clé utilisée pour les actions à une règle (ex. dommages/soins) — existe en characteristic_spell. */
    private const SINGLE_RULE_CHARACTERISTIC_KEY = 'power_spell';

    /** Entité pour toutes les conversions d'effets de sort. */
    public const ENTITY_SPELL = 'spell';

    /**
     * Retourne la characteristic_key (groupe spell) pour appliquer la conversion, ou null si pas de conversion.
     *
     * @param string $subEffectSlug Slug du sous-effet (frapper, soigner, booster, …)
     * @param array<string, mixed> $params Params du sous-effet (characteristic, value_formula, …)
     * @return string|null Clé pour DofusConversionService (ex. power_spell, pa_spell) ou null
     */
    public function resolveCharacteristicKeyForConversion(string $subEffectSlug, array $params): ?string
    {
        if (in_array($subEffectSlug, self::SINGLE_RULE_SLUGS, true)) {
            return self::SINGLE_RULE_CHARACTERISTIC_KEY;
        }

        if (in_array($subEffectSlug, self::PER_CHARACTERISTIC_SLUGS, true)) {
            $char = $params['characteristic'] ?? null;
            if (is_string($char) && $char !== '') {
                return $this->normalizeSpellKey($char);
            }
            return null;
        }

        return null;
    }

    /** Mapping clés courtes (mapping DofusDB) → characteristic_key du groupe spell en BDD. */
    private const SPELL_KEY_ALIASES = [
        'pa' => 'action_points_spell',
        'po' => 'range_spell',
        'pm' => 'pm_spell',
        'range' => 'range_spell',
        'movement_points' => 'movement_points_spell',
        'retrait_pa' => 'ap_reduction_spell',
        'retrait_pm' => 'mp_reduction_spell',
        'ap_reduction' => 'ap_reduction_spell',
        'mp_reduction' => 'mp_reduction_spell',
        'fuite' => 'dodge_spell',
        'tacle' => 'tackle_spell',
        'dodge' => 'dodge_spell',
        'tackle' => 'tackle_spell',
        'strong' => 'strong_spell',
        'vitality' => 'vitality_spell',
        'sagesse' => 'sagesse_spell',
        'chance' => 'chance_spell',
        'agi' => 'agi_spell',
        'intel' => 'intel_spell',
        'critical' => 'critical_spell',
        'echec_critique' => 'echec_critique_spell',
        'res_terre' => 'res_terre_spell',
        'res_feu' => 'res_feu_spell',
        'res_eau' => 'res_eau_spell',
        'res_air' => 'res_air_spell',
        'res_neutre' => 'res_neutre_spell',
        'do_fixe_multiple' => 'do_fixe_multiple_spell',
        'esquive_pa' => 'dodge_action_points_spell',
        'esquive_pm' => 'dodge_movement_points_spell',
        'poussée' => 'push_damage_reduction_spell',
        'poussee' => 'push_damage_reduction_spell',
        'critiques' => 'critical_damage_reduction_spell',
        'prospection' => 'magic_find_spell',
        'res_fixe_terre' => 'fixed_resistance_terre_spell',
        'res_fixe_feu' => 'fixed_resistance_feu_spell',
        'res_fixe_eau' => 'fixed_resistance_eau_spell',
        'res_fixe_air' => 'fixed_resistance_air_spell',
        'res_fixe_neutre' => 'fixed_resistance_neutre_spell',
    ];

    /**
     * Normalise la clé pour le groupe spell (alias ou suffixe _spell).
     */
    private function normalizeSpellKey(string $key): string
    {
        $key = trim($key);
        if ($key === '') {
            return $key;
        }
        if (isset(self::SPELL_KEY_ALIASES[$key])) {
            return self::SPELL_KEY_ALIASES[$key];
        }
        if (str_ends_with($key, '_spell')) {
            return $key;
        }
        return $key . '_spell';
    }
}
