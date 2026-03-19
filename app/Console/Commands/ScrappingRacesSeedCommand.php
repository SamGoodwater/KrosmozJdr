<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Type\MonsterRace;
use App\Models\User;
use App\Services\Scrapping\Catalog\DofusDbMonsterRacesCatalogService;
use Illuminate\Console\Command;

/**
 * Récupère les races de monstres depuis l'API DofusDB et les synchronise en base.
 *
 * Crée ou met à jour les entrées monster_races avec dofusdb_race_id et name.
 * Les races existantes ne sont pas réinitialisées (state, read_level, write_level préservés).
 *
 * @example php artisan scrapping:races:seed
 * @example php artisan scrapping:races:seed --lang=fr --skip-cache
 */
class ScrappingRacesSeedCommand extends Command
{
    protected $signature = 'scrapping:races:seed
                            {--lang=fr : Langue du catalogue DofusDB}
                            {--skip-cache : Ignorer le cache du catalogue}';

    protected $description = 'Récupère les races monstres depuis l\'API DofusDB et les synchronise en base';

    public function __construct(
        private readonly DofusDbMonsterRacesCatalogService $catalogService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $lang = (string) $this->option('lang');
        $skipCache = (bool) $this->option('skip-cache');

        $this->info('Chargement du catalogue DofusDB (monster-races)…');
        $races = $this->catalogService->listAll($lang, $skipCache);

        if (empty($races)) {
            $this->warn('Aucune race trouvée dans le catalogue DofusDB.');

            return self::SUCCESS;
        }

        $systemUser = User::getSystemUser();
        $createdBy = $systemUser?->id;

        $created = 0;
        $updated = 0;

        foreach ($races as $race) {
            $dofusdbRaceId = (int) ($race['id'] ?? 0);
            if ($dofusdbRaceId <= 0) {
                continue;
            }
            $name = is_string($race['name'] ?? null) && $race['name'] !== ''
                ? $race['name']
                : "DofusDB race #{$dofusdbRaceId}";

            $existing = MonsterRace::query()->where('dofusdb_race_id', $dofusdbRaceId)->first();

            if ($existing) {
                if ($existing->name !== $name) {
                    $existing->update(['name' => $name]);
                    $updated++;
                }
            } else {
                MonsterRace::create([
                    'dofusdb_race_id' => $dofusdbRaceId,
                    'name' => $name,
                    'state' => MonsterRace::STATE_DRAFT,
                    'read_level' => User::ROLE_GUEST,
                    'write_level' => User::ROLE_ADMIN,
                    'created_by' => $createdBy,
                    'id_super_race' => null,
                ]);
                $created++;
            }
        }

        $this->info(sprintf(
            'Terminé. %d créées, %d mises à jour (nom). Total catalogue : %d races.',
            $created,
            $updated,
            count($races)
        ));

        return self::SUCCESS;
    }
}
