<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Smoke test : `project:clear` reste enregistrée et exécutable.
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

    public function test_project_clear_safe_returns_success(): void
    {
        $code = Artisan::call('project:clear', [
            '--safe' => true,
        ]);

        $this->assertSame(0, $code);
    }

    public function test_project_clear_without_option_fails(): void
    {
        $code = Artisan::call('project:clear', []);

        $this->assertNotSame(0, $code);
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
}
