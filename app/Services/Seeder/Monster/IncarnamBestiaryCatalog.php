<?php

declare(strict_types=1);

namespace App\Services\Seeder\Monster;

/**
 * Alias du JSON Incarnam (`entities/monsters/incarnam.json`).
 *
 * @example $catalog = IncarnamBestiaryCatalog::load();
 */
final class IncarnamBestiaryCatalog
{
    public static function load(): BestiaryCatalog
    {
        return BestiaryCatalog::load(BestiaryCatalog::filePath('incarnam.json'));
    }
}
