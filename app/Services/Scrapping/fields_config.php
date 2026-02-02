<?php

// Types d'objets de l'API DofusDB
const TYPES = [
    1 => 'weapons',
    2 => 'armors',
    3 => 'shields',
    4 => 'sticks',
    5 => 'daggers',
    6 => 'swords',
    7 => 'hammers',
    8 => 'picks',
    19 => 'axes',
    20 => 'tools',
    9 => 'rings',
    10 => 'amulets',
    11 => 'belts',
    12 => 'potions',
    13 => 'experience_scrolls',
    14 => 'donation_items',
    15 => 'resources',
    35 => 'flowers',
    16 => 'hats',
    17 => 'cloaks',
    18 => 'pets',
    205 => 'mounts',
    26 => 'certificates',
    9 => 'accessories',
    12 => 'consumables',
    16 => 'equipment',
    203 => 'special',
];

const TYPES_RESOURCES = [
    15 => 'resources',
    35 => 'flowers',
];

const TYPES_EQUIPMENT = [
    16 => 'hats',
    17 => 'cloaks',
    18 => 'pets',
    205 => 'accessories',
    26 => 'certificates',
    16 => 'hats',
    17 => 'cloaks',
    18 => 'pets',
    205 => 'accessories',
];

const TYPES_CONSUMABLES = [
    12 => 'potions',
    13 => 'experience_scrolls',
    14 => 'donation_items',
];

