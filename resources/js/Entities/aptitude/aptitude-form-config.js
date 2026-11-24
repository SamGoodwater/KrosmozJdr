/**
 * Sections du formulaire aptitude (édition sheet).
 *
 * @module Entities/aptitude/aptitude-form-config
 */

/** Sections formulaire — édition. */
export const APTITUDE_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, description, effet et visuel.',
        fieldKeys: ['name', 'description', 'effect', 'image'],
    },
    {
        id: 'usage',
        title: 'Réutilisation, durée & portée',
        subtitle: 'Phrases libres décrivant l’usage naturel (sans PA).',
        fieldKeys: ['reuse', 'duration', 'range'],
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
