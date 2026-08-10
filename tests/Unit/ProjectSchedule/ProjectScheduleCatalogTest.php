<?php

declare(strict_types=1);

namespace Tests\Unit\ProjectSchedule;

use App\Support\ProjectSchedule\ProjectScheduleCatalog;
use PHPUnit\Framework\TestCase;

/**
 * Catalogue fixe des tâches planifiables côté admin.
 *
 * @example php artisan test --filter=ProjectScheduleCatalogTest
 */
class ProjectScheduleCatalogTest extends TestCase
{
    public function test_catalog_contains_expected_fixed_maintenance_tasks(): void
    {
        $handlers = ProjectScheduleCatalog::handlers();

        foreach ([
            'project_clear_safe',
            'project_data_sync',
            'scrap_resources_catalog',
            'project_backup',
            'media_clear_orphan_files',
            'privacy_process_deletion_requests',
            'notification_digest_daily',
            'notification_digest_weekly',
            'notification_digest_monthly',
        ] as $key) {
            $this->assertArrayHasKey($key, $handlers);
        }

        foreach ($handlers as $definition) {
            $this->assertContains($definition['type'], ['artisan', 'job']);
            $this->assertArrayHasKey('label', $definition);
        }
    }
}
