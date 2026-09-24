/**
 * Configuration partagée du formulaire campagne (édition).
 *
 * @module Entities/campaign/campaign-form-config
 */

import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';

/**
 * @param {Object} [options]
 * @param {boolean} [options.includeReadonlyMeta=true]
 * @returns {Record<string, object>}
 */
export function buildCampaignFormFieldsConfig(options = {}) {
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
        image: {
            type: 'file',
            label: 'Image',
            required: false,
            accept: 'image/*',
        },
        slug: {
            type: 'text',
            label: 'Slug',
            required: false,
            showInCompact: false,
        },
        keyword: {
            type: 'text',
            label: 'Mot-clé',
            required: false,
            showInCompact: true,
        },
        is_public: {
            type: 'checkbox',
            label: 'Public',
            required: false,
            showInCompact: true,
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
export const CAMPAIGN_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, description, visuel et publication.',
        fieldKeys: ['name', 'description', 'image', 'is_public', 'state'],
    },
    {
        id: 'meta',
        title: 'Référencement',
        subtitle: 'Slug et mot-clé.',
        fieldKeys: ['slug', 'keyword'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Niveaux d’accès et horodatage.',
        fieldKeys: ['read_level', 'write_level', 'id', 'created_at', 'updated_at'],
    },
];
