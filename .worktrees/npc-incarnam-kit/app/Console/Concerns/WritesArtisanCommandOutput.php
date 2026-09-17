<?php

declare(strict_types=1);

namespace App\Console\Concerns;

use Illuminate\Support\Facades\Artisan;

/**
 * Affiche la sortie d'une commande Artisan appelée depuis un seeder.
 */
trait WritesArtisanCommandOutput
{
    protected function writeArtisanCommandOutput(): void
    {
        $text = trim(Artisan::output());
        if ($text === '' || $this->command === null) {
            return;
        }

        $this->command->getOutput()->writeln($text);
    }
}
