<?php

declare(strict_types=1);

/**
 * Règles de mapping scrapping (DofusDB → Krosmoz). Régénéré par : php artisan db:export-seeder-data --scrapping-mappings
 */

return array (
  0 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'breed',
    'mapping_key' => 'dofusdb_id',
    'from_path' => 'id',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 0,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'breeds',
        'target_field' => 'dofusdb_id',
        'sort_order' => 0,
      ),
    ),
  ),
  1 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'breed',
    'mapping_key' => 'name',
    'from_path' => 'name',
    'from_lang_aware' => true,
    'characteristic_key' => 'name_object',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
    ),
    'sort_order' => 1,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'breeds',
        'target_field' => 'name',
        'sort_order' => 0,
      ),
    ),
  ),
  2 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'breed',
    'mapping_key' => 'description',
    'from_path' => 'description',
    'from_lang_aware' => true,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
    ),
    'sort_order' => 2,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'breeds',
        'target_field' => 'description',
        'sort_order' => 0,
      ),
    ),
  ),
  3 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'breed',
    'mapping_key' => 'description_fast',
    'from_path' => 'shortDescription',
    'from_lang_aware' => true,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
      1 => 
      array (
        'name' => 'truncate',
        'args' => 
        array (
          'max' => 255,
        ),
      ),
    ),
    'sort_order' => 3,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'breeds',
        'target_field' => 'description_fast',
        'sort_order' => 0,
      ),
    ),
  ),
  4 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'breed',
    'mapping_key' => 'specificity',
    'from_path' => 'specificity',
    'from_lang_aware' => true,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
      1 => 
      array (
        'name' => 'truncate',
        'args' => 
        array (
          'max' => 255,
        ),
      ),
    ),
    'sort_order' => 4,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'breeds',
        'target_field' => 'specificity',
        'sort_order' => 0,
      ),
    ),
  ),
  5 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'breed',
    'mapping_key' => 'image',
    'from_path' => 'img',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'storeScrappedImage',
        'args' => 
        array (
          'entityFolder' => 'breeds',
          'idPath' => 'id',
        ),
      ),
    ),
    'sort_order' => 5,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'breeds',
        'target_field' => 'image',
        'sort_order' => 0,
      ),
    ),
  ),
  6 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'dofusdb_id',
    'from_path' => 'id',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 0,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'dofusdb_id',
        'sort_order' => 0,
      ),
      1 => 
      array (
        'target_model' => 'consumables',
        'target_field' => 'dofusdb_id',
        'sort_order' => 1,
      ),
      2 => 
      array (
        'target_model' => 'items',
        'target_field' => 'dofusdb_id',
        'sort_order' => 2,
      ),
    ),
  ),
  7 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'name',
    'from_path' => 'name',
    'from_lang_aware' => true,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
    ),
    'sort_order' => 1,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'name',
        'sort_order' => 0,
      ),
      1 => 
      array (
        'target_model' => 'consumables',
        'target_field' => 'name',
        'sort_order' => 1,
      ),
      2 => 
      array (
        'target_model' => 'items',
        'target_field' => 'name',
        'sort_order' => 2,
      ),
    ),
  ),
  8 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'description',
    'from_path' => 'description',
    'from_lang_aware' => true,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
    ),
    'sort_order' => 2,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'description',
        'sort_order' => 0,
      ),
      1 => 
      array (
        'target_model' => 'consumables',
        'target_field' => 'description',
        'sort_order' => 1,
      ),
      2 => 
      array (
        'target_model' => 'items',
        'target_field' => 'description',
        'sort_order' => 2,
      ),
    ),
  ),
  9 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'level',
    'from_path' => 'level',
    'from_lang_aware' => false,
    'characteristic_key' => 'level_object',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'dofusdb_level',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 3,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'level',
        'sort_order' => 0,
      ),
      1 => 
      array (
        'target_model' => 'consumables',
        'target_field' => 'level',
        'sort_order' => 1,
      ),
      2 => 
      array (
        'target_model' => 'items',
        'target_field' => 'level',
        'sort_order' => 2,
      ),
    ),
  ),
  10 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'price',
    'from_path' => 'price',
    'from_lang_aware' => false,
    'characteristic_key' => 'price_object',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 4,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'price',
        'sort_order' => 0,
      ),
      1 => 
      array (
        'target_model' => 'consumables',
        'target_field' => 'price',
        'sort_order' => 1,
      ),
      2 => 
      array (
        'target_model' => 'items',
        'target_field' => 'price',
        'sort_order' => 2,
      ),
    ),
  ),
  11 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'image',
    'from_path' => 'img',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'storeScrappedImage',
        'args' => 
        array (
          'entityFolder' => 'items',
          'idPath' => 'id',
        ),
      ),
    ),
    'sort_order' => 5,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'image',
        'sort_order' => 0,
      ),
      1 => 
      array (
        'target_model' => 'consumables',
        'target_field' => 'image',
        'sort_order' => 1,
      ),
      2 => 
      array (
        'target_model' => 'items',
        'target_field' => 'image',
        'sort_order' => 2,
      ),
    ),
  ),
  12 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'typeId',
    'from_path' => 'typeId',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 6,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'items',
        'target_field' => 'type_id',
        'sort_order' => 0,
      ),
      1 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'type_id',
        'sort_order' => 1,
      ),
    ),
  ),
  13 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'resource_type_id',
    'from_path' => 'typeId',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'resolveResourceTypeId',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 7,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'resource_type_id',
        'sort_order' => 0,
      ),
    ),
  ),
  14 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'weight',
    'from_path' => 'realWeight',
    'from_lang_aware' => false,
    'characteristic_key' => 'weight_object',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 8,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'weight',
        'sort_order' => 0,
      ),
    ),
  ),
  15 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'rarity',
    'from_path' => 'rarity',
    'from_lang_aware' => false,
    'characteristic_key' => 'rarity_object',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'defaultRarityByLevel',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 9,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'rarity',
        'sort_order' => 0,
      ),
      1 => 
      array (
        'target_model' => 'consumables',
        'target_field' => 'rarity',
        'sort_order' => 1,
      ),
      2 => 
      array (
        'target_model' => 'items',
        'target_field' => 'rarity',
        'sort_order' => 2,
      ),
    ),
  ),
  16 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'recipe_ingredients',
    'from_path' => 'recipe',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'recipeToResourceRecipe',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 10,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'resources',
        'target_field' => 'recipe_ingredients',
        'sort_order' => 0,
      ),
    ),
  ),
  17 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'effect',
    'from_path' => 'effects',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'itemEffectsToKrosmozBonus',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 11,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'items',
        'target_field' => 'effect',
        'sort_order' => 0,
      ),
    ),
  ),
  18 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'item',
    'mapping_key' => 'bonus',
    'from_path' => 'effects',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toJson',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 12,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'items',
        'target_field' => 'bonus',
        'sort_order' => 0,
      ),
    ),
  ),
  19 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'dofusdb_id',
    'from_path' => 'id',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 0,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'monsters',
        'target_field' => 'dofusdb_id',
        'sort_order' => 0,
      ),
    ),
  ),
  20 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'name',
    'from_path' => 'name',
    'from_lang_aware' => true,
    'characteristic_key' => 'name_object',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
    ),
    'sort_order' => 1,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'name',
        'sort_order' => 0,
      ),
    ),
  ),
  21 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'description',
    'from_path' => 'description',
    'from_lang_aware' => true,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
    ),
    'sort_order' => 2,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'description',
        'sort_order' => 0,
      ),
    ),
  ),
  22 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'level',
    'from_path' => 'grades.0.level',
    'from_lang_aware' => false,
    'characteristic_key' => 'level_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'dofusdb_level',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 3,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'level',
        'sort_order' => 0,
      ),
    ),
  ),
  23 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'life',
    'from_path' => 'grades.0.lifePoints',
    'from_lang_aware' => false,
    'characteristic_key' => 'life_points_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'dofusdb_life',
        'args' => 
        array (
          'levelPath' => 'grades.0.level',
        ),
      ),
    ),
    'sort_order' => 4,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'life',
        'sort_order' => 0,
      ),
    ),
  ),
  24 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'strength',
    'from_path' => 'grades.0.strength',
    'from_lang_aware' => false,
    'characteristic_key' => 'strength_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'dofusdb_attribute',
        'args' => 
        array (
          'characteristicId' => 'strong',
        ),
      ),
    ),
    'sort_order' => 5,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'strength',
        'sort_order' => 0,
      ),
    ),
  ),
  25 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'intelligence',
    'from_path' => 'grades.0.intelligence',
    'from_lang_aware' => false,
    'characteristic_key' => 'intelligence_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'dofusdb_attribute',
        'args' => 
        array (
          'characteristicId' => 'intel',
        ),
      ),
    ),
    'sort_order' => 6,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'intelligence',
        'sort_order' => 0,
      ),
    ),
  ),
  26 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'agility',
    'from_path' => 'grades.0.agility',
    'from_lang_aware' => false,
    'characteristic_key' => 'agility_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'dofusdb_attribute',
        'args' => 
        array (
          'characteristicId' => 'agi',
        ),
      ),
    ),
    'sort_order' => 7,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'agility',
        'sort_order' => 0,
      ),
    ),
  ),
  27 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'wisdom',
    'from_path' => 'grades.0.wisdom',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampInt',
        'args' => 
        array (
          'min' => 0,
          'max' => 1000,
        ),
      ),
    ),
    'sort_order' => 8,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'wisdom',
        'sort_order' => 0,
      ),
    ),
  ),
  28 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'chance',
    'from_path' => 'grades.0.chance',
    'from_lang_aware' => false,
    'characteristic_key' => 'chance_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'dofusdb_attribute',
        'args' => 
        array (
          'characteristicId' => 'chance',
        ),
      ),
    ),
    'sort_order' => 9,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'chance',
        'sort_order' => 0,
      ),
    ),
  ),
  29 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'pa',
    'from_path' => 'grades.0.actionPoints',
    'from_lang_aware' => false,
    'characteristic_key' => 'action_points_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampInt',
        'args' => 
        array (
          'min' => 0,
          'max' => 20,
        ),
      ),
    ),
    'sort_order' => 10,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'pa',
        'sort_order' => 0,
      ),
    ),
  ),
  30 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'pm',
    'from_path' => 'grades.0.movementPoints',
    'from_lang_aware' => false,
    'characteristic_key' => 'movement_points_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampInt',
        'args' => 
        array (
          'min' => 0,
          'max' => 20,
        ),
      ),
    ),
    'sort_order' => 11,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'pm',
        'sort_order' => 0,
      ),
    ),
  ),
  31 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'kamas',
    'from_path' => 'grades.0.kamas',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampInt',
        'args' => 
        array (
          'min' => 0,
          'max' => 9999999,
        ),
      ),
    ),
    'sort_order' => 12,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'kamas',
        'sort_order' => 0,
      ),
    ),
  ),
  32 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'po',
    'from_path' => 'grades.0.bonusRange',
    'from_lang_aware' => false,
    'characteristic_key' => 'range_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampInt',
        'args' => 
        array (
          'min' => 0,
          'max' => 50,
        ),
      ),
    ),
    'sort_order' => 13,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'po',
        'sort_order' => 0,
      ),
    ),
  ),
  33 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'dodge_pa',
    'from_path' => 'grades.0.paDodge',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 14,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'dodge_pa',
        'sort_order' => 0,
      ),
    ),
  ),
  34 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'dodge_pm',
    'from_path' => 'grades.0.pmDodge',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 15,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'dodge_pm',
        'sort_order' => 0,
      ),
    ),
  ),
  35 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'ini',
    'from_path' => 'grades.0.initiative',
    'from_lang_aware' => false,
    'characteristic_key' => 'initiative_creature',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'dofusdb_ini',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 16,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'ini',
        'sort_order' => 0,
      ),
    ),
  ),
  36 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'vitality',
    'from_path' => 'grades.0.vitality',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 17,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'vitality',
        'sort_order' => 0,
      ),
    ),
  ),
  37 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'image',
    'from_path' => 'img',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'storeScrappedImage',
        'args' => 
        array (
          'entityFolder' => 'monsters',
          'idPath' => 'id',
        ),
      ),
    ),
    'sort_order' => 18,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'image',
        'sort_order' => 0,
      ),
    ),
  ),
  38 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'size',
    'from_path' => 'size',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'mapSizeToKrosmoz',
        'args' => 
        array (
          'default' => 'medium',
        ),
      ),
    ),
    'sort_order' => 19,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'monsters',
        'target_field' => 'size',
        'sort_order' => 0,
      ),
    ),
  ),
  39 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'race',
    'from_path' => 'race',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 20,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'monsters',
        'target_field' => 'monster_race_id',
        'sort_order' => 0,
      ),
    ),
  ),
  40 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'res_neutre',
    'from_path' => 'grades.0.neutralResistance',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 21,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'res_neutre',
        'sort_order' => 0,
      ),
    ),
  ),
  41 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'res_terre',
    'from_path' => 'grades.0.earthResistance',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 22,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'res_terre',
        'sort_order' => 0,
      ),
    ),
  ),
  42 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'res_feu',
    'from_path' => 'grades.0.fireResistance',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 23,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'res_feu',
        'sort_order' => 0,
      ),
    ),
  ),
  43 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'res_air',
    'from_path' => 'grades.0.airResistance',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 24,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'res_air',
        'sort_order' => 0,
      ),
    ),
  ),
  44 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'monster',
    'mapping_key' => 'res_eau',
    'from_path' => 'grades.0.waterResistance',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 25,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'creatures',
        'target_field' => 'res_eau',
        'sort_order' => 0,
      ),
    ),
  ),
  45 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'dofusdb_id',
    'from_path' => 'id',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 0,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'dofusdb_id',
        'sort_order' => 0,
      ),
    ),
  ),
  46 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'name',
    'from_path' => 'name',
    'from_lang_aware' => true,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
    ),
    'sort_order' => 1,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'name',
        'sort_order' => 0,
      ),
    ),
  ),
  47 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'description',
    'from_path' => 'description',
    'from_lang_aware' => true,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'pickLang',
        'args' => 
        array (
          'lang' => 'fr',
          'fallback' => 'fr',
        ),
      ),
    ),
    'sort_order' => 2,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'description',
        'sort_order' => 0,
      ),
    ),
  ),
  48 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'image',
    'from_path' => 'img',
    'from_lang_aware' => false,
    'characteristic_key' => NULL,
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'storeScrappedImage',
        'args' => 
        array (
          'entityFolder' => 'spells',
          'idPath' => 'id',
        ),
      ),
    ),
    'sort_order' => 3,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'image',
        'sort_order' => 0,
      ),
    ),
  ),
  49 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'pa',
    'from_path' => 'levels.0.apCost',
    'from_lang_aware' => false,
    'characteristic_key' => 'action_points_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'pa',
        ),
      ),
    ),
    'sort_order' => 4,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'pa',
        'sort_order' => 0,
      ),
    ),
  ),
  50 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'po',
    'from_path' => 'levels.0.range',
    'from_lang_aware' => false,
    'characteristic_key' => 'range_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'po',
        ),
      ),
      2 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 5,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'po',
        'sort_order' => 0,
      ),
    ),
  ),
  51 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'spell_po_min',
    'from_path' => 'levels.0.range.min',
    'from_lang_aware' => false,
    'characteristic_key' => 'spell_range_min_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'spell_po_min',
        ),
      ),
    ),
    'sort_order' => 6,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'spell_po_min',
        'sort_order' => 0,
      ),
    ),
  ),
  52 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'spell_po_max',
    'from_path' => 'levels.0.range.max',
    'from_lang_aware' => false,
    'characteristic_key' => 'spell_range_max_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'spell_po_max',
        ),
      ),
    ),
    'sort_order' => 7,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'spell_po_max',
        'sort_order' => 0,
      ),
    ),
  ),
  53 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'area',
    'from_path' => 'levels.0.effects.0.zoneDescr.shape',
    'from_lang_aware' => false,
    'characteristic_key' => 'area_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'area',
        ),
      ),
    ),
    'sort_order' => 8,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'area',
        'sort_order' => 0,
      ),
    ),
  ),
  54 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'level',
    'from_path' => 'levels.0.grade',
    'from_lang_aware' => false,
    'characteristic_key' => 'level_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'level',
        ),
      ),
      2 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 9,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'level',
        'sort_order' => 0,
      ),
    ),
  ),
  55 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'cast_per_turn',
    'from_path' => 'levels.0.maxCastPerTurn',
    'from_lang_aware' => false,
    'characteristic_key' => 'cast_per_turn_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'cast_per_turn',
        ),
      ),
      2 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 10,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'cast_per_turn',
        'sort_order' => 0,
      ),
    ),
  ),
  56 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'cast_per_target',
    'from_path' => 'levels.0.maxCastPerTarget',
    'from_lang_aware' => false,
    'characteristic_key' => 'cast_per_target_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'cast_per_target',
        ),
      ),
      2 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 11,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'cast_per_target',
        'sort_order' => 0,
      ),
    ),
  ),
  57 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'sight_line',
    'from_path' => 'levels.0.needLineOfSight',
    'from_lang_aware' => false,
    'characteristic_key' => 'sight_line_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'sight_line',
        ),
      ),
    ),
    'sort_order' => 12,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'sight_line',
        'sort_order' => 0,
      ),
    ),
  ),
  58 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'number_between_two_cast',
    'from_path' => 'levels.0.minCastInterval',
    'from_lang_aware' => false,
    'characteristic_key' => 'number_between_two_cast_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'number_between_two_cast',
        ),
      ),
      2 => 
      array (
        'name' => 'toString',
        'args' => 
        array (
        ),
      ),
    ),
    'sort_order' => 13,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'number_between_two_cast',
        'sort_order' => 0,
      ),
    ),
  ),
  59 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'number_between_two_cast_editable',
    'from_path' => 'levels.0.minCastIntervalEditable',
    'from_lang_aware' => false,
    'characteristic_key' => 'number_between_two_cast_editable_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'number_between_two_cast_editable',
        ),
      ),
    ),
    'sort_order' => 14,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'number_between_two_cast_editable',
        'sort_order' => 0,
      ),
    ),
  ),
  60 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'po_editable',
    'from_path' => 'levels.0.rangeEditable',
    'from_lang_aware' => false,
    'characteristic_key' => 'range_editable_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'po_editable',
        ),
      ),
    ),
    'sort_order' => 15,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'po_editable',
        'sort_order' => 0,
      ),
    ),
  ),
  61 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'element',
    'from_path' => 'elementId',
    'from_lang_aware' => false,
    'characteristic_key' => 'element_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'element',
        ),
      ),
    ),
    'sort_order' => 16,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'element',
        'sort_order' => 0,
      ),
    ),
  ),
  62 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'category',
    'from_path' => 'categoryId',
    'from_lang_aware' => false,
    'characteristic_key' => 'category_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'category',
        ),
      ),
    ),
    'sort_order' => 17,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'category',
        'sort_order' => 0,
      ),
    ),
  ),
  63 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'is_magic',
    'from_path' => 'levels.0.isMagic',
    'from_lang_aware' => false,
    'characteristic_key' => 'is_magic_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'toInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'is_magic',
        ),
      ),
    ),
    'sort_order' => 18,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'is_magic',
        'sort_order' => 0,
      ),
    ),
  ),
  64 => 
  array (
    'source' => 'dofusdb',
    'entity' => 'spell',
    'mapping_key' => 'powerful',
    'from_path' => 'levels.0.powerful',
    'from_lang_aware' => false,
    'characteristic_key' => 'power_spell',
    'formatters' => 
    array (
      0 => 
      array (
        'name' => 'nullableInt',
        'args' => 
        array (
        ),
      ),
      1 => 
      array (
        'name' => 'clampToCharacteristic',
        'args' => 
        array (
          'characteristicId' => 'powerful',
        ),
      ),
    ),
    'sort_order' => 19,
    'targets' => 
    array (
      0 => 
      array (
        'target_model' => 'spells',
        'target_field' => 'powerful',
        'sort_order' => 0,
      ),
    ),
  ),
);
