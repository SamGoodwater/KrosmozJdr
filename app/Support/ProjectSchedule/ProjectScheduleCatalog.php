<?php

declare(strict_types=1);

namespace App\Support\ProjectSchedule;

use App\Jobs\SendNotificationDigestsJob;
use App\Models\ProjectScheduleTask;

/**
 * Définitions des tâches planifiables (handlers) et valeurs par défaut pour la base.
 *
 * Les fréquences effectives et l’activation sont pilotées par {@see ProjectScheduleTask}
 * (éditables par le super-admin en interface).
 *
 * @example
 * foreach (ProjectScheduleCatalog::handlers() as $key => $def) { ... }
 *
 * @phpstan-type Definition array{
 *     label: string,
 *     type: 'artisan'|'job',
 *     command?: string|(callable(): string),
 *     job?: class-string,
 *     arguments?: list<mixed>,
 *     overlap_minutes?: positive-int
 * }
 */
final class ProjectScheduleCatalog
{
    /** @return array<string, Definition> */
    public static function handlers(): array
    {
        return [
            'media_clean_thumbnails' => [
                'label' => 'Nettoyage vignettes (Media Library)',
                'type' => 'artisan',
                'command' => 'media:clean-thumbnails',
                'overlap_minutes' => 30,
            ],
            'project_clear_safe' => [
                'label' => 'Nettoyage projet sûr (caches planifiés)',
                'type' => 'artisan',
                'command' => 'project:clear --safe',
                'overlap_minutes' => 60,
            ],
            'privacy_process_deletion_requests' => [
                'label' => 'Traitement demandes de suppression (RGPD)',
                'type' => 'artisan',
                'command' => 'privacy:process-deletion-requests',
                'overlap_minutes' => 60,
            ],
            'notification_digest_daily' => [
                'label' => 'Notifications digest quotidien',
                'type' => 'job',
                'job' => SendNotificationDigestsJob::class,
                'arguments' => ['daily'],
                'overlap_minutes' => 120,
            ],
            'notification_digest_weekly' => [
                'label' => 'Notifications digest hebdomadaire',
                'type' => 'job',
                'job' => SendNotificationDigestsJob::class,
                'arguments' => ['weekly'],
                'overlap_minutes' => 120,
            ],
            'notification_digest_monthly' => [
                'label' => 'Notifications digest mensuel',
                'type' => 'job',
                'job' => SendNotificationDigestsJob::class,
                'arguments' => ['monthly'],
                'overlap_minutes' => 120,
            ],
            'project_data_sync' => [
                'label' => 'Synchronisation DofusDB (auto_update)',
                'type' => 'artisan',
                'command' => 'project:data sync',
                'overlap_minutes' => 180,
            ],
            'scrap_resources_catalog' => [
                'label' => 'Scrapping catalogue ressources (types autorisés)',
                'type' => 'artisan',
                'command' => static fn (): string => sprintf(
                    'scrapping:run --entity=resource --resource-types=allowed --limit=%d --max-pages=0 --max-items=20000',
                    max(1, (int) env('SCRAPPING_RESOURCES_AUTO_SYNC_LIMIT', 100)),
                ),
                'overlap_minutes' => 240,
            ],
            'project_backup' => [
                'label' => 'Sauvegarde projet (BDD + stockage)',
                'type' => 'artisan',
                'command' => 'project:backup',
                'overlap_minutes' => 180,
            ],
            'media_clear_orphan_files' => [
                'label' => 'Nettoyage fichiers Media orphelins',
                'type' => 'artisan',
                'command' => 'project:clear-orphan-files --queue --delete',
                'overlap_minutes' => 180,
            ],
        ];
    }

    /**
     * Lignes pour migration / sync initiale (active / cron alignés sur l’ancien Kernel + .env).
     *
     * @return list<array{task_key: string, enabled: bool, cron_expression: string, without_overlapping: bool}>
     */
    public static function initialSeedRows(): array
    {
        return [
            [
                'task_key' => 'media_clean_thumbnails',
                'enabled' => true,
                'cron_expression' => '0 0 * * *',
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'project_clear_safe',
                'enabled' => (bool) env('PROJECT_CLEAR_AUTO_ENABLED', false),
                'cron_expression' => (string) env('PROJECT_CLEAR_CRON', '30 0 * * *'),
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'privacy_process_deletion_requests',
                'enabled' => true,
                'cron_expression' => '0 2 * * *',
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'notification_digest_daily',
                'enabled' => true,
                'cron_expression' => '5 0 * * *',
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'notification_digest_weekly',
                'enabled' => true,
                'cron_expression' => '10 0 * * 1',
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'notification_digest_monthly',
                'enabled' => true,
                'cron_expression' => '15 0 1 * *',
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'project_data_sync',
                'enabled' => (bool) env('PROJECT_UPDATE_AUTO_ENABLED', false),
                'cron_expression' => (string) env('PROJECT_UPDATE_CRON', '0 1 1 * *'),
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'scrap_resources_catalog',
                'enabled' => (bool) env('SCRAPPING_RESOURCES_AUTO_SYNC', false),
                'cron_expression' => self::dailyAtToCron((string) env('SCRAPPING_RESOURCES_AUTO_SYNC_AT', '03:00')),
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'project_backup',
                'enabled' => (bool) env('PROJECT_BACKUP_ENABLED', false),
                'cron_expression' => (string) env('PROJECT_BACKUP_CRON', '0 4 * * *'),
                'without_overlapping' => true,
            ],
            [
                'task_key' => 'media_clear_orphan_files',
                'enabled' => (bool) env('MEDIA_CLEAR_ORPHAN_FILES_ENABLED', false),
                'cron_expression' => (string) env('MEDIA_CLEAR_ORPHAN_FILES_CRON', '15 4 * * 0'),
                'without_overlapping' => true,
            ],
        ];
    }

    /**
     * Conversion minimaliste H:i → cron (minute heure jour mois dow).
     */
    public static function dailyAtToCron(string $hhMm): string
    {
        $parts = explode(':', trim($hhMm));
        $h = isset($parts[0]) ? max(0, min(23, (int) $parts[0])) : 3;
        $m = isset($parts[1]) ? max(0, min(59, (int) $parts[1])) : 0;

        return "{$m} {$h} * * *";
    }

    /**
     * @return array<string, array{label: string}>
     */
    public static function labelsByKey(): array
    {
        return collect(self::handlers())
            ->map(fn ($def) => ['label' => $def['label']])
            ->all();
    }
}
