/**
 * Configuration partagée du formulaire panoplie (édition).
 *
 * @module Entities/panoply/panoply-form-config
 */

import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';

/**
 * @param {Object} [options]
 * @param {boolean} [options.includeReadonlyMeta=true]
 * @returns {Record<string, object>}
 */
export function buildPanoplyFormFieldsConfig(options = {}) {
    const { includeReadonlyMeta = true } = options;

    const base = {
        name: {
            type: 'text',
            label: 'Nom',
            required: true,
            showInCompact: true,
        },
        description: {
            type: 'textarea',
            label: 'Description',
            required: false,
            showInCompact: false,
        },
        state: {
            type: 'select',
            label: 'État',
            required: false,
            options: getEntityStateOptions(),
            optionBadge: {
                enabled: true,
                leadingDot: 'entity-state',
                variant: 'soft',
            },
        },
        dofusdb_id: {
            type: 'text',
            label: 'ID DofusDB',
            required: false,
        },
        read_level: {
            type: 'select',
            label: 'Lecture (min.)',
            required: false,
            options: getUserRoleOptions(),
        },
        write_level: {
            type: 'select',
            label: 'Écriture (min.)',
            required: false,
            options: getUserRoleOptions(),
        },
    };

    if (!includeReadonlyMeta) {
        return { ...base };
    }

    return {
        ...base,
        id: { type: 'display', label: 'ID interne' },
        created_at: { type: 'display', label: 'Créé le' },
        updated_at: { type: 'display', label: 'Modifié le' },
    };
}

/** Sections formulaire — édition (grille dense). */
export const PANOPLY_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, description et état du set.',
        fieldKeys: ['name', 'description', 'state'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Identifiant externe, niveaux d’accès et horodatage.',
        fieldKeys: ['dofusdb_id', 'read_level', 'write_level', 'id', 'created_at', 'updated_at'],
    },
];
