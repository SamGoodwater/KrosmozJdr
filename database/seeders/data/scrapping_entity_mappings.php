<?php

declare(strict_types=1);

/**
 * Règles de mapping scrapping (DofusDB → Krosmoz). Régénéré par : php artisan scrapping:seeders:export --scrapping-mappings
 */

return [
    0 => [
        'source' => 'dofusdb',
        'entity' => 'breed',
        'mapping_key' => 'dofusdb_id',
        'from_path' => 'id',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 0,
        'targets' => [
            0 => [
                'target_model' => 'breeds',
                'target_field' => 'dofusdb_id',
                'sort_order' => 0,
            ],
        ],
    ],
    1 => [
        'source' => 'dofusdb',
        'entity' => 'breed',
        'mapping_key' => 'name',
        'from_path' => 'shortName',
        'from_lang_aware' => true,
        'characteristic_key' => 'name_object',
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
            1 => [
                'name' => 'truncate',
                'args' => [
                    'max' => 255,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 1,
        'targets' => [
            0 => [
                'target_model' => 'breeds',
                'target_field' => 'name',
                'sort_order' => 0,
            ],
        ],
    ],
    2 => [
        'source' => 'dofusdb',
        'entity' => 'breed',
        'mapping_key' => 'description',
        'from_path' => 'description',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
            1 => [
                'name' => 'truncate',
                'args' => [
                    'max' => 255,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 2,
        'targets' => [
            0 => [
                'target_model' => 'breeds',
                'target_field' => 'description',
                'sort_order' => 0,
            ],
        ],
    ],
    3 => [
        'source' => 'dofusdb',
        'entity' => 'breed',
        'mapping_key' => 'description_fast',
        'from_path' => 'gameplayDescription',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
            1 => [
                'name' => 'truncate',
                'args' => [
                    'max' => 255,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 3,
        'targets' => [
            0 => [
                'target_model' => 'breeds',
                'target_field' => 'description_fast',
                'sort_order' => 0,
            ],
        ],
    ],
    4 => [
        'source' => 'dofusdb',
        'entity' => 'breed',
        'mapping_key' => 'specificity',
        'from_path' => 'specificity',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
            1 => [
                'name' => 'truncate',
                'args' => [
                    'max' => 255,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 4,
        'targets' => [
            0 => [
                'target_model' => 'breeds',
                'target_field' => 'specificity',
                'sort_order' => 0,
            ],
        ],
    ],
    5 => [
        'source' => 'dofusdb',
        'entity' => 'breed',
        'mapping_key' => 'image',
        'from_path' => 'img',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'storeScrappedImage',
                'args' => [
                    'entityFolder' => 'breeds',
                    'idPath' => 'id',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 5,
        'targets' => [
            0 => [
                'target_model' => 'breeds',
                'target_field' => 'image',
                'sort_order' => 0,
            ],
        ],
    ],
    6 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'dofusdb_id',
        'from_path' => 'id',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 0,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'dofusdb_id',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'dofusdb_id',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'dofusdb_id',
                'sort_order' => 2,
            ],
        ],
    ],
    7 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'name',
        'from_path' => 'name',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 1,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'name',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'name',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'name',
                'sort_order' => 2,
            ],
        ],
    ],
    8 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'description',
        'from_path' => 'description',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 2,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'description',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'description',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'description',
                'sort_order' => 2,
            ],
        ],
    ],
    9 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'level',
        'from_path' => 'level',
        'from_lang_aware' => false,
        'characteristic_key' => 'level_object',
        'formatters' => [
            0 => [
                'name' => 'dofusdb_level',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 3,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'level',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'level',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'level',
                'sort_order' => 2,
            ],
        ],
    ],
    10 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'price',
        'from_path' => 'price',
        'from_lang_aware' => false,
        'characteristic_key' => 'price_object',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 4,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'price',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'price',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'price',
                'sort_order' => 2,
            ],
        ],
    ],
    11 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'image',
        'from_path' => 'img',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'storeScrappedImage',
                'args' => [
                    'entityFolder' => 'items',
                    'idPath' => 'id',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 5,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'image',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'image',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'image',
                'sort_order' => 2,
            ],
        ],
    ],
    12 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'typeId',
        'from_path' => 'typeId',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 6,
        'targets' => [
            0 => [
                'target_model' => 'items',
                'target_field' => 'type_id',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'resources',
                'target_field' => 'type_id',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'consumables',
                'target_field' => 'type_id',
                'sort_order' => 2,
            ],
        ],
    ],
    13 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'resource_type_id',
        'from_path' => 'typeId',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'resolveResourceTypeId',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 7,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'resource_type_id',
                'sort_order' => 0,
            ],
        ],
    ],
    71 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'consumable_type_id',
        'from_path' => 'typeId',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'resolveConsumableTypeId',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 8,
        'targets' => [
            0 => [
                'target_model' => 'consumables',
                'target_field' => 'consumable_type_id',
                'sort_order' => 0,
            ],
        ],
    ],
    72 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'item_type_id',
        'from_path' => 'typeId',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'resolveItemTypeId',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 9,
        'targets' => [
            0 => [
                'target_model' => 'items',
                'target_field' => 'item_type_id',
                'sort_order' => 0,
            ],
        ],
    ],
    14 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'weight',
        'from_path' => 'realWeight',
        'from_lang_aware' => false,
        'characteristic_key' => 'weight_object',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 10,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'weight',
                'sort_order' => 0,
            ],
        ],
    ],
    15 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'rarity',
        'from_path' => 'rarity',
        'from_lang_aware' => false,
        'characteristic_key' => 'rarity_object',
        'formatters' => [
            0 => [
                'name' => 'defaultRarityByLevel',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 11,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'rarity',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'rarity',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'rarity',
                'sort_order' => 2,
            ],
        ],
    ],
    16 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'recipe_ingredients',
        'from_path' => 'recipe',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'recipeToResourceRecipe',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 12,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'recipe_ingredients',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'recipe_ingredients',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'recipe_ingredients',
                'sort_order' => 2,
            ],
        ],
    ],
    17 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'effect',
        'from_path' => 'effects',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'itemEffectsToKrosmozBonus',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 13,
        'targets' => [
            0 => [
                'target_model' => 'resources',
                'target_field' => 'effect',
                'sort_order' => 0,
            ],
            1 => [
                'target_model' => 'consumables',
                'target_field' => 'effect',
                'sort_order' => 1,
            ],
            2 => [
                'target_model' => 'items',
                'target_field' => 'effect',
                'sort_order' => 2,
            ],
        ],
    ],
    18 => [
        'source' => 'dofusdb',
        'entity' => 'item',
        'mapping_key' => 'bonus',
        'from_path' => 'effects',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toJson',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 14,
        'targets' => [
            0 => [
                'target_model' => 'items',
                'target_field' => 'bonus',
                'sort_order' => 0,
            ],
        ],
    ],
    19 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'dofusdb_id',
        'from_path' => 'id',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 0,
        'targets' => [
            0 => [
                'target_model' => 'monsters',
                'target_field' => 'dofusdb_id',
                'sort_order' => 0,
            ],
        ],
    ],
    20 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'name',
        'from_path' => 'name',
        'from_lang_aware' => true,
        'characteristic_key' => 'name_object',
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 1,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'name',
                'sort_order' => 0,
            ],
        ],
    ],
    21 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'description',
        'from_path' => 'description',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 2,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'description',
                'sort_order' => 0,
            ],
        ],
    ],
    22 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'level',
        'from_path' => 'grades.0.level',
        'from_lang_aware' => false,
        'characteristic_key' => 'level_creature',
        'formatters' => [
            0 => [
                'name' => 'dofusdb_level',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 3,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'level',
                'sort_order' => 0,
            ],
        ],
    ],
    23 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'life',
        'from_path' => 'grades.0.lifePoints',
        'from_lang_aware' => false,
        'characteristic_key' => 'life_points_creature',
        'formatters' => [
            0 => [
                'name' => 'dofusdb_life',
                'args' => [
                    'levelPath' => 'grades.0.level',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 4,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'life',
                'sort_order' => 0,
            ],
        ],
    ],
    24 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'strength',
        'from_path' => 'grades.0.strength',
        'from_lang_aware' => false,
        'characteristic_key' => 'strength_creature',
        'formatters' => [
            0 => [
                'name' => 'dofusdb_attribute',
                'args' => [
                    'characteristicId' => 'strong',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 5,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'strength',
                'sort_order' => 0,
            ],
        ],
    ],
    25 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'intelligence',
        'from_path' => 'grades.0.intelligence',
        'from_lang_aware' => false,
        'characteristic_key' => 'intelligence_creature',
        'formatters' => [
            0 => [
                'name' => 'dofusdb_attribute',
                'args' => [
                    'characteristicId' => 'intel',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 6,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'intelligence',
                'sort_order' => 0,
            ],
        ],
    ],
    26 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'agility',
        'from_path' => 'grades.0.agility',
        'from_lang_aware' => false,
        'characteristic_key' => 'agility_creature',
        'formatters' => [
            0 => [
                'name' => 'dofusdb_attribute',
                'args' => [
                    'characteristicId' => 'agi',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 7,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'agility',
                'sort_order' => 0,
            ],
        ],
    ],
    27 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'wisdom',
        'from_path' => 'grades.0.wisdom',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampInt',
                'args' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 8,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'wisdom',
                'sort_order' => 0,
            ],
        ],
    ],
    28 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'chance',
        'from_path' => 'grades.0.chance',
        'from_lang_aware' => false,
        'characteristic_key' => 'chance_creature',
        'formatters' => [
            0 => [
                'name' => 'dofusdb_attribute',
                'args' => [
                    'characteristicId' => 'chance',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 9,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'chance',
                'sort_order' => 0,
            ],
        ],
    ],
    29 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'pa',
        'from_path' => 'grades.0.actionPoints',
        'from_lang_aware' => false,
        'characteristic_key' => 'action_points_creature',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampInt',
                'args' => [
                    'min' => 0,
                    'max' => 20,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 10,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'pa',
                'sort_order' => 0,
            ],
        ],
    ],
    30 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'pm',
        'from_path' => 'grades.0.movementPoints',
        'from_lang_aware' => false,
        'characteristic_key' => 'movement_points_creature',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampInt',
                'args' => [
                    'min' => 0,
                    'max' => 20,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 11,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'pm',
                'sort_order' => 0,
            ],
        ],
    ],
    31 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'kamas',
        'from_path' => 'grades.0.kamas',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampInt',
                'args' => [
                    'min' => 0,
                    'max' => 9999999,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 12,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'kamas',
                'sort_order' => 0,
            ],
        ],
    ],
    32 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'po',
        'from_path' => 'grades.0.bonusRange',
        'from_lang_aware' => false,
        'characteristic_key' => 'range_creature',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampInt',
                'args' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 13,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'po',
                'sort_order' => 0,
            ],
        ],
    ],
    33 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'dodge_pa',
        'from_path' => 'grades.0.paDodge',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 14,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'dodge_pa',
                'sort_order' => 0,
            ],
        ],
    ],
    34 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'dodge_pm',
        'from_path' => 'grades.0.pmDodge',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 15,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'dodge_pm',
                'sort_order' => 0,
            ],
        ],
    ],
    35 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'ini',
        'from_path' => 'grades.0.initiative',
        'from_lang_aware' => false,
        'characteristic_key' => 'initiative_creature',
        'formatters' => [
            0 => [
                'name' => 'dofusdb_ini',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 16,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'ini',
                'sort_order' => 0,
            ],
        ],
    ],
    36 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'vitality',
        'from_path' => 'grades.0.vitality',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 17,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'vitality',
                'sort_order' => 0,
            ],
        ],
    ],
    37 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'image',
        'from_path' => 'img',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'storeScrappedImage',
                'args' => [
                    'entityFolder' => 'monsters',
                    'idPath' => 'id',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 18,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'image',
                'sort_order' => 0,
            ],
        ],
    ],
    38 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'size',
        'from_path' => 'size',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'mapSizeToKrosmoz',
                'args' => [
                    'default' => 'medium',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 19,
        'targets' => [
            0 => [
                'target_model' => 'monsters',
                'target_field' => 'size',
                'sort_order' => 0,
            ],
        ],
    ],
    39 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'race',
        'from_path' => 'race',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 20,
        'targets' => [
            0 => [
                'target_model' => 'monsters',
                'target_field' => 'monster_race_id',
                'sort_order' => 0,
            ],
        ],
    ],
    40 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'res_neutre',
        'from_path' => 'grades.0.neutralResistance',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 21,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'res_neutre',
                'sort_order' => 0,
            ],
        ],
    ],
    41 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'res_terre',
        'from_path' => 'grades.0.earthResistance',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 22,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'res_terre',
                'sort_order' => 0,
            ],
        ],
    ],
    42 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'res_feu',
        'from_path' => 'grades.0.fireResistance',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 23,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'res_feu',
                'sort_order' => 0,
            ],
        ],
    ],
    43 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'res_air',
        'from_path' => 'grades.0.airResistance',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 24,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'res_air',
                'sort_order' => 0,
            ],
        ],
    ],
    44 => [
        'source' => 'dofusdb',
        'entity' => 'monster',
        'mapping_key' => 'res_eau',
        'from_path' => 'grades.0.waterResistance',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 25,
        'targets' => [
            0 => [
                'target_model' => 'creatures',
                'target_field' => 'res_eau',
                'sort_order' => 0,
            ],
        ],
    ],
    45 => [
        'source' => 'dofusdb',
        'entity' => 'monster-race',
        'mapping_key' => 'id',
        'from_path' => 'id',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 0,
        'targets' => [
        ],
    ],
    46 => [
        'source' => 'dofusdb',
        'entity' => 'panoply',
        'mapping_key' => 'dofusdb_id',
        'from_path' => 'id',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 0,
        'targets' => [
            0 => [
                'target_model' => 'panoplies',
                'target_field' => 'dofusdb_id',
                'sort_order' => 0,
            ],
        ],
    ],
    47 => [
        'source' => 'dofusdb',
        'entity' => 'panoply',
        'mapping_key' => 'name',
        'from_path' => 'name',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 1,
        'targets' => [
            0 => [
                'target_model' => 'panoplies',
                'target_field' => 'name',
                'sort_order' => 0,
            ],
        ],
    ],
    48 => [
        'source' => 'dofusdb',
        'entity' => 'panoply',
        'mapping_key' => 'description',
        'from_path' => 'description',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 2,
        'targets' => [
            0 => [
                'target_model' => 'panoplies',
                'target_field' => 'description',
                'sort_order' => 0,
            ],
        ],
    ],
    49 => [
        'source' => 'dofusdb',
        'entity' => 'panoply',
        'mapping_key' => 'bonus',
        'from_path' => 'effects',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'itemEffectsToKrosmozBonus',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 3,
        'targets' => [
            0 => [
                'target_model' => 'panoplies',
                'target_field' => 'bonus',
                'sort_order' => 0,
            ],
        ],
    ],
    50 => [
        'source' => 'dofusdb',
        'entity' => 'panoply',
        'mapping_key' => 'item_dofusdb_ids',
        'from_path' => 'items',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'extractItemIds',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 4,
        'targets' => [
            0 => [
                'target_model' => 'panoplies',
                'target_field' => 'item_dofusdb_ids',
                'sort_order' => 0,
            ],
        ],
    ],
    51 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'dofusdb_id',
        'from_path' => 'spell_global.id',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 0,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'dofusdb_id',
                'sort_order' => 0,
            ],
        ],
    ],
    52 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'name',
        'from_path' => 'spell_global.name',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 1,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'name',
                'sort_order' => 0,
            ],
        ],
    ],
    53 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'description',
        'from_path' => 'spell_global.description',
        'from_lang_aware' => true,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'pickLang',
                'args' => [
                    'lang' => 'fr',
                    'fallback' => 'fr',
                ],
            ],
            1 => [
                'name' => 'truncate',
                'args' => [
                    'max' => 255,
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 2,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'description',
                'sort_order' => 0,
            ],
        ],
    ],
    54 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'image',
        'from_path' => 'spell_global.img',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'storeScrappedImage',
                'args' => [
                    'entityFolder' => 'spells',
                    'idPath' => 'id',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 3,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'image',
                'sort_order' => 0,
            ],
        ],
    ],
    55 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'pa',
        'from_path' => 'spell_global.apCost',
        'from_lang_aware' => false,
        'characteristic_key' => 'action_points_spell',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'pa',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 4,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'pa',
                'sort_order' => 0,
            ],
        ],
    ],
    56 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'po',
        'from_path' => 'spell_global.range',
        'from_lang_aware' => false,
        'characteristic_key' => null,
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'spell_range_min_spell',
                ],
            ],
            2 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 5,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'po_min',
                'sort_order' => 0,
            ],
        ],
    ],
    57 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'spell_po_min',
        'from_path' => 'spell_global.minRange',
        'from_lang_aware' => false,
        'characteristic_key' => 'spell_range_min_spell',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'spell_po_min',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 6,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'spell_po_min',
                'sort_order' => 0,
            ],
        ],
    ],
    58 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'spell_po_max',
        'from_path' => 'spell_global.range',
        'from_lang_aware' => false,
        'characteristic_key' => 'spell_range_max_spell',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'spell_po_max',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 7,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'spell_po_max',
                'sort_order' => 0,
            ],
        ],
    ],
    59 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'area',
        'from_path' => 'spell_global.area',
        'from_lang_aware' => false,
        'characteristic_key' => 'area_spell',
        'formatters' => [
            0 => [
                'name' => 'zoneDescrToNotation',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 8,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'area',
                'sort_order' => 0,
            ],
        ],
    ],
    60 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'level',
        'from_path' => 'spell_global.grade',
        'from_lang_aware' => false,
        'characteristic_key' => 'level_spell',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'level',
                ],
            ],
            2 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 9,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'level',
                'sort_order' => 0,
            ],
        ],
    ],
    61 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'cast_per_turn',
        'from_path' => 'spell_global.maxCastPerTurn',
        'from_lang_aware' => false,
        'characteristic_key' => 'cast_per_turn_spell',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'cast_per_turn',
                ],
            ],
            2 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 10,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'cast_per_turn',
                'sort_order' => 0,
            ],
        ],
    ],
    62 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'cast_per_target',
        'from_path' => 'spell_global.maxCastPerTarget',
        'from_lang_aware' => false,
        'characteristic_key' => 'cast_per_target_spell',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'cast_per_target',
                ],
            ],
            2 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 11,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'cast_per_target',
                'sort_order' => 0,
            ],
        ],
    ],
    63 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'sight_line',
        'from_path' => 'spell_global.castTestLos',
        'from_lang_aware' => false,
        'characteristic_key' => 'sight_line_spell',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'sight_line',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 12,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'sight_line',
                'sort_order' => 0,
            ],
        ],
    ],
    64 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'number_between_two_cast',
        'from_path' => 'spell_global.minCastInterval',
        'from_lang_aware' => false,
        'characteristic_key' => 'number_between_two_cast_spell',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'number_between_two_cast',
                ],
            ],
            2 => [
                'name' => 'toString',
                'args' => [
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 13,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'number_between_two_cast',
                'sort_order' => 0,
            ],
        ],
    ],
    66 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'po_editable',
        'from_path' => 'spell_global.rangeCanBeBoosted',
        'from_lang_aware' => false,
        'characteristic_key' => 'range_editable_spell',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'po_editable',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 15,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'po_editable',
                'sort_order' => 0,
            ],
        ],
    ],
    67 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'element',
        'from_path' => 'spell_global.elementId',
        'from_lang_aware' => false,
        'characteristic_key' => 'element_spell',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'element',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 16,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'element',
                'sort_order' => 0,
            ],
        ],
    ],
    68 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'category',
        'from_path' => 'spell_global.categoryId',
        'from_lang_aware' => false,
        'characteristic_key' => 'category_spell',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'category',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 17,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'category',
                'sort_order' => 0,
            ],
        ],
    ],
    69 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'is_magic',
        'from_path' => 'spell_global.isMagic',
        'from_lang_aware' => false,
        'characteristic_key' => 'is_magic_spell',
        'formatters' => [
            0 => [
                'name' => 'toInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'is_magic',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 18,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'is_magic',
                'sort_order' => 0,
            ],
        ],
    ],
    70 => [
        'source' => 'dofusdb',
        'entity' => 'spell',
        'mapping_key' => 'powerful',
        'from_path' => 'spell_global.powerful',
        'from_lang_aware' => false,
        'characteristic_key' => 'power_spell',
        'formatters' => [
            0 => [
                'name' => 'nullableInt',
                'args' => [
                ],
            ],
            1 => [
                'name' => 'clampToCharacteristic',
                'args' => [
                    'characteristicId' => 'powerful',
                ],
            ],
        ],
        'spell_level_aggregation' => null,
        'sort_order' => 19,
        'targets' => [
            0 => [
                'target_model' => 'spells',
                'target_field' => 'powerful',
                'sort_order' => 0,
            ],
        ],
    ],
];
