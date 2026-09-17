<?php

declare(strict_types=1);

namespace App\Console\Concerns;

use App\Console\Commands\Pages\SyncBibliothequeEntityPagesCommand;
use Illuminate\Support\Facades\Artisan;

/**
 * Enchaîne {@see SyncBibliothequeEntityPagesCommand}.
 */
trait RunsBibliothequeEntityPagesSync
{
    /**
     * Synchronise les sous-pages menu Bibliothèques (classes / spécialisations).
     */
    protected function runBibliothequeEntityPagesSync(): bool
    {
        $this->line('  → pages:sync-bibliotheque-entities');
        $code = Artisan::call('pages:sync-bibliotheque-entities');
        $this->output->write(Artisan::output());

        return $code === 0;
    }
}
