/**
 * Configuration partagée du formulaire monstre (édition).
 *
 * @module Entities/monster/monster-form-config
 */

import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';

/**
 * Champs alignés sur UpdateMonsterRequest.
 *
 * @returns {Record<string, object>}
 */
export function buildMonsterFormFieldsConfig() {
    return {
        size: {
            type: 'number',
            label: 'Taille',
            required: false,
            showInCompact: true,
        },
        is_boss: {
            type: 'checkbox',
            label: 'Boss',
            required: false,
            showInCompact: true,
        },
        boss_pa: {
            type: 'number',
            label: 'PA légendaires',
            required: false,
            showInCompact: false,
            visibleWhen: { field: 'is_boss', value: true },
            tooltip:
                "Pool hors tour, utilisable entre deux tours d'autres créatures. Se recharge à la fin du tour du boss.",
        },
        monster_race_id: {
            type: 'number',
            label: 'Race (ID)',
            required: false,
            help: 'Identifiant de la race de monstre.',
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
        auto_update: {
            type: 'checkbox',
            label: 'Mise à jour auto (scraping DofusDB)',
            required: false,
            uiIcon: 'fa-solid fa-arrows-rotate',
            uiColor: '#0284c7',
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
        id: {
            type: 'display',
            label: 'ID interne',
        },
    };
}

/** Sections formulaire — édition (grille dense). */
export const MONSTER_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Coquille monstre',
        subtitle: 'Taille, boss et PA légendaires. Nom / stats sur la créature liée.',
        fieldKeys: ['size', 'is_boss', 'boss_pa', 'monster_race_id', 'state'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Synchro et niveaux d’accès.',
        fieldKeys: ['auto_update', 'read_level', 'write_level', 'id'],
    },
];
