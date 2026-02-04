<?php

declare(strict_types=1);

/**
 * Table générale characteristics : une ligne = une caractéristique (id unique).
 * Clé unique ; même nom possible selon le groupe (ex. level_creature, level_object).
 * Régénéré par : php artisan db:export-seeder-data --characteristics
 */

return [
    ['key' => 'level_object', 'name' => 'Niveau', 'short_name' => 'Niv.', 'helper' => 'Niveau (1–20).', 'descriptions' => null, 'icon' => null, 'color' => null, 'unit' => null, 'type' => 'int', 'sort_order' => 0],
    ['key' => 'rarity_object', 'name' => 'Rareté', 'short_name' => 'Rar.', 'helper' => 'Indice de rareté (0–4).', 'descriptions' => null, 'icon' => null, 'color' => null, 'unit' => null, 'type' => 'int', 'sort_order' => 1],
    ['key' => 'price_object', 'name' => 'Prix', 'short_name' => 'Prix', 'helper' => 'Prix en kamas.', 'descriptions' => null, 'icon' => null, 'color' => null, 'unit' => 'kamas', 'type' => 'int', 'sort_order' => 2],
    ['key' => 'weight_object', 'name' => 'Poids', 'short_name' => 'Poids', 'helper' => 'Poids en pods.', 'descriptions' => null, 'icon' => null, 'color' => null, 'unit' => 'pods', 'type' => 'int', 'sort_order' => 3],
    ['key' => 'level_creature', 'name' => 'Niveau', 'short_name' => 'Niv.', 'helper' => 'Niveau créature.', 'descriptions' => null, 'icon' => null, 'color' => null, 'unit' => null, 'type' => 'int', 'sort_order' => 0],
    ['key' => 'life_creature', 'name' => 'Vie', 'short_name' => 'Vie', 'helper' => 'Points de vie.', 'descriptions' => null, 'icon' => null, 'color' => null, 'unit' => null, 'type' => 'int', 'sort_order' => 1],
    ['key' => 'ini_creature', 'name' => 'Initiative', 'short_name' => 'Ini', 'helper' => 'Initiative.', 'descriptions' => null, 'icon' => null, 'color' => null, 'unit' => null, 'type' => 'int', 'sort_order' => 2],
];
