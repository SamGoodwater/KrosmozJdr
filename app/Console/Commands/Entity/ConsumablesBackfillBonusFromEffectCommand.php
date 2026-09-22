<?php

declare(strict_types=1);

namespace App\Console\Commands\Entity;

use App\Console\ArtisanExitCode;
use App\Services\Seeder\Consumable\ConsumableBonusFromEffectBackfiller;
use Illuminate\Console\Command;

/**
 * Remplit `consumables.bonus` à partir du texte `effect` (soins, bouclier, PV temp…).
 *
 * @example php artisan consumables:backfill-bonus-from-effect
 */
final class ConsumablesBackfillBonusFromEffectCommand extends Command
{
    protected $signature = 'consumables:backfill-bonus-from-effect
        {--force : Réécrit aussi les bonus déjà renseignés}';

    protected $description = 'Déduit consumables.bonus depuis effect (texte JDR ou JSON hérité)';

    public function handle(ConsumableBonusFromEffectBackfiller $backfiller): int
    {
        $onlyEmpty = ! (bool) $this->option('force');
        $result = $backfiller->backfill($onlyEmpty);

        $this->info(sprintf(
            'Bonus déduits : %d mis à jour, %d ignorés.',
            $result['updated'],
            $result['skipped']
        ));

        return ArtisanExitCode::SUCCESS;
    }
}
