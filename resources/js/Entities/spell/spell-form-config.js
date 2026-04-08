/**
 * Configuration partagée du formulaire sort (création + édition).
 *
 * @module Entities/spell/spell-form-config
 */

import { getEntityStateOptions, getUserRoleOptions, getSpellCategoryOptions } from '@/Utils/Entity/SharedConstants';
import { SPELL_RESOLUTION_MODE_OPTIONS } from '@/Entities/spell/spell-descriptors';
import { resolveSpellAttackSaveCharacteristicOptions } from '@/Entities/spell/spell-resolution-characteristic-options';

/**
 * Champs du formulaire (édition : inclut id / dates en lecture seule si `includeReadonlyMeta`).
 *
 * @param {Object} [options]
 * @param {boolean} [options.includeReadonlyMeta=true] - Faux pour la création
 * @returns {Record<string, object>}
 */
export function buildSpellFormFieldsConfig(options = {}) {
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
            showInCompact: false,
            accept: 'image/*',
            help: 'Illustration du sort. Formats courants : JPEG, PNG, WebP, GIF.',
        },
        category: {
            type: 'select',
            label: 'Catégorie',
            required: false,
            options: getSpellCategoryOptions(),
            optionBadge: {
                enabled: true,
                color: 'auto',
                autoLabelFrom: 'label',
                autoScheme: 'labelHash',
                autoTone: 'light',
                variant: 'soft',
            },
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
            type: 'text',
            label: 'Niveau (formule ou valeur)',
            required: false,
            showInCompact: true,
        },
        pa: {
            type: 'text',
            label: 'Coût PA (formule ou valeur)',
            required: false,
            showInCompact: true,
        },
        effect: {
            type: 'textarea',
            label: 'Description des effets du sort',
            required: false,
            help: 'Texte libre pour décrire les effets. Le paramétrage détaillé (degrés, sous-effets, invocations…) se fait dans la section Effets après création ou sur la fiche du sort.',
        },
        element: {
            type: 'elementPrimaries',
            label: 'Élément(s)',
            required: false,
            help: '« Aucun » : sort non lié à un élément. Sinon, cochez les primaires ; la valeur enregistrée est la combinaison 0–29.',
        },
        is_magic: {
            type: 'physiqueWakfu',
            label: 'Physique ou Wakfu',
            help: 'Physique : le sort puise dans la force physique. Wakfu : le sort puise dans le Wakfu (équivalent du « magique » côté Dofus).',
            required: false,
            showInCompact: true,
        },
        sight_line: {
            type: 'checkbox',
            label: 'Ligne de vue requise',
            required: false,
        },
        allows_reaction: {
            type: 'checkbox',
            label: 'Utilisable en réaction',
            help: 'Si activé, le sort peut être lancé en réaction : une réaction par round et par créature ; les PA dépensés pour la réaction ne sont pas réattribués au début du tour suivant.',
            required: false,
            showInCompact: true,
        },
        casting_time: {
            type: 'text',
            label: "Temps d'incantation",
            required: false,
            showInCompact: true,
            help: 'Ex. instantané, 1 action, 10 min. Laisser vide si non renseigné.',
        },
        ritual_available: {
            type: 'checkbox',
            label: 'Rituel disponible',
            required: false,
            showInCompact: true,
        },
        cast_per_turn: {
            type: 'text',
            label: 'Lancers par tour',
            required: false,
        },
        cast_per_target: {
            type: 'text',
            label: 'Lancers par cible',
            required: false,
        },
        number_between_two_cast: {
            type: 'text',
            label: 'Délai entre deux lancers (tours)',
            required: false,
        },
        po_min: {
            type: 'text',
            label: 'PO min',
            required: false,
        },
        po_max: {
            type: 'text',
            label: 'PO max',
            required: false,
        },
        po_editable: {
            type: 'checkbox',
            label: 'Portée modifiable en jeu',
            required: false,
        },
        resolution_mode: {
            type: 'select',
            label: 'Mode de résolution',
            required: false,
            options: SPELL_RESOLUTION_MODE_OPTIONS(),
        },
        attack_characteristic_key: {
            type: 'select',
            label: 'Caractéristique d’attaque',
            required: false,
            options: resolveSpellAttackSaveCharacteristicOptions,
            optionBadge: {
                enabled: true,
                autoLabelFrom: 'label',
                variant: 'soft',
            },
            visibleWhen: { field: 'resolution_mode', value: 'attack_roll' },
        },
        save_characteristic_key: {
            type: 'select',
            label: 'Caractéristique de sauvegarde',
            required: false,
            options: resolveSpellAttackSaveCharacteristicOptions,
            optionBadge: {
                enabled: true,
                autoLabelFrom: 'label',
                variant: 'soft',
            },
            visibleWhen: { field: 'resolution_mode', value: 'saving_throw' },
        },
        save_dc_formula: {
            type: 'text',
            label: 'Formule du DD',
            required: false,
            visibleWhen: { field: 'resolution_mode', value: 'saving_throw' },
        },
        save_success_note: {
            type: 'textarea',
            label: 'Effet si la sauvegarde réussit',
            required: false,
            visibleWhen: { field: 'resolution_mode', value: 'saving_throw' },
        },
        auto_success_if_willing_target: {
            type: 'checkbox',
            label: 'Réussite automatique si la cible est consentante',
            help: 'Si activé, le sort réussit sans jet lorsque la cible accepte volontairement l’effet (règle optionnelle).',
            required: false,
            visibleWhen: { field: 'resolution_mode', in: ['attack_roll', 'saving_throw'] },
            uiIcon: 'fa-solid fa-handshake',
            uiColor: '#c026d3',
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
            help: 'Activer seulement si un ID DofusDB est renseigné pour permettre la synchro automatique.',
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

/** Sections formulaire — édition (fiche). Ordre des colonnes (grille sort) : généralités, portée/coût/résolution, spécificités, admin. */
export const SPELL_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Généralités',
        subtitle: 'Nom, description, visuel ; catégorie, état, élément et types.',
        fieldKeys: ['name', 'description', 'image', 'category', 'state', 'element', 'spellTypes'],
    },
    {
        id: 'range_resolution',
        title: 'Portée, coût et résolution au combat',
        subtitle:
            'Niveau, coût PA, description texte des effets ; PO min / max et portée modifiable en jeu ; jets d’attaque, sauvegarde ou réussite automatique (cible consentante).',
        fieldKeys: [
            'level',
            'pa',
            'effect',
            'po_min',
            'po_max',
            'po_editable',
            'resolution_mode',
            'attack_characteristic_key',
            'save_characteristic_key',
            'save_dc_formula',
            'save_success_note',
            'auto_success_if_willing_target',
        ],
    },
    {
        id: 'gameplay',
        title: 'Spécificités du sort',
        subtitle:
            'Physique ou Wakfu, lancers, ligne de vue, réaction, temps d’incantation, rituel. La zone d’impact se définit dans les effets (degrés).',
        fieldKeys: [
            'is_magic',
            'sight_line',
            'allows_reaction',
            'casting_time',
            'ritual_available',
            'cast_per_turn',
            'cast_per_target',
            'number_between_two_cast',
        ],
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

/** Sections formulaire — création (pas d’ID / dates). */
export const SPELL_FORM_FIELD_SECTIONS_CREATE = [
    SPELL_FORM_FIELD_SECTIONS_EDIT[0],
    SPELL_FORM_FIELD_SECTIONS_EDIT[1],
    SPELL_FORM_FIELD_SECTIONS_EDIT[2],
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Identifiants externes, synchro et niveaux d’accès.',
        fieldKeys: ['official_id', 'dofusdb_id', 'auto_update', 'read_level', 'write_level'],
    },
];

