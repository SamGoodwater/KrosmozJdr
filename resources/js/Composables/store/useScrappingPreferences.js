/**
 * useScrappingPreferences
 *
 * @description
 * Persiste les préférences du module scrapping (type d'entité, options d'import) dans localStorage.
 * Clé : krosmoz_scrapping_prefs. Ne persiste pas de données sensibles ni de résultats.
 *
 * @example
 * const { prefs, hydrate, persist } = useScrappingPreferences();
 * hydrate(); // applique les valeurs stockées aux refs passées
 * watch([selectedEntityType, optSkipCache], () => persist({ ... }));
 */
const STORAGE_KEY = "krosmoz_scrapping_prefs";

export const DEFAULTS = {
    selectedEntityType: "monster",
    optIncludeRelations: true,
    optReplaceMode: "draft_raw_only",
    optSkipCache: false,
    optForceUpdate: false,
    optManualChoice: false,
    perPage: 100,
    filterIds: "",
    filterName: "",
    optPropertyWhitelist: "",
    optPropertyBlacklist: "",
};

/**
 * Lit les préférences depuis localStorage.
 * Applique les migrations à la lecture : optIncludeRelations false → true, optReplaceMode never → draft_raw_only.
 * @returns {Object} Objet partiel (seules les clés valides sont retournées, valeurs déjà migrées).
 */
export function loadScrappingPreferences() {
    if (typeof window === "undefined" || !window.localStorage) return {};
    try {
        const raw = window.localStorage.getItem(STORAGE_KEY);
        if (!raw) return {};
        const parsed = JSON.parse(raw);
        if (!parsed || typeof parsed !== "object") return {};
        const out = {};
        if (parsed.selectedEntityType != null) {
            const v = parsed.selectedEntityType;
            const str = typeof v === "string" ? v : (v && typeof v === "object" && typeof v.value === "string" ? v.value : null);
            if (str != null) out.selectedEntityType = str;
        }
        if (typeof parsed.optIncludeRelations === "boolean") out.optIncludeRelations = true;
        if (typeof parsed.optReplaceMode === "string" && ["never", "draft_raw_only", "always"].includes(parsed.optReplaceMode)) {
            out.optReplaceMode = parsed.optReplaceMode === "never" ? "draft_raw_only" : parsed.optReplaceMode;
        }
        if (typeof parsed.optSkipCache === "boolean") out.optSkipCache = parsed.optSkipCache;
        if (typeof parsed.optForceUpdate === "boolean") out.optForceUpdate = parsed.optForceUpdate;
        if (typeof parsed.optManualChoice === "boolean") out.optManualChoice = parsed.optManualChoice;
        if (Number.isFinite(Number(parsed.perPage))) out.perPage = Math.max(1, Math.min(200, Number(parsed.perPage)));
        if (typeof parsed.filterIds === "string") out.filterIds = parsed.filterIds;
        if (typeof parsed.filterName === "string") out.filterName = parsed.filterName;
        if (typeof parsed.optPropertyWhitelist === "string") out.optPropertyWhitelist = parsed.optPropertyWhitelist;
        if (typeof parsed.optPropertyBlacklist === "string") out.optPropertyBlacklist = parsed.optPropertyBlacklist;
        return out;
    } catch (_) {
        return {};
    }
}

/**
 * Enregistre les préférences dans localStorage.
 * @param {Object} prefs - Objet partiel (clés connues uniquement).
 */
export function saveScrappingPreferences(prefs) {
    if (!prefs || typeof prefs !== "object") return;
    if (typeof window === "undefined" || !window.localStorage) return;
    try {
        const current = loadScrappingPreferences();
        const merged = { ...current, ...prefs };
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(merged));
    } catch (_) {
        // ignore
    }
}

/**
 * Composable : fournit load/save et optionnellement lie des refs à la persistance.
 * @param {Object} refs - Refs du dashboard (selectedEntityType, optSkipCache, etc.)
 * @returns {{ loadScrappingPreferences, saveScrappingPreferences, hydrate, persist, DEFAULTS }}
 */
export function useScrappingPreferences(refs = null) {
    const persist = (overrides = {}) => {
        if (!refs) return;
        const prefs = {};
        for (const key of Object.keys(DEFAULTS)) {
            if (refs[key] == null) continue;
            const v = refs[key].value;
            prefs[key] = key === "selectedEntityType" ? (typeof v === "string" ? v : undefined) : v;
        }
        Object.assign(prefs, overrides);
        saveScrappingPreferences(prefs);
    };

    /** Applique les préférences chargées (déjà migrées) aux refs. Défauts utilisés si clé absente. */
    const hydrate = () => {
        if (!refs) return;
        const loaded = loadScrappingPreferences();
        for (const key of Object.keys(DEFAULTS)) {
            if (refs[key] != null) {
                refs[key].value = loaded[key] ?? DEFAULTS[key];
            }
        }
    };

    return {
        loadScrappingPreferences,
        saveScrappingPreferences,
        hydrate,
        persist,
        DEFAULTS,
    };
}

export default useScrappingPreferences;
