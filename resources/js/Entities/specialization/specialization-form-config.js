/**
 * Sections du formulaire spécialisation (édition dense).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/specialization/specialization-form-config
 */

/** Sections formulaire — édition (grille sheet 2 cols ; état dans le header). */
export const SPECIALIZATION_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, descriptions et visuel.',
        fieldKeys: ['name', 'short_description', 'description', 'image'],
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
