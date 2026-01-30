/**
 * applyPatchToDataset
 *
 * @description
 * Applique un patch "bulk" à un tableau d'entités côté client (mode dataset chargé).
 * Ne touche qu'aux IDs ciblés, et ne modifie que les clés présentes dans le payload.
 *
 * @example
 * allRows.value = applyPatchToDataset(allRows.value, payload, {
 *   normalize: { state: (v) => String(v) },
 *   afterPatch: (next) => next,
 * });
 */

/**
 * @param {Array<object>} rows
 * @param {object} payload - Doit contenir `ids: number[]` + des champs à appliquer
 * @param {object} [options]
 * @param {string} [options.idKey="id"]
 * @param {Record<string, (value:any, ctx:{entity:any, next:any, payload:any})=>any>} [options.normalize]
 * @param {(next:any, ctx:{entity:any, payload:any, patch:any})=>any|void} [options.afterPatch]
 * @returns {Array<object>}
 */
export function applyPatchToDataset(rows, payload, options = {}) {
  const {
    idKey = "id",
    normalize = {},
    afterPatch = null,
  } = options;

  const safeRows = Array.isArray(rows) ? rows : [];
  const ids = Array.isArray(payload?.ids) ? payload.ids : [];
  const idsSet = new Set(ids.map((v) => String(v)));

  const patch = { ...(payload || {}) };
  delete patch.ids;

  return safeRows.map((entity) => {
    const entityId = entity?.[idKey];
    if (!idsSet.has(String(entityId))) return entity;

    const next = { ...entity };
    for (const [key, value] of Object.entries(patch)) {
      if (typeof value === "undefined") continue;
      const normalizer = normalize?.[key];
      next[key] = typeof normalizer === "function"
        ? normalizer(value, { entity, next, payload })
        : value;
    }

    if (typeof afterPatch === "function") {
      const out = afterPatch(next, { entity, payload, patch });
      return out || next;
    }

    return next;
  });
}


