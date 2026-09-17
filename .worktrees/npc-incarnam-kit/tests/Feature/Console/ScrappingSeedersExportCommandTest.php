<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Console\Commands\Scrapping\ScrappingSeedersExportCommand;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Tests de la commande scrapping:seeders:export (export, backup ZIP, nettoyage des anciens backups).
 *
 * @see ScrappingSeedersExportCommand
 */
class ScrappingSeedersExportCommandTest extends TestCase
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
            $code = Artisan::call('scrapping:seeders:export', ['--characteristics' => true]);
            $this->assertSame(1, $code);
            $this->assertStringContainsString('désactivée en production', Artisan::output());
        } finally {
            $app->instance('env', $originalEnv);
        }
    }

    public function test_command_export_characteristics_exits_success(): void
    {
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);

        $code = Artisan::call('scrapping:seeders:export', ['--characteristics' => true]);

        $this->assertSame(0, $code);
    }

    public function test_command_export_characteristics_creates_data_files(): void
    {
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);

        Artisan::call('scrapping:seeders:export', ['--characteristics' => true]);

        $defRoot = $this->dataDir.'/characteristic-definitions';
        $this->assertDirectoryExists($defRoot);
        $jsonFiles = File::glob($defRoot.'/*/*-definition.json');
        $this->assertNotEmpty($jsonFiles, 'Au moins un fichier *-definition.json doit être exporté.');
        $sample = file_get_contents($jsonFiles[0]);
        $this->assertStringContainsString('"characteristic"', $sample);
    }

    public function test_command_creates_backup_zip_when_data_files_exist(): void
    {
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);

        Artisan::call('scrapping:seeders:export', ['--characteristics' => true]);
        $this->assertDirectoryExists($this->dataDir.'/characteristic-definitions');

        Artisan::call('scrapping:seeders:export', ['--characteristics' => true]);

        $zips = File::glob($this->backupDir.'/seeder-data-*.zip');
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
            $path = $this->backupDir.'/seeder-data-old-'.$i.'.zip';
            $zip = new \ZipArchive;
            $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $zip->addFromString('dummy.txt', 'test');
            $zip->close();
            touch($path, $cutoff);
        }

        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        Artisan::call('scrapping:seeders:export', ['--characteristics' => true]);

        $zips = File::glob($this->backupDir.'/seeder-data-*.zip');
        $oldZips = File::glob($this->backupDir.'/seeder-data-old-*.zip');
        $this->assertCount(0, $oldZips, 'Les anciens backups (seeder-data-old-*) doivent être supprimés.');
        $this->assertGreaterThanOrEqual(1, count($zips), 'Au moins le nouveau backup doit rester.');
    }
}
