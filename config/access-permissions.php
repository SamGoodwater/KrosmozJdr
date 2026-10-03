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
     * Accès au bloc "Administration" dans l'UI (app).
     */
    'adminPanel' => [
        ['entity' => 'users', 'ability' => 'manageAny'],
    ],

    /**
     * Accès au menu Effets / Sous-effets (contenu de jeu, MJ+).
     */
    'effectsAdmin' => [
        ['entity' => 'users', 'ability' => 'manageContent'],
    ],

    /**
     * Accès au menu "Pages" (gestion CMS).
     */
    'pagesManager' => [
        ['entity' => 'pages', 'ability' => 'updateAny'],
    ],

    /**
     * Menu « Gestion du contenu » (jeu, MJ+).
     */
    'contentManagement' => [
        ['entity' => 'users', 'ability' => 'manageContent'],
    ],

    /**
     * Pipeline sensible du contenu (Import DofusDB, mappings, IA métier) — admin+.
     */
    'contentPipeline' => [
        ['entity' => 'users', 'ability' => 'manageAny'],
    ],
];
