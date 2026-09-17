<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Console\Commands\Scrapping\ScrappingRunCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Le fichier `--batch` du scrapping doit rester sous la racine du projet (réduction lecture arbitraire).
 *
 * @see ScrappingRunCommand::resolveBatchFileUnderProjectRoot()
 */
class ScrappingRunBatchPathTest extends TestCase
{
    public function test_batch_rejects_file_outside_project_root(): void
    {
        $outside = sys_get_temp_dir().'/kroz-scrap-batch-'.uniqid('', true).'.json';
        File::put($outside, '{}');
        try {
            $code = Artisan::call('scrapping:run', ['--batch' => $outside]);
            $this->assertSame(1, $code);
            $this->assertStringContainsStringIgnoringCase('hors du répertoire', Artisan::output());
        } finally {
            @unlink($outside);
        }
    }

    public function test_batch_accepts_path_under_project_and_then_validates_content(): void
    {
        $path = storage_path('app/framework/scrapping-batch-test-'.uniqid('', false).'.json');
        File::ensureDirectoryExists(dirname($path));
        File::put($path, '{"invalid":'); // JSON cassé après lecture
        try {
            $code = Artisan::call('scrapping:run', ['--batch' => $path]);
            $this->assertSame(1, $code);
            $this->assertStringContainsString('JSON invalide', Artisan::output());
        } finally {
            @unlink($path);
        }
    }
}
