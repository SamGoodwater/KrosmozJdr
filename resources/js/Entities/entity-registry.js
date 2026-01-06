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
import { adaptMonsterEntitiesTableResponse, buildMonsterCell } from "@/Entities/monster/monster-adapter";
import { adaptCreatureEntitiesTableResponse, buildCreatureCell } from "@/Entities/creature/creature-adapter";
import { adaptNpcEntitiesTableResponse, buildNpcCell } from "@/Entities/npc/npc-adapter";
import { adaptClasseEntitiesTableResponse, buildClasseCell } from "@/Entities/classe/classe-adapter";
import { adaptConsumableEntitiesTableResponse, buildConsumableCell } from "@/Entities/consumable/consumable-adapter";
import { adaptCampaignEntitiesTableResponse, buildCampaignCell } from "@/Entities/campaign/campaign-adapter";
import { adaptScenarioEntitiesTableResponse, buildScenarioCell } from "@/Entities/scenario/scenario-adapter";
import { adaptAttributeEntitiesTableResponse, buildAttributeCell } from "@/Entities/attribute/attribute-adapter";
import { adaptPanoplyEntitiesTableResponse, buildPanoplyCell } from "@/Entities/panoply/panoply-adapter";
import { adaptCapabilityEntitiesTableResponse, buildCapabilityCell } from "@/Entities/capability/capability-adapter";
import { adaptSpecializationEntitiesTableResponse, buildSpecializationCell } from "@/Entities/specialization/specialization-adapter";
import { adaptShopEntitiesTableResponse, buildShopCell } from "@/Entities/shop/shop-adapter";
import { getResourceFieldDescriptors, RESOURCE_VIEW_FIELDS } from "@/Entities/resource/resource-descriptors";
import { getResourceTypeFieldDescriptors, RESOURCE_TYPE_VIEW_FIELDS } from "@/Entities/resource-type/resource-type-descriptors";
import { getItemFieldDescriptors, ITEM_VIEW_FIELDS } from "@/Entities/item/item-descriptors";
import { getSpellFieldDescriptors, SPELL_VIEW_FIELDS } from "@/Entities/spell/spell-descriptors";
import { getMonsterFieldDescriptors, MONSTER_VIEW_FIELDS } from "@/Entities/monster/monster-descriptors";
import { getCreatureFieldDescriptors, CREATURE_VIEW_FIELDS } from "@/Entities/creature/creature-descriptors";
import { getNpcFieldDescriptors, NPC_VIEW_FIELDS } from "@/Entities/npc/npc-descriptors";
import { getClasseFieldDescriptors, CLASSE_VIEW_FIELDS } from "@/Entities/classe/classe-descriptors";
import { getConsumableFieldDescriptors, CONSUMABLE_VIEW_FIELDS } from "@/Entities/consumable/consumable-descriptors";
import { getCampaignFieldDescriptors, CAMPAIGN_VIEW_FIELDS } from "@/Entities/campaign/campaign-descriptors";
import { getScenarioFieldDescriptors, SCENARIO_VIEW_FIELDS } from "@/Entities/scenario/scenario-descriptors";
import { getAttributeFieldDescriptors, ATTRIBUTE_VIEW_FIELDS } from "@/Entities/attribute/attribute-descriptors";
import { getPanoplyFieldDescriptors, PANOPLY_VIEW_FIELDS } from "@/Entities/panoply/panoply-descriptors";
import { getCapabilityFieldDescriptors, CAPABILITY_VIEW_FIELDS } from "@/Entities/capability/capability-descriptors";
import { getSpecializationFieldDescriptors, SPECIALIZATION_VIEW_FIELDS } from "@/Entities/specialization/specialization-descriptors";
import { getShopFieldDescriptors, SHOP_VIEW_FIELDS } from "@/Entities/shop/shop-descriptors";

