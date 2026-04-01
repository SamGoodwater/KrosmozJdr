<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Corrige propriétaires / permissions du dépôt ({@see ProjectRunService}).
 */
class ProjectFixPermissionsCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:fix-permissions
        {user : Nom d’utilisateur système cible (ex. www-data, goodwater)}';

    protected $description = 'Attribue les fichiers du projet à un utilisateur (chown, chmod Laravel).';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return self::FAILURE;
        }

        $user = trim((string) $this->argument('user'));
        if ($user === '') {
            $this->error('Utilisateur requis.');

            return self::FAILURE;
        }

        return $this->projectRunService->runOptionMap([
            'update:privilege' => $user,
        ], $this);
    }
}
