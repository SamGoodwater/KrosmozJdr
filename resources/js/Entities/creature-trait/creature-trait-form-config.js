/**
 * Sections du formulaire trait de créature (édition sheet).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/creature-trait/creature-trait-form-config
 */

/** Sections formulaire — édition (grille sheet 2 cols ; description pleine largeur). */
export const CREATURE_TRAIT_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, description et visuel.',
        fieldKeys: ['name', 'description', 'image'],
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
