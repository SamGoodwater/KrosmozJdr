/**
 * Sections du formulaire spécialisation (édition dense).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/specialization/specialization-form-config
 */

/** Sections formulaire — édition (grille sheet 2 cols ; descriptions longues repliables). */
export const SPECIALIZATION_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, accroche et visuel.',
        fieldKeys: ['name', 'short_description', 'image'],
    },
    {
        id: 'narrative',
        title: 'Description longue',
        subtitle: 'Texte détaillé de la spécialisation.',
        span: 2,
        columns: 1,
        collapsedByDefault: true,
        collapsedActionLabel: 'Afficher',
        expandedActionLabel: 'Masquer',
        fieldKeys: ['description'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Niveaux d’accès et horodatage.',
        collapsedByDefault: true,
        collapsedActionLabel: 'Afficher',
        expandedActionLabel: 'Masquer',
        fieldKeys: ['read_level', 'write_level', 'id', 'created_at', 'updated_at'],
    },
];
