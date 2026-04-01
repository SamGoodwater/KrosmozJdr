<?php

declare(strict_types=1);

namespace App\Console\Commands\Development;

use App\Console\Concerns\GuardsProductionEnvironment;
use Illuminate\Console\Command;

/**
 * Bootstrap « machine / IDE » lourd : composer update, ide-helper, pnpm, migrate.
 * Chevauche partiellement `project:dev --prepare` — voir app/Console/README.md.
 */
class PrepareProjectCommand extends Command
{
    use GuardsProductionEnvironment;

    protected $signature = 'server:prepare';

    protected $description = 'Préparer le projet : framework, migrations, autoload, ide-helper:models, meta PHPStorm';

    public function handle(): int
    {
        if (! $this->guardDevelopmentOnly()) {
            return self::FAILURE;
        }

        $this->info('Préparation du projet');

        $this->info('Mise à jour des dépendances avec Composer');
        exec('composer update');

        $this->info('Génération des fichiers ide-helper:models');
        $this->call('ide-helper:models');

        $this->info('Génération des fichiers ide-helper:generate');
        $this->call('ide-helper:generate');

        $this->info('Génération des fichiers ide-helper:eloquent');
        $this->call('ide-helper:eloquent');

        $this->info('Génération des fichiers ide-helper:meta');
        $this->call('ide-helper:meta');

        $this->info('Regénération de l\'autoloader de Composer');
        exec('composer dump-autoload');

        $this->info('Installation des dépendances pnpm');
        exec('pnpm install');

        $this->info('Exécution des migrations de la base de données');
        $this->call('migrate');

        $this->info('Optimisation du framework');
        $this->call('optimize');

        return self::SUCCESS;
    }
}
