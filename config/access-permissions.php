<?php

/**
 * Registry des permissions d'accès "UI" (non liées à une entité affichée en table).
 *
 * @description
 * Permet de dériver des "entrées de menu" ou des sections UI à partir des Policies/Gates.
 * La source de vérité reste Laravel (Gate::can / Policies).
 *
 * Structure :
 * - clé (string) => liste de règles (anyOf)
 * - une règle = [ 'entity' => <entityType>, 'ability' => <ability> ]
 *
 * @example
 * 'adminPanel' => [
 *   ['entity' => 'users', 'ability' => 'manageAny'],
 * ],
 */
return [
    /**
     * Accès au bloc "Administration" dans l'UI.
     */
    'adminPanel' => [
        ['entity' => 'users', 'ability' => 'manageAny'],
    ],

    /**
     * Accès au menu "Scrapping" (réservé aux administrateurs).
     */
    'scrapping' => [
        ['entity' => 'users', 'ability' => 'manageAny'],
    ],

    /**
     * Accès au menu "Sous-effets" / "Effets" (game_master et au-dessus).
     */
    'effectsAdmin' => [
        ['entity' => 'spells', 'ability' => 'updateAny'],
    ],

    /**
     * Accès au menu "Pages" (gestion).
     */
    'pagesManager' => [
        ['entity' => 'pages', 'ability' => 'updateAny'],
    ],
];


