<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Entity\Spell;
use App\Support\AreaNotation;

/**
 * Résumé texte et chips structurés des usages d’effets d’un sort (aligné tableau sorts / tooltips).
 */
final class SpellEffectUsagesDataService
{
    /** Slugs élément → id primaire (0=Neutre, 1=Terre, 2=Feu, 3=Air, 4=Eau, 5=Sagesse, 6=Vitalité). */
    private const ELEMENT_SLUG_TO_ID = [
        'neutral' => 0,
        'earth' => 1,
        'fire' => 2,
        'air' => 3,
        'water' => 4,
        'element_wisdom' => 5,
        'element_vitality' => 6,
        'fixed_damage_neutral_spell' => 0,
        'fixed_damage_earth_spell' => 1,
        'fixed_damage_fire_spell' => 2,
        'fixed_damage_air_spell' => 3,
        'fixed_damage_water_spell' => 4,
        'fixed_damage_sagesse_spell' => 5,
        'fixed_damage_vitalite_spell' => 6,
    ];

    /** Labels pour target_type. */
    private const TARGET_TYPE_LABELS = [
        'direct' => 'Direct',
        'trap' => 'Piège',
        'glyph' => 'Glyphe',
    ];

    public function __construct(
        private readonly EffectResolutionService $effectResolutionService
    ) {}

