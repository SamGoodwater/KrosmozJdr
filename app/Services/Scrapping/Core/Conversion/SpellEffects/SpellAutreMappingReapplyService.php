<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

use App\Models\Entity\Condition;
use App\Models\SubEffect;
use App\Services\Condition\ConditionCanonicalMapper;
use Illuminate\Support\Facades\DB;

/**
 * Réapplique les mappings DofusDB sur les sous-effets importés encore en slug « autre ».
 *
 * Cas typique : téléports / échanges mappés vers `déplacer` après import, sans re-scrap.
 */
final class SpellAutreMappingReapplyService
{
    /**
     * effectId Dofus → dofusdb_id de l’état Condition (aligné sur SpellEffectsConversionService).
     *
     * @var array<int, int>
     */
    private const FORCED_STATE_DOFUSDB_ID_BY_EFFECT_ID = [
        150 => 250,
    ];

    public function __construct(
        private readonly DofusdbEffectMappingService $mappingService,
        private readonly ConditionCanonicalMapper $conditionCanonicalMapper,
    ) {}

    /**
     * @return array{
     *   scanned: int,
     *   updated: int,
     *   by_slug: array<string, int>,
     *   skipped_unknown_slug: int,
     *   samples: list<array{effect_sub_effect_id: int, dofus_effect_id: int, from: string, to: string}>
     * }
     */
    public function reapply(bool $dryRun = false, int $sampleLimit = 20): array
    {
        $autreId = (int) SubEffect::query()->where('slug', 'autre')->value('id');
        if ($autreId <= 0) {
            return [
                'scanned' => 0,
                'updated' => 0,
                'by_slug' => [],
                'skipped_unknown_slug' => 0,
                'samples' => [],
            ];
        }

        $slugToId = SubEffect::query()->pluck('id', 'slug');
        $mappings = $this->mappingService->all();

        $scanned = 0;
        $updated = 0;
        $skippedUnknownSlug = 0;
        $bySlug = [];
        $samples = [];

        DB::table('effect_sub_effect as es')
            ->join('effect_degrees as ed', 'ed.id', '=', 'es.effect_degree_id')
            ->where('es.sub_effect_id', $autreId)
            ->whereExists(function ($q): void {
                $q->select(DB::raw(1))
                    ->from('effect_spell as esp')
                    ->whereColumn('esp.effect_id', 'ed.effect_id');
            })
            ->whereNotNull(DB::raw("JSON_EXTRACT(es.params, '$.dofus_effect_id')"))
            ->orderBy('es.id')
            ->select(['es.id', 'es.params'])
            ->chunkById(500, function ($rows) use (
                $mappings,
                $slugToId,
                $dryRun,
                $sampleLimit,
                &$scanned,
                &$updated,
                &$skippedUnknownSlug,
                &$bySlug,
                &$samples
            ): void {
                foreach ($rows as $row) {
                    $scanned++;
                    $params = $this->decodeParams($row->params);
                    $dofusEffectId = isset($params['dofus_effect_id']) && is_numeric($params['dofus_effect_id'])
                        ? (int) $params['dofus_effect_id']
                        : 0;
                    if ($dofusEffectId <= 0) {
                        continue;
                    }

                    $mapping = $mappings[$dofusEffectId] ?? null;
                    if ($mapping === null) {
                        continue;
                    }

                    $targetSlug = (string) ($mapping['sub_effect_slug'] ?? '');
                    if ($targetSlug === '' || $targetSlug === 'autre') {
                        continue;
                    }

                    $targetSubEffectId = isset($slugToId[$targetSlug]) ? (int) $slugToId[$targetSlug] : 0;
                    if ($targetSubEffectId <= 0) {
                        $skippedUnknownSlug++;

                        continue;
                    }

                    $newParams = $this->enrichParamsForSlug($targetSlug, $params, $dofusEffectId);
                    $bySlug[$targetSlug] = ($bySlug[$targetSlug] ?? 0) + 1;
                    $updated++;

                    if (count($samples) < $sampleLimit) {
                        $samples[] = [
                            'effect_sub_effect_id' => (int) $row->id,
                            'dofus_effect_id' => $dofusEffectId,
                            'from' => 'autre',
                            'to' => $targetSlug,
                        ];
                    }

                    if ($dryRun) {
                        continue;
                    }

                    DB::table('effect_sub_effect')
                        ->where('id', (int) $row->id)
                        ->update([
                            'sub_effect_id' => $targetSubEffectId,
                            'params' => json_encode($newParams, JSON_UNESCAPED_UNICODE),
                            'updated_at' => now(),
                        ]);
                }
            }, 'es.id', 'id');

        ksort($bySlug);

        return [
            'scanned' => $scanned,
            'updated' => $updated,
            'by_slug' => $bySlug,
            'skipped_unknown_slug' => $skippedUnknownSlug,
            'samples' => $samples,
        ];
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function enrichParamsForSlug(string $slug, array $params, int $dofusEffectId): array
    {
        $params['dofus_effect_id'] = $dofusEffectId;
        $params['effect_direction'] = match ($slug) {
            'booster', 'soigner', 'protéger', 'donner-pv-temporaires', 'invoquer' => 'bonus',
            'retirer' => 'malus',
            'voler-caracteristiques' => 'steal',
            default => 'action',
        };

        if ($slug === 'déplacer') {
            $valueText = isset($params['value']) && is_string($params['value'])
                ? $this->normalizeText($params['value'])
                : '';
            $kind = $this->resolveMovementKind($dofusEffectId, $valueText);
            $params['movement_kind'] = $kind;
            if ($kind === 'teleport') {
                $params['teleport'] = true;
            }
            $cells = isset($params['cells_formula']) ? trim((string) $params['cells_formula']) : '';
            if ($cells === '') {
                if (isset($params['value_converted']) && is_numeric($params['value_converted'])) {
                    $params['cells_formula'] = (string) $params['value_converted'];
                } elseif (isset($params['value_formula']) && trim((string) $params['value_formula']) !== '') {
                    $params['cells_formula'] = trim((string) $params['value_formula']);
                }
            }
        }

        if ($slug === 'appliquer-etat' || $slug === 's-appliquer-etat') {
            $this->enrichApplyStateParams($params, $dofusEffectId);
        }

        return $params;
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function enrichApplyStateParams(array &$params, int $dofusEffectId): void
    {
        $stateDofusId = self::FORCED_STATE_DOFUSDB_ID_BY_EFFECT_ID[$dofusEffectId] ?? null;
        if ($stateDofusId === null) {
            return;
        }

        $condition = Condition::query()
            ->where('dofusdb_id', $stateDofusId)
            ->orderBy('id')
            ->first();

        if ($condition === null) {
            $params['condition_dofusdb_id'] = $stateDofusId;
            $params['condition_name'] = $params['condition_name'] ?? 'Invisible';

            return;
        }

        $params['condition_dofusdb_id'] = (int) $condition->dofusdb_id;
        if (! empty($condition->icon)) {
            $params['condition_icon'] = (string) $condition->icon;
        }
        if (! empty($condition->image)) {
            $params['condition_image'] = (string) $condition->image;
        }

        $canonical = $this->conditionCanonicalMapper->resolve($condition);
        if ($canonical !== null) {
            $params['condition_id'] = (int) $canonical->id;
            $params['condition_name'] = (string) $canonical->name;
        } else {
            unset($params['condition_id']);
            $params['condition_name'] = (string) ($condition->name ?: 'Invisible');
        }
        unset($params['value'], $params['value_formula']);
    }

    private function resolveMovementKind(int $effectId, string $normalizedValue): string
    {
        $kindFromId = match ($effectId) {
            5, 1021, 1041, 4001, 4002, 1103, 4003 => 'push',
            6, 1022, 1042, 1043 => 'pull',
            4 => 'teleport',
            default => null,
        };
        if ($kindFromId !== null) {
            return $kindFromId;
        }

        if (preg_match('/\b(attire|attirer|rapproche|vers le lanceur|avance)\b/u', $normalizedValue) === 1) {
            return 'pull';
        }
        if (preg_match('/\b(repousse|repousser|pousse|eloigne|recule)\b/u', $normalizedValue) === 1) {
            return 'push';
        }
        if (preg_match('/\b(teleporte|teleportation|echange de position|transpose)\b/u', $normalizedValue) === 1) {
            return 'teleport';
        }
        if (preg_match('/\b(saute|saut|bond|bondit)\b/u', $normalizedValue) === 1) {
            return 'jump';
        }

        return 'movement';
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeParams(mixed $params): array
    {
        if (is_array($params)) {
            return $params;
        }
        if (! is_string($params) || $params === '') {
            return [];
        }
        $decoded = json_decode($params, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        $text = strtr($text, [
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
        ]);

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }
}
