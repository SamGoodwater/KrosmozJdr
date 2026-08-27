<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Console\Commands\Project\ProjectReviewCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

/**
 * Smoke tests du rapport `project:review`, sans lancer les outils réels.
 *
 * @see ProjectReviewCommand
 */
class ProjectReviewCommandTest extends TestCase
{
    public function test_project_review_generates_pint_dirty_report_with_custom_timeout(): void
    {
        Process::fake([
            '*' => Process::result(output: 'Pint OK', exitCode: 0),
        ]);

        $reportPath = 'storage/app/dev-reports/test-project-review.md';
        File::delete(base_path($reportPath));

        $code = Artisan::call('project:review', [
            '--pint' => true,
            '--pint-dirty' => true,
            '--pint-timeout' => '45',
            '--no-cursor-prompts' => true,
            '--report-path' => $reportPath,
        ]);

        $this->assertSame(0, $code);
        $this->assertFileExists(base_path($reportPath));

        $report = File::get(base_path($reportPath));
        $this->assertStringContainsString('Stratégie Pint : timeout 45s ; `--dirty` ; fallback par lots si timeout.', $report);
        $this->assertStringContainsString('Pint OK', $report);

        File::delete(base_path($reportPath));
    }
}