    /**
     * Construit le résumé texte (pour recherche/tri) et les chips structurés (pour affichage).
     *
     * @return array{summary: string, chips: list<array<string, mixed>>}
     */
    public function build(Spell $spell): array
    {
        $spell->loadMissing(['effects.degrees.effectSubEffects.subEffect']);
        $definitions = $spell->effects ?? collect();
        if ($definitions->isEmpty()) {
            return ['summary' => '', 'chips' => []];
        }

        $level = is_numeric((string) $spell->level) ? (int) $spell->level : 1;
        $baseContext = ['level' => $level];
        $parts = [];
        $chips = [];

        foreach ($definitions as $definition) {
            $degrees = $definition->degrees
                ->sort(function ($a, $b) {
                    $la = $a->required_creature_level ?? -1;
                    $lb = $b->required_creature_level ?? -1;
                    if ($la !== $lb) {
                        return $la <=> $lb;
                    }

                    return $a->degree <=> $b->degree;
                })
                ->values();
            foreach ($degrees as $degreeRow) {
                $degreeNum = $degreeRow->degree;
                $targetType = $definition->target_type ?? 'direct';
                $targetLabel = self::TARGET_TYPE_LABELS[$targetType] ?? 'Direct';
                $area = $degreeRow->area;

                $resolved = $this->effectResolutionService->resolveEffect($degreeRow, $baseContext, null, false, false);
                foreach ($resolved['sub_effects'] ?? [] as $sub) {
                    $summonMonster = $sub['summon_monster'] ?? null;
                    $hasSummon = is_array($summonMonster) && isset($summonMonster['id']);
                    $text = trim((string) ($sub['text'] ?? ''));
                    if ($text === '' && $hasSummon) {
                        $mName = trim((string) ($summonMonster['name'] ?? ''));
                        $text = $mName !== '' ? 'Invocation '.$mName.'.' : 'Invocation.';
                    }
                    $actionSlugRaw = $sub['action_slug'] ?? null;
                    $hasActionSlug = is_string($actionSlugRaw) && trim($actionSlugRaw) !== '';
                    if ($text === '' && ! $hasSummon && ! $hasActionSlug) {
                        continue;
                    }
                    $text = $this->humanizeEffectText($text);
                    if ($text === '' && $hasActionSlug) {
                        $text = '['.trim($actionSlugRaw).']';
                    }
                    $parts[] = $text;

                    $charSlug = $this->resolveCharacteristicSlugForChip($sub);
                    $elementId = self::ELEMENT_SLUG_TO_ID[$charSlug] ?? 0;
                    $elementLabel = $this->elementIdToLabel($elementId);

                    $duration = isset($sub['duration']) && is_numeric($sub['duration']) ? (int) $sub['duration'] : null;
                    $durationLabel = $this->formatDurationLabel($duration);

                    $details = [];
                    $creatureLevelLabel = $this->formatCreatureLevelRequirement($degreeRow->required_creature_level);
                    if ($creatureLevelLabel !== '') {
                        $details[] = $creatureLevelLabel;
                    }
                    if ($targetType !== 'direct') {
                        $details[] = $targetLabel;
                    }
                    if ($area !== null && (string) $area !== '') {
                        $zoneLabel = AreaNotation::describeInFrench((string) $area);
                        if ($zoneLabel !== '') {
                            $details[] = $zoneLabel;
                        }
                    }
                    $details[] = $durationLabel;
                    $displayText = $text;
                    $tooltip = $displayText.(\count($details) > 0 ? ' — '.implode(', ', $details) : '');

                    $durationFormula = $sub['duration_formula'] ?? null;
                    $durationFormulaStr = is_string($durationFormula) ? trim($durationFormula) : '';

                    $chips[] = [
                        'text' => $displayText,
                        'degree' => $degreeNum,
                        'element' => $elementId,
                        'element_label' => $elementLabel,
                        'characteristic' => $charSlug !== '' ? $charSlug : null,
                        'target_type' => $targetType,
                        'target_label' => $targetLabel,
                        'area' => $area,
                        'duration' => $duration,
                        'duration_label' => $durationLabel,
                        'duration_formula' => $durationFormulaStr !== '' ? $durationFormulaStr : null,
                        'tooltip' => $tooltip,
                        'required_creature_level' => $degreeRow->required_creature_level,
                        'creature_level_label' => $creatureLevelLabel !== '' ? $creatureLevelLabel : null,
                        'creature_level_requirement' => [
                            'value' => $degreeRow->required_creature_level,
                            'label' => $creatureLevelLabel !== '' ? $creatureLevelLabel : null,
                        ],
                        'summon_monster' => $hasSummon ? $summonMonster : null,
                        'action_slug' => $sub['action_slug'] ?? null,
                        'crit_only' => (bool) ($sub['crit_only'] ?? false),
                        'scope' => $sub['scope'] ?? null,
                        'value_formula' => is_string($sub['value_formula'] ?? null) ? trim((string) $sub['value_formula']) : null,
                        'value_formula_crit' => is_string($sub['value_formula_crit'] ?? null) ? trim((string) $sub['value_formula_crit']) : null,
                        'life_steal_formula' => is_string($sub['life_steal_formula'] ?? null) ? trim((string) $sub['life_steal_formula']) : null,
                        'condition_id' => isset($sub['context']['condition_id']) && is_numeric($sub['context']['condition_id']) ? (int) $sub['context']['condition_id'] : null,
                        'condition_dofusdb_id' => isset($sub['context']['condition_dofusdb_id']) && is_numeric($sub['context']['condition_dofusdb_id']) ? (int) $sub['context']['condition_dofusdb_id'] : null,
                        'condition_name' => is_string($sub['condition_name'] ?? null) && trim((string) $sub['condition_name']) !== '' ? trim((string) $sub['condition_name']) : null,
                        'condition_context' => $this->conditionContextForChip($sub),
                        'cells_display' => is_string($sub['cells_display'] ?? null) && trim((string) $sub['cells_display']) !== '' ? trim((string) $sub['cells_display']) : null,
                        'movement_kind' => is_string($sub['movement_kind'] ?? null) && trim((string) $sub['movement_kind']) !== '' ? trim((string) $sub['movement_kind']) : null,
                        'teleport' => (bool) ($sub['teleport'] ?? false),
                    ];
                }
            }
        }

        return [
            'summary' => implode(' • ', $parts),
            'chips' => $chips,
        ];
    }

