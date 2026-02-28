<?php

/**
 * Options pour la construction des sous-effets (pattern action → caractéristique → valeur).
 * Une seule liste de caractéristiques : stats/ressources + éléments (category pour filtrer par action).
 * Effect décide quelles catégories sont valides pour quelle action (ex. frapper ⇒ element).
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ARCHITECTURE_EFFETS_3_COUCHES.md
 * @see docs/50-Fonctionnalités/Spell-Effects/TAXONOMIE_SOUS_EFFETS.md
 * @see docs/10-BestPractices/CHARACTERISTIC_PROPERTY_NAMING_REFERENCE.md
 */
return [
    /*
    | Liste unique de caractéristiques (stats, ressources, éléments).
    | key = identifiant machine (English, no abbreviations), label = libellé affiché, category = pour filtrer par action.
    | category: stat | resource | element — Effect exige "element" pour l'action frapper, etc.
    */
    'characteristics' => [
        // Ressources
        ['key' => 'action_points', 'label' => 'PA', 'category' => 'resource'],
        ['key' => 'movement_points', 'label' => 'PM', 'category' => 'resource'],
        ['key' => 'range', 'label' => 'PO', 'category' => 'resource'],
        // Stats
        ['key' => 'agility', 'label' => 'Agilité', 'category' => 'stat'],
        ['key' => 'strength', 'label' => 'Force', 'category' => 'stat'],
        ['key' => 'intelligence', 'label' => 'Intelligence', 'category' => 'stat'],
        ['key' => 'chance', 'label' => 'Chance', 'category' => 'stat'],
        ['key' => 'wisdom', 'label' => 'Sagesse', 'category' => 'stat'],
        ['key' => 'vitality', 'label' => 'Vitalité', 'category' => 'stat'],
        ['key' => 'life_points', 'label' => 'Points de vie', 'category' => 'stat'],
        ['key' => 'shield', 'label' => 'Points de bouclier', 'category' => 'stat'],
        // Éléments : earth, fire, water, air, neutral
        ['key' => 'earth', 'label' => 'Terre', 'category' => 'element'],
        ['key' => 'fire', 'label' => 'Feu', 'category' => 'element'],
        ['key' => 'water', 'label' => 'Eau', 'category' => 'element'],
        ['key' => 'air', 'label' => 'Air', 'category' => 'element'],
        ['key' => 'neutral', 'label' => 'Neutre', 'category' => 'element'],
    ],
];
