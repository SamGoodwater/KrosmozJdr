<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Réinitialisations lourdes ({@see ProjectRunService}).
 */
class ProjectResetCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:reset
        {--pnpm : Réinitialiser pnpm (setup --refresh)}
        {--composer : Réinitialiser composer (setup --refresh)}
        {--all : Réinitialisation large (vendor/node, caches, etc.)}
        {--full : reset:all + migrate:fresh --seed (destructif)}';

    protected $description = 'Réinitialise dépendances ou base (pnpm/composer/all/full).';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return self::FAILURE;
        }

        $map = [];
        if ($this->option('pnpm')) {
            $map['reset:pnpm'] = true;
        }
        if ($this->option('composer')) {
            $map['reset:composer'] = true;
        }
        if ($this->option('all')) {
            $map['reset:all'] = true;
        }
        if ($this->option('full')) {
            $map['reset:full'] = true;
        }

        if ($map === []) {
            $this->warn('Indiquez au moins une option (--pnpm, --composer, --all, --full).');

            return self::FAILURE;
        }

        return $this->projectRunService->runOptionMap($map, $this);
    }
}
