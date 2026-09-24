/**
 * Sections du formulaire spécialisation (édition dense).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/specialization/specialization-form-config
 */

/** Sections formulaire — édition (grille dense). */
export const SPECIALIZATION_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, descriptions, visuel et état.',
        fieldKeys: ['name', 'short_description', 'description', 'image', 'state'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Niveaux d’accès et horodatage.',
        fieldKeys: ['read_level', 'write_level', 'id', 'created_at', 'updated_at'],
    },
];
