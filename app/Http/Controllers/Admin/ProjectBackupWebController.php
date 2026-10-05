<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeleteProjectBackupWebRequest;
use App\Http\Requests\Admin\RestoreProjectBackupWebRequest;
use App\Http\Requests\Admin\StoreProjectBackupWebRequest;
use App\Jobs\RunProjectBackupJob;
use App\Models\ProjectScheduleTask;
use App\Services\Project\ProjectBackupRecovery;
use App\Services\Project\ProjectBackupRestoreLauncher;
use App\Services\Project\ProjectBackupService;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use App\Support\ProjectSchedule\ProjectScheduleCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

/**
 * Interface admin : inventaire, lancement, suppression et restauration des sauvegardes.
 */
class ProjectBackupWebController extends Controller
{
    use SharesProjectConsoleJob;

    public function index(): InertiaResponse
    {
        $service = ProjectBackupService::fromConfig();
        $recovery = ProjectBackupRecovery::fromConfig()->reconcile();

        return Inertia::render('Admin/backup/Index', array_merge(
            $this->consoleJobProps(ProjectConsoleDomain::BACKUP),
            [
                'seederExportAvailable' => ! app()->environment('production'),
                'backupDirectory' => $service->resolvedBackupDirectory(),
                'retentionDays' => $service->retentionDays(),
                'backups' => $this->presentBackups($service->listBackups()),
                'operationLocked' => (bool) $recovery['operation_locked'],
                'restoreStatus' => $recovery['restore_status'],
                'schedule' => $this->scheduleMeta(),
            ]
        ));
    }

    public function store(StoreProjectBackupWebRequest $request, ProjectConsoleJobTracker $tracker): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $recovery = ProjectBackupRecovery::fromConfig()->reconcile();
        if ($recovery['operation_locked']) {
            return redirect()
                ->route('admin.backup.index')
                ->with('error', 'Une sauvegarde ou une restauration est déjà en cours.');
        }

        $options = $request->artisanOptions();
        $commandLine = ProjectConsoleJobTracker::commandLine('project:backup', $options);
        $record = $tracker->tryQueue(ProjectConsoleDomain::BACKUP, $commandLine, $user->id);
        if ($record === null) {
            return redirect()
                ->route('admin.backup.index')
                ->with('error', ProjectConsoleDomain::busyMessage(ProjectConsoleDomain::BACKUP));
        }

        Log::info('admin.project_backup.dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'option_keys' => array_keys($options),
            'console_job_id' => $record->id,
        ]);

        RunProjectBackupJob::dispatch($user->id, $options, $record->id);

        return redirect()
            ->route('admin.backup.index')
            ->with('success', 'Sauvegarde lancée.');
    }

    public function destroy(DeleteProjectBackupWebRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $service = ProjectBackupService::fromConfig();
        $name = (string) $request->validated('name');

        try {
            $service->acquireLock();
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.backup.index')
                ->with('error', $e->getMessage());
        }

        try {
            $errors = [];
            $result = $service->deleteBackup(
                $name,
                static fn () => null,
                static function (string $m) use (&$errors): void {
                    $errors[] = $m;
                },
            );
        } finally {
            $service->releaseLock();
        }

        if ($result === null) {
            Log::warning('admin.project_backup.delete_failed', [
                'user_id' => $user->id,
                'name' => $name,
                'errors' => $errors,
            ]);

            return redirect()
                ->route('admin.backup.index')
                ->with('error', $errors[0] ?? 'Suppression impossible.');
        }

        Log::info('admin.project_backup.deleted', [
            'user_id' => $user->id,
            'name' => $name,
            'deleted' => $result['deleted'],
        ]);

        return redirect()
            ->route('admin.backup.index')
            ->with('success', 'Sauvegarde supprimée.');
    }

    public function restore(
        RestoreProjectBackupWebRequest $request,
        ProjectBackupRestoreLauncher $launcher,
    ): RedirectResponse {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $name = (string) $request->validated('name');
        $result = $launcher->start(
            $name,
            $user->id,
            $request->boolean('no_safety_backup'),
        );

        Log::info('admin.project_backup.restore_dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'name' => $name,
            'ok' => $result['ok'],
        ]);

        return redirect()
            ->route('admin.backup.index')
            ->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function restoreStatus(): JsonResponse
    {
        $recovery = ProjectBackupRecovery::fromConfig()->reconcile();

        return response()->json([
            'restoreStatus' => $recovery['restore_status'],
            'operationLocked' => (bool) $recovery['operation_locked'],
            'maintenance' => (bool) $recovery['maintenance'],
            'reconciled' => (bool) $recovery['reconciled'],
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return list<array<string, mixed>>
     */
    private function presentBackups(array $items): array
    {
        return array_map(static function (array $item): array {
            return [
                'name' => $item['name'],
                'format' => $item['format'],
                'run_id' => $item['run_id'],
                'components' => $item['components'],
                'integrity' => $item['integrity'],
                'size' => $item['size'],
                'mtime' => $item['mtime'],
                'created_at' => $item['created_at'],
                'restorable' => $item['format'] === 'zip' && $item['integrity'] === 'ok',
            ];
        }, $items);
    }

    /**
     * @return array{enabled: bool, cron_expression: string|null, without_overlapping: bool, label: string}|null
     */
    private function scheduleMeta(): ?array
    {
        $definition = ProjectScheduleCatalog::handlers()['project_backup'] ?? null;
        if ($definition === null) {
            return null;
        }

        try {
            if (! Schema::hasTable('project_schedule_tasks')) {
                return [
                    'enabled' => (bool) env('PROJECT_BACKUP_ENABLED', false),
                    'cron_expression' => (string) env('PROJECT_BACKUP_CRON', '0 4 * * *'),
                    'without_overlapping' => true,
                    'label' => (string) ($definition['label'] ?? 'Sauvegarde projet'),
                    'source' => 'env',
                ];
            }

            $row = ProjectScheduleTask::query()->where('task_key', 'project_backup')->first();
            if ($row === null) {
                return [
                    'enabled' => false,
                    'cron_expression' => null,
                    'without_overlapping' => true,
                    'label' => (string) ($definition['label'] ?? 'Sauvegarde projet'),
                    'source' => 'missing',
                ];
            }

            return [
                'enabled' => (bool) $row->enabled,
                'cron_expression' => $row->cron_expression,
                'without_overlapping' => (bool) $row->without_overlapping,
                'label' => (string) ($definition['label'] ?? 'Sauvegarde projet'),
                'source' => 'database',
            ];
        } catch (Throwable) {
            return null;
        }
    }
}
