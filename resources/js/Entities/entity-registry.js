/**
 * Entity registry (frontend)
 *
 * @description
 * Point d'entrée unique pour récupérer les briques d'une entité côté frontend
 * (descriptors + responseAdapter).
 *
 * But: éviter la duplication (imports/cas particuliers) dans les pages d’index.
 *
 * @example
 * import { getEntityResponseAdapter } from "@/Entities/entity-registry";
 * const adapter = getEntityResponseAdapter("resources");
 */

import { adaptResourceEntitiesTableResponse, buildResourceCell } from "@/Entities/resource/resource-adapter";
import { adaptResourceTypeEntitiesTableResponse, buildResourceTypeCell } from "@/Entities/resource-type/resource-type-adapter";
import { adaptItemEntitiesTableResponse, buildItemCell } from "@/Entities/item/item-adapter";
import { adaptSpellEntitiesTableResponse, buildSpellCell } from "@/Entities/spell/spell-adapter";
import { getResourceFieldDescriptors, RESOURCE_VIEW_FIELDS } from "@/Entities/resource/resource-descriptors";
import { getResourceTypeFieldDescriptors, RESOURCE_TYPE_VIEW_FIELDS } from "@/Entities/resource-type/resource-type-descriptors";
import { getItemFieldDescriptors, ITEM_VIEW_FIELDS } from "@/Entities/item/item-descriptors";
import { getSpellFieldDescriptors, SPELL_VIEW_FIELDS } from "@/Entities/spell/spell-descriptors";

/**
 * @typedef {'resources'|'resource-types'|'items'|'spells'} EntityTypeKey
 */

/**
 * Normalise les variantes (singulier/camelCase) vers les clés utilisées par Table v2.
 *
 * @param {string} raw
 * @returns {EntityTypeKey|string}
 */
export function normalizeEntityType(raw) {
  const s = String(raw || "");
  if (s === "resource") return "resources";
  if (s === "resources") return "resources";
  if (s === "resourceType" || s === "resource-types" || s === "resourceTypes") return "resource-types";
  if (s === "item" || s === "items") return "items";
  if (s === "spell" || s === "spells") return "spells";
  return s;
}

/**
 * @param {string} entityType
 * @returns {{ key: EntityTypeKey|string, getDescriptors: (ctx:any)=>any, buildCell: Function, viewFields: any, responseAdapter: Function, defaults: any } | null}
 */
export function getEntityConfig(entityType) {
  const key = /** @type {EntityTypeKey|string} */ (normalizeEntityType(entityType));
  switch (key) {
    case "resources":
      return {
        key,
        getDescriptors: getResourceFieldDescriptors,
        buildCell: buildResourceCell,
        viewFields: RESOURCE_VIEW_FIELDS,
        responseAdapter: adaptResourceEntitiesTableResponse,
        defaults: { minimalImportantFields: ["level", "resource_type", "rarity"] },
      };
    case "resource-types":
      return {
        key,
        getDescriptors: getResourceTypeFieldDescriptors,
        buildCell: buildResourceTypeCell,
        viewFields: RESOURCE_TYPE_VIEW_FIELDS,
        responseAdapter: adaptResourceTypeEntitiesTableResponse,
        defaults: { minimalImportantFields: ["decision", "resources_count", "dofusdb_type_id"] },
      };
    case "items":
      return {
        key,
        getDescriptors: getItemFieldDescriptors,
        buildCell: buildItemCell,
        viewFields: ITEM_VIEW_FIELDS,
        responseAdapter: adaptItemEntitiesTableResponse,
        defaults: { minimalImportantFields: ["level", "item_type", "rarity"] },
      };
    case "spells":
      return {
        key,
        getDescriptors: getSpellFieldDescriptors,
        buildCell: buildSpellCell,
        viewFields: SPELL_VIEW_FIELDS,
        responseAdapter: adaptSpellEntitiesTableResponse,
        defaults: { minimalImportantFields: ["level", "pa", "po"] },
      };
    default:
      return null;
  }
}

/**
 * @param {string} entityType
 * @returns {(payload:any) => ({meta:any, rows:any[]}) | null}
 */
export function getEntityResponseAdapter(entityType) {
  const cfg = getEntityConfig(entityType);
  return cfg?.responseAdapter || null;
}


