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

