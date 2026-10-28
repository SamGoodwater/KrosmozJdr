/**
 * Sections du formulaire état / condition (édition sheet).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/condition/condition-form-config
 */

import { CONDITION_MECHANICAL_FLAGS } from '@/Composables/condition/conditionDisplay';

/** Clés des flags mécaniques (cases à cocher). */
const MECHANICAL_FLAG_KEYS = CONDITION_MECHANICAL_FLAGS.map((f) => f.key);

/** Sections formulaire — édition (grille sheet 2 cols ; flags pleine largeur). */
export const CONDITION_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, description et visuel.',
        fieldKeys: ['name', 'description', 'image'],
    },
    {
        id: 'rules',
        title: 'Règles',
        subtitle: 'Dissipation et effets mécaniques.',
        span: 2,
        columns: 1,
        fieldKeys: ['dissipable', ...MECHANICAL_FLAG_KEYS],
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
