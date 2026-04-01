<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use Illuminate\Console\Command;

/**
 * Point d’entrée « données projet » pour l’import de la table des matières des règles.
 *
 * Délègue à `pages:import-rules-toc` pour garder une seule implémentation tout en exposant
 * le flux sous le namespace `project:data:*` (cohérent avec la doc domaine « données »).
 */
class ProjectDataImportRulesTocCommand extends Command
{
    protected $signature = 'project:data:import-rules-toc
        {path? : Chemin du fichier TABLE_DES_MATIERES.md}
        {--dry-run : Affiche le plan sans écrire en base}
        {--force-content : Écrase le contenu existant des sections avec les markdown source}';

    protected $description = 'Alias project:data — import TOC règles (délègue à pages:import-rules-toc).';

    public function handle(): int
    {
        $params = [];
        $path = $this->argument('path');
        if ($path !== null && trim((string) $path) !== '') {
            $params['path'] = $path;
        }
        if ($this->option('dry-run')) {
            $params['--dry-run'] = true;
        }
        if ($this->option('force-content')) {
            $params['--force-content'] = true;
        }

        return $this->call('pages:import-rules-toc', $params);
    }
}
