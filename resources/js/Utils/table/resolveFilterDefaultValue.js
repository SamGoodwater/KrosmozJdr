/**
 * Résout la valeur initiale d’un filtre tableau (descriptor `filterable`).
 *
 * @description
 * - `defaultValue` : valeur déjà prête (ids, etc.)
 * - `defaultByLabel` / `defaultByDofusTypeId` : résolution via `filterOptions`
 * - `defaultByCatalog` : options avec `show_in_catalog`
 *
 * @example
 * resolveFilterDefaultValue(
 *   { defaultByLabel: ['Amulette'] },
 *   [{ value: '12', label: 'Amulette', dofusdb_type_id: 1 }],
 * )
 * // ['12']
 */

import { isShownInCatalog, normalizeItemTypeLabel } from "@/Utils/Entity/gameplayItemTypes";

/**
 * @param {object|null|undefined} filter
 * @param {Array<{value?: unknown, label?: unknown, dofusdb_type_id?: unknown}>} options
 * @returns {unknown}
 */
export function resolveFilterDefaultValue(filter, options = []) {
    if (!filter || typeof filter !== "object") return undefined;
    if (Object.prototype.hasOwnProperty.call(filter, "defaultValue")) {
        return filter.defaultValue;
    }

    const byLabel = Array.isArray(filter.defaultByLabel)
        ? filter.defaultByLabel.map(normalizeItemTypeLabel).filter(Boolean)
        : [];
    const byDofus = Array.isArray(filter.defaultByDofusTypeId)
        ? filter.defaultByDofusTypeId.map(Number).filter((n) => Number.isFinite(n) && n > 0)
        : [];
    const byCatalog = Boolean(filter.defaultByCatalog);
    if (byLabel.length === 0 && byDofus.length === 0 && !byCatalog) return undefined;

    const opts = Array.isArray(options) ? options : [];
    if (opts.length === 0) return undefined;

    const wantedLabels = new Set(byLabel);
    const wantedDofus = new Set(byDofus);
    const ids = [];
    const seen = new Set();

    for (const opt of opts) {
        const value = opt?.value;
        if (value === null || typeof value === "undefined" || value === "") continue;
        const dofus = Number(opt?.dofusdb_type_id);
        const label = normalizeItemTypeLabel(opt?.label);
        const match =
            (byCatalog && isShownInCatalog(opt))
            || (Number.isFinite(dofus) && dofus > 0 && wantedDofus.has(dofus))
            || wantedLabels.has(label);
        if (!match) continue;
        const key = String(value);
        if (seen.has(key)) continue;
        seen.add(key);
        ids.push(key);
    }

    return ids.length > 0 ? ids : undefined;
}
