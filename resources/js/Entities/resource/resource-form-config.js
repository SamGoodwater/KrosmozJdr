/**
 * Sections du formulaire ressource (édition dense).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/resource/resource-form-config
 */

/** Sections formulaire — édition (grille sheet 2 cols ; liens pleine largeur). */
export const RESOURCE_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Généralités',
        subtitle: 'Nom, description et visuel.',
        fieldKeys: ['name', 'description', 'image'],
    },
    {
        id: 'stats',
        title: 'Caractéristiques',
        subtitle: 'Niveau, rareté, poids et prix.',
        fieldKeys: ['level', 'rarity', 'weight', 'price'],
    },
    {
        id: 'links',
        title: 'Type de ressource',
        subtitle: 'Lien vers le type.',
        span: 2,
        columns: 1,
        fieldKeys: ['resource_type_id'],
    },
    {
        id: 'advanced',
        title: 'Effet & bonus',
        subtitle: 'Texte d’effet et bonus structurés.',
        span: 2,
        columns: 1,
        collapsedByDefault: true,
        collapsedActionLabel: 'Afficher',
        expandedActionLabel: 'Masquer',
        fieldKeys: ['effect', 'bonus'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Identifiants externes, synchro, niveaux d’accès.',
        collapsedByDefault: true,
        collapsedActionLabel: 'Afficher',
        expandedActionLabel: 'Masquer',
        fieldKeys: [
            'official_id',
            'dofusdb_id',
            'dofus_version',
            'auto_update',
            'read_level',
            'write_level',
        ],
    },
];
