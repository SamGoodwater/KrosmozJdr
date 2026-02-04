<?php

declare(strict_types=1);

/**
 * Groupe spell : caractéristiques des sorts (propriétés du modèle Spell).
 * entity = '*' ou 'spell' : s'applique au sort.
 * [d] = valeur Dofus (import).
 * Les effets (dégâts, soins, retrait PA/PM, bouclier, états, etc.) sont gérés par SpellEffect + SpellEffectType, pas par les caractéristiques.
 * Régénéré par : php artisan db:export-seeder-data --characteristics
 *
 * Référence : modèle Spell, Creation sort.pdf, SpellEffectType (catégories damage, heal, ap, pm, shield, etc.).
 */

return [
    ['characteristic_key' => 'level_spell', 'entity' => '*', 'db_column' => 'level', 'min' => 1, 'max' => 200, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => 'floor([d]/10)', 'sort_order' => 0],
    ['characteristic_key' => 'pa_spell', 'entity' => '*', 'db_column' => 'pa', 'min' => 0, 'max' => 12, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 1],
    ['characteristic_key' => 'po_spell', 'entity' => '*', 'db_column' => 'po', 'min' => 0, 'max' => null, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 2],
    ['characteristic_key' => 'area_spell', 'entity' => '*', 'db_column' => 'area', 'min' => 0, 'max' => null, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 3],
    ['characteristic_key' => 'element_spell', 'entity' => '*', 'db_column' => 'element', 'min' => 0, 'max' => 28, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 4],
    ['characteristic_key' => 'powerful_spell', 'entity' => '*', 'db_column' => 'powerful', 'min' => 0, 'max' => null, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 5],
    ['characteristic_key' => 'cast_per_turn_spell', 'entity' => '*', 'db_column' => 'cast_per_turn', 'min' => 0, 'max' => null, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 6],
    ['characteristic_key' => 'cast_per_target_spell', 'entity' => '*', 'db_column' => 'cast_per_target', 'min' => 0, 'max' => null, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 7],
    ['characteristic_key' => 'sight_line_spell', 'entity' => '*', 'db_column' => 'sight_line', 'min' => 0, 'max' => 1, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => null, 'sort_order' => 8],
    ['characteristic_key' => 'number_between_two_cast_spell', 'entity' => '*', 'db_column' => 'number_between_two_cast', 'min' => 0, 'max' => null, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 9],
    ['characteristic_key' => 'category_spell', 'entity' => '*', 'db_column' => 'category', 'min' => 0, 'max' => null, 'default_value' => '0', 'required' => false, 'validation_message' => null, 'conversion_formula' => '[d]', 'sort_order' => 10],
    ['characteristic_key' => 'is_magic_spell', 'entity' => '*', 'db_column' => 'is_magic', 'min' => 0, 'max' => 1, 'default_value' => '1', 'required' => false, 'validation_message' => null, 'conversion_formula' => null, 'sort_order' => 11],
];
