<?php

namespace App\Jobs;

use App\Models\ScrappingJob;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Conversion\UnknownCharacteristicRunTracker;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessScrappingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Délai maximum d'exécution (secondes). Un monstre avec relations (sorts, drops)
     * peut nécessiter de nombreux appels API et écritures BDD (pile d'import).
     */
    public $timeout = 600;

    /** Tentatives avant abandon (transient: DB "gone away", API timeout). */
    public $tries = 2;

    /** Délai avant retry (secondes). */
    public $backoff = 30;

    public function __construct(private string $scrappingJobId) {}

    public function handle(Orchestrator $orchestrator, CollectAliasResolver $aliasResolver): void
    {
        DB::reconnect();

        $job = ScrappingJob::query()->find($this->scrappingJobId);
        if (! $job || $job->isTerminal()) {
            return;
        }

        $payload = is_array($job->payload) ? $job->payload : [];
        $entities = is_array($payload['entities'] ?? null) ? $payload['entities'] : [];
        $options = is_array($payload['options'] ?? null) ? $payload['options'] : [];
        $runId = (string) ($job->run_id ?: ($options['run_id'] ?? ''));
        if ($runId === '') {
            $runId = (string) \Illuminate\Support\Str::uuid();
            $options['run_id'] = $runId;
        }
        UnknownCharacteristicRunTracker::reset($runId);

        $job->status = ScrappingJob::STATUS_RUNNING;
        $job->run_id = $runId;
        $job->progress_total = count($entities);
        $job->progress_done = 0;
        $job->started_at = now();
        $job->error = null;
        $job->save();

        $results = [];
        $successCount = 0;
        $errorCount = 0;
        $doneCount = 0;

        foreach ($entities as $entity) {
            $job->refresh();
            if ($job->status === ScrappingJob::STATUS_CANCELLED) {
                $job->summary = $this->buildSummary($job->progress_total, $successCount, $errorCount, true);
                $job->results = $results;
                $job->finished_at = now();
                $job->save();
                return;
            }

            $type = (string) ($entity['type'] ?? '');
            $id = (int) ($entity['id'] ?? 0);
            if ($type === '' || $id <= 0) {
                $results[] = [
                    'type' => $type,
                    'id' => $id,
                    'success' => false,
                    'error' => 'Entite ou ID invalide',
                    'validation_errors' => [],
                    'relations' => [],
                ];
                $errorCount++;
                $doneCount++;
                $this->saveProgress($job, $doneCount, $results, $successCount, $errorCount);
                continue;
            }

            $resolved = $this->resolveEntityForImport($aliasResolver, $type);
            $skipInfo = $orchestrator->resolveSkipForEntity($resolved['entity'], $id, $options);
            if ($skipInfo !== null) {
                $results[] = [
                    'type' => $type,
                    'id' => $id,
                    'success' => true,
                    'data' => ['skipped' => true, 'primary_id' => $skipInfo['primary_id'], 'table' => $skipInfo['table']],
                    'error' => null,
                    'validation_errors' => [],
                    'relations' => [],
                ];
                $successCount++;
            } else {
                try {
                    $result = $orchestrator->runOne($resolved['source'], $resolved['entity'], $id, $options);
                $success = $result->isSuccess();
                $results[] = [
                    'type' => $type,
                    'id' => $id,
                    'success' => $success,
                    'data' => $success ? ($result->getIntegrationResult()?->getData() ?? $result->getConverted()) : null,
                    'error' => $success ? null : $result->getMessage(),
                    'validation_errors' => $success ? [] : $result->getValidationErrors(),
                    'relations' => $success ? ($result->getRelations() ?? []) : [],
                ];
                if ($success) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                } catch (\Throwable $e) {
                $results[] = [
                    'type' => $type,
                    'id' => $id,
                    'success' => false,
                    'error' => $e->getMessage(),
                    'validation_errors' => [],
                    'relations' => [],
                ];
                $errorCount++;
            }
            }

            $doneCount++;
            if ($doneCount === 1 || $doneCount % 5 === 0 || $doneCount === count($entities)) {
                $this->saveProgress($job, $doneCount, $results, $successCount, $errorCount);
            }
        }

        $job->refresh();
        if ($job->status === ScrappingJob::STATUS_CANCELLED) {
            $job->summary = $this->buildSummary($job->progress_total, $successCount, $errorCount, true);
            $job->results = $results;
            $job->finished_at = now();
            $job->save();
            return;
        }

        $job->status = $errorCount > 0 ? ScrappingJob::STATUS_FAILED : ScrappingJob::STATUS_SUCCEEDED;
        $job->summary = $this->buildSummary($job->progress_total, $successCount, $errorCount, false);
        $job->results = $results;
        $job->progress_done = $doneCount;
        $job->error = null;
        $job->finished_at = now();
        $job->save();
    }

    public function failed(\Throwable $exception): void
    {
        $job = ScrappingJob::query()->find($this->scrappingJobId);
        if (! $job) {
            return;
        }
        $job->status = ScrappingJob::STATUS_FAILED;
        $job->error = $exception->getMessage();
        $job->finished_at = now();
        $job->save();

        Log::channel('scrapping')->error('scrapping.job.failed', [
            'job_id' => $job->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    private function saveProgress(ScrappingJob $job, int $doneCount, array $results, int $successCount, int $errorCount): void
    {
        $job->progress_done = $doneCount;
        $job->summary = $this->buildSummary($job->progress_total, $successCount, $errorCount, false);
        $job->results = $results;
        $job->save();
    }

    private function buildSummary(int $total, int $success, int $errors, bool $cancelled): array
    {
        return [
            'total' => $total,
            'success' => $success,
            'errors' => $errors,
            'cancelled' => $cancelled,
        ];
    }

    /**
     * @return array{source: string, entity: string}
     */
    private function resolveEntityForImport(CollectAliasResolver $aliasResolver, string $type): array
    {
        $cfg = $aliasResolver->resolve($type);
        if ($cfg !== null) {
            return [
                'source' => (string) ($cfg['source'] ?? 'dofusdb'),
                'entity' => (string) ($cfg['entity'] ?? $type),
            ];
        }

        return ['source' => 'dofusdb', 'entity' => $type === 'class' ? 'breed' : $type];
    }
}
