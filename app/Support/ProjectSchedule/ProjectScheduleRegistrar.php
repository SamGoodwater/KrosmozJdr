<?php

declare(strict_types=1);

namespace App\Support\ProjectSchedule;

use App\Jobs\SendNotificationDigestsJob;
use App\Models\ProjectScheduleTask;
use Cron\CronExpression;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Applique le planning Laravel depuis la base + catalogue de handlers sécurisé.
 *
 * Si la table n’existe pas encore (migrate non exécuté), conserve le comportement historique (.env).
 *
 * @example
 * ProjectScheduleRegistrar::register($schedule);
 */
final class ProjectScheduleRegistrar
{
    public static function register(Schedule $schedule): void
    {
        try {
            if (! Schema::hasTable('project_schedule_tasks')) {
                Log::notice('ProjectScheduleRegistrar : table `project_schedule_tasks` absente — mode secours (`.env`). Exécutez `php artisan migrate`.');

                self::registerLegacyEnvSchedule($schedule);

                return;
            }

            $handlers = ProjectScheduleCatalog::handlers();
            $tasks = ProjectScheduleTask::query()
                ->whereIn('task_key', array_keys($handlers), 'and', false)
                ->get()
                ->keyBy('task_key');
        } catch (Throwable $e) {
            Log::warning('ProjectScheduleRegistrar : planning BDD indisponible — mode secours (`.env`).', [
                'exception' => $e->getMessage(),
            ]);

            self::registerLegacyEnvSchedule($schedule);

            return;
        }

        foreach ($handlers as $key => $definition) {
            $row = $tasks->get($key);
            if ($row === null || ! $row->enabled) {
                continue;
            }

            $cronExpr = trim($row->cron_expression);
            if ($cronExpr === '') {
                Log::warning('ProjectScheduleRegistrar : cron vide ignoré.', ['task_key' => $key]);

                continue;
            }

            if (! CronExpression::isValidExpression($cronExpr)) {
                Log::warning('ProjectScheduleRegistrar : expression cron invalide ignorée.', [
                    'task_key' => $key,
                    'cron_expression' => $cronExpr,
                ]);

                continue;
            }

            self::applyDefinition($schedule, $definition, $cronExpr, (bool) $row->without_overlapping);
        }
    }

    /**
     * Comportement historique avant table BDD — tant que migrate n’a pas créé les lignes.
     */
    private static function registerLegacyEnvSchedule(Schedule $schedule): void
    {
        $schedule->command('media:clean-thumbnails')->daily();
        if ((bool) env('PROJECT_CLEAR_AUTO_ENABLED', false)) {
            $schedule->command('project:clear --safe')
                ->cron((string) env('PROJECT_CLEAR_CRON', '30 0 * * *'));
        }
        $schedule->command('privacy:process-deletion-requests')->dailyAt('02:00');

        $schedule->job(new SendNotificationDigestsJob('daily'))->dailyAt('00:05');
        $schedule->job(new SendNotificationDigestsJob('weekly'))->weeklyOn(1, '00:10');
        $schedule->job(new SendNotificationDigestsJob('monthly'))->monthlyOn(1, '00:15');

        if ((bool) env('PROJECT_UPDATE_AUTO_ENABLED', false)) {
            $cron = (string) env('PROJECT_UPDATE_CRON', '0 1 1 * *');
            $schedule->command('project:data sync')->cron($cron);
        }

        if ((bool) env('SCRAPPING_RESOURCES_AUTO_SYNC', false)) {
            $at = (string) env('SCRAPPING_RESOURCES_AUTO_SYNC_AT', '03:00');
            $limit = (int) env('SCRAPPING_RESOURCES_AUTO_SYNC_LIMIT', 100);
            $schedule
                ->command(sprintf('scrapping:run --entity=resource --resource-types=allowed --limit=%d --max-pages=0 --max-items=20000', max(1, $limit)))
                ->dailyAt($at);
        }

        if ((bool) env('PROJECT_BACKUP_ENABLED', false)) {
            $cron = (string) env('PROJECT_BACKUP_CRON', '0 4 * * *');
            $schedule->command('project:backup')->cron($cron);
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private static function applyDefinition(Schedule $schedule, array $definition, string $cronExpr, bool $withoutOverlapping): void
    {
        $overlap = $definition['overlap_minutes'] ?? 1440;

        $event = match ($definition['type']) {
            'artisan' => $schedule->command(
                is_callable($definition['command'] ?? null)
                    ? ($definition['command'])()
                    : (string) ($definition['command'] ?? '')
            ),
            'job' => $schedule->job(self::instantiateJob($definition)),
            default => throw new \InvalidArgumentException('Type de tâche inconnu pour handler.'),
        };

        $event->cron(trim($cronExpr));
        if ($withoutOverlapping) {
            $event->withoutOverlapping($overlap);
        }
    }

    /** @param  array<string, mixed>  $definition */
    private static function instantiateJob(array $definition): object
    {
        $class = $definition['job'] ?? null;
        if (! is_string($class) || ! class_exists($class)) {
            throw new \InvalidArgumentException('Classe Job manquante pour la définition.');
        }

        /** @phpstan-ignore-next-line */
        return new $class(...(($definition['arguments'] ?? []) ?: []));
    }
}
