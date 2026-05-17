<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Console\Commands\Project\ProjectClearCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Smoke test : la commande `project:clear` reste enregistrée et exécutable en environnement de test.
 *
 * @see ProjectClearCommand
 */
class ProjectClearCommandTest extends TestCase
{
    public function test_project_clear_cache_option_returns_success(): void
    {
        $code = Artisan::call('project:clear', [
            '--cache' => true,
        ]);

        $this->assertSame(0, $code);
    }

    public function test_project_clear_removes_review_files(): void
    {
        $dir = storage_path('app/dev-reports');
        File::ensureDirectoryExists($dir);
        $path = $dir.'/smoke-clear-review-delete-me.md';
        File::put($path, "_test_\n");

        $code = Artisan::call('project:clear', [
            '--reviews' => true,
        ]);

        $this->assertSame(0, $code);
        $this->assertFileDoesNotExist($path);
        $this->assertDirectoryExists($dir);
    }

    public function test_project_clean_alias_same_command_as_clear(): void
    {
        $code = Artisan::call('project:clean', [
            '--cache' => true,
        ]);

        $this->assertSame(0, $code);
    }
}
