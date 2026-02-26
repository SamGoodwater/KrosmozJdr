<?php

/**
 * Options pour la construction des sous-effets (pattern action → caractéristique → valeur).
 * Une seule liste de caractéristiques : stats/ressources + éléments (category pour filtrer par action).
 * Effect décide quelles catégories sont valides pour quelle action (ex. frapper ⇒ element).
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ARCHITECTURE_EFFETS_3_COUCHES.md
 * @see docs/50-Fonctionnalités/Spell-Effects/TAXONOMIE_SOUS_EFFETS.md
 */
return [
    /*
    | Liste unique de caractéristiques (stats, ressources, éléments).
    | key = identifiant machine, label = libellé affiché, category = pour filtrer par action.
    | category: stat | resource | element — Effect exige "element" pour l'action frapper, etc.
    */
    'characteristics' => [
        ['key' => 'pa', 'label' => 'PA', 'category' => 'resource'],
        ['key' => 'pm', 'label' => 'PM', 'category' => 'resource'],
        ['key' => 'po', 'label' => 'PO', 'category' => 'resource'],
        ['key' => 'agi', 'label' => 'Agilité', 'category' => 'stat'],
        ['key' => 'force', 'label' => 'Force', 'category' => 'stat'],
        ['key' => 'intel', 'label' => 'Intelligence', 'category' => 'stat'],
        ['key' => 'chance', 'label' => 'Chance', 'category' => 'stat'],
        ['key' => 'sagesse', 'label' => 'Sagesse', 'category' => 'stat'],
        ['key' => 'vita', 'label' => 'Vitalité', 'category' => 'stat'],
        ['key' => 'pv', 'label' => 'Points de vie', 'category' => 'stat'],
        ['key' => 'bouclier', 'label' => 'Points de bouclier', 'category' => 'stat'],
        ['key' => 'neutre', 'label' => 'Neutre', 'category' => 'element'],
        ['key' => 'feu', 'label' => 'Feu', 'category' => 'element'],
        ['key' => 'eau', 'label' => 'Eau', 'category' => 'element'],
        ['key' => 'terre', 'label' => 'Terre', 'category' => 'element'],
        ['key' => 'air', 'label' => 'Air', 'category' => 'element'],
    ],
];
