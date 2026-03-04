<?php

declare(strict_types=1);

/**
 * Groupe object : item, consumable, resource, panoply.
 * Régénéré par : php artisan scrapping:seeders:export --characteristics
 */

return array (
  0 => 
  array (
    'characteristic_key' => 'level_object',
    'entity' => '*',
    'db_column' => 'level',
    'min' => '1',
    'max' => '20',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '1',
    'conversion_formula' => 'floor([d]/10)',
    'conversion_dofus_sample' => 
    array (
      1 => 3,
      40 => 43,
      80 => 83,
      120 => 125,
      160 => 164,
      200 => 200,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 1,
      4 => 4,
      8 => 8,
      12 => 12,
      16 => 16,
      20 => 20,
    ),
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => NULL,
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  1 => 
  array (
    'characteristic_key' => 'rarity_object',
    'entity' => '*',
    'db_column' => 'rarity',
    'min' => '0',
    'max' => '5',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '{"0":"0","1":"0","3":"0","8":"1","23":"1","characteristic":"level"}',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => NULL,
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  2 => 
  array (
    'characteristic_key' => 'price_object',
    'entity' => '*',
    'db_column' => 'price',
    'min' => '0',
    'max' => NULL,
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => NULL,
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => NULL,
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  3 => 
  array (
    'characteristic_key' => 'weight_object',
    'entity' => '*',
    'db_column' => 'weight',
    'min' => '0',
    'max' => NULL,
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => NULL,
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => 
    array (
      1 => 114,
      40 => 151,
      80 => 233,
      120 => 389,
      160 => 500,
      200 => 500,
    ),
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => NULL,
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  4 => 
  array (
    'characteristic_key' => 'weight_object',
    'entity' => 'resource',
    'db_column' => 'weight',
    'min' => '0',
    'max' => NULL,
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => 
    array (
      1 => 114,
      40 => 151,
      80 => 233,
      120 => 389,
      160 => 500,
      200 => 500,
    ),
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => NULL,
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  5 => 
  array (
    'characteristic_key' => 'hit_bonus_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '5',
    'formula' => '[level] * (5/20)',
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 1,
    'base_price_per_unit' => '1200.00',
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  6 => 
  array (
    'characteristic_key' => 'fixed_damage_neutral_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '5',
    'formula' => '[level] * (5/20)',
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => 
    array (
      1 => 1,
      40 => 3,
      80 => 5,
      120 => 10,
      160 => 15,
      200 => 20,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 1,
      4 => 2,
      8 => 3,
      12 => 4,
      16 => 4,
      20 => 5,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 5,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  7 => 
  array (
    'characteristic_key' => 'fixed_damage_earth_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '5',
    'formula' => '[level] * (5/20)',
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 5,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  8 => 
  array (
    'characteristic_key' => 'fixed_damage_fire_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '5',
    'formula' => '[level] * (5/20)',
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 5,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  9 => 
  array (
    'characteristic_key' => 'fixed_damage_air_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '5',
    'formula' => '[level] * (5/20)',
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 5,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  10 => 
  array (
    'characteristic_key' => 'fixed_damage_water_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '5',
    'formula' => '[level] * (5/20)',
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => 
    array (
      1 => 1,
      40 => 2,
      100 => 10,
      160 => 15,
      200 => 19,
    ),
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 5,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  11 => 
  array (
    'characteristic_key' => 'fixed_damage_multiple_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => '[level] * (3/20)',
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => 
    array (
      1 => 1,
      40 => 2,
      80 => 3,
      120 => 5,
      160 => 15,
      200 => 20,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 0,
      8 => 1,
      12 => 2,
      16 => 3,
      20 => 3,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '900.00',
    'rune_price_per_unit' => '1800.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  12 => 
  array (
    'characteristic_key' => 'life_points_max_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => NULL,
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.4+ 13.8587 * pow(([d]-10)/340, 0.8))',
    'conversion_dofus_sample' => 
    array (
      1 => 10,
      40 => 50,
      80 => 80,
      120 => 150,
      160 => 230,
      200 => 350,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 1,
      4 => 3,
      8 => 5,
      12 => 8,
      16 => 10,
      20 => 15,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 20,
    'base_price_per_unit' => '50.00',
    'rune_price_per_unit' => '100.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  13 => 
  array (
    'characteristic_key' => 'vitality_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => '[level]*(8/20)',
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.0408 * pow([d], 0.9412))',
    'conversion_dofus_sample' => 
    array (
      1 => 16,
      40 => 31,
      80 => 61,
      120 => 136,
      160 => 200,
      200 => 307,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 4,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '600.00',
    'rune_price_per_unit' => '1200.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  14 => 
  array (
    'characteristic_key' => 'wisdom_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.0408 * pow([d], 0.9412))',
    'conversion_dofus_sample' => 
    array (
      1 => 21,
      40 => 11,
      80 => 19,
      120 => 27,
      160 => 33,
      200 => 35,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 4,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '600.00',
    'rune_price_per_unit' => '1200.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  15 => 
  array (
    'characteristic_key' => 'save_vitality_wisdom_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => NULL,
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => '800.00',
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  16 => 
  array (
    'characteristic_key' => 'skills_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '1',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 3,
    'base_price_per_unit' => '400.00',
    'rune_price_per_unit' => '800.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  17 => 
  array (
    'characteristic_key' => 'passive_skills_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '500.00',
    'rune_price_per_unit' => '1000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  18 => 
  array (
    'characteristic_key' => 'initiative_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.1973 * pow([d], 0.4519))',
    'conversion_dofus_sample' => 
    array (
      1 => 10,
      40 => 50,
      80 => 100,
      120 => 200,
      160 => 350,
      200 => 500,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 2,
      16 => 3,
      20 => 3,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 3,
    'base_price_per_unit' => '100.00',
    'rune_price_per_unit' => '200.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  19 => 
  array (
    'characteristic_key' => 'strength_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.0408 * pow([d], 0.9412))',
    'conversion_dofus_sample' => 
    array (
      1 => 7,
      40 => 16,
      80 => 24,
      120 => 38,
      160 => 42,
      200 => 48,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 4,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '500.00',
    'rune_price_per_unit' => '1000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  20 => 
  array (
    'characteristic_key' => 'intelligence_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.0408 * pow([d], 0.9412))',
    'conversion_dofus_sample' => 
    array (
      1 => 7,
      40 => 15,
      80 => 29,
      120 => 38,
      160 => 40,
      200 => 44,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 4,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '500.00',
    'rune_price_per_unit' => '1000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  21 => 
  array (
    'characteristic_key' => 'chance_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.0408 * pow([d], 0.9412))',
    'conversion_dofus_sample' => 
    array (
      1 => 7,
      40 => 15,
      80 => 29,
      120 => 38,
      160 => 43,
      200 => 50,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 4,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '500.00',
    'rune_price_per_unit' => '1000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  22 => 
  array (
    'characteristic_key' => 'agility_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.0408 * pow([d], 0.9412))',
    'conversion_dofus_sample' => 
    array (
      1 => 8,
      40 => 15,
      80 => 30,
      120 => 36,
      160 => 42,
      200 => 46,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 4,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '500.00',
    'rune_price_per_unit' => '1000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  23 => 
  array (
    'characteristic_key' => 'save_strength_intelligence_chance_agility_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  24 => 
  array (
    'characteristic_key' => 'action_points_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '6',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => 
    array (
      1 => 1,
      40 => 1,
      80 => 1,
      120 => 1,
      160 => 1,
      200 => 1,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 6,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 1,
    'base_price_per_unit' => '1300.00',
    'rune_price_per_unit' => '2600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  25 => 
  array (
    'characteristic_key' => 'dodge_action_points_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.1202 * [d] + 0.5109)',
    'conversion_dofus_sample' => 
    array (
      1 => 23,
      40 => 3,
      80 => 4,
      120 => 2,
      160 => 7,
      200 => 7,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 5,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '300.00',
    'rune_price_per_unit' => '600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  26 => 
  array (
    'characteristic_key' => 'movement_points_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => 
    array (
      1 => 0,
      40 => 1,
      80 => 1,
      120 => 1,
      160 => 1,
      200 => 1,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 0,
      8 => 1,
      12 => 2,
      16 => 3,
      20 => 3,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 1,
    'base_price_per_unit' => '1000.00',
    'rune_price_per_unit' => '2000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  27 => 
  array (
    'characteristic_key' => 'dodge_movement_points_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.1202 * [d] + 0.5109)',
    'conversion_dofus_sample' => 
    array (
      1 => 2,
      40 => 5,
      80 => 7,
      120 => 10,
      160 => 17,
      200 => 25,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 2,
      16 => 3,
      20 => 3,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '300.00',
    'rune_price_per_unit' => '600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  28 => 
  array (
    'characteristic_key' => 'summoning_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '5',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => 
    array (
      1 => 1,
      40 => 1,
      80 => 1,
      120 => 1,
      160 => 1,
      200 => 1,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 1,
      8 => 2,
      12 => 3,
      16 => 4,
      20 => 5,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 1,
    'base_price_per_unit' => '800.00',
    'rune_price_per_unit' => '1600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  29 => 
  array (
    'characteristic_key' => 'range_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '6',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 1,
    'base_price_per_unit' => '800.00',
    'rune_price_per_unit' => '1600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  30 => 
  array (
    'characteristic_key' => 'tackle_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(1.1 + 2* pow(([d]-1)/12, 0.6))',
    'conversion_dofus_sample' => 
    array (
      1 => 1,
      40 => 2,
      80 => 5,
      120 => 6,
      160 => 8,
      200 => 15,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 1,
      4 => 2,
      8 => 4,
      12 => 5,
      16 => 7,
      20 => 8,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '300.00',
    'rune_price_per_unit' => '600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  31 => 
  array (
    'characteristic_key' => 'dodge_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(1.1 + 2* pow(([d]-1)/12, 0.6))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '300.00',
    'rune_price_per_unit' => '600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  32 => 
  array (
    'characteristic_key' => 'wakfu_recharge_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => '1500.00',
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  33 => 
  array (
    'characteristic_key' => 'armor_class_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '5',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '[d]',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => '1100.00',
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  34 => 
  array (
    'characteristic_key' => 'fixed_resistance_neutral_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '7',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6))',
    'conversion_dofus_sample' => 
    array (
      1 => 1,
      40 => 2,
      80 => 4,
      120 => 6,
      160 => 8,
      200 => 10,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 1,
      4 => 3,
      8 => 4,
      12 => 5,
      16 => 6,
      20 => 7,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 3,
    'base_price_per_unit' => '600.00',
    'rune_price_per_unit' => '1200.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  35 => 
  array (
    'characteristic_key' => 'fixed_resistance_earth_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '7',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6))',
    'conversion_dofus_sample' => 
    array (
      1 => 26,
      40 => 3,
      80 => 5,
      120 => 10,
      160 => 8,
      200 => 14,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 2,
      8 => 4,
      12 => 6,
      16 => 8,
      20 => 10,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 3,
    'base_price_per_unit' => '600.00',
    'rune_price_per_unit' => '1200.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  36 => 
  array (
    'characteristic_key' => 'fixed_resistance_fire_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '7',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6))',
    'conversion_dofus_sample' => 
    array (
      1 => 26,
      40 => 3,
      80 => 5,
      120 => 11,
      160 => 8,
      200 => 15,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 2,
      8 => 4,
      12 => 6,
      16 => 8,
      20 => 10,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 3,
    'base_price_per_unit' => '600.00',
    'rune_price_per_unit' => '1200.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  37 => 
  array (
    'characteristic_key' => 'fixed_resistance_air_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '7',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6))',
    'conversion_dofus_sample' => 
    array (
      1 => 26,
      40 => 3,
      80 => 5,
      120 => 10,
      160 => 9,
      200 => 15,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 2,
      8 => 4,
      12 => 6,
      16 => 8,
      20 => 10,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 3,
    'base_price_per_unit' => '600.00',
    'rune_price_per_unit' => '1200.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  38 => 
  array (
    'characteristic_key' => 'fixed_resistance_water_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '7',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6))',
    'conversion_dofus_sample' => 
    array (
      1 => 26,
      40 => 3,
      80 => 5,
      120 => 10,
      160 => 8,
      200 => 16,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 2,
      8 => 4,
      12 => 6,
      16 => 8,
      20 => 10,
    ),
    'forgemagie_allowed' => true,
    'forgemagie_max' => 3,
    'base_price_per_unit' => '600.00',
    'rune_price_per_unit' => '1200.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  39 => 
  array (
    'characteristic_key' => 'resistance_50_percent_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '1',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '{"0":"0","80":"1","95":"0","characteristic":"d"}',
    'conversion_dofus_sample' => 
    array (
      1 => -1,
      40 => 3,
      80 => 3,
      120 => 5,
      160 => 6,
      200 => 6,
    ),
    'conversion_krosmoz_sample' => 
    array (
      1 => 0,
      4 => 0,
      8 => 0,
      12 => 0,
      16 => 0,
      20 => 0,
    ),
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => '2500.00',
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  40 => 
  array (
    'characteristic_key' => 'invulnerability_100_percent_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '1',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => '{"1":"0","95":"1","characteristic":"d"}',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => false,
    'forgemagie_max' => 0,
    'base_price_per_unit' => '5000.00',
    'rune_price_per_unit' => NULL,
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  41 => 
  array (
    'characteristic_key' => 'all_damage_bonus_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '6',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '900.00',
    'rune_price_per_unit' => '1800.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  42 => 
  array (
    'characteristic_key' => 'critical_hit_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.1202 * [d] + 0.5109)',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 1,
    'base_price_per_unit' => '1200.00',
    'rune_price_per_unit' => '2400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  43 => 
  array (
    'characteristic_key' => 'power_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.0408 * pow([d], 0.9412))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  44 => 
  array (
    'characteristic_key' => 'magic_find_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '8',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.0408 * pow([d], 0.9412))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '500.00',
    'rune_price_per_unit' => '1000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  45 => 
  array (
    'characteristic_key' => 'heal_bonus_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '6',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  46 => 
  array (
    'characteristic_key' => 'reflect_damage_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '4',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.1202 * [d] + 0.5109)',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '800.00',
    'rune_price_per_unit' => '1600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  47 => 
  array (
    'characteristic_key' => 'ap_reduction_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.1202 * [d] + 0.5109)',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  48 => 
  array (
    'characteristic_key' => 'mp_reduction_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.1202 * [d] + 0.5109)',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '700.00',
    'rune_price_per_unit' => '1400.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  49 => 
  array (
    'characteristic_key' => 'push_damage_bonus_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '6',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '800.00',
    'rune_price_per_unit' => '1600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  50 => 
  array (
    'characteristic_key' => 'push_damage_reduction_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '6',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '800.00',
    'rune_price_per_unit' => '1600.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  51 => 
  array (
    'characteristic_key' => 'critical_damage_bonus_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '6',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '1000.00',
    'rune_price_per_unit' => '2000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  52 => 
  array (
    'characteristic_key' => 'critical_damage_reduction_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '6',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7))',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 2,
    'base_price_per_unit' => '1000.00',
    'rune_price_per_unit' => '2000.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
  53 => 
  array (
    'characteristic_key' => 'received_damage_multiplier_distance_object',
    'entity' => '*',
    'db_column' => NULL,
    'min' => '0',
    'max' => '3',
    'formula' => NULL,
    'formula_display' => NULL,
    'default_value' => '0',
    'conversion_formula' => 'floor(0.1202 * [d] + 0.5109)',
    'conversion_dofus_sample' => NULL,
    'conversion_krosmoz_sample' => NULL,
    'forgemagie_allowed' => true,
    'forgemagie_max' => 1,
    'base_price_per_unit' => '900.00',
    'rune_price_per_unit' => '1800.00',
    'value_available' => NULL,
    'item_type_ids' => 
    array (
    ),
  ),
);
