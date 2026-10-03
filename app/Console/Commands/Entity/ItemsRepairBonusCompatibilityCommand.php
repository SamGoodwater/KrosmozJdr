<?php

declare(strict_types=1);

namespace App\Console\Commands\Entity;

use App\Console\ArtisanExitCode;
use App\Models\Entity\Item;
use App\Services\Characteristic\Pricing\EntityPriceRecalculator;
use App\Services\Entity\ItemBonusCompatibilityService;
use Illuminate\Console\Command;

/**
 * Retire les clés invalides de `effect` sur les items raw/draft/auto.
 *
 * @example php artisan items:repair-bonus-compatibility --dry-run
 * @example php artisan items:repair-bonus-compatibility --apply
 */
final class ItemsRepairBonusCompatibilityCommand extends Command
{
    protected $signature = 'items:repair-bonus-compatibility
        {--dry-run : Simulation (défaut si --apply absent)}
        {--apply : Applique les corrections et recalcule le prix}';

    protected $description = 'Corrige effect (clés invalides) sur items non jouables';

    public function handle(
        ItemBonusCompatibilityService $service,
        EntityPriceRecalculator $priceRecalculator,
    ): int {
        $apply = (bool) $this->option('apply');
        if (! $apply) {
            $this->info('Mode simulation (--apply absent).');
        }

        $result = $service->repair($apply);

        foreach ($result['reports'] as $report) {
            $this->line(sprintf(
                '#%d clés retirées : %s',
                $report['item_id'],
                implode(', ', $report['removed'])
            ));
        }

        if ($apply) {
            foreach ($result['reports'] as $report) {
                $item = Item::query()->find($report['item_id']);
                if ($item instanceof Item) {
                    $priceRecalculator->recalculateItem($item, resetCustom: false);
                }
            }
            $this->info("{$result['updated']} objet(s) corrigé(s), {$result['skipped']} ignoré(s).");
        } else {
            $this->info(count($result['reports']).' objet(s) seraient corrigés.');
        }

        return ArtisanExitCode::SUCCESS;
    }
}
