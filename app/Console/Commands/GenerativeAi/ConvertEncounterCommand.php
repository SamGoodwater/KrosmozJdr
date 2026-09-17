<?php

declare(strict_types=1);

namespace App\Console\Commands\GenerativeAi;

use Illuminate\Console\Command;

/**
 * Convertit une rencontre (monstre + 2–3 sorts-créature) via le pipeline IA.
 *
 * @example php artisan ia:convert-encounter --id=12 --brief="chef Bouftou niveau 10"
 */
final class ConvertEncounterCommand extends Command
{
    protected $signature = 'ia:convert-encounter
        {--id= : Id local du monstre}
        {--official-id= : official_id du monstre (ex. jdr:bestiary:piou-vert)}
        {--brief= : Brief MJ}
        {--user= : Id utilisateur admin obligatoire (gate generate)}
        {--force : Ignorer les gardes de fiche jouable côté métier}';

    protected $description = 'Conversion IA d’une rencontre (monstre + 2–3 sorts-créature) → état auto';

    public function handle(): int
    {
        $params = ['type' => 'encounter'];
        $id = $this->option('id');
        if (is_numeric($id) && (int) $id > 0) {
            $params['--id'] = (string) $id;
        }
        $official = $this->option('official-id');
        if (is_string($official) && $official !== '') {
            $params['--official-id'] = $official;
        }
        $brief = $this->option('brief');
        if (is_string($brief) && $brief !== '') {
            $params['--brief'] = $brief;
        }
        $user = $this->option('user');
        if (is_numeric($user) && (int) $user > 0) {
            $params['--user'] = (string) $user;
        }
        if ((bool) $this->option('force')) {
            $params['--force'] = true;
        }

        return $this->call('ia:convert', $params);
    }
}
