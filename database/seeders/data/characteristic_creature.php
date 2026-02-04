<?php

declare(strict_types=1);

/**
 * Groupe creature : monster, class, npc.
 * entity = '*' : s'applique à toutes les entités du groupe (défaut).
 * entity = 'monster' (ou autre) : surcharge pour une entité précise.
 * Régénéré par : php artisan db:export-seeder-data --characteristics
 */

return [
    ['characteristic_key' => 'level_creature', 'entity' => '*', 'db_column' => 'level', 'min' => 1, 'max' => 200, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => 'floor([d]/10)', 'sort_order' => 0],
    ['characteristic_key' => 'life_creature', 'entity' => '*', 'db_column' => 'life', 'min' => 1, 'max' => 9999, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => 'floor([d]/200)+[level]*5', 'sort_order' => 1],
    ['characteristic_key' => 'ini_creature', 'entity' => '*', 'db_column' => 'ini', 'min' => 0, 'max' => 9999, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 2],
];
