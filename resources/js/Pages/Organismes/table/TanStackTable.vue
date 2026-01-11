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

import { computed, ref, watch, onMounted, onUnmounted } from "vue";
import { getCoreRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from "@tanstack/vue-table";
import TanStackTableHeader from "@/Pages/Molecules/table/TanStackTableHeader.vue";
import TanStackTableRow from "@/Pages/Molecules/table/TanStackTableRow.vue";
import TanStackTableSkeletonBody from "@/Pages/Molecules/table/TanStackTableSkeletonBody.vue";
import TanStackTableToolbar from "@/Pages/Molecules/table/TanStackTableToolbar.vue";
import TanStackTableFilters from "@/Pages/Molecules/table/TanStackTableFilters.vue";
import TanStackTablePagination from "@/Pages/Molecules/table/TanStackTablePagination.vue";
import { useTanStackTablePreferences } from "@/Composables/table/useTanStackTablePreferences";
import { getCurrentScreenSize } from "@/Entities/entity/EntityDescriptorHelpers.js";
import { getEntityConfig } from "@/Entities/entity-registry.js";
import Btn from "@/Pages/Atoms/action/Btn.vue";

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
    /**
     * Type d'entité (pour la colonne Actions et le menu contextuel).
     */
    entityType: { type: String, default: null },
    /**
     * Afficher la colonne Actions.
     */
    showActionsColumn: { type: Boolean, default: false },
});

const emit = defineEmits([
    "row-click",
    "row-dblclick",
    "sort-change",
    // Compat: selon les listeners (template) on peut avoir besoin de la forme kebab-case
    "update:selectedIds",
    "update:selected-ids",
    "action", // Émis pour chaque action d'entité
]);

const columnsConfig = computed(() => Array.isArray(props.config?.columns) ? props.config.columns : []);

/**
 * UI (style global du tableau).
 * @see docs/30-UI/TANSTACK_TABLE.md
 */
const uiSize = computed(() => String(props.config?.ui?.size || "md"));
const uiColor = computed(() => String(props.config?.ui?.color || "primary"));
const debug = computed(() => Boolean(props.config?.ui?.debug));

/**
 * Debug global (pratique quand la config n'est pas facile à éditer).
 * Active via:
 * - localStorage.setItem('tanstack_table_debug','1')
 * - window.__TANSTACK_TABLE_DEBUG__ = true
 */
const debugEnabled = computed(() => {
    if (debug.value) return true;
    try {
        if (typeof window !== "undefined" && window.__TANSTACK_TABLE_DEBUG__ === true) return true;
        // Support URL param: ?tanstack_table_debug=1 (pratique sans console)
        if (typeof window !== "undefined") {
            const params = new URLSearchParams(window.location?.search || "");
            if (params.get("tanstack_table_debug") === "1") return true;
        }
        if (typeof window !== "undefined" && window.localStorage?.getItem("tanstack_table_debug") === "1") return true;
    } catch {
        // ignore
    }
    return false;
});

const enableDebug = () => {
    try {
        window.localStorage?.setItem("tanstack_table_debug", "1");
        window.location?.reload();
    } catch {
        // ignore
    }
};

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

// Colonnes sans "actions" (car gérée manuellement via showActionsColumn)
const columnsWithoutActions = computed(() => {
    return filteredColumnsConfig.value.filter((col) => col.id !== 'actions');
});

// Search + Filters (client-first)
const searchEnabled = computed(() => Boolean(props.config?.features?.search?.enabled));
const searchPlaceholder = computed(() => props.config?.features?.search?.placeholder || "Rechercher…");
const searchDebounceMs = computed(() => Number(props.config?.features?.search?.debounceMs ?? 150));

const filtersEnabled = computed(() => Boolean(props.config?.features?.filters?.enabled));
const hasFilterableColumns = computed(() => {
    return (columnsWithoutActions.value || []).some((c) => Boolean(c?.filter?.id && c?.filter?.type));
});
const filterOptions = computed(() => {
    if (props.filterOptions && typeof props.filterOptions === "object") return props.filterOptions;
    return props.config?.filterOptions || {};
});
const activeFilters = ref({});
let _filterDebugCount = 0;

const debugSample = computed(() => {
    if (!debugEnabled.value) return null;
    const sampleRow = (props.rows || [])[0] || null;
    const items = (filteredColumnsConfig.value || [])
        .filter((c) => c?.filter?.id && c?.filter?.type)
        .map((col) => {
            const f = col.filter;
            const raw = activeFilters.value?.[f.id];
            const rowValue = sampleRow ? getFilterValueFor(sampleRow, col) : null;
            return {
                columnId: col.id,
                filterId: f.id,
                filterType: f.type,
                raw: raw ?? null,
                rowValue: rowValue ?? null,
            };
        });

    return {
        rowsTotal: (props.rows || []).length,
        rowsFiltered: filteredRows.value.length,
        activeFilters: activeFilters.value || {},
        selectionInternal: Array.from(selectedIds.value || []),
        selectionProp: Array.isArray(props.selectedIds) ? props.selectedIds : null,
        sampleRowId: sampleRow?.id ?? null,
        sample: items,
    };
});

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

