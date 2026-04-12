/**
 * Configuration partagée du formulaire capacité (création + édition).
 *
 * @module Entities/capability/capability-form-config
 */

import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';
import { getElementOptions } from '@/Utils/Entity/Elements';

/**
 * Champs du formulaire (édition : inclut id / dates en lecture seule si `includeReadonlyMeta`).
 *
 * @param {Object} [options]
 * @param {boolean} [options.includeReadonlyMeta=true]
 * @returns {Record<string, object>}
 */
export function buildCapabilityFormFieldsConfig(options = {}) {
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
            help: 'Illustration de la capacité.',
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
        element: {
            type: 'select',
            label: 'Élément',
            required: false,
            options: getElementOptions(),
        },
        level: {
            type: 'text',
            label: 'Niveau (formule ou valeur)',
            required: false,
            showInCompact: true,
        },
        pa: {
            type: 'text',
            label: 'Coût PA',
            required: false,
            showInCompact: true,
        },
        po: {
            type: 'text',
            label: 'Portée (PO)',
            required: false,
            showInCompact: true,
        },
        po_editable: {
            type: 'checkbox',
            label: 'Portée modifiable en jeu',
            required: false,
        },
        effect: {
            type: 'richtext',
            label: 'Effets (texte riche)',
            required: false,
            help: 'Contrairement aux sorts, pas d’éditeur d’effets structurés : décrivez les effets ici.',
            /** Passé à {@link RichTextEditorField} (zone d’édition plus haute sur la fiche pleine largeur). */
            richEditorHeight: 'min-h-[300px] md:min-h-[420px]',
        },
        time_before_use_again: {
            type: 'text',
            label: 'Temps avant réutilisation',
            required: false,
        },
        casting_time: {
            type: 'text',
            label: "Temps d'incantation",
            required: false,
        },
        duration: {
            type: 'text',
            label: 'Durée',
            required: false,
        },
        is_magic: {
            type: 'physiqueWakfu',
            label: 'Physique ou Wakfu',
            required: false,
        },
        ritual_available: {
            type: 'checkbox',
            label: 'Rituel disponible',
            required: false,
        },
        powerful: {
            type: 'text',
            label: 'Puissance / notation',
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
        id: {
            type: 'display',
            label: 'ID interne',
        },
        created_at: {
            type: 'display',
            label: 'Créé le',
        },
        updated_at: {
            type: 'display',
            label: 'Modifié le',
        },
    };
}

/** Sections formulaire — édition (fiche). */
export const CAPABILITY_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Généralités',
        subtitle: 'Nom, description, visuel, état et élément.',
        fieldKeys: ['name', 'description', 'image', 'state', 'element'],
    },
    {
        id: 'cost_effect',
        title: 'Coût et portée',
        subtitle: 'Niveau, PA, PO et portée modulable en jeu.',
        fieldKeys: ['level', 'pa', 'po', 'po_editable'],
    },
    {
        id: 'gameplay',
        title: 'Déroulé & spécificités',
        subtitle: 'Relance, incantation, durée, magie, rituel, puissance.',
        fieldKeys: [
            'time_before_use_again',
            'casting_time',
            'duration',
            'is_magic',
            'ritual_available',
            'powerful',
        ],
    },
    {
        id: 'effect_richtext',
        title: 'Effets (texte riche)',
        subtitle:
            'Environ les deux tiers de la ligne sur tablette et plus ; les métadonnées occupent le tiers restant.',
        fieldKeys: ['effect'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Niveaux d’accès et horodatage.',
        fieldKeys: ['read_level', 'write_level', 'id', 'created_at', 'updated_at'],
    },
];

/** Sections formulaire — création (sans id / dates). */
export const CAPABILITY_FORM_FIELD_SECTIONS_CREATE = [
    CAPABILITY_FORM_FIELD_SECTIONS_EDIT[0],
    CAPABILITY_FORM_FIELD_SECTIONS_EDIT[1],
    CAPABILITY_FORM_FIELD_SECTIONS_EDIT[2],
    CAPABILITY_FORM_FIELD_SECTIONS_EDIT[3],
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Niveaux d’accès.',
        fieldKeys: ['read_level', 'write_level'],
    },
];

/**
 * Valeurs par défaut à la création (alignées StoreCapabilityRequest / modèle).
 *
 * @returns {Record<string, unknown>}
 */
export function getCapabilityCreateDefaultEntity() {
    return {
        name: '',
        description: '',
        effect: '',
        level: '1',
        pa: '0',
        po: '0',
        po_editable: true,
        time_before_use_again: '',
        casting_time: '',
        duration: '',
        element: 0,
        is_magic: false,
        ritual_available: false,
        powerful: '',
        state: 'draft',
        read_level: 0,
        write_level: 3,
        image: '',
    };
}
