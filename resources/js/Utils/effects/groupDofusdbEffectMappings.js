/**
 * Filtre et groupe les mappings effectId DofusDB → sous-effet.
 *
 * @description
 * Le volume `autre` est voulu (hors périmètre) : masqué par défaut, sauf
 * recherche / effectId prérempli.
 *
 * @example
 * filterDofusdbEffectMappings([{ sub_effect_slug: 'autre', dofusdb_effect_id: 1 }], { showAutre: false })
 * // []
 */

/**
 * @param {unknown} row
 * @returns {string}
 */
function mappingSearchHaystack(row) {
    if (!row || typeof row !== "object") return "";
    return [
        row.dofusdb_effect_id,
        row.sub_effect_slug,
        row.sub_effect_label,
        row.characteristic_source,
        row.characteristic_key,
    ]
        .filter((v) => v !== null && typeof v !== "undefined" && v !== "")
        .map((v) => String(v).toLowerCase())
        .join(" ");
}

/**
 * @param {Array<Record<string, unknown>>} rows
 * @param {{ query?: string, showAutre?: boolean, prefillEffectId?: string }} [options]
 * @returns {Array<Record<string, unknown>>}
 */
export function filterDofusdbEffectMappings(rows, options = {}) {
    const list = Array.isArray(rows) ? rows : [];
    const query = String(options.query ?? "").trim().toLowerCase();
    const showAutre = Boolean(options.showAutre);
    const prefill = String(options.prefillEffectId ?? "").trim();

    return list.filter((row) => {
        if (query) {
            return mappingSearchHaystack(row).includes(query);
        }
        const slug = String(row?.sub_effect_slug ?? "");
        if (!showAutre && slug === "autre") {
            return prefill !== "" && String(row?.dofusdb_effect_id ?? "") === prefill;
        }
        return true;
    });
}

/**
 * @param {Array<Record<string, unknown>>} rows
 * @returns {Array<{ slug: string, label: string, rows: Array<Record<string, unknown>> }>}
 */
export function groupDofusdbEffectMappings(rows) {
    const list = Array.isArray(rows) ? rows : [];
    /** @type {Map<string, { slug: string, label: string, rows: Array<Record<string, unknown>> }>} */
    const groups = new Map();

    for (const row of list) {
        const slug = String(row?.sub_effect_slug ?? "").trim() || "(sans slug)";
        const existing = groups.get(slug);
        if (existing) {
            existing.rows.push(row);
            continue;
        }
        groups.set(slug, {
            slug,
            label: String(row?.sub_effect_label ?? slug),
            rows: [row],
        });
    }

    return Array.from(groups.values());
}
