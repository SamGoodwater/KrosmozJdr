/**
 * useTableVirtualizer
 *
 * @description
 * Virtualisation des lignes pour tableaux avec beaucoup de données (500+).
 * Utilise @tanstack/vue-virtual pour ne rendre que les lignes visibles.
 * À utiliser uniquement en mode client (le mode serveur pagine déjà).
 *
 * @example
 * const parentRef = ref(null);
 * const { virtualItems, totalSize, isEnabled, getRowForIndex } = useTableVirtualizer({
 *   parentRef,
 *   rowCount: computed(() => rowsToRender.value.length),
 *   rowHeight: 52,
 *   enabled: computed(() => virtualizationEnabled && !serverSide),
 * });
 */

import { useVirtualizer } from "@tanstack/vue-virtual";
import { computed, toValue } from "vue";

const DEFAULT_ROW_HEIGHT = 48;

export function useTableVirtualizer(options = {}) {
    const {
        parentRef,
        rowCount = 0,
        rowHeight = DEFAULT_ROW_HEIGHT,
        enabled = false,
        overscan = 5,
    } = options;

    const rowVirtualizer = useVirtualizer(
        computed(() => {
            const count = Math.max(0, Number(toValue(rowCount)) || 0);
            const active = Boolean(toValue(enabled)) && count > 0;
            return {
                count: active ? count : 0,
                getScrollElement: () => parentRef?.value ?? null,
                estimateSize: () => Number(rowHeight) || DEFAULT_ROW_HEIGHT,
                overscan,
            };
        }),
    );

    const virtualItems = computed(() => {
        const v = rowVirtualizer.value;
        return v?.getVirtualItems?.() ?? [];
    });

    const totalSize = computed(() => {
        const v = rowVirtualizer.value;
        return v?.getTotalSize?.() ?? 0;
    });

    const isEnabled = computed(() => {
        const count = Math.max(0, Number(toValue(rowCount)) || 0);
        return Boolean(toValue(enabled)) && count > 0;
    });

    return {
        virtualItems,
        totalSize,
        isEnabled,
        rowVirtualizer,
    };
}
