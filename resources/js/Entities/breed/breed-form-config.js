/**
 * Sections du formulaire classe / breed (édition dense).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/breed/breed-form-config
 */

/**
 * Sections formulaire — édition.
 * Champs courts en grille sheet 2 cols ; textes longs / TipTap en `span: 2` + `columns: 1`.
 */
export const BREED_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Généralités',
        subtitle: 'Nom, accroche et dé de classe.',
        fieldKeys: ['name', 'description_fast', 'life_dice'],
    },
    {
        id: 'texts',
        title: 'Descriptions & textes',
        subtitle: 'Description longue et spécificité (pleine largeur).',
        span: 2,
        columns: 1,
        fieldKeys: ['description', 'specificity'],
    },
    {
        id: 'gameplay',
        title: 'Règles de classe',
        subtitle: 'Mécanique signature et évolution (éditeurs TipTap pleine largeur).',
        span: 2,
        columns: 1,
        fieldKeys: ['mechanics', 'evolution'],
    },
    {
        id: 'visuals',
        title: 'Visuels',
        subtitle: 'Images et symboles de la classe.',
        span: 2,
        columns: 1,
        collapsedByDefault: true,
        collapsedActionLabel: 'Afficher',
        expandedActionLabel: 'Masquer',
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
        collapsedByDefault: true,
        collapsedActionLabel: 'Afficher',
        expandedActionLabel: 'Masquer',
        fieldKeys: [
            'dofusdb_id',
            'dofus_version',
            'auto_update',
            'read_level',
            'write_level',
        ],
    },
];
