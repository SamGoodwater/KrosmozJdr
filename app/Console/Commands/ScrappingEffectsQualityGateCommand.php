<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DofusdbEffectMapping;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectConversionFormulaResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Quality gate CI pour bloquer un import massif d'effets de sorts si les seuils ne sont pas atteints.
 */
final class ScrappingEffectsQualityGateCommand extends Command
{
    protected $signature = 'scrapping:effects:quality-gate
                            {--min-coverage=99 : Couverture minimale de conversion attendue (0-100)}
                            {--max-missing-mappings=0 : Nombre maximal de mappings source=characteristic sans key}
                            {--max-missing-value-converted=0 : Nombre maximal de sous-effets attendus sans value_converted}
                            {--allow-empty : Autorise expected_rows=0 (base vide) sans faire echouer la gate}
                            {--sample-limit=20 : Nombre max d\'exemples collecte par l\'audit sous-jacent}
                            {--json : Sortie JSON}';

    protected $description = 'Gate de qualite des effets de sorts (utile en CI)';

    public function handle(SpellEffectConversionFormulaResolver $formulaResolver): int
    {
        $minCoverage = max(0.0, min(100.0, (float) $this->option('min-coverage')));
        $maxMissingMappings = max(0, (int) $this->option('max-missing-mappings'));
        $maxMissingValueConverted = max(0, (int) $this->option('max-missing-value-converted'));
        $allowEmpty = (bool) $this->option('allow-empty');
        $sampleLimit = max(1, (int) $this->option('sample-limit'));
        $asJson = (bool) $this->option('json');

        $mappingMissing = DofusdbEffectMapping::query()
            ->where('characteristic_source', DofusdbEffectMapping::SOURCE_CHARACTERISTIC)
            ->where(function ($q): void {
                $q->whereNull('characteristic_key')->orWhere('characteristic_key', '');
            })
            ->count();

        $conversionSummary = $this->buildConversionSummary($formulaResolver);
        $expectedRows = $conversionSummary['expected_rows'];
        $missingValueConverted = $conversionSummary['missing_value_converted_rows'];
        $coverage = $conversionSummary['coverage_percent'];

        /** @var list<string> $violations */
        $violations = [];
        if ($mappingMissing > $maxMissingMappings) {
            $violations[] = "mapping_missing_characteristic_key={$mappingMissing} > max={$maxMissingMappings}";
        }
        if ($missingValueConverted > $maxMissingValueConverted) {
            $violations[] = "conversion_missing_value_converted={$missingValueConverted} > max={$maxMissingValueConverted}";
        }
        if ($coverage < $minCoverage) {
            $violations[] = "conversion_coverage_percent={$coverage} < min={$minCoverage}";
        }
        if (!$allowEmpty && $expectedRows === 0) {
            $violations[] = 'conversion_expected_rows=0 et --allow-empty absent';
        }

        $ok = $violations === [];
        $result = [
            'ok' => $ok,
            'thresholds' => [
                'min_coverage' => $minCoverage,
                'max_missing_mappings' => $maxMissingMappings,
                'max_missing_value_converted' => $maxMissingValueConverted,
                'allow_empty' => $allowEmpty,
            ],
            'summary' => [
                'mapping_missing_characteristic_key' => $mappingMissing,
                'conversion_expected_rows' => $expectedRows,
                'conversion_missing_value_converted' => $missingValueConverted,
                'conversion_coverage_percent' => $coverage,
            ],
            'violations' => $violations,
        ];

        if ($asJson) {
            $this->line((string) json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $ok ? self::SUCCESS : self::FAILURE;
        }

        $this->info('Quality gate effets de sorts');
        $this->table(
            ['Indicateur', 'Valeur', 'Seuil'],
            [
                ['Mappings sans key', (string) $mappingMissing, '<= ' . $maxMissingMappings],
                ['Sous-effets attendus', (string) $expectedRows, $allowEmpty ? '>= 0 (allow-empty)' : '> 0'],
                ['Sous-effets manquants value_converted', (string) $missingValueConverted, '<= ' . $maxMissingValueConverted],
                ['Couverture conversion', $coverage . '%', '>= ' . $minCoverage . '%'],
            ]
        );

        if ($violations !== []) {
            $this->newLine();
            $this->error('Gate KO');
            foreach ($violations as $violation) {
                $this->line('- ' . $violation);
            }
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Gate OK');
        return self::SUCCESS;
    }

    /**
     * @return array{expected_rows:int,missing_value_converted_rows:int,coverage_percent:float}
     */
    private function buildConversionSummary(SpellEffectConversionFormulaResolver $formulaResolver): array
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

        $expectedRows = 0;
        $missingRows = 0;

        $query->chunkById(500, function ($rows) use (
            $formulaResolver,
            &$expectedRows,
            &$missingRows
        ): void {
            foreach ($rows as $row) {
                $slug = (string) $row->slug;
                $params = $this->decodeParams($row->params);
                $resolvedCharacteristic = $formulaResolver->resolveCharacteristicKeyForConversion($slug, $params);
                $hasValueFormula = isset($params['value_formula']) && is_string($params['value_formula']) && trim($params['value_formula']) !== '';
                if ($resolvedCharacteristic === null || !$hasValueFormula) {
                    continue;
                }

                $expectedRows++;
                $hasConverted = array_key_exists('value_converted', $params)
                    && $params['value_converted'] !== null
                    && $params['value_converted'] !== '';
                if (!$hasConverted) {
                    $missingRows++;
                }
            }
        }, 'es.id', 'id');

        $coverage = $expectedRows > 0
            ? round((($expectedRows - $missingRows) / $expectedRows) * 100, 2)
            : 100.0;

        return [
            'expected_rows' => $expectedRows,
            'missing_value_converted_rows' => $missingRows,
            'coverage_percent' => $coverage,
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
}

