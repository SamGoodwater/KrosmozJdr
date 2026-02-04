<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Tests de la commande db:export-seeder-data (export, backup ZIP, nettoyage des anciens backups).
 *
 * @see App\Console\Commands\ExportSeederDataCommand
 */
class ExportSeederDataCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $dataDir;

    private string $backupDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataDir = database_path('seeders/data');
        $this->backupDir = storage_path('app/seeders-data-backups');
    }

    public function test_command_refuses_to_run_in_production(): void
    {
        $app = $this->app;
        $originalEnv = $app->environment();
        $app->instance('env', 'production');
        try {
            $code = Artisan::call('db:export-seeder-data', ['--characteristics' => true]);
            $this->assertSame(1, $code);
            $this->assertStringContainsString('désactivée en production', Artisan::output());
        } finally {
            $app->instance('env', $originalEnv);
        }
    }

    public function test_command_export_characteristics_exits_success(): void
    {
        $this->seed(\Database\Seeders\CharacteristicSeeder::class);
        $this->seed(\Database\Seeders\CreatureCharacteristicSeeder::class);
        $this->seed(\Database\Seeders\ObjectCharacteristicSeeder::class);
        $this->seed(\Database\Seeders\SpellCharacteristicSeeder::class);

        $code = Artisan::call('db:export-seeder-data', ['--characteristics' => true]);

        $this->assertSame(0, $code);
    }

    public function test_command_export_characteristics_creates_data_files(): void
    {
        $this->seed(\Database\Seeders\CharacteristicSeeder::class);
        $this->seed(\Database\Seeders\CreatureCharacteristicSeeder::class);
        $this->seed(\Database\Seeders\ObjectCharacteristicSeeder::class);
        $this->seed(\Database\Seeders\SpellCharacteristicSeeder::class);

        Artisan::call('db:export-seeder-data', ['--characteristics' => true]);

        $path = $this->dataDir . '/characteristics.php';
        $this->assertFileExists($path);
        $this->assertStringContainsString('return', file_get_contents($path));
        $this->assertFileExists($this->dataDir . '/characteristic_creature.php');
        $this->assertFileExists($this->dataDir . '/characteristic_object.php');
        $this->assertFileExists($this->dataDir . '/characteristic_spell.php');
    }

    public function test_command_creates_backup_zip_when_data_files_exist(): void
    {
        $this->seed(\Database\Seeders\CharacteristicSeeder::class);
        $this->seed(\Database\Seeders\CreatureCharacteristicSeeder::class);
        $this->seed(\Database\Seeders\ObjectCharacteristicSeeder::class);
        $this->seed(\Database\Seeders\SpellCharacteristicSeeder::class);

        Artisan::call('db:export-seeder-data', ['--characteristics' => true]);
        $this->assertFileExists($this->dataDir . '/characteristics.php');

        Artisan::call('db:export-seeder-data', ['--characteristics' => true]);

        $zips = File::glob($this->backupDir . '/seeder-data-*.zip');
        $this->assertNotEmpty($zips, 'Au moins un backup ZIP doit exister après un second export.');
    }

    public function test_cleanup_removes_old_backups_when_more_than_seven(): void
    {
        if (! File::isDirectory($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }

        $oldCount = 8;
        $cutoff = time() - (8 * 24 * 60 * 60);
        for ($i = 0; $i < $oldCount; $i++) {
            $path = $this->backupDir . '/seeder-data-old-' . $i . '.zip';
            $zip = new \ZipArchive();
            $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $zip->addFromString('dummy.txt', 'test');
            $zip->close();
            touch($path, $cutoff);
        }

        $this->seed(\Database\Seeders\CharacteristicSeeder::class);
        $this->seed(\Database\Seeders\CreatureCharacteristicSeeder::class);
        $this->seed(\Database\Seeders\ObjectCharacteristicSeeder::class);
        $this->seed(\Database\Seeders\SpellCharacteristicSeeder::class);
        Artisan::call('db:export-seeder-data', ['--characteristics' => true]);

        $zips = File::glob($this->backupDir . '/seeder-data-*.zip');
        $oldZips = File::glob($this->backupDir . '/seeder-data-old-*.zip');
        $this->assertCount(0, $oldZips, 'Les anciens backups (seeder-data-old-*) doivent être supprimés.');
        $this->assertGreaterThanOrEqual(1, count($zips), 'Au moins le nouveau backup doit rester.');
    }
}
