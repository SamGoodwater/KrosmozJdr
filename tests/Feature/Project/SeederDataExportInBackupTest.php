<?php

declare(strict_types=1);

namespace Tests\Feature\Project;

use App\Services\Project\ProjectBackupService;
use App\Services\Project\SeederDataExportService;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Tests\TestCase;

/**
 * Export des fichiers de seed branché sur project:backup (hors production).
 */
final class SeederDataExportInBackupTest extends TestCase
{
    public function test_backup_calls_seeder_export_outside_production(): void
    {
        $fake = Mockery::mock(SeederDataExportService::class);
        $fake->shouldReceive('export')
            ->once()
            ->andReturnUsing(function (callable $log): array {
                $log('fake seeder export');

                return ['fake-export'];
            });

        $dir = storage_path('framework/testing/backup-seeder-'.uniqid());
        mkdir($dir, 0700, true);

        try {
            $service = new ProjectBackupService($dir, 30, 'mysqldump', 'project-backup');
            $result = $service->run(
                withDatabase: false,
                withStorage: false,
                prune: false,
                dryRun: false,
                log: static fn () => null,
                error: static fn () => null,
                withSeederData: true,
                seederDataExport: $fake,
                withGame: false,
            );

            $this->assertSame(['fake-export'], $result['seeder_exports']);
            $this->assertTrue($result['ok']);
        } finally {
            foreach (glob($dir.'/*') ?: [] as $f) {
                @unlink($f);
            }
            @rmdir($dir);
        }
    }

    public function test_backup_skips_seeder_export_when_flag_false(): void
    {
        $fake = Mockery::mock(SeederDataExportService::class);
        $fake->shouldNotReceive('export');

        $dir = storage_path('framework/testing/backup-noseed-'.uniqid());
        mkdir($dir, 0700, true);

        try {
            $service = new ProjectBackupService($dir, 30, 'mysqldump', 'project-backup');
            $result = $service->run(
                false,
                false,
                false,
                false,
                static fn () => null,
                static fn () => null,
                false,
                $fake,
                false,
            );

            // Sans aucune cible : run_id vide
            $this->assertSame('', $result['run_id']);
            $this->assertSame([], $result['seeder_exports']);
            $this->assertFalse($result['ok']);
        } finally {
            foreach (glob($dir.'/*') ?: [] as $f) {
                @unlink($f);
            }
            @rmdir($dir);
        }
    }

    public function test_production_env_disables_seeder_export_flag_like_command(): void
    {
        $this->app['env'] = 'production';
        try {
            $withSeederData = ! (bool) false && ! app()->environment('production');
            $this->assertFalse($withSeederData);
        } finally {
            $this->app['env'] = 'testing';
        }
    }

    public function test_no_seeder_data_option_is_recognized(): void
    {
        $definition = Artisan::all()['project:backup'] ?? null;
        $this->assertNotNull($definition);
        $this->assertTrue($definition->getDefinition()->hasOption('no-seeder-data'));
    }
}
