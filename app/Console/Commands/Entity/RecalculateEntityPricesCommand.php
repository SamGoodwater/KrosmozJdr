<?php

declare(strict_types=1);

namespace App\Console\Commands\Entity;

use App\Console\ArtisanExitCode;
use App\Services\Characteristic\Pricing\EntityPriceRecalculator;
use Illuminate\Console\Command;

/**
 * Réécrit le prix automatique des équipements ou consommables et vide l’ajustement manuel.
 *
 * @example php artisan entities:recalculate-prices items
 * @example php artisan entities:recalculate-prices consumables
 */
final class RecalculateEntityPricesCommand extends Command
{
    protected $signature = 'entities:recalculate-prices
        {type : items|consumables}';

    protected $description = 'Recalcule les prix kamas (formule) et remplace le total affiché (consommables jouables exclus).';

    public function handle(EntityPriceRecalculator $recalculator): int
    {
        $type = (string) $this->argument('type');
        if (! in_array($type, ['items', 'consumables'], true)) {
            $this->error('type doit être items ou consumables.');

            return ArtisanExitCode::FAILURE;
        }

        $updated = $type === 'items'
            ? $recalculator->recalculateAllItems(function (int $count, int $total): void {
                $this->line(sprintf('[%3d %%] %d / %d équipements', (int) round(100 * $count / max(1, $total)), $count, $total));
            })
            : $recalculator->recalculateAllConsumables(function (int $count, int $total): void {
                $this->line(sprintf('[%3d %%] %d / %d consommables', (int) round(100 * $count / max(1, $total)), $count, $total));
            });

        $label = $type === 'items' ? 'équipement(s)' : 'consommable(s)';
        $this->info($updated.' '.$label.' mis à jour.');

        return ArtisanExitCode::SUCCESS;
    }
}
