<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\Project\ProjectInitVerifyService;
use Illuminate\Console\Command;

/**
 * Vérifie que le socle post-seed / post-init est présent (pages, caractéristiques, types…).
 *
 * @example php artisan project:init:verify
 * @example php artisan project:init:verify --with-rules --json
 */
class ProjectInitVerifyCommand extends Command
{
    protected $signature = 'project:init:verify
        {--with-rules : Exiger des pages CMS regles-* (import TOC)}
        {--min-spells=0 : Seuil minimal de sorts en base (avertissement)}
        {--json : Sortie JSON}';

    protected $description = 'Contrôle le socle de données après project:init / project:seed';

    public function handle(ProjectInitVerifyService $service): int
    {
        $result = $service->verify(
            (bool) $this->option('with-rules'),
            (int) $this->option('min-spells')
        );

        if ((bool) $this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $result['ok'] ? ArtisanExitCode::SUCCESS : ArtisanExitCode::FAILURE;
        }

        foreach ($result['warnings'] as $warning) {
            $this->warn('⚠ '.$warning);
        }

        if ($result['ok']) {
            $this->info('Vérification init OK.');

            return ArtisanExitCode::SUCCESS;
        }

        $this->error('Vérification init en échec :');
        foreach ($result['failures'] as $failure) {
            $this->line('  • '.$failure);
        }

        return ArtisanExitCode::FAILURE;
    }
}
