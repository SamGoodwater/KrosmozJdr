<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

use App\Models\Entity\Spell;
use App\Support\DofusDbElementId;

/**
 * Déduit la résolution Krosmoz d'un sort à partir de ses sous-effets convertis.
 *
 * Aligné sur les règles 3.3.2.3 :
 * - dégâts monocibles (même avec retrait PA/PM) → jet d'attaque vs CA + esquive ;
 * - états hostiles, zones, placement offensif → jet de sauvegarde ;
 * - un retrait PA/PM seul ne force pas une sauvegarde ;
 * - soutien pur → réussite automatique.
 *
 * DofusDB n'expose pas `isMagic` : le booléen Wakfu/physique est donc inféré ici.
 *
 * @example
 * $resolution = (new SpellResolutionInferenceService)->infer($effects, $spellRaw);
 */
final class SpellResolutionInferenceService
{
    public const SAVE_DC_DEFAULT_FORMULA = '8 + modificateur de caractéristique + bonus de maîtrise';

    /**
     * @param  list<array<string, mixed>>  $effects
     * @param  array<string, mixed>  $spellRaw
     * @return array{
     *     resolution_mode: string,
     *     attack_characteristic_key: string|null,
     *     save_characteristic_key: string|null,
     *     save_dc_formula: string|null,
     *     save_success_note: string|null,
     *     is_magic: bool
     * }
     */
    public function infer(array $effects, array $spellRaw = []): array
    {
        $signals = $this->collectSignals($effects);

        if ($this->shouldUseSavingThrow($signals)) {
            return [
                'resolution_mode' => Spell::RESOLUTION_SAVING_THROW,
                'attack_characteristic_key' => null,
                'save_characteristic_key' => $signals['save_ability'] ?? 'sagesse',
                'save_dc_formula' => self::SAVE_DC_DEFAULT_FORMULA,
                'save_success_note' => $signals['has_damage']
                    ? 'En cas de sauvegarde réussie, appliquer uniquement ce que la fiche indique.'
                    : "En cas de sauvegarde réussie, annuler l'effet du sort.",
                'is_magic' => true,
            ];
        }

        if ($signals['has_damage'] || $signals['has_removal']) {
            return [
                'resolution_mode' => Spell::RESOLUTION_ATTACK_ROLL,
                'attack_characteristic_key' => $this->inferAttackCharacteristic($signals, $spellRaw),
                'save_characteristic_key' => null,
                'save_dc_formula' => null,
                'save_success_note' => null,
                'is_magic' => false,
            ];
        }

        return [
            'resolution_mode' => Spell::RESOLUTION_AUTO_SUCCESS,
            'attack_characteristic_key' => null,
            'save_characteristic_key' => null,
            'save_dc_formula' => null,
            'save_success_note' => null,
            'is_magic' => true,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $effects
     * @return array{
     *     has_damage: bool,
     *     has_area: bool,
     *     has_hostile_state: bool,
     *     has_placement: bool,
     *     has_removal: bool,
     *     has_support: bool,
     *     damage_element: string|null,
     *     save_ability: string|null
     * }
     */
    private function collectSignals(array $effects): array
    {
        $hasDamage = false;
        $hasArea = false;
        $hasHostileState = false;
        $hasPlacement = false;
        $hasRemoval = false;
        $hasSupport = false;
        $damageElement = null;
        $saveAbility = null;

        foreach ($effects as $effect) {
            if (! is_array($effect)) {
                continue;
            }

            $area = isset($effect['area']) ? trim((string) $effect['area']) : '';
            if ($area !== '' && $area !== 'point') {
                $hasArea = true;
            }

            $subEffects = $effect['sub_effects'] ?? [];
            if (! is_array($subEffects)) {
                continue;
            }

            foreach ($subEffects as $subEffect) {
                if (! is_array($subEffect)) {
                    continue;
                }

                $slug = (string) ($subEffect['sub_effect_slug'] ?? '');
                $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];
                $characteristic = isset($params['characteristic'])
                    ? strtolower(trim((string) $params['characteristic']))
                    : '';

                if ($slug === 'frapper') {
                    $hasDamage = true;
                    if ($damageElement === null && $characteristic !== '') {
                        $damageElement = $characteristic;
                    }

                    continue;
                }

                if ($slug === 'déplacer') {
                    $hasPlacement = true;
                    $saveAbility ??= $this->saveAbilityFromMovementKind(
                        is_string($params['movement_kind'] ?? null) ? (string) $params['movement_kind'] : null
                    );

                    continue;
                }

                if ($slug === 'retirer' || $slug === 'voler-caracteristiques') {
                    $hasRemoval = true;
                    $saveAbility ??= $this->saveAbilityFromCharacteristicKey($characteristic);

                    continue;
                }

                if ($slug === 'appliquer-etat') {
                    $hasHostileState = true;
                    $saveAbility ??= $this->saveAbilityFromStateParams($params);

                    continue;
                }

                if (in_array($slug, ['booster', 'soigner', 'protéger', 'donner-pv-temporaires', 'invoquer', 's-appliquer-etat'], true)) {
                    $hasSupport = true;
                }

                if ($slug === DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER) {
                    $otherText = strtolower((string) ($params['value'] ?? ''));
                    if ($otherText === '') {
                        continue;
                    }
                    if ($this->isRemovalText($otherText)) {
                        $hasRemoval = true;
                        $saveAbility ??= $this->saveAbilityFromOtherText($otherText);
                    }
                    if ($this->isPlacementText($otherText)) {
                        $hasPlacement = true;
                        $saveAbility ??= 'strong';
                    }
                    if ($this->isDamageText($otherText)) {
                        $hasDamage = true;
                    }
                    if ($this->isSupportText($otherText)) {
                        $hasSupport = true;
                    }
                    if ($this->isStateText($otherText)) {
                        $hasHostileState = true;
                        $saveAbility ??= 'sagesse';
                    }
                }
            }
        }

        return [
            'has_damage' => $hasDamage,
            'has_area' => $hasArea,
            'has_hostile_state' => $hasHostileState,
            'has_placement' => $hasPlacement,
            'has_removal' => $hasRemoval,
            'has_support' => $hasSupport,
            'damage_element' => $damageElement,
            'save_ability' => $saveAbility,
        ];
    }

    /**
     * @param  array{
     *     has_damage: bool,
     *     has_area: bool,
     *     has_hostile_state: bool,
     *     has_placement: bool,
     *     has_removal: bool,
     *     has_support: bool,
     *     damage_element: string|null,
     *     save_ability: string|null
     * }  $signals
     */
    private function shouldUseSavingThrow(array $signals): bool
    {
        if ($signals['has_hostile_state'] || $signals['has_placement']) {
            return true;
        }

        // Zone hostile → sauvegarde. Un retrait PA/PM monocible reste une touche.
        if ($signals['has_area'] && ($signals['has_damage'] || $signals['has_removal'])) {
            return true;
        }

        return false;
    }

    /**
     * @param  array{damage_element: string|null}  $signals
     * @param  array<string, mixed>  $spellRaw
     */
    private function inferAttackCharacteristic(array $signals, array $spellRaw): string
    {
        $fromDamage = $this->attackCharacteristicFromElementSlug($signals['damage_element'] ?? null);
        if ($fromDamage !== null) {
            return $fromDamage;
        }

        $elementId = isset($spellRaw['elementId']) && is_numeric($spellRaw['elementId'])
            ? (int) $spellRaw['elementId']
            : (isset($spellRaw['spell_global']['elementId']) && is_numeric($spellRaw['spell_global']['elementId'])
                ? (int) $spellRaw['spell_global']['elementId']
                : null);

        return match ($elementId) {
            DofusDbElementId::FIRE => 'intel',
            DofusDbElementId::WATER => 'chance',
            DofusDbElementId::EARTH => 'strong',
            DofusDbElementId::AIR => 'agi',
            default => 'strong',
        };
    }

    private function attackCharacteristicFromElementSlug(?string $slug): ?string
    {
        if ($slug === null || $slug === '') {
            return null;
        }

        return match ($slug) {
            'fire', 'feu' => 'intel',
            'water', 'eau' => 'chance',
            'earth', 'terre' => 'strong',
            'air' => 'agi',
            'neutral', 'neutre' => 'strong',
            default => null,
        };
    }

    private function saveAbilityFromMovementKind(?string $kind): string
    {
        return match ($kind) {
            'push', 'pull' => 'strong',
            'jump', 'teleport', 'movement' => 'agi',
            default => 'strong',
        };
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function saveAbilityFromStateParams(array $params): string
    {
        $flags = is_array($params['condition_flags'] ?? null) ? $params['condition_flags'] : [];
        $flag = static fn (string $snake, string $camel): bool => (bool) ($flags[$snake] ?? $flags[$camel] ?? false);

        if ($flag('cant_be_moved', 'cantBeMoved')
            || $flag('cant_be_pushed', 'cantBePushed')
            || $flag('cant_switch_position', 'cantSwitchPosition')) {
            return 'strong';
        }
        if ($flag('prevents_spell_cast', 'preventsSpellCast')
            || $flag('incurable', 'incurable')
            || $flag('invulnerable', 'invulnerable')
            || $flag('invulnerable_melee', 'invulnerableMelee')
            || $flag('invulnerable_range', 'invulnerableRange')) {
            return 'sagesse';
        }
        if ($flag('cant_deal_damage', 'cantDealDamage')) {
            return 'sagesse';
        }

        $name = $this->normalizeDecisionText((string) ($params['condition_name'] ?? ''));
        if ($name !== '') {
            $fromName = $this->saveAbilityFromOtherText($name);
            if ($fromName !== null) {
                return $fromName;
            }
        }

        return 'sagesse';
    }

    private function saveAbilityFromCharacteristicKey(string $key): ?string
    {
        if ($key === '') {
            return null;
        }

        if (str_contains($key, 'pa') || str_contains($key, 'pm') || str_contains($key, 'action_points')
            || str_contains($key, 'movement_points') || str_contains($key, 'sagesse') || str_contains($key, 'wisdom')) {
            return 'sagesse';
        }
        if (str_contains($key, 'fuite') || str_contains($key, 'tacle') || str_contains($key, 'agi')
            || str_contains($key, 'dodge') || str_contains($key, 'tackle')) {
            return 'agi';
        }
        if (str_contains($key, 'strong') || str_contains($key, 'force')) {
            return 'strong';
        }
        if (str_contains($key, 'intel')) {
            return 'intel';
        }
        if (str_contains($key, 'chance')) {
            return 'chance';
        }
        if (str_contains($key, 'vital')) {
            return 'vitality';
        }

        return 'sagesse';
    }

    private function saveAbilityFromOtherText(string $otherText): ?string
    {
        $text = $this->normalizeDecisionText($otherText);

        if (preg_match('/\b(pa|pm|esquive pa|esquive pm|sagesse)\b/u', $text) === 1) {
            return 'sagesse';
        }
        if (preg_match('/\b(fuite|tacle|agilite)\b/u', $text) === 1) {
            return 'agi';
        }
        if (preg_match('/\b(force|repousse|attire|renversement)\b/u', $text) === 1) {
            return 'strong';
        }
        if (preg_match('/\b(intelligence)\b/u', $text) === 1) {
            return 'intel';
        }
        if (preg_match('/\b(chance)\b/u', $text) === 1) {
            return 'chance';
        }
        if (preg_match('/\b(vitalite|poison|maladie)\b/u', $text) === 1) {
            return 'vitality';
        }

        return null;
    }

    private function isRemovalText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);
        if ($normalized === '' || str_contains($normalized, 'kamas')) {
            return false;
        }
        if (str_contains($normalized, 'dommage') && str_contains($normalized, 'pa utilise')) {
            return false;
        }

        $mentionsNegativePattern = preg_match('/-\s*#|\bretire\b|\bretrait\b/u', $normalized) === 1;
        $mentionsSteal = preg_match('/\b(vole|vol de)\b/u', $normalized) === 1;
        $mentionsStat = preg_match('/\b(pa|pm|fuite|tacle|portee|sagesse|intelligence|agilite|chance|force|vitalite)\b/u', $normalized) === 1;

        return $mentionsNegativePattern || ($mentionsSteal && $mentionsStat);
    }

    private function isPlacementText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);

        return preg_match('/\b(repousse|attire|teleporte|pousse|avance|recule|deplace|echange de position)\b/u', $normalized) === 1;
    }

    private function isDamageText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);

        return preg_match('/\b(dommage|dommages|degat|degats|vol de vie|frappe)\b/u', $normalized) === 1;
    }

    private function isSupportText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);

        return preg_match('/\b(invoque|soin|protege|bouclier|boost|augmente|rend)\b/u', $normalized) === 1;
    }

    private function isStateText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);

        return preg_match('/\b(etat|envoûtement|envoutement|condition|immobilis|pesanteur|aveugle|empoison)\b/u', $normalized) === 1;
    }

    private function normalizeDecisionText(string $text): string
    {
        $value = trim(mb_strtolower($text));
        if ($value === '') {
            return '';
        }

        $value = strip_tags($value);
        $value = str_replace(
            ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç'],
            ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c'],
            $value
        );
        $value = preg_replace('/<[^>]+>/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
