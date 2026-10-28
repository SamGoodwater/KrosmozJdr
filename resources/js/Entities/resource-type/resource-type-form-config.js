/**
 * Sections du formulaire type de ressource (édition sheet).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/resource-type/resource-type-form-config
 */

/** Sections formulaire — édition (grille sheet 2 cols). */
export const RESOURCE_TYPE_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom et affichage catalogue.',
        fieldKeys: ['name', 'show_in_catalog', 'decision'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Identifiant DofusDB, niveaux d’accès et horodatage.',
        collapsedByDefault: true,
        collapsedActionLabel: 'Afficher',
        expandedActionLabel: 'Masquer',
        fieldKeys: [
            'dofusdb_type_id',
            'read_level',
            'write_level',
            'id',
            'created_at',
            'updated_at',
        ],
    },
];
