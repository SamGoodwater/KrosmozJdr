<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DofusdbEffectMapping;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectConversionFormulaResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Audit qualité des effets de sorts: couverture mapping + qualité value_converted.
 *
 * Objectif:
 * - Identifier les mappings incomplets (source=characteristic sans characteristic_key).
 * - Mesurer les sous-effets de sorts qui devraient avoir value_converted mais ne l'ont pas.
 *
 * Commande orientée robustesse: sortie compacte, JSON optionnel, scan en chunks.
 */
final class ScrappingEffectsQualityAuditCommand extends Command
{
    protected $signature = 'scrapping:effects:audit-quality
                            {--json : Sortie JSON}
                            {--sample-limit=20 : Nombre max d\'exemples par catégorie}';

    protected $description = 'Audit qualité du pipeline effets de sorts (mappings + conversion value_converted)';

    public function handle(SpellEffectConversionFormulaResolver $formulaResolver): int
    {
        $sampleLimit = max(1, (int) $this->option('sample-limit'));
        $asJson = (bool) $this->option('json');

        $mappingAudit = $this->buildMappingAudit($sampleLimit);
        $conversionAudit = $this->buildConversionAudit($formulaResolver, $sampleLimit);

        $payload = [
            'summary' => [
                'mapping_missing_characteristic_key' => $mappingAudit['missing_characteristic_key_count'],
                'conversion_expected_rows' => $conversionAudit['expected_rows'],
                'conversion_missing_value_converted' => $conversionAudit['missing_value_converted_rows'],
                'conversion_coverage_percent' => $conversionAudit['coverage_percent'],
            ],
            'warnings' => $this->buildWarnings($mappingAudit, $conversionAudit),
            'mapping' => $mappingAudit,
            'conversion' => $conversionAudit,
        ];

        if ($asJson) {
            $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        }

        $this->info('Audit qualité effets de sorts');
        $this->table(
            ['Indicateur', 'Valeur'],
            [
                ['Mappings source=characteristic sans key', (string) $mappingAudit['missing_characteristic_key_count']],
                ['Sous-effets attendus (value_converted)', (string) $conversionAudit['expected_rows']],
                ['Sous-effets manquants value_converted', (string) $conversionAudit['missing_value_converted_rows']],
                ['Couverture conversion', $conversionAudit['coverage_percent'] . '%'],
            ]
        );

        if ($payload['warnings'] !== []) {
            $this->newLine();
            foreach ($payload['warnings'] as $warning) {
                $this->warn((string) $warning);
            }
        }

        $this->line('');
        $this->line('Top slugs manquants (value_converted)');
        $this->table(
            ['slug', 'count', 'effect_sub_effect ids (échantillon)'],
            array_map(
                static fn (array $row): array => [
                    (string) $row['slug'],
                    (string) $row['count'],
                    implode(',', $row['sample_ids']),
                ],
                $conversionAudit['missing_by_slug']
            )
        );

        $this->line('');
        $this->line('Exemples mappings incomplets');
        $this->table(
            ['effect_id', 'sub_effect_slug', 'source'],
            array_map(
                static fn (array $row): array => [
                    (string) $row['dofusdb_effect_id'],
                    (string) $row['sub_effect_slug'],
                    (string) $row['characteristic_source'],
                ],
                $mappingAudit['missing_characteristic_key_samples']
            )
        );

        return self::SUCCESS;
    }

    /**
     * @return array{
     *   total_rows:int,
     *   by_source:array<string,int>,
     *   missing_characteristic_key_count:int,
     *   missing_characteristic_key_samples:list<array{dofusdb_effect_id:int,sub_effect_slug:string,characteristic_source:string}>
     * }
     */
    private function buildMappingAudit(int $sampleLimit): array
    {
        $totalRows = DofusdbEffectMapping::query()->count();

        $bySource = DofusdbEffectMapping::query()
            ->select('characteristic_source', DB::raw('COUNT(*) AS c'))
            ->groupBy('characteristic_source')
            ->pluck('c', 'characteristic_source')
            ->map(static fn ($v): int => (int) $v)
            ->all();

        $missingQuery = DofusdbEffectMapping::query()
            ->where('characteristic_source', DofusdbEffectMapping::SOURCE_CHARACTERISTIC)
            ->where(function ($q): void {
                $q->whereNull('characteristic_key')->orWhere('characteristic_key', '');
            });

        $missingCount = (clone $missingQuery)->count();
        $missingSamples = (clone $missingQuery)
            ->orderBy('dofusdb_effect_id')
            ->limit($sampleLimit)
            ->get(['dofusdb_effect_id', 'sub_effect_slug', 'characteristic_source'])
            ->map(static fn (DofusdbEffectMapping $row): array => [
                'dofusdb_effect_id' => (int) $row->dofusdb_effect_id,
                'sub_effect_slug' => (string) $row->sub_effect_slug,
                'characteristic_source' => (string) $row->characteristic_source,
            ])
            ->all();

        return [
            'total_rows' => $totalRows,
            'by_source' => $bySource,
            'missing_characteristic_key_count' => $missingCount,
            'missing_characteristic_key_samples' => $missingSamples,
        ];
    }

    /**
     * @return array{
     *   total_spell_effect_sub_effect_rows:int,
     *   expected_rows:int,
     *   missing_value_converted_rows:int,
     *   coverage_percent:float,
     *   missing_by_slug:list<array{slug:string,count:int,sample_ids:list<int>}>
     * }
     */
    private function buildConversionAudit(SpellEffectConversionFormulaResolver $formulaResolver, int $sampleLimit): array
    {
        $query = DB::table('effect_sub_effect as es')
            ->join('sub_effects as se', 'se.id', '=', 'es.sub_effect_id')
            ->whereExists(function ($q): void {
                $q->select(DB::raw(1))
                    ->from('effect_usages as eu')
                    ->whereColumn('eu.effect_id', 'es.effect_id')
                    ->where('eu.entity_type', 'spell');
            })
            ->orderBy('es.id')
            ->select(['es.id', 'se.slug', 'es.params']);

        $totalRows = 0;
        $expectedRows = 0;
        $missingRows = 0;
        /** @var array<string, array{count:int,sample_ids:list<int>}> $missingBySlug */
        $missingBySlug = [];

        $query->chunkById(500, function ($rows) use (
            $formulaResolver,
            &$totalRows,
            &$expectedRows,
            &$missingRows,
            &$missingBySlug,
            $sampleLimit
        ): void {
            foreach ($rows as $row) {
                $totalRows++;
                $slug = (string) $row->slug;
                $paramsRaw = $row->params;
                $params = $this->decodeParams($paramsRaw);

                $resolvedCharacteristic = $formulaResolver->resolveCharacteristicKeyForConversion($slug, $params);
                $hasValueFormula = isset($params['value_formula']) && is_string($params['value_formula']) && trim($params['value_formula']) !== '';
                if ($resolvedCharacteristic === null || !$hasValueFormula) {
                    continue;
                }

                $expectedRows++;
                $hasConverted = array_key_exists('value_converted', $params)
                    && $params['value_converted'] !== null
                    && $params['value_converted'] !== '';

                if ($hasConverted) {
                    continue;
                }

                $missingRows++;
                if (!isset($missingBySlug[$slug])) {
                    $missingBySlug[$slug] = ['count' => 0, 'sample_ids' => []];
                }
                $missingBySlug[$slug]['count']++;
                if (count($missingBySlug[$slug]['sample_ids']) < $sampleLimit) {
                    $missingBySlug[$slug]['sample_ids'][] = (int) $row->id;
                }
            }
        }, 'es.id');

        uasort($missingBySlug, static fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        $missingBySlugRows = [];
        foreach ($missingBySlug as $slug => $data) {
            $missingBySlugRows[] = [
                'slug' => $slug,
                'count' => $data['count'],
                'sample_ids' => $data['sample_ids'],
            ];
        }

        $coverage = $expectedRows > 0
            ? round((($expectedRows - $missingRows) / $expectedRows) * 100, 2)
            : 100.0;

        return [
            'total_spell_effect_sub_effect_rows' => $totalRows,
            'expected_rows' => $expectedRows,
            'missing_value_converted_rows' => $missingRows,
            'coverage_percent' => $coverage,
            'missing_by_slug' => $missingBySlugRows,
        ];
    }

    /**
     * @return array<string,mixed>
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

    /**
     * @param array{missing_characteristic_key_count:int} $mappingAudit
     * @param array{expected_rows:int,missing_value_converted_rows:int} $conversionAudit
     * @return list<string>
     */
    private function buildWarnings(array $mappingAudit, array $conversionAudit): array
    {
        $warnings = [];

        if ($conversionAudit['expected_rows'] === 0) {
            $warnings[] = 'Aucune ligne de conversion attendue detectee. Verifiez que des effets de sorts ont bien ete importes avant de conclure sur la couverture.';
        }

        if ($mappingAudit['missing_characteristic_key_count'] > 0) {
            $warnings[] = 'Des mappings characteristic sans characteristic_key existent: corriger ces lignes avant un import massif.';
        }

        if ($conversionAudit['missing_value_converted_rows'] > 0) {
            $warnings[] = 'Des sous-effets attendent value_converted mais restent vides: corriger les slugs prioritaires avant production.';
        }

        return $warnings;
    }
}

