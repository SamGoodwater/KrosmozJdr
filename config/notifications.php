<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Types de notifications configurables par l'utilisateur
    |--------------------------------------------------------------------------
    |
    | Chaque clé est un type. 'label' : libellé pour l'UI.
    | 'channels_default' : canaux par défaut (database = in-app, mail = email).
    | 'frequency_default' : instant | daily | weekly | monthly (pour digest).
    | 'roles' : rôles éligibles (optionnel). Si absent, tous les rôles connectés.
    |
    */
    'types' => [
        'last_connection' => [
            'label' => 'Dernière connexion',
            'channels_default' => ['database'],
            'frequency_default' => 'instant',
        ],
        'profile_modified' => [
            'label' => 'Modification de mes informations personnelles',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
        ],
        'entity_modified' => [
            'label' => 'Modification d\'une entité que j\'ai créée (avec détail des changements)',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
        ],
        'entity_deleted' => [
            'label' => 'Suppression d\'une entité que j\'ai créée',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
        ],
        'page_section_modified' => [
            'label' => 'Modification d\'une page/section que j\'ai créée ou sur laquelle j\'ai les droits d\'écriture (avec détail)',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
        ],
        'page_section_deleted' => [
            'label' => 'Suppression d\'une page/section que j\'ai créée ou sur laquelle j\'ai les droits d\'écriture',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
        ],
        'entity_created' => [
            'label' => 'Création d\'une entité (admin)',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
            'roles' => ['admin', 'super_admin'],
        ],
        'entity_modified_admin' => [
            'label' => 'Modification d\'une entité (admin, avec détail)',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
            'roles' => ['admin', 'super_admin'],
        ],
        'entity_deleted_admin' => [
            'label' => 'Suppression d\'une entité (admin)',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
            'roles' => ['admin', 'super_admin'],
        ],
        'page_section_modified_admin' => [
            'label' => 'Modification d\'une page/section (admin, avec détail)',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
            'roles' => ['admin', 'super_admin'],
        ],
        'page_section_deleted_admin' => [
            'label' => 'Suppression d\'une page/section (admin)',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
            'roles' => ['admin', 'super_admin'],
        ],
        'entity_restored' => [
            'label' => 'Restauration d\'une entité',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
        ],
        'entity_force_deleted' => [
            'label' => 'Suppression définitive d\'une entité',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
        ],
        'new_account_registered' => [
            'label' => 'Nouveau compte créé (inscription)',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
            'roles' => ['admin', 'super_admin'],
        ],
        'user_deleted' => [
            'label' => 'Suppression d\'un utilisateur',
            'channels_default' => ['database', 'mail'],
            'frequency_default' => 'instant',
            'roles' => ['admin', 'super_admin'],
        ],
        'project_maintenance' => [
            'label' => 'Résultat init/update projet (scrapping)',
            'channels_default' => ['database'],
            'frequency_default' => 'instant',
            'roles' => ['admin', 'super_admin'],
        ],
    ],

    /*
    | Canaux reconnus (database = in-app / pop, mail = email).
    */
    'channels' => [
        'database' => 'Sur le site (centre de notifications)',
        'mail' => 'Par email',
    ],

    /*
    | Fréquences possibles pour les types qui le supportent.
    */
    'frequencies' => [
        'instant' => 'Au fur et à mesure',
        'daily' => 'Quotidienne (résumé)',
        'weekly' => 'Hebdomadaire (résumé)',
        'monthly' => 'Mensuelle (résumé)',
    ],
];