// Taille d'écran actuelle (réactive)
const currentScreenSize = ref(getCurrentScreenSize());

// Mettre à jour la taille d'écran au resize
const updateScreenSize = () => {
    currentScreenSize.value = getCurrentScreenSize();
};

onMounted(() => {
    if (typeof window !== "undefined") {
        window.addEventListener("resize", updateScreenSize);
        updateScreenSize();
    }
});

onUnmounted(() => {
    if (typeof window !== "undefined") {
        window.removeEventListener("resize", updateScreenSize);
    }
});

const getCellFor = (row, col) => {
    const cellId = col?.cellId || col?.id;
    
    // Si la cellule existe déjà (pré-générée), l'utiliser
    const fromCells = row?.cells?.[cellId];
    if (fromCells) return fromCells;

    // Génération à la volée pour Resource (et autres entités avec modèles)
    const entity = row?.rowParams?.entity;
    if (entity && typeof entity.toCell === "function") {
        // Récupérer la configuration de la colonne pour le format
        const colFormat = col?.format?.[currentScreenSize.value] || col?.format?.md || {};
        
        // Générer la cellule via entity.toCell()
        const cell = entity.toCell(cellId, {
            size: currentScreenSize.value,
            context: "table",
            format: colFormat,
            href: col?.cell?.href, // Pour les colonnes route, passer le href si défini
        });
        
        if (cell) {
            // Mettre en cache la cellule générée pour éviter de la régénérer
            if (!row.cells) row.cells = {};
            row.cells[cellId] = cell;
            return cell;
        }
    }

    // Fallback : utiliser entityConfig.buildCell si disponible
    if (props.entityType) {
        const entityConfig = getEntityConfig(props.entityType);
        if (entityConfig?.buildCell && entity) {
            try {
                const cell = entityConfig.buildCell(cellId, entity, {}, {
                    size: currentScreenSize.value,
                    context: "table",
                });
                if (cell) {
                    // Mettre en cache
                    if (!row.cells) row.cells = {};
                    row.cells[cellId] = cell;
                    return cell;
                }
            } catch (e) {
                console.warn(`[TanStackTable] buildCell failed for ${cellId}:`, e);
            }
        }
    }

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

    // Fallback important: certains filtres portent sur un champ "technique" (ex: *_id)
    // alors que la cellule affiche un label (ex: "Type"). Dans ce cas on utilise
    // la valeur brute du backend quand elle est disponible dans rowParams.entity.
    const filterId = col?.filter?.id;
    const entity = row?.rowParams?.entity;
    if (filterId && entity && Object.prototype.hasOwnProperty.call(entity, filterId)) {
        const v = entity?.[filterId];
        if (typeof v !== "undefined") return v;
    }

    return cell?.value ?? null;
};

const passesFilter = (row, col) => {
    const f = col?.filter;
    if (!f?.id || !f?.type) return true;
    const raw = activeFilters.value?.[f.id];
    if (raw === null || typeof raw === "undefined" || raw === "") return true;
    if (Array.isArray(raw) && raw.length === 0) return true;

    const rowValue = getFilterValueFor(row, col);

    if (debugEnabled.value && _filterDebugCount < 25) {
        _filterDebugCount++;
        console.log("[TanStackTable] passesFilter", {
            columnId: col?.id,
            filterId: f.id,
            filterType: f.type,
            raw,
            rowValue,
            rowId: row?.id,
        });
    }

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

    // multi
    if (Array.isArray(raw)) {
        return raw.map((v) => String(v)).includes(String(rowValue ?? ""));
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
            for (const col of columnsWithoutActions.value) {
                if (!passesFilter(row, col)) return false;
            }
        }

        // search
        if (!searchEnabled.value || !search) return true;

        const searchableCols = columnsWithoutActions.value.filter((c) => c?.search?.enabled);
        for (const col of searchableCols) {
            const v = getSearchValueFor(row, col);
            if (normalize(v).includes(search)) return true;
        }
        return false;
    });
});

watch(
    () => JSON.stringify(activeFilters.value),
    (v) => {
        if (!debugEnabled.value) return;
        console.log("[TanStackTable] activeFilters changed", v);
    },
);

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
    return columnsWithoutActions.value.map((col) => {
        const canSort = Boolean(col?.sort?.enabled);

        const def = {
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
        };
        // IMPORTANT: ne pas passer sortingFn si ce n'est pas une fonction.
        // Sinon TanStack peut tenter de l'appeler directement et crasher (sortingFn is not a function).
        if (typeof col?.sort?.sortingFn === "function") {
            def.sortingFn = col.sort.sortingFn;
        }
        return def;
    });
});

