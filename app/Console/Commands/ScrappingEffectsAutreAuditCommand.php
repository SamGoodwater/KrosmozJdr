<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Audit spécifique des sous-effets "autre" sur les sorts importés.
 *
 * Objectif:
 * - Mesurer la part de "autre" dans les sous-effets de sorts.
 * - Identifier les "autre" probablement convertibles (retrait/placement/soin/dégâts/etc.).
 * - Donner un top actionnable (effectId DofusDB quand disponible, textes normalisés).
 */
final class ScrappingEffectsAutreAuditCommand extends Command
{
    protected $signature = 'scrapping:effects:audit-autre
                            {--json : Sortie JSON}
                            {--top=20 : Nombre de lignes max pour les tops}
                            {--sample-limit=20 : Nombre max d\'exemples par catégorie}';

    protected $description = 'Audit des sous-effets "autre" pour améliorer la qualité de conversion des sorts';

    public function handle(): int
    {
        $asJson = (bool) $this->option('json');
        $top = max(1, (int) $this->option('top'));
        $sampleLimit = max(1, (int) $this->option('sample-limit'));

        $baseSpellRowsQuery = DB::table('effect_sub_effect as es')
            ->join('sub_effects as se', 'se.id', '=', 'es.sub_effect_id')
            ->whereExists(function ($q): void {
                $q->select(DB::raw(1))
                    ->from('effect_usages as eu')
                    ->whereColumn('eu.effect_id', 'es.effect_id')
                    ->where('eu.entity_type', 'spell');
            });

        $totalSpellRows = (clone $baseSpellRowsQuery)->count();
        $autreRows = (clone $baseSpellRowsQuery)->where('se.slug', 'autre')->count();

        $autreQuery = (clone $baseSpellRowsQuery)
            ->where('se.slug', 'autre')
            ->orderBy('es.id')
            ->select(['es.id', 'es.effect_id', 'es.params']);

        $reasonCounts = [
            'damage_like' => 0,
            'removal_like' => 0,
            'movement_like' => 0,
            'support_like' => 0,
            'unknown' => 0,
        ];
        $convertibleRows = 0;
        $withDofusEffectId = 0;
        $dofusEffectIdCounts = [];
        $normalizedTextCounts = [];
        $reasonSamples = [
            'damage_like' => [],
            'removal_like' => [],
            'movement_like' => [],
            'support_like' => [],
            'unknown' => [],
        ];

        $autreQuery->chunkById(500, function ($rows) use (
            &$reasonCounts,
            &$convertibleRows,
            &$withDofusEffectId,
            &$dofusEffectIdCounts,
            &$normalizedTextCounts,
            &$reasonSamples,
            $sampleLimit
        ): void {
            foreach ($rows as $row) {
                $params = $this->decodeParams($row->params);
                $rawText = isset($params['value']) && is_string($params['value']) ? $params['value'] : '';
                $normalizedText = $this->normalizeText($rawText);
                $reason = $this->classifyAutreText($normalizedText);

                $reasonCounts[$reason]++;
                if ($reason !== 'unknown') {
                    $convertibleRows++;
                }

                $normalizedKey = $normalizedText !== '' ? $normalizedText : '[vide]';
                $normalizedTextCounts[$normalizedKey] = ($normalizedTextCounts[$normalizedKey] ?? 0) + 1;

                $dofusEffectId = $params['dofus_effect_id'] ?? null;
                if (is_numeric($dofusEffectId)) {
                    $withDofusEffectId++;
                    $idKey = (int) $dofusEffectId;
                    $dofusEffectIdCounts[$idKey] = ($dofusEffectIdCounts[$idKey] ?? 0) + 1;
                }

                if (count($reasonSamples[$reason]) < $sampleLimit) {
                    $reasonSamples[$reason][] = [
                        'effect_sub_effect_id' => (int) $row->id,
                        'effect_id' => (int) $row->effect_id,
                        'dofus_effect_id' => is_numeric($dofusEffectId) ? (int) $dofusEffectId : null,
                        'value' => $rawText,
                    ];
                }
            }
        }, 'es.id', 'id');

        arsort($dofusEffectIdCounts);
        arsort($normalizedTextCounts);
        arsort($reasonCounts);

        $autreRate = $totalSpellRows > 0 ? round(($autreRows / $totalSpellRows) * 100, 2) : 0.0;
        $convertibleRate = $autreRows > 0 ? round(($convertibleRows / $autreRows) * 100, 2) : 0.0;
        $dofusCoverageRate = $autreRows > 0 ? round(($withDofusEffectId / $autreRows) * 100, 2) : 0.0;

        $payload = [
            'summary' => [
                'total_spell_sub_effect_rows' => $totalSpellRows,
                'autre_rows' => $autreRows,
                'autre_rate_percent' => $autreRate,
                'autre_convertible_rows' => $convertibleRows,
                'autre_convertible_rate_percent' => $convertibleRate,
                'autre_with_dofus_effect_id' => $withDofusEffectId,
                'autre_with_dofus_effect_id_rate_percent' => $dofusCoverageRate,
            ],
            'by_reason' => $reasonCounts,
            'top_dofus_effect_ids' => $this->formatTopNumericMap($dofusEffectIdCounts, $top),
            'top_normalized_texts' => $this->formatTopStringMap($normalizedTextCounts, $top),
            'samples_by_reason' => $reasonSamples,
            'warnings' => $this->buildWarnings($autreRate, $convertibleRate, $withDofusEffectId, $autreRows),
        ];

        if ($asJson) {
            $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        }

        $this->info('Audit des sous-effets "autre"');
        $this->table(
            ['Indicateur', 'Valeur'],
            [
                ['Sous-effets sorts (total)', (string) $totalSpellRows],
                ['Sous-effets "autre"', (string) $autreRows],
                ['Taux "autre"', $autreRate . '%'],
                ['"autre" convertibles (heuristique)', (string) $convertibleRows . ' (' . $convertibleRate . '%)'],
                ['"autre" avec dofus_effect_id', (string) $withDofusEffectId . ' (' . $dofusCoverageRate . '%)'],
            ]
        );

        $this->newLine();
        $this->line('Répartition des "autre" par catégorie heuristique');
        $reasonRows = [];
        foreach ($reasonCounts as $reason => $count) {
            $reasonRows[] = [$reason, (string) $count];
        }
        $this->table(['Catégorie', 'Count'], $reasonRows);

        $this->newLine();
        $this->line('Top dofus_effect_id présents dans "autre"');
        $this->table(
            ['dofus_effect_id', 'count'],
            array_map(
                static fn (array $row): array => [(string) $row['key'], (string) $row['count']],
                $this->formatTopNumericMap($dofusEffectIdCounts, $top)
            )
        );

        $this->newLine();
        $this->line('Top textes normalisés dans "autre"');
        $this->table(
            ['texte_normalise', 'count'],
            array_map(
                static fn (array $row): array => [(string) $row['key'], (string) $row['count']],
                $this->formatTopStringMap($normalizedTextCounts, $top)
            )
        );

        if ($payload['warnings'] !== []) {
            $this->newLine();
            foreach ($payload['warnings'] as $warning) {
                $this->warn($warning);
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeParams(mixed $paramsRaw): array
    {
        if (is_array($paramsRaw)) {
            return $paramsRaw;
        }
        if (is_string($paramsRaw) && $paramsRaw !== '') {
            $decoded = json_decode($paramsRaw, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    private function normalizeText(string $text): string
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

    /**
     * @return 'damage_like'|'removal_like'|'movement_like'|'support_like'|'unknown'
     */
    private function classifyAutreText(string $normalized): string
    {
        if ($normalized === '') {
            return 'unknown';
        }

        if (preg_match('/\b(dommage|dommages|degat|degats|vol de vie|frappe)\b/u', $normalized) === 1) {
            return 'damage_like';
        }

        if (str_contains($normalized, 'kamas') === false
            && preg_match('/\b(retrait|retire|pa|pm|fuite|tacle|portee|sagesse|intelligence|agilite|chance|force|vitalite)\b/u', $normalized) === 1
            && (preg_match('/-\s*#|\b(vole|vol de|retrait|retire)\b/u', $normalized) === 1)
        ) {
            return 'removal_like';
        }

        if (preg_match('/\b(repousse|attire|teleporte|pousse|avance|recule|deplace|echange de position)\b/u', $normalized) === 1) {
            return 'movement_like';
        }

        if (preg_match('/\b(invoque|soin|protege|bouclier|boost|augmente|rend)\b/u', $normalized) === 1) {
            return 'support_like';
        }

        return 'unknown';
    }

    /**
     * @param array<int,int> $map
     * @return list<array{key:int,count:int}>
     */
    private function formatTopNumericMap(array $map, int $top): array
    {
        $rows = [];
        $n = 0;
        foreach ($map as $key => $count) {
            $rows[] = ['key' => (int) $key, 'count' => (int) $count];
            $n++;
            if ($n >= $top) {
                break;
            }
        }

        return $rows;
    }

    /**
     * @param array<string,int> $map
     * @return list<array{key:string,count:int}>
     */
    private function formatTopStringMap(array $map, int $top): array
    {
        $rows = [];
        $n = 0;
        foreach ($map as $key => $count) {
            $rows[] = ['key' => $key, 'count' => (int) $count];
            $n++;
            if ($n >= $top) {
                break;
            }
        }

        return $rows;
    }

    /**
     * @return list<string>
     */
    private function buildWarnings(float $autreRate, float $convertibleRate, int $withDofusEffectId, int $autreRows): array
    {
        $warnings = [];

        if ($autreRows === 0) {
            $warnings[] = 'Aucun sous-effet "autre" detecte: excellent signal, mais verifier sur un lot plus large.';
            return $warnings;
        }

        if ($autreRate > 25.0) {
            $warnings[] = 'Taux "autre" eleve (>25%): prioriser le mapping des effectId les plus frequents.';
        }
        if ($convertibleRate > 35.0) {
            $warnings[] = 'Beaucoup de "autre" semblent convertibles: forte opportunite de reduction rapide du bruit.';
        }
        if ($withDofusEffectId === 0) {
            $warnings[] = 'Aucun dofus_effect_id dans les params "autre": relancer des imports recents pour beneficier de l’audit par effectId.';
        }

        return $warnings;
    }
}

