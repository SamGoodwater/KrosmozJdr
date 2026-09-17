<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\Type\MonsterRace;
use App\Models\User;

/**
 * Une race DofusDB scrapable, pour que le mode `race-mode=allowed` n’injecte pas `raceIds=[]`.
 */
trait SeedsAllowScrapMonsterRace
{
    /**
     * Garantit une race `allow_scrap` avec `dofusdb_race_id` (Bouftou, id DofusDB 32).
     */
    protected function seedAllowScrapMonsterRace(): void
    {
        MonsterRace::query()->updateOrCreate(
            ['name' => 'Bouftou'],
            [
                'dofusdb_race_id' => 32,
                'allow_scrap' => true,
                'show_in_catalog' => true,
                'state' => 'playable',
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]
        );
    }
}
