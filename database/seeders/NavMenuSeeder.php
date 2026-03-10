<?php

namespace Database\Seeders;

use App\Services\PageService;
use Illuminate\Database\Seeder;

/**
 * Seeder du menu de navigation.
 *
 * Assure que la structure du menu (config/nav_menu.php) est prise en compte
 * et invalide le cache du menu pour les prochains chargements.
 */
class NavMenuSeeder extends Seeder
{
    public function run(): void
    {
        $bibliotheques = config('nav_menu.bibliotheques', []);
        if (empty($bibliotheques)) {
            $this->command?->warn('NavMenuSeeder : config/nav_menu.php manquant ou vide.');
            return;
        }
        PageService::clearMenuCache();
        $this->command?->info('NavMenuSeeder : cache du menu invalidé.');
    }
}
