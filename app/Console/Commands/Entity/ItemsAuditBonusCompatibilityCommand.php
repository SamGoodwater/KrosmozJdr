<?php

declare(strict_types=1);

namespace App\Console\Commands\Entity;

use App\Console\ArtisanExitCode;
use App\Models\Entity\Item;
use App\Services\Entity\ItemBonusCompatibilityService;
use Illuminate\Console\Command;

/**
 * Signale les clés effect/bonus incompatibles (type d’équipement ou limites).
 *
 * @example php artisan items:audit-bonus-compatibility
 */
final class ItemsAuditBonusCompatibilityCommand extends Command
{
    protected $signature = 'items:audit-bonus-compatibility';

    protected $description = 'Audit des bonus items (effect puis bonus) : compatibilité et limites';

    public function handle(ItemBonusCompatibilityService $service): int
    {
        $rows = $service->audit(Item::query()->orderBy('id')->cursor());
        if ($rows === []) {
            $this->info('Aucune anomalie détectée.');

            return ArtisanExitCode::SUCCESS;
        }

        foreach ($rows as $row) {
            $this->line(sprintf(
                '#%d %s [%s] (%s)',
                $row['item_id'],
                $row['name'],
                $row['state'],
                $row['source']
            ));
            foreach ($row['issues'] as $issue) {
                $this->line('  - '.$issue);
            }
        }

        $this->warn(count($rows).' objet(s) avec au moins une anomalie.');

        return ArtisanExitCode::SUCCESS;
    }
}
