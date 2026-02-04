<?php

declare(strict_types=1);

/**
 * Groupe creature : monster, class, npc.
 * entity = '*' : défaut pour toutes les entités du groupe.
 * entity = 'monster' | 'class' | 'npc' : surcharge pour une entité précise.
 * [d] = valeur Dofus (import), [level] = niveau créature.
 * Régénéré par : php artisan db:export-seeder-data --characteristics
 *
 * Limites et formules issues de docs/400- Règles (caractéristiques principales/secondaires, conversion D&D→Krosmoz).
 */

return [
    // --- Niveau, vie, ressources de combat ---
    ['characteristic_key' => 'level_creature', 'entity' => '*', 'db_column' => 'level', 'min' => 1, 'max' => 200, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => 'floor([d]/10)', 'sort_order' => 0],
    ['characteristic_key' => 'life_creature', 'entity' => '*', 'db_column' => 'life', 'min' => 1, 'max' => 9999, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => 'floor([d]/200)+[level]*5', 'sort_order' => 1],
    ['characteristic_key' => 'pa_creature', 'entity' => '*', 'db_column' => 'pa', 'min' => 6, 'max' => 12, 'default_value' => '6', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 2],
    ['characteristic_key' => 'pm_creature', 'entity' => '*', 'db_column' => 'pm', 'min' => 3, 'max' => 6, 'default_value' => '3', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 3],
    ['characteristic_key' => 'po_creature', 'entity' => '*', 'db_column' => 'po', 'min' => 0, 'max' => 6, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 4],
    ['characteristic_key' => 'ini_creature', 'entity' => '*', 'db_column' => 'ini', 'min' => 0, 'max' => null, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 5],
    ['characteristic_key' => 'ca_creature', 'entity' => '*', 'db_column' => 'ca', 'min' => 0, 'max' => 26, 'default_value' => '10', 'required' => false, 'validation_message' => null, 'conversion_formula' => '10+floor(([d]-10)/2)', 'sort_order' => 6],
    // --- Caractéristiques principales (score 6–31, mod = floor((score−10)/2)) ---
    ['characteristic_key' => 'vitality_creature', 'entity' => '*', 'db_column' => 'vitality', 'min' => 6, 'max' => 31, 'default_value' => '8', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 10],
    ['characteristic_key' => 'strong_creature', 'entity' => '*', 'db_column' => 'strong', 'min' => 6, 'max' => 31, 'default_value' => '8', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 11],
    ['characteristic_key' => 'agi_creature', 'entity' => '*', 'db_column' => 'agi', 'min' => 6, 'max' => 31, 'default_value' => '8', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 12],
    ['characteristic_key' => 'intel_creature', 'entity' => '*', 'db_column' => 'intel', 'min' => 6, 'max' => 31, 'default_value' => '8', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 13],
    ['characteristic_key' => 'sagesse_creature', 'entity' => '*', 'db_column' => 'sagesse', 'min' => 6, 'max' => 31, 'default_value' => '8', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 14],
    ['characteristic_key' => 'chance_creature', 'entity' => '*', 'db_column' => 'chance', 'min' => 6, 'max' => 31, 'default_value' => '8', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 15],
    // --- Touche, invocations, esquives, fuite, tacle ---
    ['characteristic_key' => 'touch_creature', 'entity' => '*', 'db_column' => 'touch', 'min' => 0, 'max' => 16, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 20],
    ['characteristic_key' => 'invocation_creature', 'entity' => '*', 'db_column' => 'invocation', 'min' => 1, 'max' => 6, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 21],
    ['characteristic_key' => 'dodge_pa_creature', 'entity' => '*', 'db_column' => 'dodge_pa', 'min' => 0, 'max' => 24, 'default_value' => '8', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 22],
    ['characteristic_key' => 'dodge_pm_creature', 'entity' => '*', 'db_column' => 'dodge_pm', 'min' => 0, 'max' => 24, 'default_value' => '8', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 23],
    ['characteristic_key' => 'fuite_creature', 'entity' => '*', 'db_column' => 'fuite', 'min' => 0, 'max' => 21, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 24],
    ['characteristic_key' => 'tacle_creature', 'entity' => '*', 'db_column' => 'tacle', 'min' => 0, 'max' => 21, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 25],
    // --- Résistances fixes (0–10) ---
    ['characteristic_key' => 'res_fixe_neutre_creature', 'entity' => '*', 'db_column' => 'res_fixe_neutre', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 30],
    ['characteristic_key' => 'res_fixe_terre_creature', 'entity' => '*', 'db_column' => 'res_fixe_terre', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 31],
    ['characteristic_key' => 'res_fixe_feu_creature', 'entity' => '*', 'db_column' => 'res_fixe_feu', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 32],
    ['characteristic_key' => 'res_fixe_air_creature', 'entity' => '*', 'db_column' => 'res_fixe_air', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 33],
    ['characteristic_key' => 'res_fixe_eau_creature', 'entity' => '*', 'db_column' => 'res_fixe_eau', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 34],
    // --- Dommages fixes (0–10) ---
    ['characteristic_key' => 'do_fixe_neutre_creature', 'entity' => '*', 'db_column' => 'do_fixe_neutre', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 40],
    ['characteristic_key' => 'do_fixe_terre_creature', 'entity' => '*', 'db_column' => 'do_fixe_terre', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 41],
    ['characteristic_key' => 'do_fixe_feu_creature', 'entity' => '*', 'db_column' => 'do_fixe_feu', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 42],
    ['characteristic_key' => 'do_fixe_air_creature', 'entity' => '*', 'db_column' => 'do_fixe_air', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 43],
    ['characteristic_key' => 'do_fixe_eau_creature', 'entity' => '*', 'db_column' => 'do_fixe_eau', 'min' => 0, 'max' => 10, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 44],
];
