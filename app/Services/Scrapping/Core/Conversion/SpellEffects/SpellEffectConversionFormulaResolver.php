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
