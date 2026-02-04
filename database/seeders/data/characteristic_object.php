<?php

declare(strict_types=1);

/**
 * Groupe object : item, consumable, resource, panoply.
 * entity = '*' : s'applique à toutes les entités du groupe (défaut).
 * entity = 'resource' (ou autre) : surcharge pour une entité précise.
 * Régénéré par : php artisan db:export-seeder-data --characteristics
 */

return [
    ['characteristic_key' => 'level_object', 'entity' => '*', 'db_column' => 'level', 'min' => 1, 'max' => 200, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => 'floor([d]/10)', 'sort_order' => 0],
    ['characteristic_key' => 'level_object', 'entity' => 'resource', 'db_column' => 'level', 'min' => 1, 'max' => 20, 'default_value' => '1', 'required' => false, 'validation_message' => 'Le niveau doit être entre :min et :max.', 'conversion_formula' => 'floor([d]/10)', 'sort_order' => 0],
    ['characteristic_key' => 'rarity_object', 'entity' => '*', 'db_column' => 'rarity', 'min' => 0, 'max' => 4, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '{"characteristic":"level","0":0,"3":1,"7":2,"10":3,"17":4}', 'sort_order' => 1],
    ['characteristic_key' => 'price_object', 'entity' => 'resource', 'db_column' => 'price', 'min' => 0, 'max' => null, 'default_value' => '1', 'required' => false, 'validation_message' => 'Le prix doit être >= :min.', 'conversion_formula' => null, 'sort_order' => 2],
    ['characteristic_key' => 'weight_object', 'entity' => 'resource', 'db_column' => 'weight', 'min' => 0, 'max' => null, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => null, 'sort_order' => 3],
];