    /**
     * Slug caractéristique / élément pour l’UI (store spell) : params ou contexte pivot (ex. element numérique).
     *
     * @param  array<string, mixed>  $sub
     */
    private function resolveCharacteristicSlugForChip(array $sub): string
    {
        $raw = strtolower(trim((string) ($sub['characteristic'] ?? '')));
        if ($raw !== '') {
            return $raw;
        }
        $ctx = $sub['context'] ?? null;
        if (! is_array($ctx)) {
            return '';
        }
        $el = $ctx['element'] ?? null;
        if ($el === null || $el === '') {
            return '';
        }
        if (is_numeric($el)) {
            $id = (int) $el;
            foreach (self::ELEMENT_SLUG_TO_ID as $slug => $eid) {
                if ($eid === $id) {
                    return $slug;
                }
            }

            return '';
        }

        return strtolower(trim((string) $el));
    }

    /**
     * @param  array<string, mixed>  $sub
     * @return array<string, mixed>|null
     */
    private function conditionContextForChip(array $sub): ?array
    {
        $ctx = $sub['context'] ?? null;
        if (! is_array($ctx)) {
            return null;
        }

        $hasCondition = in_array((string) ($sub['action_slug'] ?? ''), ['appliquer-etat', 's-appliquer-etat'], true)
            || isset($ctx['condition_id'])
            || isset($ctx['condition_dofusdb_id'])
            || isset($ctx['condition_name']);
        if (! $hasCondition) {
            return null;
        }

        return [
            'condition_id' => isset($ctx['condition_id']) && is_numeric($ctx['condition_id']) ? (int) $ctx['condition_id'] : null,
            'condition_dofusdb_id' => isset($ctx['condition_dofusdb_id']) && is_numeric($ctx['condition_dofusdb_id']) ? (int) $ctx['condition_dofusdb_id'] : null,
            'condition_name' => is_string($ctx['condition_name'] ?? null) && trim((string) $ctx['condition_name']) !== '' ? trim((string) $ctx['condition_name']) : null,
            'duration' => isset($ctx['duration']) && is_numeric($ctx['duration']) ? (int) $ctx['duration'] : null,
            'dispellable' => is_bool($ctx['dispellable'] ?? null) ? $ctx['dispellable'] : null,
            'target_mask' => is_string($ctx['target_mask'] ?? null) && trim((string) $ctx['target_mask']) !== '' ? trim((string) $ctx['target_mask']) : null,
        ];
    }

    /**
     * Libellé : niveau minimum de créature requis pour l’usage — distinct du niveau du sort.
     */
    private function formatCreatureLevelRequirement(?int $requiredCreatureLevel): string
    {
        if ($requiredCreatureLevel === null) {
            return '';
        }

        return "Créature niveau ≥ {$requiredCreatureLevel}";
    }

    /** Traduit duration 0 ou 1 en "Immédiat", sinon "X tour(s)". */
    private function formatDurationLabel(?int $duration): string
    {
        if ($duration === null) {
            return 'Immédiat';
        }
        if ($duration === 0 || $duration === 1) {
            return 'Immédiat';
        }

        return $duration.' tour'.($duration > 1 ? 's' : '');
    }

    private function elementIdToLabel(int $id): string
    {
        return match ($id) {
            0 => 'Neutre',
            1 => 'Terre',
            2 => 'Feu',
            3 => 'Air',
            4 => 'Eau',
            5 => 'Sagesse',
            6 => 'Vitalité',
            default => 'Neutre',
        };
    }

    /** Remplace les slugs d'éléments par les libellés français. */
    private function humanizeEffectText(string $text): string
    {
        $elementLabels = [
            'water' => 'Eau',
            'earth' => 'Terre',
            'fire' => 'Feu',
            'air' => 'Air',
            'neutral' => 'Neutre',
            'element_wisdom' => 'Sagesse',
            'element_vitality' => 'Vitalité',
        ];
        foreach ($elementLabels as $slug => $label) {
            $text = preg_replace('/\b'.preg_quote($slug, '/').'\b/i', $label, $text);
        }

        return $text;
    }
}
