/**
 * Configuration partagée du formulaire PNJ (édition).
 *
 * @module Entities/npc/npc-form-config
 */

import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';

const SIZE_OPTIONS = [
    { value: 0, label: 'Minuscule' },
    { value: 1, label: 'Petit' },
    { value: 2, label: 'Moyen' },
    { value: 3, label: 'Grand' },
    { value: 4, label: 'Colossal' },
    { value: 5, label: 'Gigantesque' },
];

const ROLE_OPTIONS = [
    { value: '', label: '—' },
    { value: 'social', label: 'Social' },
    { value: 'merchant', label: 'Marchand' },
    { value: 'guard', label: 'Garde' },
    { value: 'ally', label: 'Allié' },
    { value: 'enemy', label: 'Ennemi' },
    { value: 'other', label: 'Autre' },
];

const HOSTILITY_OPTIONS = [
    { value: 0, label: 'Amical' },
    { value: 1, label: 'Curieux' },
    { value: 2, label: 'Neutre' },
    { value: 3, label: 'Hostile' },
    { value: 4, label: 'Agressif' },
];

/**
 * @param {Object} [options]
 * @param {Array<{ id: number, name: string }>} [options.breeds]
 * @param {Array<{ id: number, name: string }>} [options.specializations]
 * @returns {Record<string, object>}
 */
export function buildNpcFormFieldsConfig(options = {}) {
    const { breeds = [], specializations = [] } = options;

    return {
        name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
        location: { type: 'text', label: 'Lieu', required: false, showInCompact: true },
        level: { type: 'text', label: 'Niveau', required: false, showInCompact: true },
        hostility: {
            type: 'select',
            label: 'Hostilité',
            required: false,
            showInCompact: true,
            options: HOSTILITY_OPTIONS,
        },
        npc_role: {
            type: 'select',
            label: 'Rôle',
            required: false,
            showInCompact: true,
            options: ROLE_OPTIONS,
        },
        size: {
            type: 'select',
            label: 'Taille',
            required: false,
            showInCompact: true,
            options: SIZE_OPTIONS,
        },
        age: { type: 'text', label: 'Âge', required: false, showInCompact: true },
        breed_id: {
            type: 'select',
            label: 'Classe',
            required: false,
            showInCompact: true,
            options: [
                { value: '', label: '—' },
                ...breeds.map((b) => ({ value: b.id, label: b.name })),
            ],
        },
        specialization_id: {
            type: 'select',
            label: 'Spécialisation',
            required: false,
            showInCompact: false,
            options: [
                { value: '', label: '—' },
                ...specializations.map((s) => ({ value: s.id, label: s.name })),
            ],
        },
        story: { type: 'textarea', label: 'Histoire', required: false, showInCompact: false },
        historical: { type: 'textarea', label: 'Historique', required: false, showInCompact: false },
        description: { type: 'textarea', label: 'Description', required: false, showInCompact: false },
        state: {
            type: 'select',
            label: 'État',
            required: false,
            showInCompact: true,
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
        id: { type: 'display', label: 'ID interne' },
    };
}

/** Sections formulaire — édition (grille dense). */
export const NPC_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Identité',
        subtitle: 'Nom, lieu, niveau, rôle et hostilité.',
        fieldKeys: ['name', 'location', 'level', 'hostility', 'npc_role', 'state'],
    },
    {
        id: 'profile',
        title: 'Profil',
        subtitle: 'Taille, âge, classe et spécialisation.',
        fieldKeys: ['size', 'age', 'breed_id', 'specialization_id'],
    },
    {
        id: 'narrative',
        title: 'Narratif',
        subtitle: 'Histoire, historique et description.',
        fieldKeys: ['story', 'historical', 'description'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Niveaux d’accès et identifiant.',
        fieldKeys: ['read_level', 'write_level', 'id'],
    },
];