return [
    // ENDPOINTS
    'endpoints' => [
        'breeds' => [
            'endpoint' => '/breeds',
            'enabled' => true,
            'entity' => 'breed',
        ],
        'monsters' => [
            'endpoint' => '/monsters',
            'enabled' => true,
            'entity' => 'monstre',
        ],
        'items' => [
            'endpoint' => '/items&type={type}',
            'filters' => [
                'type' => TYPES_EQUIPMENT,
            ],
            'enabled' => true,
            'entity' => 'item',
        ],
        'items' => [
            'endpoint' => '/items&type={type}',
            'filters' => [
                'type' => TYPES_CONSUMABLES,
            ],
            'enabled' => true,
            'entity' => 'consumable',
        ],
        'items' => [
            'endpoint' => '/items&type={type}',
            'filters' => [
                'type' => TYPES_RESOURCES,
            ],
            'enabled' => true,
            'entity' => 'resource',
        ],
        'spells' => [
            'endpoint' => '/spells',
            'enabled' => true,
            'entity' => 'spell',
        ],
        'effects' => [
            'endpoint' => '/effects',
            'enabled' => true,
            'entity' => null,
        ],
        'item_sets' => [
            'endpoint' => '/item-sets',
            'enabled' => true,
            'entity' => 'panoply',
        ],
        
    ],

    // ENTITIES
    'entities' => [
        // ATTRIBUTES
        'attribute' => [
            'collect' => false
        ],

        // CAMPAIGN
        'Campaing' => [
            'collect' => false
        ],

        // CAPABILITY
        'capability' => [
            'collect' => false
        ],

        // BREED (classe jouable)
        'breed' => [
            'collect' => true,
            'fields' => [
                'official_id' => [
                    'field_name' => 'official_id',
                    'convert' => false,
                ],
                'dofusdb_id' => [
                    'field_name' => 'dofusdb_id',
                    'convert' => false,
                ],
                'name' => [
                    'field_name' => 'name',
                    'convert' => false,
                ],
                'description_fast' => [
                    'field_name' => 'description_fast',
                    'convert' => false,
                ],
                'description' => [
                    'field_name' => 'description',
                    'convert' => false,
                ],
                'life' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'life_dice' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'specificity' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'dofus_version' => [
                    'field_name' => 'dofus_version',
                    'convert' => false,
                ],
                'image' => [
                    'field_name' => 'img',
                    'convert' => false,
                ],
                'icon' => [
                    'field_name' => 'icon',
                    'convert' => false,
                ],
            ],
        ],

        // CONSUMABLE
        'consumable' => [
            'collect' => true,
            'fields' => [
                'official_id' => [
                    'field_name' => 'official_id',
                    'convert' => false,
                ],
                'dofusdb_id' => [
                    'field_name' => 'dofusdb_id',
                    'convert' => false,
                ],
                'name' => [
                    'field_name' => 'name',
                    'convert' => false,
                ],
                'description' => [
                    'field_name' => 'description',
                    'convert' => false,
                ],
                'effect' => [
                    'field_name' => 'effects',
                    'convert' => true,
                ],
                'level' => [
                    'field_name' => 'level',
                    'convert' => true,
                ],
                'rarity' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'recipe' => [
                    'field_name' => 'items&type={type}',
                    'filters' => [
                        'type' => TYPES_RESOURCES,
                    ],
                    'convert' => true,
                ],
                'price' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'dofus_version' => [
                    'field_name' => 'dofus_version',
                    'convert' => false,
                ],
                'image' => [
                    'field_name' => 'img',
                    'convert' => false,
                ],
                'consumable_type_id' => [
                    'field_name' => 'consumable_type_id',
                    'convert' => false,
                ],
            ],
        ],

        // ITEM
        'item' => [
            'collect' => true,
            'fields' => [
                'official_id' => [
                    'field_name' => 'official_id',
                    'convert' => false,
                ],
                'dofus_version' => [
                    'field_name' => 'dofus_version',
                    'convert' => false,
                ],
                'name' => [
                    'field_name' => 'name',
                    'convert' => false,
                ],
                'level' => [
                    'field_name' => 'level',
                    'convert' => false,
                ],
                'description' => [
                    'field_name' => 'description',
                    'convert' => false,
                ],
                'effect' => [
                    'field_name' => 'effects',
                    'convert' => true,
                ],
                'bonus' => [
                    'field_name' => 'bonus',
                    'convert' => false,
                ],
                'recipe' => [
                    'field_name' => 'items&type={type}',
                    'filters' => [
                        'type' => TYPES_RESOURCES,
                    ],
                    'convert' => true,
                ],
                'price' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'rarity' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'dofus_version' => [
                    'field_name' => 'dofus_version',
                    'convert' => false,
                ],
                'image' => [
                    'field_name' => 'img',
                    'convert' => false,
                ],
                'item_type_id' => [
                    'field_name' => 'item_type_id',
                    'convert' => false,
                ],
            ],
        ],

        // MONSTER
        'monster' => [
            'collect' => true,
            'fields' => [
                'official_id' => [
                    'field_name' => 'official_id',
                    'convert' => false,
                ],
                'name' => [
                    'field_name' => 'name',
                    'convert' => false,
                ],
                'description' => [
                    'field_name' => 'description',
                    'convert' => false,
                ],
                'hostility' => [
                    'field_name' => 'hostility',
                    'convert' => false,
                ],
                'location' => [
                    'field_name' => 'location',
                    'convert' => false,
                ],
                'level' => [
                    'field_name' => 'level',
                    'convert' => false,
                ],
                'other_info' => [
                    'field_name' => 'other_info',
                    'convert' => false,
                ],
                'life' => [
                    'field_name' => 'life',
                    'convert' => true,
                ],
                'pa' => [
                    'field_name' => 'pa',
                    'convert' => true,
                ],
                'pm' => [
                    'field_name' => 'pm',
                    'convert' => true,
                ],
                'po' => [
                    'field_name' => 'po',
                    'convert' => true,
                ],
                'ini' => [
                    'field_name' => 'initiative',
                    'convert' => true,
                ],
                'invocation' => [
                    'field_name' => 'invocation',
                    'convert' => true,
                ],
                'touch' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'ca' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'dodge_pa' => [
                    'field_name' => 'dodge_pa',
                    'convert' => true,
                ],
                'dodge_pm' => [
                    'field_name' => 'dodge_pm',
                    'convert' => true,
                ],
                'fuite' => [
                    'field_name' => 'fuite',
                    'convert' => true,
                ],
                'tacle' => [
                    'field_name' => 'tacle',
                    'convert' => true,
                ],
                'vitality' => [
                    'field_name' => 'vitality',
                    'convert' => true,
                ],
                'sagesse' => [
                    'field_name' => 'sagesse',
                    'convert' => true,
                ],
                'strong' => [
                    'field_name' => 'strong',
                    'convert' => true,
                ],
                'intel' => [
                    'field_name' => 'intel',
                    'convert' => true,
                ],
                'agi' => [
                    'field_name' => 'agi',
                    'convert' => true,
                ],
                'chance' => [
                    'field_name' => 'chance',
                    'convert' => true,
                ],
                'do_fixe_neutre' => [
                    'field_name' => 'do_fixe_neutre',
                    'convert' => true,
                ],
                'do_fixe_terre' => [
                    'field_name' => 'do_fixe_terre',
                    'convert' => true,
                ],
                'do_fixe_feu' => [
                    'field_name' => 'do_fixe_feu',
                    'convert' => true,
                ],
                'do_fixe_air' => [
                    'field_name' => 'do_fixe_air',
                    'convert' => true,
                ],
                'do_fixe_eau' => [
                    'field_name' => 'do_fixe_eau',
                    'convert' => true,
                ],
                'res_fixe_neutre' => [
                    'field_name' => 'res_fixe_neutre',
                    'convert' => true,
                ],
                'res_fixe_terre' => [
                    'field_name' => 'res_fixe_terre',
                    'convert' => true,
                ],
                'res_fixe_feu' => [
                    'field_name' => 'res_fixe_feu',
                    'convert' => true,
                ],
                'res_fixe_air' => [
                    'field_name' => 'res_fixe_air',
                    'convert' => true,
                ],
                'res_fixe_eau' => [
                    'field_name' => 'res_fixe_eau',
                    'convert' => true,
                ],
                'res_neutre' => [
                    'field_name' => 'res_neutre',
                    'convert' => true,
                ],
                'res_terre' => [
                    'field_name' => 'res_terre',
                    'convert' => true,
                ],
                'res_feu' => [
                    'field_name' => 'res_feu',
                    'convert' => true,
                ],
                'res_air' => [
                    'field_name' => 'res_air',
                    'convert' => true,
                ],
                'res_eau' => [
                    'field_name' => 'res_eau',
                    'convert' => true,
                ],
                'acrobatie_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'discretion_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'escamotage_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'athletisme_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'intimidation_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'arcane_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'histoire_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'investigation_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'nature_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'religion_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'dressage_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'medecine_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'perception_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'perspicacite_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'survie_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'persuasion_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'representation_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'supercherie_bonus' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'acrobatie_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'discretion_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'escamotage_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'athletisme_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'intimidation_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'arcane_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'histoire_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'investigation_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'nature_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'religion_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'dressage_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'medecine_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'perception_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'perspicacite_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'survie_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'persuasion_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'representation_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'supercherie_mastery' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'kamas' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'drop_' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'other_item' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'other_consumable' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'other_resource' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'other_spell' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'image' => [
                    'field_name' => 'img',
                    'convert' => false,
                ],
                'official_id' => [
                    'field_name' => 'official_id',
                    'convert' => false,
                ],
                'dofusdb_id' => [
                    'field_name' => 'dofusdb_id',
                    'convert' => false,
                ],
                'dofus_version' => [
                    'field_name' => 'dofus_version',
                    'convert' => false,
                ],
                'size' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'is_boss' => [
                    'field_name' => "is_boss",
                    'convert' => false,
                ],
                'boss_pa' => [
                    'field_name' => null,
                    'convert' => true,
                ],
                'monster_race_id' => [
                    'field_name' => 'monster_race_id',
                    'convert' => false,
                ],
            ],
        ],

        // NPC
        'npc' => [
            'collect' => false
        ],

        // PANOPLY
        'panoply' => [
            'collect' => true,
            'fields' => [
                'official_id' => [
                    'field_name' => 'official_id',
                    'convert' => false,
                ],
                'name' => [
                    'field_name' => 'name',
                    'convert' => false,
                ],
                'description' => [
                    'field_name' => 'description',
                    'convert' => false,
                ],
                'bonus' => [
                    'field_name' => 'bonus',
                    'convert' => false,
                ],
            ],
        ],

        // RESSOURCE
        'resource' => [
            'collect' => true,
            'fields' => [
                'dofusdb_id' => [
                    'field_name' => 'dofusdb_id',
                    'convert' => false,
                ],
                'official_id' => [
                    'field_name' => 'official_id',
                    'convert' => false,
                ],
                'name' => [
                    'field_name' => 'name',
                    'convert' => false,
                ],
                'description' => [
                    'field_name' => 'description',
                    'convert' => false,
                ],
                'level' => [
                    'field_name' => 'level',
                    'convert' => true,
                ],
                'price' => [
                    'field_name' => 'price',
                    'convert' => false,
                ],
                'weight' => [
                    'field_name' => 'weight',
                    'convert' => true,
                ],
                'rarity' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'dofus_version' => [
                    'field_name' => 'dofus_version',
                    'convert' => false,
                ],
                'image' => [
                    'field_name' => 'img',
                    'convert' => false,
                ],
                'resource_type_id' => [
                    'field_name' => 'resource_type_id',
                    'convert' => false,
                ],
            ],
        ],

        // SCENARIO
        'scenario' => [
            'collect' => false
        ],

        // SHOP
        'shop' => [
            'collect' => false
        ],

        // SPECIALIZATION
        'specialization' => [
            'collect' => false,
        ],

        // SPELL
        'spell' => [
            'collect' => true,
            'fields' => [
                'official_id' => [
                    'field_name' => 'official_id',
                    'convert' => false,
                ],
                'dofusdb_id' => [
                    'field_name' => 'dofusdb_id',
                    'convert' => false,
                ],
                'name' => [
                    'field_name' => 'name',
                    'convert' => false,
                ],
                'description' => [
                    'field_name' => 'description',
                    'convert' => false,
                ],
                'effect' => [
                    'field_name' => 'effect',
                    'convert' => true,
                ],
                'area' => [
                    'field_name' => 'area',
                    'convert' => true,
                ],
                'level' => [
                    'field_name' => 'level',
                    'convert' => true,
                ],
                'po' => [
                    'field_name' => 'po',
                    'convert' => true,
                ],
                'po_editable' => [
                    'field_name' => 'po_editable',
                    'convert' => true,
                ],
                'pa' => [
                    'field_name' => 'pa',
                    'convert' => true,
                ],
                'cast_per_turn' => [
                    'field_name' => 'cast_per_turn',
                    'convert' => true,
                ],
                'cast_per_target' => [
                    'field_name' => 'cast_per_target',
                    'convert' => true,
                ],
                'sight_line' => [
                    'field_name' => 'sight_line',
                    'convert' => true,
                ],
                'number_between_two_cast' => [
                    'field_name' => 'number_between_two_cast',
                    'convert' => true,
                ],
                'number_between_two_cast_editable' => [
                    'field_name' => 'number_between_two_cast_editable',
                    'convert' => true,
                ],
                'element' => [
                    'field_name' => 'element',
                    'convert' => true,
                ],
                'category' => [
                    'field_name' => 'category',
                    'convert' => false,
                ],
                'is_magic' => [
                    'field_name' => null,
                    'convert' => false,
                ],
                'powerful' => [
                    'field_name' => null,
                    'convert' => true,
                ],
                'image' => [
                    'field_name' => 'img',
                    'convert' => false,
                ],
            ],
        ],
    ],
];