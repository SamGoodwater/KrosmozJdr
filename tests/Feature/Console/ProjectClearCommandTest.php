<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Smoke test : la commande `project:clear` reste enregistrée et exécutable en environnement de test.
 *
 * @see App\Console\Commands\Project\ProjectClearCommand
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
}
