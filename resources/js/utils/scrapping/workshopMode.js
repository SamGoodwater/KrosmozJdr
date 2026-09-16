/**
 * Modes atelier DofusDB et options d’import associées.
 * @see docs/features/scrapping/README.md
 */

/** @typedef {"retrieve"|"update"|"complete"} WorkshopMode */

export const WORKSHOP_MODES = [
    {
        value: "retrieve",
        label: "Récupérer",
        hint: "Importe les fiches DofusDB (nouvelles, et brouillons/raw si auto_update). Les nouvelles fiches sont créées en état raw.",
    },
    {
        value: "update",
        label: "Mettre à jour",
        hint: "Met à jour les fiches déjà en base. « Respecter auto_update » (défaut) n’écrase que les fiches auto_update ; décoché = forcer.",
    },
    {
        value: "complete",
        label: "Compléter",
        hint: "Ne récupère que les identifiants DofusDB absents localement (only_missing). N’écrase jamais l’existant.",
    },
];

/**
 * @param {string} mode
 * @returns {mode is WorkshopMode}
 */
export function isWorkshopMode(mode) {
    return mode === "retrieve" || mode === "update" || mode === "complete";
}

export const WORKSHOP_PAGE_PATH = "/admin/content/dofusdb";
export const IA_GENERATION_PAGE_PATH = "/admin/content/ia-generation";

/**
 * Mode atelier depuis l’URL (`?mode=retrieve|update|complete`).
 *
 * @param {string} [url]
 * @returns {WorkshopMode}
 *
 * @example
 * workshopModeFromUrl("/admin/content/dofusdb?mode=complete"); // "complete"
 */
export function workshopModeFromUrl(url) {
    const raw = String(url || "");
    const queryIndex = raw.indexOf("?");
    if (queryIndex === -1) {
        return "retrieve";
    }
    const params = new URLSearchParams(raw.slice(queryIndex + 1));
    const mode = params.get("mode");
    return isWorkshopMode(mode) ? mode : "retrieve";
}

/**
 * Lien atelier avec mode en query.
 *
 * @param {string} [mode]
 * @returns {string}
 *
 * @example
 * workshopPageHref("update"); // "/admin/content/dofusdb?mode=update"
 */
export function workshopPageHref(mode) {
    const value = isWorkshopMode(mode) ? mode : "retrieve";
    return `${WORKSHOP_PAGE_PATH}?mode=${value}`;
}

/**
 * @param {{ mode?: string, respectAutoUpdate?: boolean }} opts
 * @returns {"ignore"|"draft_raw_auto_update"|"auto_update"|"force"}
 */
export function workshopUpdateMode(opts = {}) {
    const mode = isWorkshopMode(opts.mode) ? opts.mode : "retrieve";
    if (mode === "complete") {
        return "ignore";
    }
    if (mode === "update") {
        return opts.respectAutoUpdate === false ? "force" : "auto_update";
    }
    return "draft_raw_auto_update";
}

/**
 * Clés de propriétés (mapping + image) pour les checkboxes atelier.
 * @param {Record<string, { comparisonKeys?: string[] }>|null|undefined} config
 * @param {string} entity
 * @returns {string[]}
 */
export function propertyKeysFromConfig(config, entity) {
    const keys = config?.[entity]?.comparisonKeys;
    const out = [];
    const seen = new Set();
    const list = Array.isArray(keys) ? keys : [];
    for (const raw of [...list, "image"]) {
        const key = String(raw || "").trim();
        if (!key || seen.has(key)) {
            continue;
        }
        seen.add(key);
        out.push(key);
    }
    return out;
}

/**
 * Construit whitelist / images à partir des cases cochées.
 * Toutes cochées = pas de filtre (whitelist vide).
 *
 * @param {string[]} allKeys
 * @param {string[]} selectedKeys
 * @returns {{ property_whitelist: string[], with_images: boolean }}
 */
export function propertyPayloadFromSelection(allKeys, selectedKeys) {
    const selected = Array.isArray(selectedKeys) ? selectedKeys.map((k) => String(k)) : [];
    const all = Array.isArray(allKeys) ? allKeys.map((k) => String(k)) : [];
    const withImages = selected.includes("image");
    const allSelected = all.length > 0 && all.every((k) => selected.includes(k));
    return {
        property_whitelist: allSelected || selected.length === 0 ? [] : selected,
        with_images: withImages,
    };
}
