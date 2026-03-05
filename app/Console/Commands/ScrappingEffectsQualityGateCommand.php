<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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

    public function handle(): int
    {
        $minCoverage = max(0.0, min(100.0, (float) $this->option('min-coverage')));
        $maxMissingMappings = max(0, (int) $this->option('max-missing-mappings'));
        $maxMissingValueConverted = max(0, (int) $this->option('max-missing-value-converted'));
        $allowEmpty = (bool) $this->option('allow-empty');
        $sampleLimit = max(1, (int) $this->option('sample-limit'));
        $asJson = (bool) $this->option('json');

        $auditExitCode = Artisan::call('scrapping:effects:audit-quality', [
            '--json' => true,
            '--sample-limit' => $sampleLimit,
        ]);

        if ($auditExitCode !== 0) {
            $message = 'Impossible d\'executer scrapping:effects:audit-quality.';
            if ($asJson) {
                $this->line((string) json_encode([
                    'ok' => false,
                    'error' => $message,
                    'audit_exit_code' => $auditExitCode,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $this->error($message);
            }
            return self::FAILURE;
        }

        $auditPayload = json_decode(Artisan::output(), true);
        if (!is_array($auditPayload)) {
            $message = 'Sortie JSON invalide de scrapping:effects:audit-quality.';
            if ($asJson) {
                $this->line((string) json_encode([
                    'ok' => false,
                    'error' => $message,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $this->error($message);
            }
            return self::FAILURE;
        }

        $mappingMissing = (int) data_get($auditPayload, 'summary.mapping_missing_characteristic_key', 0);
        $expectedRows = (int) data_get($auditPayload, 'summary.conversion_expected_rows', 0);
        $missingValueConverted = (int) data_get($auditPayload, 'summary.conversion_missing_value_converted', 0);
        $coverage = (float) data_get($auditPayload, 'summary.conversion_coverage_percent', 0.0);

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
}

