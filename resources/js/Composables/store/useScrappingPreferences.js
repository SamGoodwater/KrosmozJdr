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

const DEFAULTS = {
    selectedEntityType: "monster",
    optSkipCache: false,
    optWithImages: true,
    optForceUpdate: false,
    optManualChoice: false,
    optIncludeRelations: true,
    perPage: 100,
    filterIds: "",
    filterName: "",
};

/**
 * Lit les préférences depuis localStorage.
 * @returns {Object} Objet partiel (seules les clés valides sont retournées).
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
        if (typeof parsed.optSkipCache === "boolean") out.optSkipCache = parsed.optSkipCache;
        if (typeof parsed.optWithImages === "boolean") out.optWithImages = parsed.optWithImages;
        if (typeof parsed.optForceUpdate === "boolean") out.optForceUpdate = parsed.optForceUpdate;
        if (typeof parsed.optManualChoice === "boolean") out.optManualChoice = parsed.optManualChoice;
        if (typeof parsed.optIncludeRelations === "boolean") out.optIncludeRelations = parsed.optIncludeRelations;
        if (Number.isFinite(Number(parsed.perPage))) out.perPage = Math.max(1, Math.min(200, Number(parsed.perPage)));
        if (typeof parsed.filterIds === "string") out.filterIds = parsed.filterIds;
        if (typeof parsed.filterName === "string") out.filterName = parsed.filterName;
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
        const raw = refs.selectedEntityType?.value;
        const selectedEntityTypeStr =
            typeof raw === "string" ? raw : raw && typeof raw === "object" && typeof raw.value === "string" ? raw.value : undefined;
        const prefs = {
            selectedEntityType: selectedEntityTypeStr,
            optSkipCache: refs.optSkipCache?.value,
            optWithImages: refs.optWithImages?.value,
            optForceUpdate: refs.optForceUpdate?.value,
            optManualChoice: refs.optManualChoice?.value,
            optIncludeRelations: refs.optIncludeRelations?.value,
            perPage: refs.perPage?.value,
            filterIds: refs.filterIds?.value,
            filterName: refs.filterName?.value,
        };
        Object.assign(prefs, overrides);
        saveScrappingPreferences(prefs);
    };

    const hydrate = () => {
        const loaded = loadScrappingPreferences();
        if (!refs || !Object.keys(loaded).length) return;
        if (loaded.selectedEntityType != null) {
            const v = loaded.selectedEntityType;
            refs.selectedEntityType.value = typeof v === "string" ? v : (v && typeof v === "object" && typeof v.value === "string" ? v.value : refs.selectedEntityType?.value ?? "");
        }
        if (loaded.optSkipCache != null) refs.optSkipCache.value = loaded.optSkipCache;
        if (loaded.optWithImages != null) refs.optWithImages.value = loaded.optWithImages;
        if (loaded.optForceUpdate != null) refs.optForceUpdate.value = loaded.optForceUpdate;
        if (loaded.optManualChoice != null) refs.optManualChoice.value = loaded.optManualChoice;
        if (loaded.optIncludeRelations != null) refs.optIncludeRelations.value = loaded.optIncludeRelations;
        if (loaded.perPage != null) refs.perPage.value = loaded.perPage;
        if (loaded.filterIds != null) refs.filterIds.value = loaded.filterIds;
        if (loaded.filterName != null) refs.filterName.value = loaded.filterName;
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
