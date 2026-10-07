/**
 * Sections du formulaire ressource (édition dense).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/resource/resource-form-config
 */

/** Sections formulaire — édition (grille sheet 2 cols ; état dans le header). */
export const RESOURCE_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Généralités',
        subtitle: 'Nom, description, visuel et type.',
        fieldKeys: ['name', 'description', 'image', 'resource_type_id'],
    },
    {
        id: 'gameplay',
        title: 'Ressource',
        subtitle: 'Niveau, rareté, effet, bonus, poids et prix.',
        fieldKeys: ['level', 'rarity', 'effect', 'bonus', 'weight', 'price'],
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
