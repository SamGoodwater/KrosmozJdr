<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Optimisation locale : optimize:clear, IDE Helper, dump-autoload, optimize.
 *
 * @example php artisan project:optimize
 * @example php artisan project:optimize --ide-only
 */
class ProjectOptimizeCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:optimize
        {--clear-only : optimize:clear Laravel uniquement}
        {--ide-only : IDE Helper + dump-autoload uniquement}';

    protected $description = 'optimize:clear → ide-helper → dump-autoload → optimize (pipeline dev).';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return self::FAILURE;
        }

        $clearOnly = (bool) $this->option('clear-only');
        $ideOnly = (bool) $this->option('ide-only');

        if ($clearOnly && $ideOnly) {
            $this->error('Incompatible : --clear-only et --ide-only.');

            return self::FAILURE;
        }

        if ($clearOnly) {
            return $this->projectRunService->runProjectOptimizePipeline($this, 'clear-only');
        }

        if ($ideOnly) {
            return $this->projectRunService->runProjectOptimizePipeline($this, 'ide-only');
        }

        return $this->projectRunService->runProjectOptimizePipeline($this, 'full');
    }
}
