<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\AcceptsYesNoFlags;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Console\YesNoFlags;
use App\Services\Project\ProjectDevServers;
use Illuminate\Console\Command;

/**
 * Environnement de développement : project:prepare puis serveurs PHP + Vite.
 *
 * @example php artisan project:dev
 * @example php artisan project:dev --queue
 * @example php artisan project:dev --no-prepare
 * @example php artisan project:dev -y
 */
class ProjectDevCommand extends Command
{
    use AcceptsYesNoFlags;
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectDevServers $projectDevServers
    ) {
        parent::__construct();
    }

    protected $signature = 'project:dev
        {--no-prepare : Ne pas exécuter project:prepare avant les serveurs}
        {--clear : Supprimer les artefacts de tests avant project:prepare}
        {--queue : Démarrer aussi queue:listen en arrière-plan}
        {--watch : Mode watch CSS au lieu du serveur Vite}
        '.YesNoFlags::SIGNATURE;

    protected $description = 'Prépare le projet (CSS, doc, migrations, optimize) puis lance PHP + Vite.';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->abortIfConflictingYesNoFlags()) {
            return ArtisanExitCode::FAILURE;
        }

        if (! $this->option('no-prepare')) {
            $prepareOptions = array_merge(
                ['--clear' => $this->option('clear')],
                $this->yesNoCallOptions()
            );
            if ($this->call('project:prepare', $prepareOptions) !== ArtisanExitCode::SUCCESS) {
                return ArtisanExitCode::FAILURE;
            }
        }

        if ($this->option('watch')) {
            return $this->projectDevServers->runDevWatch($this);
        }

        return $this->projectDevServers->runDev($this, (bool) $this->option('queue'));
    }
}
