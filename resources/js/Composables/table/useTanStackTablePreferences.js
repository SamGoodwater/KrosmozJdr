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
const PREFS_VERSION = 2;

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

    // Migration: les anciennes prefs (v1) contenaient souvent des valeurs explicites pour toutes les colonnes,
    // ce qui écrase les nouveaux defaults responsive. En v2, on n'applique les overrides que si la colonne a été "touchée".
    const isV2 = Number(saved?.version) === PREFS_VERSION;

    const visibleColumns = ref(isV2 ? (saved?.visibleColumns || {}) : (defaults.visibleColumns || {}));
    const touchedColumns = ref(isV2 ? (saved?.touchedColumns || []) : []);
    const pageSize = ref(saved?.pageSize || defaults.pageSize || null);

    const persist = () => {
        if (typeof window === "undefined") return;
        try {
            window.localStorage?.setItem(key, JSON.stringify({
                version: PREFS_VERSION,
                visibleColumns: visibleColumns.value,
                touchedColumns: touchedColumns.value,
                pageSize: pageSize.value,
            }));
        } catch {
            // ignore
        }
    };

    watch(visibleColumns, persist, { deep: true });
    watch(touchedColumns, persist, { deep: true });
    watch(pageSize, persist);

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

    return {
        visibleColumns,
        touchedColumns,
        setColumnVisible,
        pageSize,
        setPageSize,
    };
}

export default useTanStackTablePreferences;


