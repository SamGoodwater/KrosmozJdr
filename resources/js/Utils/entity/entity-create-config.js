/**
 * Configuration création d'entité (Q9) — champs visibles dans CreateEntityModal.
 *
 * @description
 * Par défaut, CreateEntityModal masque les champs techniques (ids, audit, scrapping).
 * Cette liste **ré-autorise** des clés optionnelles par type, en plus du socle minimal
 * (nom, description, niveau, type/race selon descriptors).
 *
 * @example
 * import { getEntityCreateAllowFieldKeys } from '@/Utils/entity/entity-create-config';
 * :create-allow-field-keys="getEntityCreateAllowFieldKeys('items')"
 */

import { normalizeEntityType } from '@/Entities/entity-registry';

/** @type {Record<string, string[]>} */
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
    npcs: ['dofusdb_id', 'auto_update'],
    npc: ['dofusdb_id', 'auto_update'],
};

/**
 * @param {string} entityType - singulier ou pluriel (registre)
 * @returns {string[]}
 */
export function getEntityCreateAllowFieldKeys(entityType) {
    const normalized = normalizeEntityType(entityType);
    return CREATE_ALLOW_FIELD_KEYS_BY_TYPE[normalized] ?? ['dofusdb_id', 'auto_update'];
}
