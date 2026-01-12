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
import { getResourceFieldDescriptors, RESOURCE_QUICK_EDIT_FIELDS } from "@/Entities/resource/resource-descriptors";
import { getResourceTypeFieldDescriptors, RESOURCE_TYPE_QUICK_EDIT_FIELDS } from "@/Entities/resource-type/resource-type-descriptors";
import { getItemFieldDescriptors, ITEM_QUICK_EDIT_FIELDS } from "@/Entities/item/item-descriptors";
import { getSpellFieldDescriptors, SPELL_QUICK_EDIT_FIELDS } from "@/Entities/spell/spell-descriptors";
import { getMonsterFieldDescriptors, MONSTER_QUICK_EDIT_FIELDS } from "@/Entities/monster/monster-descriptors";
import { getCreatureFieldDescriptors, CREATURE_QUICK_EDIT_FIELDS } from "@/Entities/creature/creature-descriptors";
import { getNpcFieldDescriptors, NPC_QUICK_EDIT_FIELDS } from "@/Entities/npc/npc-descriptors";
import { getClasseFieldDescriptors, CLASSE_QUICK_EDIT_FIELDS } from "@/Entities/classe/classe-descriptors";
import { getConsumableFieldDescriptors, CONSUMABLE_QUICK_EDIT_FIELDS } from "@/Entities/consumable/consumable-descriptors";
import { getCampaignFieldDescriptors, CAMPAIGN_QUICK_EDIT_FIELDS } from "@/Entities/campaign/campaign-descriptors";
import { getScenarioFieldDescriptors, SCENARIO_QUICK_EDIT_FIELDS } from "@/Entities/scenario/scenario-descriptors";
import { getAttributeFieldDescriptors, ATTRIBUTE_QUICK_EDIT_FIELDS } from "@/Entities/attribute/attribute-descriptors";
import { getPanoplyFieldDescriptors, PANOPLY_QUICK_EDIT_FIELDS } from "@/Entities/panoply/panoply-descriptors";
import { getCapabilityFieldDescriptors, CAPABILITY_QUICK_EDIT_FIELDS } from "@/Entities/capability/capability-descriptors";
import { getSpecializationFieldDescriptors, SPECIALIZATION_QUICK_EDIT_FIELDS } from "@/Entities/specialization/specialization-descriptors";
import { getShopFieldDescriptors, SHOP_QUICK_EDIT_FIELDS } from "@/Entities/shop/shop-descriptors";

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
        // buildCell pour Resource : utilise resource.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Resource, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const resource = entityData instanceof Resource 
            ? entityData 
            : new Resource(entityData);
          
          // Générer la cellule via resource.toCell()
          return resource.toCell(fieldKey, {
            size: opts.size || 'md',
            context: opts.context || 'table',
            ...opts,
          });
        },
        viewFields: {
          quickEdit: RESOURCE_QUICK_EDIT_FIELDS,
          compact: ['rarity', 'level', 'usable', 'price', 'dofus_version', 'is_visible'],
          extended: ['rarity', 'resource_type', 'level', 'usable', 'price', 'weight', 'dofus_version', 'is_visible', 'auto_update', 'dofusdb_id', 'created_by', 'created_at', 'updated_at'],
        },
        responseAdapter: createEntityAdapter(Resource, ResourceMapper),
        defaults: { minimalImportantFields: ["level", "rarity", "usable", "is_visible"] },
      };
    case "resource-types":
      return {
        key,
        getDescriptors: getResourceTypeFieldDescriptors,
        // buildCell pour ResourceType : utilise resourceType.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance ResourceType, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const resourceType = entityData instanceof ResourceType 
            ? entityData 
            : new ResourceType(entityData);
          
          // Générer la cellule via resourceType.toCell()
          return resourceType.toCell(fieldKey, {
            size: opts.size || 'md',
            context: opts.context || 'table',
            ...opts,
          });
        },
        viewFields: RESOURCE_TYPE_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(ResourceType),
        defaults: { minimalImportantFields: ["decision", "resources_count", "dofusdb_type_id"] },
      };
    case "items":
      return {
        key,
        getDescriptors: getItemFieldDescriptors,
        // buildCell pour Item : utilise item.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Item, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const item = entityData instanceof Item
            ? entityData
            : new Item(entityData);

          return item.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: ITEM_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Item),
        defaults: { minimalImportantFields: ["level", "item_type", "rarity"] },
      };
    case "spells":
      return {
        key,
        getDescriptors: getSpellFieldDescriptors,
        // buildCell pour Spell : utilise spell.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Spell, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const spell = entityData instanceof Spell
            ? entityData
            : new Spell(entityData);

          return spell.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: SPELL_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Spell),
        defaults: { minimalImportantFields: ["level", "pa", "po", "element", "category"] },
      };
    case "monsters":
      return {
        key,
        getDescriptors: getMonsterFieldDescriptors,
        // buildCell pour Monster : utilise monster.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Monster, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const monster = entityData instanceof Monster
            ? entityData
            : new Monster(entityData);

          return monster.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: MONSTER_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Monster),
        defaults: { minimalImportantFields: ["creature_name", "monster_race", "size", "is_boss"] },
      };
    case "creatures":
      return {
        key,
        getDescriptors: getCreatureFieldDescriptors,
        // buildCell pour Creature : utilise creature.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Creature, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const creature = entityData instanceof Creature
            ? entityData
            : new Creature(entityData);

          return creature.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: CREATURE_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Creature),
        defaults: { minimalImportantFields: ["name", "level", "hostility", "life"] },
      };
    case "npcs":
      return {
        key,
        getDescriptors: getNpcFieldDescriptors,
        // buildCell pour NPC : utilise npc.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Npc, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const npc = entityData instanceof Npc
            ? entityData
            : new Npc(entityData);

          return npc.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: NPC_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Npc),
        defaults: { minimalImportantFields: ["creature_name", "classe", "specialization"] },
      };
    case "classes":
      return {
        key,
        getDescriptors: getClasseFieldDescriptors,
        // buildCell pour Classe : utilise classe.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Classe, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const classe = entityData instanceof Classe
            ? entityData
            : new Classe(entityData);

          return classe.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: CLASSE_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Classe),
        defaults: { minimalImportantFields: ["name", "life", "life_dice"] },
      };
    case "consumables":
      return {
        key,
        getDescriptors: getConsumableFieldDescriptors,
        // buildCell pour Consumable : utilise consumable.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Consumable, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const consumable = entityData instanceof Consumable
            ? entityData
            : new Consumable(entityData);

          return consumable.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: CONSUMABLE_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Consumable),
        defaults: { minimalImportantFields: ["level", "consumable_type", "rarity"] },
      };
    case "campaigns":
      return {
        key,
        getDescriptors: getCampaignFieldDescriptors,
        // buildCell pour Campaign : utilise campaign.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Campaign, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const campaign = entityData instanceof Campaign
            ? entityData
            : new Campaign(entityData);

          return campaign.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: CAMPAIGN_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Campaign),
        defaults: { minimalImportantFields: ["name", "state", "is_public"] },
      };
    case "scenarios":
      return {
        key,
        getDescriptors: getScenarioFieldDescriptors,
        // buildCell pour Scenario : utilise scenario.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Scenario, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const scenario = entityData instanceof Scenario
            ? entityData
            : new Scenario(entityData);

          return scenario.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: SCENARIO_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Scenario),
        defaults: { minimalImportantFields: ["name", "state", "is_public"] },
      };
    case "attributes":
      return {
        key,
        getDescriptors: getAttributeFieldDescriptors,
        // buildCell pour Attribute : utilise attribute.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Attribute, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const attribute = entityData instanceof Attribute
            ? entityData
            : new Attribute(entityData);

          return attribute.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: ATTRIBUTE_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Attribute),
        defaults: { minimalImportantFields: ["name", "usable", "is_visible"] },
      };
    case "panoplies":
      return {
        key,
        getDescriptors: getPanoplyFieldDescriptors,
        // buildCell pour Panoply : utilise panoply.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Panoply, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const panoply = entityData instanceof Panoply
            ? entityData
            : new Panoply(entityData);

          return panoply.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: PANOPLY_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Panoply),
        defaults: { minimalImportantFields: ["name", "bonus", "items_count"] },
      };
    case "capabilities":
      return {
        key,
        getDescriptors: getCapabilityFieldDescriptors,
        // buildCell pour Capability : utilise capability.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Capability, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const capability = entityData instanceof Capability
            ? entityData
            : new Capability(entityData);

          return capability.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: CAPABILITY_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Capability),
        defaults: { minimalImportantFields: ["name", "level", "pa", "po", "element"] },
      };
    case "specializations":
      return {
        key,
        getDescriptors: getSpecializationFieldDescriptors,
        // buildCell pour Specialization : utilise specialization.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Specialization, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const specialization = entityData instanceof Specialization
            ? entityData
            : new Specialization(entityData);

          return specialization.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: SPECIALIZATION_QUICK_EDIT_FIELDS,
        responseAdapter: createEntityAdapter(Specialization),
        defaults: { minimalImportantFields: ["name", "description", "capabilities_count"] },
      };
    case "shops":
      return {
        key,
        getDescriptors: getShopFieldDescriptors,
        // buildCell pour Shop : utilise shop.toCell() depuis l'instance du modèle
        buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
          // Si entityData est déjà une instance Shop, l'utiliser directement
          // Sinon, créer une instance depuis les données brutes
          const shop = entityData instanceof Shop
            ? entityData
            : new Shop(entityData);

          return shop.toCell(fieldKey, { ...ctx, ...opts });
        },
        viewFields: SHOP_QUICK_EDIT_FIELDS,
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


