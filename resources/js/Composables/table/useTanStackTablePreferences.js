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

    const visibleColumns = ref(saved?.visibleColumns || defaults.visibleColumns || {});
    const pageSize = ref(saved?.pageSize || defaults.pageSize || null);

    const persist = () => {
        if (typeof window === "undefined") return;
        try {
            window.localStorage?.setItem(key, JSON.stringify({
                visibleColumns: visibleColumns.value,
                pageSize: pageSize.value,
            }));
        } catch {
            // ignore
        }
    };

    watch(visibleColumns, persist, { deep: true });
    watch(pageSize, persist);

    const setColumnVisible = (columnId, isVisible) => {
        visibleColumns.value = { ...visibleColumns.value, [columnId]: Boolean(isVisible) };
    };

    const setPageSize = (size) => {
        const n = Number(size);
        if (!Number.isFinite(n) || n <= 0) return;
        pageSize.value = n;
    };

    return {
        visibleColumns,
        setColumnVisible,
        pageSize,
        setPageSize,
    };
}

export default useTanStackTablePreferences;


