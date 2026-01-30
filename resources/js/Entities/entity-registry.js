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

// Import des modèles
import { Resource } from "@/Models/Entity/Resource";
import { ResourceType } from "@/Models/Entity/ResourceType";
import { Item } from "@/Models/Entity/Item";
import { Spell } from "@/Models/Entity/Spell";
import { Monster } from "@/Models/Entity/Monster";
import { Creature } from "@/Models/Entity/Creature";
import { Npc } from "@/Models/Entity/Npc";
import { Classe } from "@/Models/Entity/Classe";
import { Consumable } from "@/Models/Entity/Consumable";
import { Campaign } from "@/Models/Entity/Campaign";
import { Scenario } from "@/Models/Entity/Scenario";
import { Attribute } from "@/Models/Entity/Attribute";
import { Panoply } from "@/Models/Entity/Panoply";
import { Capability } from "@/Models/Entity/Capability";
import { Specialization } from "@/Models/Entity/Specialization";
import { Shop } from "@/Models/Entity/Shop";

// Import du mapper Resource (seul mapper existant pour l'instant)
import { ResourceMapper } from "@/Mappers/Entity/ResourceMapper";

// Import de l'adapter générique
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";
import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { getCreatureFieldDescriptors } from "@/Entities/creature/creature-descriptors";
import { getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
import { getClasseFieldDescriptors } from "@/Entities/classe/classe-descriptors";
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";
import { getCampaignFieldDescriptors } from "@/Entities/campaign/campaign-descriptors";
import { getScenarioFieldDescriptors } from "@/Entities/scenario/scenario-descriptors";
import { getAttributeFieldDescriptors } from "@/Entities/attribute/attribute-descriptors";
import { getPanoplyFieldDescriptors } from "@/Entities/panoply/panoply-descriptors";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { getSpecializationFieldDescriptors } from "@/Entities/specialization/specialization-descriptors";
import { getShopFieldDescriptors } from "@/Entities/shop/shop-descriptors";

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
 * @returns {{ key: EntityTypeKey|string, getDescriptors: (ctx:any)=>any, responseAdapter: Function, defaults: any } | null}
 */
export function getEntityConfig(entityType) {
  const key = /** @type {EntityTypeKey|string} */ (normalizeEntityType(entityType));
  switch (key) {
    case "resources":
      return {
        key,
        getDescriptors: getResourceFieldDescriptors,
        // viewFields supprimé : utiliser descriptors._quickeditConfig.fields directement
        responseAdapter: createEntityAdapter(Resource, ResourceMapper),
        defaults: { minimalImportantFields: ["level", "rarity", "state", "read_level"] },
      };
    case "resource-types":
      return {
        key,
        getDescriptors: getResourceTypeFieldDescriptors,
        responseAdapter: createEntityAdapter(ResourceType),
        defaults: { minimalImportantFields: ["decision", "resources_count", "dofusdb_type_id"] },
      };
    case "items":
      return {
        key,
        getDescriptors: getItemFieldDescriptors,
        responseAdapter: createEntityAdapter(Item),
        defaults: { minimalImportantFields: ["level", "item_type", "rarity"] },
      };
    case "spells":
      return {
        key,
        getDescriptors: getSpellFieldDescriptors,
        responseAdapter: createEntityAdapter(Spell),
        defaults: { minimalImportantFields: ["level", "pa", "po", "element", "category"] },
      };
    case "monsters":
      return {
        key,
        getDescriptors: getMonsterFieldDescriptors,
        responseAdapter: createEntityAdapter(Monster),
        defaults: { minimalImportantFields: ["creature_name", "monster_race", "size", "is_boss"] },
      };
    case "creatures":
      return {
        key,
        getDescriptors: getCreatureFieldDescriptors,
        responseAdapter: createEntityAdapter(Creature),
        defaults: { minimalImportantFields: ["name", "level", "hostility", "life"] },
      };
    case "npcs":
      return {
        key,
        getDescriptors: getNpcFieldDescriptors,
        responseAdapter: createEntityAdapter(Npc),
        defaults: { minimalImportantFields: ["creature_name", "classe", "specialization"] },
      };
    case "classes":
      return {
        key,
        getDescriptors: getClasseFieldDescriptors,
        responseAdapter: createEntityAdapter(Classe),
        defaults: { minimalImportantFields: ["name", "life", "life_dice"] },
      };
    case "consumables":
      return {
        key,
        getDescriptors: getConsumableFieldDescriptors,
        responseAdapter: createEntityAdapter(Consumable),
        defaults: { minimalImportantFields: ["level", "consumable_type", "rarity"] },
      };
    case "campaigns":
      return {
        key,
        getDescriptors: getCampaignFieldDescriptors,
        responseAdapter: createEntityAdapter(Campaign),
        defaults: { minimalImportantFields: ["name", "state", "is_public"] },
      };
    case "scenarios":
      return {
        key,
        getDescriptors: getScenarioFieldDescriptors,
        responseAdapter: createEntityAdapter(Scenario),
        defaults: { minimalImportantFields: ["name", "state", "is_public"] },
      };
    case "attributes":
      return {
        key,
        getDescriptors: getAttributeFieldDescriptors,
        responseAdapter: createEntityAdapter(Attribute),
        defaults: { minimalImportantFields: ["name", "state", "read_level"] },
      };
    case "panoplies":
      return {
        key,
        getDescriptors: getPanoplyFieldDescriptors,
        responseAdapter: createEntityAdapter(Panoply),
        defaults: { minimalImportantFields: ["name", "bonus", "items_count"] },
      };
    case "capabilities":
      return {
        key,
        getDescriptors: getCapabilityFieldDescriptors,
        responseAdapter: createEntityAdapter(Capability),
        defaults: { minimalImportantFields: ["name", "level", "pa", "po", "element"] },
      };
    case "specializations":
      return {
        key,
        getDescriptors: getSpecializationFieldDescriptors,
        responseAdapter: createEntityAdapter(Specialization),
        defaults: { minimalImportantFields: ["name", "description", "capabilities_count"] },
      };
    case "shops":
      return {
        key,
        getDescriptors: getShopFieldDescriptors,
        responseAdapter: createEntityAdapter(Shop),
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


