/**
 * Helpers pour tests des field descriptors (schémas `edit` / `edition`, options, visibilité).
 */

/**
 * @param {Record<string, unknown>} desc
 * @returns {Record<string, unknown>|undefined}
 */
export function getDescriptorForm(desc) {
  return desc?.edition?.form ?? desc?.edit?.form;
}

/**
 * @param {Record<string, unknown>} desc
 * @returns {unknown}
 */
export function getDescriptorFormOptions(desc) {
  const form = getDescriptorForm(desc);
  return form?.options;
}

/**
 * @param {unknown} options
 * @param {Record<string, unknown>} [ctx]
 * @returns {unknown[]|undefined}
 */
export function resolveDescriptorOptions(options, ctx = {}) {
  if (options == null) return undefined;
  if (typeof options === "function") {
    return options(ctx);
  }
  if (Array.isArray(options)) return options;
  return undefined;
}

/**
 * Libellé affiché (ancien `label` racine ou `general.label`).
 *
 * @param {Record<string, unknown>} desc
 * @returns {string|undefined}
 */
export function descriptorLabel(desc) {
  if (!desc || typeof desc !== "object") return undefined;
  if (typeof desc.label === "string") return desc.label;
  const g = desc.general;
  if (g && typeof g === "object" && typeof g.label === "string") return g.label;
  return undefined;
}

/**
 * Indique si le descriptor définit des tailles d’affichage tableau.
 *
 * @param {Record<string, unknown>} desc
 * @returns {boolean}
 */
export function descriptorHasTableSizes(desc) {
  return Boolean(desc?.display?.sizes ?? desc?.table?.cell?.sizes);
}
