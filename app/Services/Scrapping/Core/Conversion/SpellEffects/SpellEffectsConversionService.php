<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

use App\Services\Scrapping\Config\DofusDbEffectCatalog;
use Illuminate\Support\Str;

/**
 * Sous-service de conversion dédié aux effets de sorts DofusDB vers KrosmozJDR.
 *
 * Prend en entrée les données brutes du sort et les spell-levels (déjà récupérés),
 * résout chaque effectId via DofusDbEffectCatalog, applique le mapping effectId → SubEffect
 * et produit une structure prête pour l'intégration (EffectGroup + Effects + sous-effets).
 *
 * @see docs/50-Fonctionnalités/Scrapping/DOFUSDB_EFFECTS_CONVERSION.md
 */
final class SpellEffectsConversionService
{
    public function __construct(
        private DofusDbEffectCatalog $effectCatalog,
    ) {
    }

    /**
     * Convertit les effets d'un sort DofusDB (sort brut + spell-levels) en structure KrosmozJDR.
     *
     * @param array<string, mixed> $spellRaw Réponse GET /spells/{id} (doit contenir id, name, spellLevels)
     * @param list<array<string, mixed>> $spellLevelsData Liste des réponses GET /spell-levels/{levelId} (grade, effects[], criticalEffect[])
     * @param array{lang?: string} $options lang pour le catalogue d'effets (défaut fr)
     */
    public function convert(
        array $spellRaw,
        array $spellLevelsData,
        array $options = []
    ): SpellEffectsConversionResult {
        $lang = (string) ($options['lang'] ?? 'fr');
        $spellName = $this->extractSpellName($spellRaw, $lang);
        $spellId = isset($spellRaw['id']) ? (int) $spellRaw['id'] : 0;
        $baseSlug = $this->buildSlug($spellName, $spellId);

        $effectGroup = [
            'name' => $spellName,
            'slug' => $baseSlug,
        ];

        $effects = [];
        foreach ($spellLevelsData as $levelData) {
            $grade = isset($levelData['grade']) ? (int) $levelData['grade'] : 0;
            $effects[] = $this->convertOneLevel(
                $levelData,
                $spellName,
                $baseSlug,
                $grade,
                $lang
            );
        }

        usort($effects, static fn (array $a, array $b) => ($a['degree'] ?? 0) <=> ($b['degree'] ?? 0));

        return new SpellEffectsConversionResult($effectGroup, $effects);
    }

    /**
     * @param array<string, mixed> $levelData Un spell-level (effects[], criticalEffect[])
     * @return array{degree: int, name: string, slug: string, description: string|null, sub_effects: list<array>}
     */
    private function convertOneLevel(
        array $levelData,
        string $spellName,
        string $baseSlug,
        int $grade,
        string $lang
    ): array {
        $degree = $grade > 0 ? $grade : 1;
        $slug = $baseSlug . '-' . $degree;

        $subEffects = [];
        $effectsList = $levelData['effects'] ?? [];
        $criticalList = $this->indexCriticalEffectsByOrder($levelData['criticalEffect'] ?? []);

        foreach ($effectsList as $index => $instance) {
            if (!is_array($instance)) {
                continue;
            }
            $effectId = isset($instance['effectId']) ? (int) $instance['effectId'] : 0;
            if ($effectId === 0) {
                continue;
            }

            $definition = $this->effectCatalog->get($effectId, $lang);
            $mapping = DofusDbEffectMapping::getSubEffectForEffectId($effectId);
            if ($mapping === null) {
                continue;
            }

            [$subEffectSlug, $charSource] = $mapping;
            $order = isset($instance['order']) ? (int) $instance['order'] : $index;
            $params = $this->buildParams($instance, $definition, $charSource);
            $critOnly = false;

            $criticalInstance = $criticalList[$order] ?? null;
            if ($criticalInstance !== null && is_array($criticalInstance)) {
                $critFormula = $this->buildValueFormula($criticalInstance);
                if ($critFormula !== null && $critFormula !== '') {
                    $params['value_formula_crit'] = $critFormula;
                }
            }

            $subEffects[] = [
                'order' => $order,
                'sub_effect_slug' => $subEffectSlug,
                'params' => $params,
                'crit_only' => $critOnly,
            ];
        }

        return [
            'degree' => $degree,
            'name' => $spellName,
            'slug' => $slug,
            'description' => null,
            'sub_effects' => $subEffects,
        ];
    }

    /**
     * @param list<array<string, mixed>> $criticalEffect
     * @return array<int, array<string, mixed>> order => instance
     */
    private function indexCriticalEffectsByOrder(array $criticalEffect): array
    {
        $indexed = [];
        foreach ($criticalEffect as $inst) {
            if (!is_array($inst)) {
                continue;
            }
            $order = isset($inst['order']) ? (int) $inst['order'] : count($indexed);
            $indexed[$order] = $inst;
        }
        return $indexed;
    }

    /**
     * @param array<string, mixed> $instance Instance d'effet (diceNum, diceSide, value, effectElement)
     * @param array<string, mixed> $definition Définition /effects/{id} (elementId, characteristic)
     * @return array<string, mixed> params pour le pivot (value_formula, characteristic, value_formula_crit si fourni ailleurs)
     */
    private function buildParams(array $instance, array $definition, string $charSource): array
    {
        $params = [
            'value_formula' => $this->buildValueFormula($instance),
            'value_formula_crit' => null,
        ];

        if ($charSource === 'element') {
            $elementId = isset($instance['effectElement']) && is_numeric($instance['effectElement'])
                ? (int) $instance['effectElement']
                : (isset($definition['elementId']) && is_numeric($definition['elementId']) ? (int) $definition['elementId'] : null);
            $key = DofusDbEffectMapping::elementIdToCharacteristicKey($elementId);
            if ($key !== null) {
                $params['characteristic'] = $key;
            }
        }

        return $params;
    }

    /**
     * @param array<string, mixed> $instance
     */
    private function buildValueFormula(array $instance): ?string
    {
        $diceNum = isset($instance['diceNum']) && is_numeric($instance['diceNum']) ? (int) $instance['diceNum'] : null;
        $diceSide = isset($instance['diceSide']) && is_numeric($instance['diceSide']) ? (int) $instance['diceSide'] : null;
        if ($diceNum !== null && $diceSide !== null && $diceNum > 0 && $diceSide > 0) {
            return $diceNum . 'd' . $diceSide;
        }
        $value = isset($instance['value']) && is_numeric($instance['value']) ? (int) $instance['value'] : null;
        if ($value !== null) {
            return (string) $value;
        }
        return null;
    }

    private function extractSpellName(array $spellRaw, string $lang): string
    {
        $name = $spellRaw['name'] ?? null;
        if (is_array($name) && isset($name[$lang])) {
            return (string) $name[$lang];
        }
        if (is_string($name)) {
            return $name;
        }
        if (is_array($name)) {
            return (string) ($name['fr'] ?? reset($name) ?? 'Sans nom');
        }
        return 'Sans nom';
    }

    private function buildSlug(string $name, int $spellId): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'spell';
        }
        return $spellId > 0 ? $base . '-' . $spellId : $base;
    }
}
