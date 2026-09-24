/**
 * Configuration partagée du formulaire équipement (édition).
 *
 * @module Entities/item/item-form-config
 */

import {
    getEntityStateOptions,
    getRarityOptions,
    getUserRoleOptions,
} from '@/Utils/Entity/SharedConstants';

/**
 * Champs du formulaire équipement.
 *
 * @param {Object} [options]
 * @param {boolean} [options.includeReadonlyMeta=true]
 * @param {Array<{ id: number, name: string }>} [options.itemTypes]
 * @returns {Record<string, object>}
 */
export function buildItemFormFieldsConfig(options = {}) {
    const { includeReadonlyMeta = true, itemTypes = [] } = options;

    const itemTypeOptions = (itemTypes || []).map((t) => ({
        value: t.id,
        label: t.name || `Type #${t.id}`,
    }));

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
        level: {
            type: 'number',
            label: 'Niveau',
            required: false,
            showInCompact: true,
        },
        rarity: {
            type: 'select',
            label: 'Rareté',
            required: false,
            showInCompact: true,
            options: getRarityOptions().map(({ value, label }) => ({ value, label })),
        },
        item_type_id: itemTypeOptions.length
            ? {
                  type: 'select',
                  label: 'Type d’équipement',
                  required: false,
                  showInCompact: true,
                  options: itemTypeOptions,
                  searchable: true,
              }
            : {
                  type: 'number',
                  label: 'Type (ID)',
                  required: false,
                  help: 'Identifiant du type d’équipement (type_item_types).',
              },
        effect: {
            type: 'textarea',
            label: 'Effet (résumé)',
            required: false,
            help: 'Résumé converti (Krosmoz). Les bonus structurés sont dans Bonus / Effets & usages.',
        },
        bonus: {
            type: 'textarea',
            label: 'Bonus (JSON)',
            required: false,
            help: 'Objet plat clé → valeur, ex. {"strength":10,"vitality":20}.',
        },
        recipe: {
            type: 'textarea',
            label: 'Recette (texte)',
            required: false,
            help: 'Notes libres. La recette structurée (ressources + quantités) est gérée sous le formulaire.',
        },
        official_id: {
            type: 'text',
            label: 'ID officiel',
            required: false,
        },
        dofusdb_id: {
            type: 'text',
            label: 'ID DofusDB',
            required: false,
        },
        auto_update: {
            type: 'checkbox',
            label: 'Mise à jour auto (scraping DofusDB)',
            help: 'Activer seulement si un ID DofusDB est renseigné.',
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

/** Sections formulaire — édition (grille dense). */
export const ITEM_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Généralités',
        subtitle: 'Nom, description, visuel et état.',
        fieldKeys: ['name', 'description', 'image', 'state'],
    },
    {
        id: 'gameplay',
        title: 'Équipement',
        subtitle: 'Niveau, rareté, type, effet et bonus.',
        fieldKeys: ['level', 'rarity', 'item_type_id', 'effect', 'bonus', 'recipe'],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Identifiants externes, synchro, niveaux d’accès et horodatage.',
        fieldKeys: [
            'official_id',
            'dofusdb_id',
            'auto_update',
            'read_level',
            'write_level',
            'id',
            'created_at',
            'updated_at',
        ],
    },
];
