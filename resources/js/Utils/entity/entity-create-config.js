/**
 * Configuration création d'entité — champs du modal (formulaire court).
 *
 * @description
 * CreateEntityModal n’affiche qu’un **socle** (nom, description, quelques clés métier).
 * Le reste se complète sur la vue Modifier après création.
 *
 * @example
 * import { getEntityCreateCoreFieldKeys } from '@/Utils/entity/entity-create-config';
 * getEntityCreateCoreFieldKeys('npcs');
 */

import { normalizeEntityType } from '@/Entities/entity-registry';

/** @type {Record<string, string[]>} */
const CREATE_CORE_FIELD_KEYS_BY_TYPE = {
    spells: ['name', 'description', 'pa', 'spell_type_id', 'element'],
    items: ['name', 'description', 'item_type_id', 'level'],
    consumables: ['name', 'description', 'consumable_type_id', 'level'],
    resources: ['name', 'description', 'resource_type_id', 'level'],
    capabilities: ['name', 'description', 'element'],
    monsters: ['name', 'description', 'level', 'monster_race_id'],
    breeds: ['name', 'description'],
    panoplies: ['name', 'description'],
    conditions: ['name', 'description'],
    'creature-traits': ['name', 'description'],
    specializations: ['name', 'short_description', 'description'],
    campaigns: ['name', 'description'],
    scenarios: ['name', 'description'],
    shops: ['name', 'description'],
    npcs: ['name', 'description', 'level', 'npc_role', 'location'],
};

/** Types dont le `store` web est encore un stub vide. */
const QUICK_CREATE_UNSUPPORTED_TYPES = ['panoplies', 'scenarios', 'shops'];

/** @deprecated Utiliser getEntityCreateCoreFieldKeys — conservé pour les Index existants. */
const CREATE_ALLOW_FIELD_KEYS_BY_TYPE = {
    spells: ['dofusdb_id', 'auto_update'],
    spell: ['dofusdb_id', 'auto_update'],
    items: ['item_type_id', 'level', 'dofusdb_id', 'auto_update'],
    item: ['item_type_id', 'level', 'dofusdb_id', 'auto_update'],
    consumables: ['consumable_type_id', 'level', 'dofusdb_id', 'auto_update'],
    consumable: ['consumable_type_id', 'level', 'dofusdb_id', 'auto_update'],
    resources: ['resource_type_id', 'level', 'dofusdb_id', 'auto_update'],
    resource: ['resource_type_id', 'level', 'dofusdb_id', 'auto_update'],
    capabilities: ['dofusdb_id', 'auto_update', 'element'],
    capability: ['dofusdb_id', 'auto_update', 'element'],
    monsters: ['dofusdb_id', 'auto_update', 'monster_race_id', 'level'],
    monster: ['dofusdb_id', 'auto_update', 'monster_race_id', 'level'],
    breeds: ['dofusdb_id', 'auto_update'],
    breed: ['dofusdb_id', 'auto_update'],
    panoplies: ['dofusdb_id', 'auto_update', 'level'],
    panoply: ['dofusdb_id', 'auto_update', 'level'],
    conditions: ['dofusdb_id'],
    condition: ['dofusdb_id'],
    'creature-traits': ['dofusdb_id'],
    'creature-trait': ['dofusdb_id'],
    specializations: ['dofusdb_id'],
    specialization: ['dofusdb_id'],
    campaigns: [],
    campaign: [],
    scenarios: [],
    scenario: [],
    shops: ['dofusdb_id'],
    shop: ['dofusdb_id'],
    npcs: ['name', 'level', 'location', 'breed_id', 'npc_role'],
    npc: ['name', 'level', 'location', 'breed_id', 'npc_role'],
};

/**
 * @param {string} entityType
 * @returns {string[]}
 */
export function getEntityCreateCoreFieldKeys(entityType) {
    const normalized = normalizeEntityType(entityType);
    return CREATE_CORE_FIELD_KEYS_BY_TYPE[normalized] ?? ['name', 'description'];
}

/**
 * True si le modal de création courte a un `store` Laravel utilisable.
 *
 * @param {string} entityType
 * @returns {boolean}
 */
export function entitySupportsQuickCreate(entityType) {
    const normalized = normalizeEntityType(entityType);
    return !QUICK_CREATE_UNSUPPORTED_TYPES.includes(normalized);
}

/**
 * @param {string} entityType - singulier ou pluriel (registre)
 * @returns {string[]}
 */
export function getEntityCreateAllowFieldKeys(entityType) {
    const normalized = normalizeEntityType(entityType);
    return CREATE_ALLOW_FIELD_KEYS_BY_TYPE[normalized] ?? ['dofusdb_id', 'auto_update'];
}

/**
 * Libellé court pour le bouton / titre de création.
 *
 * @param {string} entityType
 * @returns {string}
 */
export function getEntityCreateLabel(entityType) {
    const labels = {
        items: 'un équipement',
        spells: 'un sort',
        monsters: 'un monstre',
        npcs: 'un PNJ',
        breeds: 'une classe',
        consumables: 'un consommable',
        resources: 'une ressource',
        capabilities: 'une capacité',
        specializations: 'une spécialisation',
        panoplies: 'une panoplie',
        conditions: 'un état',
        'creature-traits': 'un trait',
        campaigns: 'une campagne',
        scenarios: 'un scénario',
        shops: 'un hôtel de vente',
    };
    return labels[normalizeEntityType(entityType)] || 'une fiche';
}