/**
 * @typedef {'resources'|'resource-types'|'items'|'spells'|'monsters'|'creatures'|'npcs'|'classes'|'consumables'|'campaigns'|'scenarios'|'attributes'|'panoplies'|'capabilities'|'specializations'|'shops'} EntityTypeKey
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
  if (s === "monster" || s === "monsters") return "monsters";
  if (s === "creature" || s === "creatures") return "creatures";
  if (s === "npc" || s === "npcs") return "npcs";
  if (s === "classe" || s === "classes") return "classes";
  if (s === "consumable" || s === "consumables") return "consumables";
  if (s === "campaign" || s === "campaigns") return "campaigns";
  if (s === "scenario" || s === "scenarios") return "scenarios";
  if (s === "attribute" || s === "attributes") return "attributes";
  if (s === "panoply" || s === "panoplies") return "panoplies";
  if (s === "capability" || s === "capabilities") return "capabilities";
  if (s === "specialization" || s === "specializations") return "specializations";
  if (s === "shop" || s === "shops") return "shops";
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
    case "monsters":
      return {
        key,
        getDescriptors: getMonsterFieldDescriptors,
        buildCell: buildMonsterCell,
        viewFields: MONSTER_VIEW_FIELDS,
        responseAdapter: adaptMonsterEntitiesTableResponse,
        defaults: { minimalImportantFields: ["creature_name", "monster_race", "size"] },
      };
    case "creatures":
      return {
        key,
        getDescriptors: getCreatureFieldDescriptors,
        buildCell: buildCreatureCell,
        viewFields: CREATURE_VIEW_FIELDS,
        responseAdapter: adaptCreatureEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "level", "hostility"] },
      };
    case "npcs":
      return {
        key,
        getDescriptors: getNpcFieldDescriptors,
        buildCell: buildNpcCell,
        viewFields: NPC_VIEW_FIELDS,
        responseAdapter: adaptNpcEntitiesTableResponse,
        defaults: { minimalImportantFields: ["creature_name", "classe", "specialization"] },
      };
    case "classes":
      return {
        key,
        getDescriptors: getClasseFieldDescriptors,
        buildCell: buildClasseCell,
        viewFields: CLASSE_VIEW_FIELDS,
        responseAdapter: adaptClasseEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "life", "life_dice"] },
      };
    case "consumables":
      return {
        key,
        getDescriptors: getConsumableFieldDescriptors,
        buildCell: buildConsumableCell,
        viewFields: CONSUMABLE_VIEW_FIELDS,
        responseAdapter: adaptConsumableEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "level", "rarity"] },
      };
    case "campaigns":
      return {
        key,
        getDescriptors: getCampaignFieldDescriptors,
        buildCell: buildCampaignCell,
        viewFields: CAMPAIGN_VIEW_FIELDS,
        responseAdapter: adaptCampaignEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "slug", "state"] },
      };
    case "scenarios":
      return {
        key,
        getDescriptors: getScenarioFieldDescriptors,
        buildCell: buildScenarioCell,
        viewFields: SCENARIO_VIEW_FIELDS,
        responseAdapter: adaptScenarioEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "slug", "state"] },
      };
    case "attributes":
      return {
        key,
        getDescriptors: getAttributeFieldDescriptors,
        buildCell: buildAttributeCell,
        viewFields: ATTRIBUTE_VIEW_FIELDS,
        responseAdapter: adaptAttributeEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "description"] },
      };
    case "panoplies":
      return {
        key,
        getDescriptors: getPanoplyFieldDescriptors,
        buildCell: buildPanoplyCell,
        viewFields: PANOPLY_VIEW_FIELDS,
        responseAdapter: adaptPanoplyEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "bonus", "items_count"] },
      };
    case "capabilities":
      return {
        key,
        getDescriptors: getCapabilityFieldDescriptors,
        buildCell: buildCapabilityCell,
        viewFields: CAPABILITY_VIEW_FIELDS,
        responseAdapter: adaptCapabilityEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "level", "pa", "po"] },
      };
    case "specializations":
      return {
        key,
        getDescriptors: getSpecializationFieldDescriptors,
        buildCell: buildSpecializationCell,
        viewFields: SPECIALIZATION_VIEW_FIELDS,
        responseAdapter: adaptSpecializationEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "description", "capabilities_count"] },
      };
    case "shops":
      return {
        key,
        getDescriptors: getShopFieldDescriptors,
        buildCell: buildShopCell,
        viewFields: SHOP_VIEW_FIELDS,
        responseAdapter: adaptShopEntitiesTableResponse,
        defaults: { minimalImportantFields: ["name", "location", "npc_name", "items_count"] },
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