const setFilters = (v) => {
    if (debugEnabled.value) console.log("[TanStackTable] update:filters", v);
    activeFilters.value = v || {};
};
const resetFilters = () => {
    activeFilters.value = {};
};
const applyFilters = () => {
    paginationState.value = { ...paginationState.value, pageIndex: 0 };
};

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
    () => [searchText.value, JSON.stringify(activeFilters.value), columnsWithoutActions.value.map((c) => c.id).join(",")].join("|"),
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
    const next = Array.from(selectedIds.value);
    emit("update:selectedIds", next);
    emit("update:selected-ids", next);
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
    // Normaliser l'id (certains JSON peuvent être string, mais tout le système attend des IDs numériques)
    const idRaw = row?.id;
    const id = typeof idRaw === "string" ? Number(idRaw) : idRaw;
    if (id === null || typeof id === "undefined") return;
    if (typeof id === "number" && !Number.isFinite(id)) return;
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
        const idRaw = r?.id;
        const id = typeof idRaw === "string" ? Number(idRaw) : idRaw;
        if (id === null || typeof id === "undefined") continue;
        if (typeof id === "number" && !Number.isFinite(id)) continue;
        if (checked) next.add(id);
        else next.delete(id);
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

    const cols = columnsWithoutActions.value;
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
                :ui-color="uiColor"
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
                :filter-values="activeFilters"
                :filter-options="filterOptions"
                :ui-color="uiColor"
                :debug="debugEnabled"
                @update:filters="setFilters"
                @apply="applyFilters"
                @reset="resetFilters"
            />
        </div>

        <!-- Table -->
        <div class="relative overflow-hidden p-1" :class="[bgClass]">
            <div class="overflow-x-auto">
                <table class="table w-full" :class="[tableVariantClass, tableSizeClass]">
                <TanStackTableHeader
                    :columns="columnsWithoutActions"
                    :sort-by="sortBy"
                    :sort-order="sortOrder"
                    @sort="handleSort"
                    :show-selection="showSelectionCheckboxes"
                    :all-selected="allSelectedOnPage"
                    :some-selected="someSelectedOnPage"
                    :ui-color="uiColor"
                    :show-actions-column="showActionsColumn"
                    @toggle-all="toggleAllOnPage"
                />

                <TanStackTableSkeletonBody
                    v-if="loading"
                    :columns="columnsWithoutActions"
                    :rows-count="skeletonRows"
                    :show-selection="showSelectionCheckboxes"
                    :show-actions-column="showActionsColumn"
                />

                <tbody v-else-if="rowsToRender.length">
                    <TanStackTableRow
                        v-for="row in rowsToRender"
                        :key="row.id"
                        :row="row"
                        :columns="columnsWithoutActions"
                        :show-selection="showSelectionCheckboxes"
                        :is-selected="isSelected(row)"
                        :selected-bg-class="rowSelectedBgClass"
                        :ui-color="uiColor"
                        :entity-type="entityType"
                        :show-actions-column="showActionsColumn"
                        @toggle-select="(r, checked) => toggleRow(r, checked)"
                        @row-click="handleRowClick"
                        @row-dblclick="(r) => emit('row-dblclick', r)"
                        @action="(actionKey, entity, row) => emit('action', actionKey, entity, row)"
                    />
                </tbody>

                <tbody v-else>
                    <tr>
                        <td :colspan="columnsWithoutActions.length + (showSelectionCheckboxes ? 1 : 0) + (showActionsColumn ? 1 : 0)" class="text-center py-8 text-base-content/60">
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
                :ui-color="uiColor"
                @first="() => table.setPageIndex(0)"
                @prev="() => table.previousPage()"
                @go="(i) => table.setPageIndex(Number(i))"
                @next="() => table.nextPage()"
                @last="() => table.setPageIndex(Math.max(0, table.getPageCount() - 1))"
                @set-page-size="(n) => table.setPageSize(Number(n))"
            />
        </div>

        <!-- Debug panel (opt-in) -->
        <div v-if="!debugEnabled" class="text-xs text-base-content/60">
            <div class="relative p-3 rounded-lg border border-base-300 flex items-center justify-between gap-3" :class="[bgClass]">
                <div>
                    <div class="font-semibold">Debug Table</div>
                    <div class="opacity-70">Clique pour activer (recharge la page)</div>
                </div>
                <Btn size="xs" variant="outline" :color="uiColor" opacity="sm" backdrop="sm" type="button" @click="enableDebug">
                    Activer debug
                </Btn>
            </div>
        </div>

        <div v-else class="text-xs text-base-content/70">
            <div class="relative p-3 rounded-lg border border-base-300" :class="[bgClass]">
                <div class="font-semibold mb-2">Debug Table (filters)</div>
                <pre class="whitespace-pre-wrap break-words">{{ JSON.stringify(debugSample, null, 2) }}</pre>
                <div class="mt-2 opacity-70">
                    Activer/désactiver: <code>localStorage.tanstack_table_debug = "1"</code> / removeItem
                </div>
            </div>
        </div>
    </div>
</template>


