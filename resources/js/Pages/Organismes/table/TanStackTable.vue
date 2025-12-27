<script setup>
/**
 * TanStackTable Organism
 *
 * @description
 * Tableau générique basé sur TanStack Table (Table v2).
 * - Client-first: tri côté client par défaut sur dataset
 * - Serveur opt-in: géré par wrapper (EntityTanStackTable) via `serverUrl` (Phase 2)
 * - Cellules: rend `Cell{type,value,params}` via `CellRenderer`
 * - Loading: skeleton par cellule
 *
 * @see docs/30-UI/TANSTACK_TABLE.md
 *
 * @example
 * <TanStackTable :config="config" :rows="rows" :loading="loading" />
 */

import { computed, ref, watch } from "vue";
import { getCoreRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from "@tanstack/vue-table";
import TanStackTableHeader from "@/Pages/Molecules/table/TanStackTableHeader.vue";
import TanStackTableRow from "@/Pages/Molecules/table/TanStackTableRow.vue";
import TanStackTableSkeletonBody from "@/Pages/Molecules/table/TanStackTableSkeletonBody.vue";
import TanStackTableToolbar from "@/Pages/Molecules/table/TanStackTableToolbar.vue";
import TanStackTableFilters from "@/Pages/Molecules/table/TanStackTableFilters.vue";
import TanStackTablePagination from "@/Pages/Molecules/table/TanStackTablePagination.vue";
import { useTanStackTablePreferences } from "@/Composables/table/useTanStackTablePreferences";

const props = defineProps({
    /**
     * TanStackTableConfig (spec Table v2)
     */
    config: { type: Object, required: true },
    /**
     * Dataset (TableRow[])
     */
    rows: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
    /**
     * Options de filtres (par filterId). Permet au wrapper d'injecter des options serveur.
     */
    filterOptions: { type: Object, default: null },
    /**
     * IDs sélectionnés (mode contrôlé). Si fourni, TanStackTable se synchronise dessus.
     */
    selectedIds: { type: Array, default: null },
});

const emit = defineEmits(["row-click", "row-dblclick", "sort-change", "update:selectedIds"]);

const columnsConfig = computed(() => Array.isArray(props.config?.columns) ? props.config.columns : []);

/**
 * UI (style global du tableau).
 * @see docs/30-UI/TANSTACK_TABLE.md
 */
const uiSize = computed(() => String(props.config?.ui?.size || "md"));
const uiColor = computed(() => String(props.config?.ui?.color || "primary"));

// Table variant (stripes)
const uiTableVariant = computed(() => {
    const v = props.config?.ui?.tableVariant ?? props.config?.ui?.variant ?? "zebra";
    return String(v || "zebra");
});

// Background variant (bg-glass-md, bg-soft-md, ...)
const normalizeBgVariant = (v) => {
    if (!v) return { variant: "glass", size: "md" };
    const raw = String(v);

    // Support: "outline-md" / "glass-xl" etc.
    const parts = raw.split("-");
    if (parts.length >= 2) {
        const maybeVariant = parts[0];
        const maybeSize = parts[1];
        return { variant: maybeVariant, size: maybeSize };
    }

    return { variant: raw, size: String(props.config?.ui?.bgSize ?? "md") };
};

const bgSettings = computed(() => {
    // Priorité: bgVariant explicite, sinon variant (compat)
    const v = props.config?.ui?.bgVariant ?? props.config?.ui?.variant ?? "glass";
    const { variant, size } = normalizeBgVariant(v);

    // si variant est un mode table (zebra/plain), fallback background glass
    if (["zebra", "striped", "plain", "default"].includes(variant)) {
        return { variant: "glass", size: String(props.config?.ui?.bgSize ?? "md") };
    }

    return { variant, size };
});

const bgVariant = computed(() => bgSettings.value.variant);
const bgSize = computed(() => {
    const s = String(bgSettings.value.size || "md");
    if (s === "xs") return "xs";
    if (s === "sm") return "sm";
    if (s === "lg") return "lg";
    if (s === "xl") return "xl";
    return "md";
});

const tableVariantClass = computed(() => {
    // DaisyUI: `table-zebra` (stripes) est la variante principale utilisée ici.
    if (uiTableVariant.value === "zebra" || uiTableVariant.value === "striped") return "table-zebra";
    if (uiTableVariant.value === "plain" || uiTableVariant.value === "default") return "";
    return "table-zebra";
});

const tableSizeClass = computed(() => {
    // DaisyUI table sizes: table-xs / table-sm / table-md / table-lg
    if (uiSize.value === "xs") return "table-xs";
    if (uiSize.value === "sm") return "table-sm";
    if (uiSize.value === "lg") return "table-lg";
    return "table-md";
});

const bgClass = computed(() => {
    // Classes SCSS (resources/scss/src/_bg.scss) : explicites

    let bg = "bg-";
    switch (bgVariant.value) {
        case "glass":
            bg += "glass-";
            break;
        case "ghost":
            bg += "ghost-";
            break;
        case "soft":
            bg += "soft-";
            break;
        case "outline":
            bg += "outline-";
            break;
        case "dash":
            bg += "dash-";
            break;
        default:
            bg += "glass-";
            break;
    }

    bg += bgSize.value;

    return bg + " " + "bg-color-" + uiColor.value;
});

const rowSelectedBgClass = computed(() => {
    const c = uiColor.value;
    if (c === "primary") return "bg-primary/10";
    if (c === "secondary") return "bg-secondary/10";
    if (c === "accent") return "bg-accent/10";
    if (c === "info") return "bg-info/10";
    if (c === "success") return "bg-success/10";
    if (c === "warning") return "bg-warning/10";
    if (c === "error") return "bg-error/10";
    if (c === "neutral") return "bg-neutral/10";
    return "bg-base-200/50";
});

// Préférences (colonnes visibles + pageSize)
const prefs = useTanStackTablePreferences(props.config?.id, {
    visibleColumns: {},
    pageSize: props.config?.features?.pagination?.perPage?.default ?? 25,
});

const visibleColumns = computed(() => prefs.visibleColumns.value || {});

/**
 * Applique `defaultHidden` seulement si aucune préférence explicite n'existe pour la colonne.
 * But: respecter `defaultHidden` (y compris pour des colonnes ajoutées plus tard) sans écraser
 * le choix d'un utilisateur (localStorage).
 */
const applyDefaultColumnVisibility = () => {
    const current = prefs.visibleColumns.value || {};
    let changed = false;

    for (const col of columnsConfig.value || []) {
        if (!col?.id) continue;

        // Colonnes non masquables / main = toujours visibles
        if (col?.hideable === false || col?.isMain) {
            if (current[col.id] !== true) {
                current[col.id] = true;
                changed = true;
            }
            continue;
        }

        // Si aucune préférence explicite, appliquer defaultHidden
        if (typeof current[col.id] === "undefined") {
            current[col.id] = col?.defaultHidden ? false : true;
            changed = true;
        }
    }

    if (changed) {
        prefs.visibleColumns.value = { ...current };
    }
};

watch(
    () => columnsConfig.value.map((c) => `${c?.id}:${c?.defaultHidden ? 1 : 0}:${c?.hideable === false ? 1 : 0}:${c?.isMain ? 1 : 0}`).join("|"),
    () => applyDefaultColumnVisibility(),
    { immediate: true },
);

const filteredColumnsConfig = computed(() => {
    return (columnsConfig.value || []).filter((col) => visibleColumns.value?.[col.id] !== false);
});

// Search + Filters (client-first)
const searchEnabled = computed(() => Boolean(props.config?.features?.search?.enabled));
const searchPlaceholder = computed(() => props.config?.features?.search?.placeholder || "Rechercher…");
const searchDebounceMs = computed(() => Number(props.config?.features?.search?.debounceMs ?? 150));

const filtersEnabled = computed(() => Boolean(props.config?.features?.filters?.enabled));
const hasFilterableColumns = computed(() => {
    return (filteredColumnsConfig.value || []).some((c) => Boolean(c?.filter?.id && c?.filter?.type));
});
const filterOptions = computed(() => {
    if (props.filterOptions && typeof props.filterOptions === "object") return props.filterOptions;
    return props.config?.filterOptions || {};
});
const activeFilters = ref({});

let _searchTimeout = null;
const searchText = ref("");
const updateSearch = (value) => {
    const v = String(value ?? "");
    if (_searchTimeout) clearTimeout(_searchTimeout);
    _searchTimeout = setTimeout(() => {
        searchText.value = v;
    }, Math.max(0, searchDebounceMs.value));
};

const normalize = (s) => {
    const v = String(s ?? "").toLowerCase();
    // simple diacritics-insensitive
    return v.normalize("NFD").replace(/\p{Diacritic}/gu, "");
};

const getCellFor = (row, col) => {
    const cellId = col?.cellId || col?.id;
    const fromCells = row?.cells?.[cellId];
    if (fromCells) return fromCells;

    // Convenience: colonne "id" sans cellule dédiée (le row.id existe toujours)
    if (cellId === "id" && (row?.id !== null && typeof row?.id !== "undefined")) {
        const v = row.id;
        return { type: "text", value: v, params: { sortValue: v, searchValue: String(v) } };
    }

    return { type: "text", value: null, params: {} };
};

const getFilterValueFor = (row, col) => {
    if (typeof col?.filter?.filterValue === "function") {
        try {
            return col.filter.filterValue(row);
        } catch {
            // fallback
        }
    }
    const cell = getCellFor(row, col);
    if (typeof cell?.params?.filterValue !== "undefined") return cell.params.filterValue;
    return cell?.value ?? null;
};

const passesFilter = (row, col) => {
    const f = col?.filter;
    if (!f?.id || !f?.type) return true;
    const raw = activeFilters.value?.[f.id];
    if (raw === null || typeof raw === "undefined" || raw === "") return true;

    const rowValue = getFilterValueFor(row, col);

    if (f.type === "boolean") {
        const want = String(raw) === "1" || String(raw).toLowerCase() === "true";

        // Important: éviter Boolean("0") === true
        const rowBool = (() => {
            if (typeof rowValue === "boolean") return rowValue;
            const s = String(rowValue ?? "").toLowerCase();
            if (s === "1" || s === "true" || s === "yes" || s === "oui") return true;
            if (s === "0" || s === "false" || s === "no" || s === "non") return false;
            return Boolean(rowValue);
        })();

        return rowBool === want;
    }

    if (f.type === "text") {
        return normalize(rowValue).includes(normalize(raw));
    }

    // select (default)
    return String(rowValue ?? "") === String(raw);
};

const getSearchValueFor = (row, col) => {
    if (typeof col?.search?.searchValue === "function") {
        try {
            return col.search.searchValue(row);
        } catch {
            // fallback
        }
    }
    const cell = getCellFor(row, col);
    if (typeof cell?.params?.searchValue !== "undefined") return cell.params.searchValue;
    return cell?.value ?? "";
};

const filteredRows = computed(() => {
    const rows = props.rows || [];
    const search = normalize(searchText.value);

    return rows.filter((row) => {
        // filters
        if (filtersEnabled.value) {
            for (const col of filteredColumnsConfig.value) {
                if (!passesFilter(row, col)) return false;
            }
        }

        // search
        if (!searchEnabled.value || !search) return true;

        const searchableCols = filteredColumnsConfig.value.filter((c) => c?.search?.enabled);
        for (const col of searchableCols) {
            const v = getSearchValueFor(row, col);
            if (normalize(v).includes(search)) return true;
        }
        return false;
    });
});

const sortBy = ref("");
const sortOrder = ref("asc");

const getCellObject = (row, col) => {
    // Alias pour l'usage sort/filter/search
    return getCellFor(row, col);
};

const getSortValue = (row, col) => {
    const cell = getCellObject(row, col);
    const v = cell?.params?.sortValue;
    if (typeof v !== "undefined") return v;
    return cell?.value ?? null;
};

const tanstackColumns = computed(() => {
    return filteredColumnsConfig.value.map((col) => {
        const canSort = Boolean(col?.sort?.enabled);

        return {
            id: col.id,
            accessorFn: (row) => {
                if (typeof col?.sort?.sortValue === "function") {
                    try {
                        return col.sort.sortValue(row);
                    } catch {
                        return getSortValue(row, col);
                    }
                }
                return getSortValue(row, col);
            },
            enableSorting: canSort,
            sortingFn: col?.sort?.sortingFn || undefined,
        };
    });
});

const sortingState = computed(() => {
    if (!sortBy.value) return [];
    return [{ id: sortBy.value, desc: sortOrder.value === "desc" }];
});

const paginationState = ref({
    pageIndex: 0,
    pageSize: prefs.pageSize.value || (props.config?.features?.pagination?.perPage?.default ?? 25),
});

watch(
    () => paginationState.value.pageSize,
    (n) => prefs.setPageSize(n),
);

watch(
    () => [searchText.value, JSON.stringify(activeFilters.value), filteredColumnsConfig.value.map((c) => c.id).join(",")].join("|"),
    () => {
        // Reset page quand le dataset change
        paginationState.value = { ...paginationState.value, pageIndex: 0 };
    },
);

const table = useVueTable({
    get data() {
        return filteredRows.value;
    },
    get columns() {
        return tanstackColumns.value;
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    state: {
        get sorting() {
            return sortingState.value;
        },
        get pagination() {
            return paginationState.value;
        },
    },
    onPaginationChange: (updater) => {
        const next = typeof updater === "function" ? updater(paginationState.value) : updater;
        paginationState.value = next;
    },
});

const rowsToRender = computed(() => {
    return table.getRowModel().rows.map((r) => r.original);
});

const handleSort = (col) => {
    if (!col?.sort?.enabled) return;
    if (sortBy.value === col.id) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortBy.value = col.id;
        sortOrder.value = "asc";
    }
    emit("sort-change", { sortBy: sortBy.value, sortOrder: sortOrder.value });
};

const skeletonRows = computed(() => Number(props.config?.ui?.skeletonRows ?? 8));

// Pagination config
const paginationEnabled = computed(() => Boolean(props.config?.features?.pagination?.enabled));
const perPageOptions = computed(() => props.config?.features?.pagination?.perPage?.options || [10, 25, 50, 100]);

// Selection (Phase 1: local Set)
const selectionEnabled = computed(() => Boolean(props.config?.features?.selection?.enabled));
const checkboxMode = computed(() => props.config?.features?.selection?.checkboxMode || "auto");
const clickToSelect = computed(() => Boolean(props.config?.features?.selection?.clickToSelect));
const selectedIds = ref(new Set());

// Sync contrôlé (selectedIds prop -> internal Set)
watch(
    () => props.selectedIds,
    (next) => {
        if (!Array.isArray(next)) return;
        selectedIds.value = new Set(next);
    },
    { immediate: true },
);

const emitSelection = () => {
    if (!Array.isArray(props.selectedIds)) return; // mode non contrôlé: pas d'emit obligatoire
    emit("update:selectedIds", Array.from(selectedIds.value));
};

const selectedCount = computed(() => selectedIds.value.size);
const showSelectionCheckboxes = computed(() => {
    if (!selectionEnabled.value) return false;
    if (checkboxMode.value === "none") return false;
    if (checkboxMode.value === "always") return true;
    return selectedCount.value > 0;
});

const pageRows = computed(() => table.getRowModel().rows.map((r) => r.original));

const isSelected = (row) => selectedIds.value.has(row?.id);

const allSelectedOnPage = computed(() => {
    const rows = pageRows.value || [];
    if (!rows.length) return false;
    return rows.every((r) => selectedIds.value.has(r.id));
});

const someSelectedOnPage = computed(() => {
    const rows = pageRows.value || [];
    if (!rows.length) return false;
    const count = rows.filter((r) => selectedIds.value.has(r.id)).length;
    return count > 0 && count < rows.length;
});

const toggleRow = (row, checked) => {
    const id = row?.id;
    if (id === null || typeof id === "undefined") return;
    const next = new Set(selectedIds.value);
    if (checked) next.add(id);
    else next.delete(id);
    selectedIds.value = next;
    emitSelection();
};

const toggleAllOnPage = (checked) => {
    const rows = pageRows.value || [];
    const next = new Set(selectedIds.value);
    for (const r of rows) {
        if (checked) next.add(r.id);
        else next.delete(r.id);
    }
    selectedIds.value = next;
    emitSelection();
};

const clearSelection = () => {
    selectedIds.value = new Set();
    emitSelection();
};

const handleRowClick = (row) => {
    emit("row-click", row);
    if (!selectionEnabled.value || !clickToSelect.value) return;
    toggleRow(row, !isSelected(row));
};

const toggleColumnVisibility = (col) => {
    if (!col?.id) return;
    if (col?.hideable === false || col?.isMain) {
        prefs.setColumnVisible(col.id, true);
        return;
    }
    const current = visibleColumns.value?.[col.id] !== false;
    prefs.setColumnVisible(col.id, !current);
};

// CSV export (Phase 1: export rows filtrées/triées, ou sélection si active)
const exportEnabled = computed(() => Boolean(props.config?.features?.export?.csv));
const exportFilename = computed(() => props.config?.features?.export?.filename || `${props.config?.id || "table"}.csv`);

const toCsvCell = (value) => {
    const v = value === null || typeof value === "undefined" ? "" : String(value);
    if (/[",\n]/.test(v)) return `"${v.replaceAll('"', '""')}"`;
    return v;
};

const downloadCsv = (filename, csvText) => {
    const blob = new Blob([csvText], { type: "text/csv;charset=utf-8" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
};

const handleExport = () => {
    if (!exportEnabled.value) return;

    const cols = filteredColumnsConfig.value;
    const headers = cols.map((c) => toCsvCell(c.label || c.id)).join(",");

    const allSorted = table.getPrePaginationRowModel().rows.map((r) => r.original);
    const selected = selectedCount.value
        ? allSorted.filter((r) => selectedIds.value.has(r.id))
        : allSorted;

    const lines = selected.map((row) => {
        return cols.map((col) => {
            const cell = getCellFor(row, col);
            return toCsvCell(cell?.value ?? "");
        }).join(",");
    });

    downloadCsv(exportFilename.value, [headers, ...lines].join("\n"));
};
</script>

<template>
    <div class="space-y-2">
        <!-- Toolbar (Header) -->
        <div class="relative px-3 py-2" :class="[bgClass]">
            <TanStackTableToolbar
                :search-enabled="searchEnabled"
                :search-value="searchText"
                :search-placeholder="searchPlaceholder"
                :ui-size="uiSize"
                :column-visibility-enabled="Boolean(props.config?.features?.columnVisibility?.enabled)"
                :columns="columnsConfig"
                :visible-columns="visibleColumns"
                :export-enabled="exportEnabled"
                :selection-count="selectedCount"
                @update:search="updateSearch"
                @toggle-column="toggleColumnVisibility"
                @export="handleExport"
                @clear-selection="clearSelection"
            />
        </div>

        <!-- Filters -->
        <div
            v-if="filtersEnabled && hasFilterableColumns"
            class="relative px-3 py-2"
            :class="[bgClass]"
        >
            <TanStackTableFilters
                :columns="filteredColumnsConfig"
                :filter-values="activeFilters.value"
                :filter-options="filterOptions"
                @update:filters="(v) => { activeFilters.value = v; }"
                @reset="() => { activeFilters.value = {}; }"
            />
        </div>

        <!-- Table -->
        <div class="relative overflow-hidden p-1" :class="[bgClass]">
            <div class="overflow-x-auto">
                <table class="table w-full" :class="[tableVariantClass, tableSizeClass]">
                <TanStackTableHeader
                    :columns="filteredColumnsConfig"
                    :sort-by="sortBy"
                    :sort-order="sortOrder"
                    @sort="handleSort"
                    :show-selection="showSelectionCheckboxes"
                    :all-selected="allSelectedOnPage"
                    :some-selected="someSelectedOnPage"
                    @toggle-all="toggleAllOnPage"
                />

                <TanStackTableSkeletonBody
                    v-if="loading"
                    :columns="filteredColumnsConfig"
                    :rows-count="skeletonRows"
                    :show-selection="showSelectionCheckboxes"
                />

                <tbody v-else-if="rowsToRender.length">
                    <TanStackTableRow
                        v-for="row in rowsToRender"
                        :key="row.id"
                        :row="row"
                        :columns="filteredColumnsConfig"
                        :show-selection="showSelectionCheckboxes"
                        :is-selected="isSelected(row)"
                        :selected-bg-class="rowSelectedBgClass"
                        @toggle-select="(r, checked) => toggleRow(r, checked)"
                        @row-click="handleRowClick"
                        @row-dblclick="(r) => emit('row-dblclick', r)"
                    />
                </tbody>

                <tbody v-else>
                    <tr>
                        <td :colspan="filteredColumnsConfig.length + (showSelectionCheckboxes ? 1 : 0)" class="text-center py-8 text-base-content/60">
                            Aucune donnée
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="paginationEnabled" class="relative px-2 py-1 " :class="[bgClass]">
            <TanStackTablePagination
                :page-index="paginationState.pageIndex"
                :page-count="table.getPageCount()"
                :page-size="paginationState.pageSize"
                :total-rows="filteredRows.length"
                :per-page-options="perPageOptions"
                :can-prev="table.getCanPreviousPage()"
                :can-next="table.getCanNextPage()"
                :ui-size="uiSize"
                @prev="() => table.previousPage()"
                @next="() => table.nextPage()"
                @set-page-size="(n) => table.setPageSize(Number(n))"
            />
        </div>
    </div>
</template>


