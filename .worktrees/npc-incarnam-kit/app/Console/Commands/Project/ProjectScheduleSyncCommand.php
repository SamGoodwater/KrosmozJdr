<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Models\ProjectScheduleTask;
use App\Support\ProjectSchedule\ProjectScheduleCatalog;
use Illuminate\Console\Command;

/**
 * Insère les clés de tâches manquantes dans `project_schedule_tasks` (sans écraser la config existante).
 *
 * @example php artisan project:schedule:sync
 */
class ProjectScheduleSyncCommand extends Command
{
    protected $signature = 'project:schedule:sync';

    protected $description = 'Ajoute en base les entrées du catalogue de scheduling si absentes (post-déploiement)';

    public function handle(): int
    {
        $handlers = ProjectScheduleCatalog::handlers();
        $defaultsByKey = collect(ProjectScheduleCatalog::initialSeedRows())->keyBy('task_key');

        $created = 0;
        foreach (array_keys($handlers) as $key) {
            if (ProjectScheduleTask::query()->where('task_key', $key)->exists()) {
                continue;
            }

            $seed = $defaultsByKey->get($key);
            if ($seed === null) {
                $this->warn("  Définition de graine absente pour « {$key} », ignorée.");

                continue;
            }

            ProjectScheduleTask::query()->create([
                'task_key' => $key,
                'enabled' => (bool) $seed['enabled'],
                'cron_expression' => (string) $seed['cron_expression'],
                'without_overlapping' => (bool) ($seed['without_overlapping'] ?? true),
            ]);

            $this->line("  + Tâche ajoutée : {$key}");
            $created++;
        }

        $this->info('Synchronisation terminée : '.$created.' nouvelle(s) entrée(s).');

        return ArtisanExitCode::SUCCESS;
    }
}
