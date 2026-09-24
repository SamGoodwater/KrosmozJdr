/**
 * Sections du formulaire classe / breed (édition dense).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/breed/breed-form-config
 */

/** Sections formulaire — édition (grille dense). */
export const BREED_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Généralités',
        subtitle: 'Nom, descriptions et état.',
        fieldKeys: ['name', 'description_fast', 'description', 'state'],
    },
    {
        id: 'gameplay',
        title: 'Règles de classe',
        subtitle: 'Évolution, dés de vie, spécificité.',
        fieldKeys: ['evolution', 'life_dice', 'specificity'],
    },
    {
        id: 'visuals',
        title: 'Visuels',
        subtitle: 'Images et symboles de la classe.',
        fieldKeys: [
            'image',
            'icon',
            'symbol_full',
            'symbol_bw',
            'logo_male',
            'logo_female',
            'image_full_male',
            'image_full_female',
        ],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Identifiants externes, synchro, niveaux d’accès.',
        fieldKeys: [
            'dofusdb_id',
            'dofus_version',
            'auto_update',
            'read_level',
            'write_level',
        ],
    },
];
