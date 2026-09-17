<?php

declare(strict_types=1);

namespace App\Console\Commands\Characteristics;

use App\Console\ArtisanExitCode;
use App\Services\Characteristic\Formula\CreatureFormulaPlaceholderValidator;
use Illuminate\Console\Command;

/**
 * Valide les placeholders `[id]` des définitions JSON créature (seed) contre une liste blanche.
 *
 * @example php artisan characteristics:validate-creature-formula-placeholders
 */
class ValidateCreatureFormulaPlaceholdersCommand extends Command
{
    protected $signature = 'characteristics:validate-creature-formula-placeholders';

    protected $description = 'Vérifie que les formules creature (seed JSON) n’utilisent pas de variables [id] inconnues';

    public function handle(CreatureFormulaPlaceholderValidator $validator): int
    {
        $dir = database_path('seeders/data/characteristic-definitions/creature');
        $errors = $validator->validateCreatureDefinitionsDirectory($dir);

        if ($errors === []) {
            $this->info('Aucune variable inconnue dans les formules creature.');

            return ArtisanExitCode::SUCCESS;
        }

        $this->error(count($errors).' placeholder(s) inconnu(s) :');
        foreach ($errors as $e) {
            $this->line(sprintf(
                '  • %s [%s] entity=%s field=%s → [%s]',
                basename($e['file']),
                $e['characteristic'],
                $e['entity'],
                $e['field'],
                $e['unknown']
            ));
        }

        return ArtisanExitCode::FAILURE;
    }
}
