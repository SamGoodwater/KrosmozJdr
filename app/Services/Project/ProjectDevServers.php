<?php

declare(strict_types=1);

namespace App\Services\Project;

use Illuminate\Console\Command;

/**
 * Démarrage local Laravel / Vite / queue pour `project:dev`.
 */
class ProjectDevServers
{
    public function __construct(
        private readonly ProjectPrepareService $prepareService
    ) {}

    public function killServers(Command $command): void
    {
        $command->info('Arrêt des serveurs sur les ports 8000, 8001, 8002, 5173...');
        exec('lsof -t -i:8000 -i:8001 -i:8002 -i:5173 | xargs -r kill -9');
    }

    /**
     * @param  bool  $withQueue  Démarre aussi `queue:listen` en arrière-plan
     */
    public function runDev(Command $command, bool $withQueue = false): int
    {
        if (RefusesRootExecution::abort($command)) {
            return Command::FAILURE;
        }

        $command->info('Lancement des serveurs de développement...');
        $this->killServers($command);

        $command->info('Démarrage du serveur Laravel sur le port 8000...');
        exec('php artisan serve --host=127.0.0.1 --port=8000 > /dev/null 2>&1 &');

        sleep(3);

        $laravelResponse = @file_get_contents('http://127.0.0.1:8000');
        if ($laravelResponse !== false) {
            $command->info('Serveur Laravel démarré sur http://127.0.0.1:8000');
        } else {
            $command->warn('Serveur Laravel en cours de démarrage...');
        }

        if ($withQueue) {
            $command->info('Démarrage de queue:listen...');
            exec('php artisan queue:listen --tries=1 > /dev/null 2>&1 &');
        }

        $command->info('Démarrage de Vite sur le port 5173...');
        $this->runViteDevWithSelfHeal($command);

        return Command::SUCCESS;
    }

    public function runDevWatch(Command $command): int
    {
        if (RefusesRootExecution::abort($command)) {
            return Command::FAILURE;
        }

        $command->info('Lancement du serveur (watch)...');
        $this->runProcess($command, 'pnpm run dev:css:optimized:watch');

        return Command::SUCCESS;
    }

    private function runViteDevWithSelfHeal(Command $command): void
    {
        $exitCode = $this->runInteractiveProcess('pnpm run dev:optimized');
        if ($exitCode === Command::SUCCESS) {
            return;
        }

        $check = $this->prepareService->runShellInProject($command, "node -e \"require('@tailwindcss/oxide');\"");
        if ($check === Command::SUCCESS) {
            $command->error('Erreur lors de pnpm run dev:optimized');

            return;
        }

        $command->warn('Dépendance native Tailwind manquante détectée. Tentative de réparation automatique...');
        $repair = $this->prepareService->runShellInProject($command, 'pnpm install --force');
        if ($repair !== Command::SUCCESS) {
            $command->error('Échec de la réparation automatique des dépendances pnpm.');

            return;
        }

        $command->info('Relance de Vite après réparation des dépendances...');
        $retry = $this->runInteractiveProcess('pnpm run dev:optimized');
        if ($retry !== Command::SUCCESS) {
            $command->error('Erreur lors de pnpm run dev:optimized (après réparation automatique).');
        }
    }

    private function runProcess(Command $command, string $shellCommand): void
    {
        $returnVar = $this->runInteractiveProcess($shellCommand);
        if ($returnVar !== 0) {
            $command->error("Erreur lors de $shellCommand");
        } else {
            $command->info("$shellCommand terminé avec succès");
        }
    }

    private function runInteractiveProcess(string $shellCommand): int
    {
        $descriptorspec = [0 => STDIN, 1 => STDOUT, 2 => STDERR];
        $process = proc_open($shellCommand, $descriptorspec, $pipes);
        if (! is_resource($process)) {
            return Command::FAILURE;
        }

        $returnVar = proc_close($process);

        if (! is_int($returnVar)) {
            return Command::FAILURE;
        }

        return $returnVar;
    }
}
