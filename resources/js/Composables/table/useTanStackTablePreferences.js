/**
 * useTanStackTablePreferences
 *
 * @description
 * Persiste les préférences UI d'un TanStackTable (colonnes visibles, pageSize, etc.)
 * dans localStorage, via une clé stable (ex: config.id).
 *
 * @example
 * const { visibleColumns, setColumnVisible, pageSize, setPageSize } = useTanStackTablePreferences('resources.index');
 */

import { ref, watch } from "vue";

const STORAGE_PREFIX = "tanstack_table_prefs_";
/** v4 : défaut d’affichage `minimal` (les prefs v3 en `line` sont migrées). */
const PREFS_VERSION = 4;

function safeParse(json) {
    try {
        return JSON.parse(json);
    } catch {
        return null;
    }
}

export function useTanStackTablePreferences(tableId, defaults = {}) {
    const key = STORAGE_PREFIX + String(tableId || "unknown");

    const saved = typeof window !== "undefined"
        ? safeParse(window.localStorage?.getItem(key) || "")
        : null;

    // Migration: v1 sans version ; v2 touchedColumns ; v3 quickEdit + sorting ; v4 défaut minimal
    const savedVer = Number(saved?.version);
    const hasModernColumnPrefs = Number.isFinite(savedVer) && savedVer >= 2;

    const visibleColumns = ref(hasModernColumnPrefs ? (saved?.visibleColumns || {}) : (defaults.visibleColumns || {}));
    const touchedColumns = ref(hasModernColumnPrefs ? (saved?.touchedColumns || []) : []);
    const pageSize = ref(saved?.pageSize || defaults.pageSize || null);
    /** displayMode: 'table' | 'line' | 'minimal'. Défaut: 'minimal'. */
    const defaultDisplayMode = defaults.displayMode ?? "minimal";
    let initialDisplayMode = saved?.displayMode ?? defaultDisplayMode;
    const migrateLineToMinimal =
        Boolean(saved) && Number.isFinite(savedVer) && savedVer < 4 && initialDisplayMode === "line";
    if (migrateLineToMinimal) {
        initialDisplayMode = defaultDisplayMode;
    }
    const displayMode = ref(initialDisplayMode);
    /** Quick edit panel : désactivé par défaut ; l’utilisateur réactive via le toggle (persisté). */
    const quickEditEnabled = ref(
        typeof saved?.quickEditEnabled === "boolean"
            ? saved.quickEditEnabled
            : (defaults.quickEditEnabled === true),
    );
    /**
     * Tri multi (TanStack) persisté : [{ id: string, desc: boolean }, ...]
     * Ignoré si colonnes inconnues au chargement (filtré côté TanStackTable).
     */
    const sorting = ref(Array.isArray(saved?.sorting) ? saved.sorting : (defaults.sorting || []));

    const persist = () => {
        if (typeof window === "undefined") return;
        try {
            window.localStorage?.setItem(key, JSON.stringify({
                version: PREFS_VERSION,
                visibleColumns: visibleColumns.value,
                touchedColumns: touchedColumns.value,
                pageSize: pageSize.value,
                displayMode: displayMode.value,
                quickEditEnabled: quickEditEnabled.value,
                sorting: sorting.value,
            }));
        } catch {
            // ignore
        }
    };

    watch(visibleColumns, persist, { deep: true });
    watch(touchedColumns, persist, { deep: true });
    watch(pageSize, persist);
    watch(displayMode, persist);
    watch(quickEditEnabled, persist);
    watch(sorting, persist, { deep: true });

    if (migrateLineToMinimal) {
        persist();
    }

    const setColumnVisible = (columnId, isVisible) => {
        const id = String(columnId || "");
        if (!id) return;
        visibleColumns.value = { ...visibleColumns.value, [id]: Boolean(isVisible) };
        if (!touchedColumns.value.includes(id)) {
            touchedColumns.value = [...touchedColumns.value, id];
        }
    };

    const setPageSize = (size) => {
        const n = Number(size);
        if (!Number.isFinite(n) || n <= 0) return;
        pageSize.value = n;
    };

    /**
     * Réinitialise uniquement les préférences de colonnes.
     * But: revenir aux defaults responsive du descriptor, sans perdre pageSize.
     */
    const resetColumns = () => {
        visibleColumns.value = {};
        touchedColumns.value = [];
    };

    const setDisplayMode = (mode) => {
        if (mode === "table" || mode === "line" || mode === "minimal") {
            displayMode.value = mode;
        }
    };

    const setQuickEditEnabled = (v) => {
        quickEditEnabled.value = Boolean(v);
    };

    const setSorting = (list) => {
        sorting.value = Array.isArray(list) ? list : [];
    };

    return {
        visibleColumns,
        touchedColumns,
        setColumnVisible,
        pageSize,
        setPageSize,
        resetColumns,
        displayMode,
        setDisplayMode,
        quickEditEnabled,
        setQuickEditEnabled,
        sorting,
        setSorting,
    };
}

export default useTanStackTablePreferences;


