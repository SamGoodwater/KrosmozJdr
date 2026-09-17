<?php

declare(strict_types=1);

namespace App\Services\Project;

use App\Console\YesNoFlags;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * CSS, doc, migrations, IDE Helper et dépendances Composer/pnpm pour `project:prepare` / `project:deps`.
 */
class ProjectPrepareService
{
    public function __construct(
        private readonly ProjectClearService $clearService
    ) {}

    /**
     * Prépare l’environnement : CSS, caches, doc, migrations, puis pipeline IDE / optimize.
     */
    public function prepare(Command $command): int
    {
        if (RefusesRootExecution::abort($command)) {
            return Command::FAILURE;
        }

        $this->updateCss($command);
        $this->clearService->clearCache($command);
        $command->call('view:clear');
        $this->updateAtomicIndexes($command);
        $this->updateDocs($command);

        $migrate = $command->call('setup', array_merge(
            ['--db' => true],
            $this->yesNoCallOptions($command)
        ));
        if ($migrate !== Command::SUCCESS) {
            return $migrate;
        }

        return $this->optimize($command);
    }

    /**
     * optimize:clear → IDE Helper → dump-autoload → optimize.
     */
    public function optimize(Command $command): int
    {
        if (RefusesRootExecution::abort($command)) {
            return Command::FAILURE;
        }

        $this->optimiseLaravel($command);
        $this->optimiseIde($command);
        $this->dumpAutoload($command);

        return $command->call('optimize');
    }

    public function updateCss(Command $command): void
    {
        $command->info('Rebuild CSS...');
        $result = shell_exec('pnpm run css 2>&1');
        if ($result !== null) {
            $command->info($result);
        }
    }

    public function updateAtomicIndexes(Command $command): void
    {
        $command->info('Régénération des index Atomic Design (atoms/molecules/organisms)...');
        exec('pnpm run update:atomic-index');
    }

    public function updateDocs(Command $command): void
    {
        $command->info('Génération de l’index et du schéma de la doc...');
        exec('pnpm run update:docs');
    }

    public function dumpAutoload(Command $command): void
    {
        $command->info('Composer dump-autoload…');
        exec('composer dump-autoload');
    }

    /**
     * Génère IDE Helper. `-y` écrase les modèles ; `--no` ou non-interactif écrit `_ide_helper_models.php`.
     */
    public function optimiseIde(Command $command): void
    {
        $command->info('Génération des fichiers IDE Helper…');
        $ide_helper_models = ['--nowrite' => true];
        if (is_callable([$command, 'ideHelperModelsArguments'])) {
            $ide_helper_models = $command->ideHelperModelsArguments();
        }
        $command->call('ide-helper:models', $ide_helper_models);
        $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
        $artisan = base_path('artisan');
        $commands = ['ide-helper:generate', 'ide-helper:eloquent', 'ide-helper:meta'];
        foreach ($commands as $cmd) {
            $process = new Process([$php, $artisan, $cmd], base_path());
            $process->run();
            if (! $process->isSuccessful()) {
                $command->warn("{$cmd} a échoué (incompatibilité connue avec Socialite 5.x).");
            }
        }
    }

    public function optimiseLaravel(Command $command): void
    {
        $command->info('Nettoyage des optimisations Laravel (config/routes/views)...');
        $command->call('optimize:clear');
    }

    /**
     * Met à jour les paquets Composer du projet (respecte composer.json / lock).
     */
    public function updateComposer(Command $command): int
    {
        if (! is_file(base_path('composer.json'))) {
            return Command::SUCCESS;
        }

        $command->info('Mise à jour des dépendances Composer (composer update)…');

        return $this->runShellInProject($command, 'composer update');
    }

    /**
     * Met à jour les dépendances npm selon package.json (pnpm up).
     */
    public function updatePnpm(Command $command): int
    {
        if (! is_file(base_path('package.json'))) {
            return Command::SUCCESS;
        }

        $command->info('Mise à jour des paquets npm (pnpm up)…');

        if ($this->runShellInProject($command, 'pnpm up') !== Command::SUCCESS) {
            return Command::FAILURE;
        }

        return $this->ensureTailwindNativeBinding($command);
    }

    public function runSetupUpdate(Command $command): int
    {
        return $command->call('setup', array_merge(
            ['--update' => true],
            $this->yesNoCallOptions($command)
        ));
    }

    /**
     * @return array<string, bool>
     */
    private function yesNoCallOptions(Command $command): array
    {
        return YesNoFlags::callOptions($command);
    }

    /**
     * @return array{exitCode:int, output:string}
     */
    public function runShellWithStreaming(Command $command, string $commandLine): array
    {
        $output = '';

        try {
            $process = Process::fromShellCommandline($commandLine, base_path());
            $process->setTimeout(null);
            $process->run(function (string $type, string $buffer) use ($command, &$output): void {
                $output .= $buffer;
                $command->getOutput()->write($buffer);
            });

            return [
                'exitCode' => $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE,
                'output' => $output,
            ];
        } catch (Throwable $e) {
            $command->error($e->getMessage());

            return [
                'exitCode' => Command::FAILURE,
                'output' => $output.$e->getMessage(),
            ];
        }
    }

    public function runShellInProject(Command $command, string $commandLine): int
    {
        try {
            $process = Process::fromShellCommandline($commandLine, base_path());
            $process->setTimeout(null);
            $process->run(function (string $type, string $buffer) use ($command): void {
                $command->getOutput()->write($buffer);
            });

            return $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE;
        } catch (Throwable $e) {
            $command->error($e->getMessage());

            return Command::FAILURE;
        }
    }

    private function ensureTailwindNativeBinding(Command $command): int
    {
        $check = $this->runShellWithStreaming($command, "node -e \"require('@tailwindcss/oxide');\"");
        if ($check['exitCode'] === Command::SUCCESS) {
            return Command::SUCCESS;
        }

        $command->warn('Binding natif Tailwind indisponible après mise à jour pnpm. Réparation automatique...');
        $repair = $this->runShellInProject($command, 'pnpm install --force');
        if ($repair !== Command::SUCCESS) {
            $command->error('Échec de la réparation automatique des dépendances pnpm.');

            return Command::FAILURE;
        }

        $retry = $this->runShellWithStreaming($command, "node -e \"require('@tailwindcss/oxide');\"");

        return $retry['exitCode'];
    }
}
