<?php

declare(strict_types=1);

namespace App\Services\Project;

use Illuminate\Support\Facades\Artisan;

/**
 * Réécrit les fichiers de seed versionnés depuis la base (hors production).
 *
 * Domaines : caractéristiques, types d’items, mappings scrapping, équipements versionnés.
 *
 * @example
 * $paths = app(SeederDataExportService::class)->export(fn (string $m) => $this->line($m));
 */
class SeederDataExportService
{
    /**
     * @param  callable(string): void  $log
     * @return list<string>  Libellés des exports réalisés
     */
    public function export(callable $log): array
    {
        if (app()->environment('production')) {
            $log('Export des fichiers de seed ignoré (environnement production).');

            return [];
        }

        $done = [];

        $code = Artisan::call('scrapping:seeders:export', [
            '--characteristics' => true,
            '--item-types' => true,
            '--scrapping-mappings' => true,
        ]);
        $output = trim(Artisan::output());
        if ($output !== '') {
            $log($output);
        }
        if ($code === 0) {
            $done[] = 'scrapping:seeders:export (characteristics, item-types, scrapping-mappings)';
            $log('Seeders data : caractéristiques, types item, mappings scrapping exportés.');
        } else {
            $log('Échec partiel : scrapping:seeders:export (code '.$code.').');
        }

        $code = Artisan::call('items:seeder-export', [
            '--versioned' => true,
        ]);
        $output = trim(Artisan::output());
        if ($output !== '') {
            $log($output);
        }
        if ($code === 0) {
            $done[] = 'items:seeder-export --versioned';
            $log('Seeders data : équipements versionnés exportés.');
        } else {
            $log('Échec partiel : items:seeder-export (code '.$code.').');
        }

        return $done;
    }
}
