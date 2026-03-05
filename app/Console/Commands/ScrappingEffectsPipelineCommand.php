<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Pipeline prêt à l'emploi: import batch de sorts puis quality gate des effets.
 */
final class ScrappingEffectsPipelineCommand extends Command
{
    protected $signature = 'scrapping:effects:pipeline
                            {--simulate : Import sans écriture BDD}
                            {--allow-empty : Autorise expected_rows=0 à la gate}
                            {--ids= : Liste d\'IDs de sorts (virgules)}
                            {--levelMin= : Filtre niveau minimum}
                            {--levelMax= : Filtre niveau maximum}
                            {--limit=100 : Taille de page API}
                            {--max-pages=0 : Nombre max de pages (0=illimité)}
                            {--max-items=300 : Nombre max d\'items collectés}
                            {--skip-cache : Ignore le cache HTTP}
                            {--include-relations=1 : Inclure les relations à l\'import (1/0)}
                            {--min-coverage=99 : Couverture minimale attendue pour la gate}
                            {--max-missing-mappings=0 : Max mappings source=characteristic sans key}
                            {--max-missing-value-converted=0 : Max sous-effets attendus sans value_converted}
                            {--sample-limit=20 : Echantillons max pour l\'audit}
                            {--json : Sortie JSON consolidée}';

    protected $description = 'Import des sorts puis quality gate des effets de sorts';

    public function handle(): int
    {
        $asJson = (bool) $this->option('json');

        $importArgs = [
            '--entity' => 'spell',
            '--output' => 'summary',
            '--limit' => max(1, (int) $this->option('limit')),
            '--max-pages' => max(0, (int) $this->option('max-pages')),
            '--max-items' => max(0, (int) $this->option('max-items')),
            '--include-relations' => (int) $this->option('include-relations') === 0 ? 0 : 1,
        ];
        if ((bool) $this->option('simulate')) {
            $importArgs['--simulate'] = true;
        }
        if ((bool) $this->option('skip-cache')) {
            $importArgs['--skip-cache'] = true;
        }
        if (is_string($this->option('ids')) && trim((string) $this->option('ids')) !== '') {
            $importArgs['--ids'] = (string) $this->option('ids');
        }
        if (is_string($this->option('levelMin')) && trim((string) $this->option('levelMin')) !== '') {
            $importArgs['--levelMin'] = (string) $this->option('levelMin');
        }
        if (is_string($this->option('levelMax')) && trim((string) $this->option('levelMax')) !== '') {
            $importArgs['--levelMax'] = (string) $this->option('levelMax');
        }

        $gateArgs = [
            '--min-coverage' => max(0.0, min(100.0, (float) $this->option('min-coverage'))),
            '--max-missing-mappings' => max(0, (int) $this->option('max-missing-mappings')),
            '--max-missing-value-converted' => max(0, (int) $this->option('max-missing-value-converted')),
            '--sample-limit' => max(1, (int) $this->option('sample-limit')),
        ];
        if ((bool) $this->option('allow-empty')) {
            $gateArgs['--allow-empty'] = true;
        }

        if ($asJson) {
            $importCode = Artisan::call('scrapping:run', $importArgs + ['--json' => true]);
            $importPayload = $this->decodeJsonPayload((string) Artisan::output());

            $gateCode = Artisan::call('scrapping:effects:quality-gate', $gateArgs + ['--json' => true]);
            $gatePayload = $this->decodeJsonPayload((string) Artisan::output());

            $ok = $importCode === 0 && $gateCode === 0;
            $this->line((string) json_encode([
                'ok' => $ok,
                'import' => [
                    'exit_code' => $importCode,
                    'args' => $importArgs,
                    'result' => $importPayload,
                ],
                'gate' => [
                    'exit_code' => $gateCode,
                    'args' => $gateArgs,
                    'result' => $gatePayload,
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $ok ? self::SUCCESS : self::FAILURE;
        }

        $this->info('Pipeline effets de sorts: import puis quality gate');
        $this->newLine();
        $this->info('1/2 Import des sorts');
        $importCode = $this->call('scrapping:run', $importArgs);
        if ($importCode !== 0) {
            $this->newLine();
            $this->error("Import KO (code {$importCode})");
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('2/2 Quality gate');
        $gateCode = $this->call('scrapping:effects:quality-gate', $gateArgs);
        if ($gateCode !== 0) {
            $this->newLine();
            $this->error("Gate KO (code {$gateCode})");
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Pipeline OK');
        return self::SUCCESS;
    }

    /**
     * @return array<string,mixed>|null
     */
    private function decodeJsonPayload(string $raw): ?array
    {
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }
}