/**
 * Valeurs par défaut pour la création d’un sort (alignées modèle / StoreSpellRequest).
 * `auto_update` à false tant qu’aucun ID DofusDB n’est associé au scraping.
 *
 * @returns {Record<string, unknown>}
 */
/**
 * Fusionne le champ `spellTypes` (IDs multiples) pour les formulaires sort.
 *
 * @param {Record<string, object>} baseConfig
 * @param {Array<{ id: number, name: string, color?: string|null, icon?: string|null }>} availableSpellTypes
 * @returns {Record<string, object>}
 */
export function mergeSpellTypesFieldIntoSpellFormConfig(baseConfig, availableSpellTypes = []) {
    const options = (availableSpellTypes || []).map((t) => ({
        value: Number(t.id),
        label: String(t.name ?? `#${t.id}`),
        color: t.color ?? null,
        icon: t.icon != null && String(t.icon).trim() !== '' ? String(t.icon) : null,
    }));
    return {
        ...baseConfig,
        spellTypes: {
            type: 'spellTypesMulti',
            label: 'Types de sort',
            required: false,
            help: 'Plusieurs types possibles pour un même sort (ex. offensif + entrave).',
            options,
        },
    };
}

export function getSpellCreateDefaultEntity() {
    return {
        name: '',
        description: '',
        effect: '',
        level: '1',
        pa: '3',
        po_min: '1',
        po_max: '1',
        po_editable: true,
        cast_per_turn: '1',
        cast_per_target: '0',
        sight_line: true,
        allows_reaction: false,
        casting_time: '',
        ritual_available: false,
        number_between_two_cast: '0',
        element: null,
        category: 1,
        spellTypes: [],
        is_magic: true,
        resolution_mode: 'attack_roll',
        attack_characteristic_key: '',
        save_characteristic_key: '',
        save_dc_formula: '',
        save_success_note: '',
        auto_success_if_willing_target: false,
        state: 'draft',
        read_level: 0,
        write_level: 3,
        auto_update: false,
        official_id: '',
        dofusdb_id: '',
        image: '',
    };
}
