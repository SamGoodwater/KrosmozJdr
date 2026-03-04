/**
 * Entity View UI helpers
 *
 * @description
 * Helpers partagés pour les vues d'entités (Large/Compact/Minimal) afin de :
 * - Centraliser la logique de tooltip (helper) à partir des descriptors
 * - Fournir des libellés courts (ex: "nvx") quand pertinent
 * - Décider si un champ peut s'afficher sans libellé dans les metas (ex: type)
 *
 * @example
 * import { getEntityFieldTooltip, getEntityFieldShortLabel } from "@/Utils/Entity/entity-view-ui";
 * const tooltip = getEntityFieldTooltip(descriptors.value?.level);
 * const label = getEntityFieldShortLabel("level", "Niveau"); // "nvx"
 */

/**
 * Retourne le tooltip le plus pertinent pour un champ.
 *
 * @param {Object} desc - Descriptor du champ (getXFieldDescriptors()[key])
 * @returns {string}
 */
export function getEntityFieldTooltip(desc) {
  if (!desc || typeof desc !== "object") return "";
  return String(
    desc?.display?.tooltip ||
      desc?.table?.header?.tooltip ||
      desc?.general?.tooltip ||
      desc?.edition?.form?.help ||
      "",
  );
}

/**
 * Retourne un libellé "court" pour l'affichage compact/minimal.
 *
 * @param {string} fieldKey
 * @param {string} label - Libellé standard (souvent desc.general.label)
 * @returns {string}
 */
export function getEntityFieldShortLabel(fieldKey, label) {
  const key = String(fieldKey || "");
  const fallback = String(label || key);
  const map = {
    level: "nvx",
  };
  return map[key] || fallback;
}

/**
 * Champs dont la valeur est généralement compréhensible sans afficher le nom de propriété
 * dans une zone "meta" (ex: type).
 *
 * @param {string} fieldKey
 * @returns {boolean}
 */
export function shouldOmitLabelInMeta(fieldKey) {
  const key = String(fieldKey || "");
  return [
    "resource_type",
    "item_type",
    "monster_race",
    "spell_types",
    "category",
    "element",
  ].includes(key);
}

/**
 * Mapping des aliases de groupes de caractéristiques dans `tableMeta.characteristics`.
 * Permet de gérer les différences de nommage entre entité UI et payload API.
 *
 * @param {string} entityType
 * @returns {string[]}
 */
export function getCharacteristicsEntityAliases(entityType) {
  const key = String(entityType || "").toLowerCase();
  const aliases = {
    item: ["item", "object"],
    spell: ["spell"],
    capability: ["capability", "spell"],
    monster: ["creature", "monster"],
    resource: ["resource", "object"],
  };
  return aliases[key] || [key];
}

/**
 * Retourne le mapping `byDbColumn` correspondant à l'entité depuis `tableMeta`.
 *
 * @param {Object} tableMeta
 * @param {string} entityType
 * @returns {Record<string, any>}
 */
export function getEntityCharacteristicsByDbColumn(tableMeta, entityType) {
  const characteristics = tableMeta?.characteristics || {};
  const aliases = getCharacteristicsEntityAliases(entityType);
  for (const alias of aliases) {
    const byDbColumn = characteristics?.[alias]?.byDbColumn;
    if (byDbColumn && typeof byDbColumn === "object") {
      return byDbColumn;
    }
  }
  return {};
}

/**
 * Résout les métadonnées UI d'un champ en priorisant:
 * 1) caractéristiques BDD (tableMeta.characteristics)
 * 2) descriptors frontend
 * 3) fallback brut
 *
 * @param {Object} options
 * @param {string} options.fieldKey
 * @param {Object} options.descriptors
 * @param {Object} options.tableMeta
 * @param {string} options.entityType
 * @returns {{label:string, shortLabel:string, icon:string, tooltip:string, color:string, characteristic:any}}
 */
export function resolveEntityFieldUi(options = {}) {
  const fieldKey = String(options.fieldKey || "");
  const descriptors = options.descriptors || {};
  const tableMeta = options.tableMeta || {};
  const entityType = String(options.entityType || "");

  const desc = descriptors?.[fieldKey] || {};
  const byDbColumn = getEntityCharacteristicsByDbColumn(tableMeta, entityType);
  const characteristic = byDbColumn?.[fieldKey] || null;

  const descriptorLabel = String(desc?.general?.label || fieldKey);
  const characteristicLabel = String(
    characteristic?.short_name || characteristic?.name || ""
  );
  const label = characteristicLabel || descriptorLabel;

  const descriptorTooltip = getEntityFieldTooltip(desc);
  const characteristicTooltip = String(
    characteristic?.helper ||
      (Array.isArray(characteristic?.descriptions)
        ? characteristic.descriptions.join(" ")
        : characteristic?.descriptions || "") ||
      ""
  );
  const tooltip = characteristicTooltip || descriptorTooltip;

  const icon = String(
    characteristic?.icon || desc?.general?.icon || "fa-solid fa-info-circle"
  );
  const color = String(characteristic?.color || "");

  return {
    label,
    shortLabel: getEntityFieldShortLabel(fieldKey, label),
    icon,
    tooltip,
    color,
    characteristic,
  };
}

/**
 * Détermine le style de badge d'un champ (couleur + auto params).
 * Priorise:
 * 1) couleur de cellule (`cell.params.color`)
 * 2) couleur issue des caractéristiques BDD
 * 3) mapping local fourni par la vue
 *
 * @param {Object} options
 * @param {string} options.fieldKey
 * @param {Object} options.cell
 * @param {{color?:string}} options.fieldUi
 * @param {Record<string,string>} [options.localColorMap]
 * @returns {{color:string, autoLabel?:string, autoScheme?:string, autoTone?:string}}
 */
export function resolveEntityBadgeUi(options = {}) {
  const fieldKey = String(options.fieldKey || "");
  const cell = options.cell || {};
  const fieldUi = options.fieldUi || {};
  const localColorMap = options.localColorMap || {};

  let color = "neutral";
  if (cell?.params?.color) {
    color = String(cell.params.color);
  } else if (fieldUi?.color) {
    // Si la couleur vient de la BDD (hex), on bascule sur un thème stable.
    color = "primary";
  } else if (localColorMap[fieldKey]) {
    color = String(localColorMap[fieldKey]);
  }

  if (fieldKey === "rarity" && cell?.value) {
    return {
      color,
      autoLabel: String(cell.value),
      autoScheme: "rarity",
      autoTone: "mid",
    };
  }
  if (fieldKey === "level" && cell?.value) {
    return {
      color,
      autoLabel: String(cell.value),
      autoScheme: "level",
      autoTone: "mid",
    };
  }

  return { color };
}

