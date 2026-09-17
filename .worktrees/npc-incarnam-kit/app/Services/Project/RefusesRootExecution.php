<?php

declare(strict_types=1);

namespace App\Services\Project;

use Illuminate\Console\Command;

/**
 * Refuse l’exécution CLI en tant que root (fichiers créés root cassent le dev).
 */
final class RefusesRootExecution
{
    /**
     * @return bool true si la commande doit s’arrêter
     */
    public static function abort(Command $command): bool
    {
        $currentUser = trim((string) shell_exec('whoami'));
        if ($currentUser !== 'root') {
            return false;
        }

        $command->error('Ces commandes ne doivent pas être exécutées en tant que root.');
        $command->line('Utilisez un utilisateur normal, ou : php artisan project:fix-permissions nom_utilisateur');

        return true;
    }
}
