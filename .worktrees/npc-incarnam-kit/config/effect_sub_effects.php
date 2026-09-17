<?php

/**
 * Options pour la construction des sous-effets (pattern action → caractéristique → valeur).
 * Une seule liste de caractéristiques : stats/ressources + éléments (category pour filtrer par action).
 * Effect décide quelles catégories sont valides pour quelle action (ex. frapper ⇒ element).
 *
 * @see docs/features/effects/README.md
 * @see docs/features/effects/README.md
 * @see docs/features/characteristics/README.md
 */
return [
    /*
    | Liste unique de caractéristiques (stats, ressources, éléments).
    | key = alias reconnu par SpellEffectConversionFormulaResolver, label = libellé affiché.
    | category: stat | resource | element | skill — Effect exige "element" pour l'action frapper, etc.
    */
    'characteristics' => [
        // Ressources
        ['key' => 'pa', 'label' => 'PA', 'category' => 'resource'],
        ['key' => 'pm', 'label' => 'PM', 'category' => 'resource'],
        ['key' => 'po', 'label' => 'PO', 'category' => 'resource'],
        // Stats
        ['key' => 'agi', 'label' => 'Agilité', 'category' => 'stat'],
        ['key' => 'strong', 'label' => 'Force', 'category' => 'stat'],
        ['key' => 'intel', 'label' => 'Intelligence', 'category' => 'stat'],
        ['key' => 'chance', 'label' => 'Chance', 'category' => 'stat'],
        ['key' => 'sagesse', 'label' => 'Sagesse', 'category' => 'stat'],
        ['key' => 'vitality', 'label' => 'Vitalité', 'category' => 'stat'],
        ['key' => 'bouclier', 'label' => 'Points de bouclier', 'category' => 'stat'],
        // Compétences actives
        ['key' => 'acrobatics', 'label' => 'Acrobaties', 'category' => 'skill'],
        ['key' => 'animal_handling', 'label' => 'Dressage', 'category' => 'skill'],
        ['key' => 'arcana', 'label' => 'Arcane', 'category' => 'skill'],
        ['key' => 'athletics', 'label' => 'Athlétisme', 'category' => 'skill'],
        ['key' => 'deception', 'label' => 'Tromperie', 'category' => 'skill'],
        ['key' => 'history', 'label' => 'Histoire', 'category' => 'skill'],
        ['key' => 'insight', 'label' => 'Perspicacité', 'category' => 'skill'],
        ['key' => 'intimidation', 'label' => 'Intimidation', 'category' => 'skill'],
        ['key' => 'investigation', 'label' => 'Investigation', 'category' => 'skill'],
        ['key' => 'medicine', 'label' => 'Médecine', 'category' => 'skill'],
        ['key' => 'nature', 'label' => 'Nature', 'category' => 'skill'],
        ['key' => 'perception', 'label' => 'Perception', 'category' => 'skill'],
        ['key' => 'performance', 'label' => 'Représentation', 'category' => 'skill'],
        ['key' => 'persuasion', 'label' => 'Persuasion', 'category' => 'skill'],
        ['key' => 'religion', 'label' => 'Religion', 'category' => 'skill'],
        ['key' => 'sleight_of_hand', 'label' => 'Escamotage', 'category' => 'skill'],
        ['key' => 'stealth', 'label' => 'Discrétion', 'category' => 'skill'],
        ['key' => 'survival', 'label' => 'Survie', 'category' => 'skill'],
        // Éléments : earth, fire, water, air, neutral
        ['key' => 'earth', 'label' => 'Terre', 'category' => 'element'],
        ['key' => 'fire', 'label' => 'Feu', 'category' => 'element'],
        ['key' => 'water', 'label' => 'Eau', 'category' => 'element'],
        ['key' => 'air', 'label' => 'Air', 'category' => 'element'],
        ['key' => 'neutral', 'label' => 'Neutre', 'category' => 'element'],
        ['key' => 'element_wisdom', 'label' => 'Sagesse', 'category' => 'element'],
        ['key' => 'element_vitality', 'label' => 'Vitalité', 'category' => 'element'],
    ],
];
