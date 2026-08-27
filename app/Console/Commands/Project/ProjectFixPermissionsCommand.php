<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\AcceptsYesNoFlags;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Console\YesNoFlags;
use Illuminate\Console\Command;

/**
 * Corrige propriétaires / permissions du dépôt (chown, chmod Laravel).
 * Ne touche pas aux binaires globaux composer / pnpm.
 *
 * @example php artisan project:fix-permissions nom_utilisateur
 * @example php artisan project:fix-permissions nom_utilisateur -y
 */
class ProjectFixPermissionsCommand extends Command
{
    use AcceptsYesNoFlags;
    use GuardsProductionEnvironment;

    protected $signature = 'project:fix-permissions
        {user : Nom d’utilisateur système cible (ex. www-data, goodwater)}
        '.YesNoFlags::SIGNATURE;

    protected $description = 'Attribue les fichiers du projet à un utilisateur (chown, chmod Laravel).';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->abortIfConflictingYesNoFlags()) {
            return ArtisanExitCode::FAILURE;
        }

        $user = trim((string) $this->argument('user'));
        if ($user === '' || ! preg_match('/^[a-zA-Z0-9_-]+$/', $user)) {
            $this->error('Nom d\'utilisateur invalide. Utilisez uniquement des lettres, chiffres, tirets et underscores.');

            return ArtisanExitCode::FAILURE;
        }

        $userExists = shell_exec('id '.escapeshellarg($user).' 2>/dev/null');
        if (empty($userExists)) {
            $this->error("L'utilisateur '$user' n'existe pas sur ce système.");

            return ArtisanExitCode::FAILURE;
        }

        $currentUser = trim((string) shell_exec('whoami'));
        if ($user !== $currentUser) {
            $this->warn("Tu es actuellement connecté en tant que '$currentUser'");
            $this->warn("Tu vas changer les permissions pour l'utilisateur '$user'");

            if (! $this->confirmOrFlag('Es-tu sûr de vouloir continuer ?', $this->isNonInteractiveInput())) {
                $this->info('Opération annulée.');

                return ArtisanExitCode::SUCCESS;
            }
        }

        $escapedUser = escapeshellarg($user);
        $this->info("Correction des permissions pour l'utilisateur : $user");
        shell_exec("chown -R {$escapedUser}:{$escapedUser} . 2>&1");

        if (is_dir('storage/')) {
            shell_exec('chmod -R 775 storage/');
        }
        if (is_dir('bootstrap/cache/')) {
            shell_exec('chmod -R 775 bootstrap/cache/');
        }
        if (is_dir('public/')) {
            shell_exec('chmod -R 775 public/');
        }
        if (file_exists('artisan')) {
            shell_exec('chmod 755 artisan');
        }

        $this->info('Permissions du dépôt corrigées.');

        return ArtisanExitCode::SUCCESS;
    }
}
