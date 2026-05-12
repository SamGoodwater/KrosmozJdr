<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\PromptsPrimarySuperAdmin;
use Illuminate\Console\Command;

/**
 * Crée le premier super_admin interactif (hors flux `project:init`).
 */
class ProjectSuperAdminCommand extends Command
{
    use PromptsPrimarySuperAdmin;

    protected $signature = 'project:super-admin';

    protected $description = 'Crée le compte super_admin principal (si aucun super_admin humain n’existe)';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Cette commande ne doit pas être utilisée en production sans processus contrôlé.');

            return ArtisanExitCode::FAILURE;
        }

        try {
            $this->runPrimarySuperAdminPrompt();
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        return ArtisanExitCode::SUCCESS;
    }
}
