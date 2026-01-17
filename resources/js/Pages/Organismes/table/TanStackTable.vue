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

import { computed, ref, watch, onMounted, onUnmounted, toValue, shallowRef } from "vue";
import { getCoreRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from "@tanstack/vue-table";
import TanStackTableHeader from "@/Pages/Molecules/table/TanStackTableHeader.vue";
import TanStackTableRow from "@/Pages/Molecules/table/TanStackTableRow.vue";
import TanStackTableSkeletonBody from "@/Pages/Molecules/table/TanStackTableSkeletonBody.vue";
import TanStackTableToolbar from "@/Pages/Molecules/table/TanStackTableToolbar.vue";
import TanStackTableFilters from "@/Pages/Molecules/table/TanStackTableFilters.vue";
import TanStackTablePagination from "@/Pages/Molecules/table/TanStackTablePagination.vue";
import { useTanStackTablePreferences } from "@/Composables/table/useTanStackTablePreferences";
import { BREAKPOINTS } from "@/Utils/Entity/Constants.js";
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

/**
 * Couleur de fond du tableau (neutral par défaut).
 * On conserve `uiColor` pour les éléments interactifs (boutons, toggles, etc.).
 */
const bgColor = computed(() => {
    const c = String(props.config?.ui?.bgColor || "neutral");
    if (c === "primary") return "primary";
    if (c === "secondary") return "secondary";
    if (c === "accent") return "accent";
    if (c === "info") return "info";
    if (c === "success") return "success";
    if (c === "warning") return "warning";
    if (c === "error") return "error";
    return "neutral";
});

// IMPORTANT: pas de concaténation de classes Tailwind/DaisyUI.
const bgVariantSizeClass = computed(() => {
    const v = bgVariant.value;
    const s = bgSize.value;

    if (v === "glass") {
        if (s === "xs") return "bg-glass-xs";
        if (s === "sm") return "bg-glass-sm";
        if (s === "lg") return "bg-glass-lg";
        if (s === "xl") return "bg-glass-xl";
        return "bg-glass-md";
    }
    if (v === "ghost") {
        if (s === "xs") return "bg-ghost-xs";
        if (s === "sm") return "bg-ghost-sm";
        if (s === "lg") return "bg-ghost-lg";
        if (s === "xl") return "bg-ghost-xl";
        return "bg-ghost-md";
    }
    if (v === "soft") {
        if (s === "xs") return "bg-soft-xs";
        if (s === "sm") return "bg-soft-sm";
        if (s === "lg") return "bg-soft-lg";
        if (s === "xl") return "bg-soft-xl";
        return "bg-soft-md";
    }
    if (v === "outline") {
        if (s === "xs") return "bg-outline-xs";
        if (s === "sm") return "bg-outline-sm";
        if (s === "lg") return "bg-outline-lg";
        if (s === "xl") return "bg-outline-xl";
        return "bg-outline-md";
    }
    if (v === "dash") {
        if (s === "xs") return "bg-dash-xs";
        if (s === "sm") return "bg-dash-sm";
        if (s === "lg") return "bg-dash-lg";
        if (s === "xl") return "bg-dash-xl";
        return "bg-dash-md";
    }

    // fallback
    if (s === "xs") return "bg-glass-xs";
    if (s === "sm") return "bg-glass-sm";
    if (s === "lg") return "bg-glass-lg";
    if (s === "xl") return "bg-glass-xl";
    return "bg-glass-md";
});

const bgColorClass = computed(() => {
    const c = bgColor.value;
    if (c === "primary") return "bg-color-primary";
    if (c === "secondary") return "bg-color-secondary";
    if (c === "accent") return "bg-color-accent";
    if (c === "info") return "bg-color-info";
    if (c === "success") return "bg-color-success";
    if (c === "warning") return "bg-color-warning";
    if (c === "error") return "bg-color-error";
    return "bg-color-neutral";
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
    return `${bgVariantSizeClass.value} ${bgColorClass.value}`;
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

// Utiliser directement le ref pour garantir la réactivité maximale
// Le ref est déjà réactif et se met à jour automatiquement
const visibleColumns = prefs.visibleColumns;
const touchedColumns = prefs.touchedColumns;

/**
 * La visibilité des colonnes a 3 sources (priorité) :
 * - choix utilisateur (prefs.visibleColumns[id] === true/false)
 * - visibilité responsive (col.defaultVisible[xs|sm|md|lg|xl])
 * - fallback (col.defaultHidden / sinon visible)
 *
 * IMPORTANT:
 * - on n'écrit PAS les defaults responsive dans localStorage, sinon ils deviennent "figés"
 *   et ne suivent plus les changements de taille d'écran.
 */
const effectiveVisibleColumns = computed(() => {
    const prefsMap = visibleColumns.value || {};
    const touched = new Set(Array.isArray(touchedColumns.value) ? touchedColumns.value : []);
    const size = currentScreenSize.value;
    const next = {};

    for (const col of columnsConfig.value || []) {
        if (!col?.id) continue;

        // Colonnes non masquables / main = toujours visibles
        if (col?.hideable === false || col?.isMain) {
            next[col.id] = true;
            continue;
        }

        // 1) choix explicite utilisateur (uniquement si la colonne a été "touchée")
        if (touched.has(col.id) && typeof prefsMap[col.id] === "boolean") {
            next[col.id] = prefsMap[col.id];
            continue;
        }

        // 2) visibilité responsive (descriptor/table.defaultVisible)
        const dv = col?.defaultVisible;
        if (dv && typeof dv === "object" && typeof dv?.[size] === "boolean") {
            next[col.id] = dv[size];
            continue;
        }

        // 3) fallback historique: defaultHidden
        if (typeof col?.defaultHidden === "boolean") {
            next[col.id] = !col.defaultHidden;
            continue;
        }

        next[col.id] = true;
    }

    return next;
});

// Sécurité: si une colonne non-masquable est forcée à false en prefs, on la remet à true.
watch(
    () => columnsConfig.value.map((c) => `${c?.id}:${c?.hideable === false ? 1 : 0}:${c?.isMain ? 1 : 0}`).join("|"),
    () => {
        const current = prefs.visibleColumns.value || {};
        let changed = false;
        for (const col of columnsConfig.value || []) {
            if (!col?.id) continue;
            if (col?.hideable === false || col?.isMain) {
                if (current[col.id] === false) {
                    current[col.id] = true;
                    changed = true;
                }
            }
        }
        if (changed) prefs.visibleColumns.value = { ...current };
    },
    { immediate: true },
);

// Colonnes visibles (source de vérité = prefs.visibleColumns)
// On ne dépend pas de TanStack pour le rendu car on rend nos propres cellules.
const visibleColumnsFromTable = computed(() => {
    const visCols = effectiveVisibleColumns.value || {};
    return (columnsConfig.value || []).filter((col) => {
        if (!col?.id || col.id === "actions") return false;
        // Colonnes non masquables / main = toujours visibles
        if (col?.hideable === false || col?.isMain) return true;
        return visCols[col.id] !== false;
    });
});

// Colonnes sans "actions" pour les filtres (utiliser toutes les colonnes configurées)
const columnsWithoutActions = computed(() => {
    return (columnsConfig.value || []).filter((col) => col.id !== 'actions');
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

/**
 * Appliquer des filtres par défaut (déclaratifs) si fournis sur les colonnes.
 * Exemple d'usage dans un descriptor:
 * table: { filterable: { id: 'usable', type: 'toggle', defaultValue: true } }
 *
 * Règle: on ne remplace jamais un filtre déjà défini (même vide) par le user.
 */
const applyDefaultFilters = () => {
    const current = activeFilters.value || {};
    const next = { ...current };
    let changed = false;

    for (const col of columnsWithoutActions.value || []) {
        const f = col?.filter;
        if (!f?.id || !f?.type) continue;
        if (typeof f?.defaultValue === "undefined") continue;
        if (Object.prototype.hasOwnProperty.call(next, f.id)) continue;
        next[f.id] = f.defaultValue;
        changed = true;
    }

    if (changed) activeFilters.value = next;
};

watch(
    () => columnsWithoutActions.value.map((c) => `${c?.id}:${c?.filter?.id || ""}:${c?.filter?.type || ""}:${typeof c?.filter?.defaultValue !== "undefined" ? "1" : "0"}`).join("|"),
    () => applyDefaultFilters(),
    { immediate: true },
);

const debugSample = computed(() => {
    if (!debugEnabled.value) return null;
    const sampleRow = (props.rows || [])[0] || null;
    const items = (columnsWithoutActions.value || [])
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
        screenSize: currentScreenSize.value,
        tableContainerWidth: tableContainerWidth.value,
        visibleColumnsPrefs: visibleColumns.value || {},
        touchedColumns: touchedColumns.value || [],
        effectiveVisibleColumns: effectiveVisibleColumns.value || {},
        renderedColumnIds: (visibleColumnsFromTable.value || []).map((c) => c?.id).filter(Boolean),
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

// Taille "réactive" basée sur la largeur du conteneur du tableau (pas la fenêtre).
// But: dans un layout avec panneau latéral, la fenêtre peut être XL mais le tableau est "md".
const currentScreenSize = ref("md");
const tableContainerEl = ref(null);
const tableContainerWidth = ref(null);

const resolveScreenSizeFromWidth = (width) => {
    const w = Number(width);
    if (!Number.isFinite(w) || w <= 0) return "md";
    if (w < BREAKPOINTS.sm) return "xs";
    if (w < BREAKPOINTS.md) return "sm";
    if (w < BREAKPOINTS.lg) return "md";
    if (w < BREAKPOINTS.xl) return "lg";
    return "xl";
};

const updateScreenSize = (forcedWidth = null) => {
    const w = forcedWidth
        ?? tableContainerEl.value?.clientWidth
        ?? (typeof window !== "undefined" ? window.innerWidth : null);
    tableContainerWidth.value = (typeof w === "number" && Number.isFinite(w)) ? w : null;
    currentScreenSize.value = resolveScreenSizeFromWidth(w);
};

onMounted(() => {
    if (typeof window !== "undefined") {
        // Fallback : si ResizeObserver non supporté, on suit le resize fenêtre.
        window.addEventListener("resize", () => updateScreenSize());
        updateScreenSize();

        // Source de vérité: largeur du conteneur du tableau.
        if (typeof ResizeObserver !== "undefined") {
            const ro = new ResizeObserver((entries) => {
                const entry = entries?.[0];
                const w = entry?.contentRect?.width;
                updateScreenSize(typeof w === "number" ? w : null);
            });
            if (tableContainerEl.value) ro.observe(tableContainerEl.value);
            // stocker sur l'élément pour cleanup
            tableContainerEl.value.__krosmozResizeObserver = ro;
        }
    }
});

onUnmounted(() => {
    if (typeof window !== "undefined") {
        window.removeEventListener("resize", () => updateScreenSize());
        try {
            const ro = tableContainerEl.value?.__krosmozResizeObserver;
            if (ro && typeof ro.disconnect === "function") ro.disconnect();
        } catch {
            // ignore
        }
    }
});

const getCellFor = (row, col) => {
    const cellId = col?.cellId || col?.id;
    
    // Génération à la volée pour Resource (et autres entités avec modèles)
    // IMPORTANT: Ne pas utiliser row.cells car cela peut causer des problèmes de réactivité
    // et de partage de données entre les lignes. Chaque cellule doit être générée à la volée.
    const entity = row?.rowParams?.entity;
    if (entity && typeof entity.toCell === "function") {
        // Récupérer la configuration de la colonne pour le format
        const colFormat = col?.format?.[currentScreenSize.value] || col?.format?.md || {};
        
        // Récupérer les descriptors avec le contexte complet stocké dans _metadata
        const context = props.config?._metadata?.context || {};
        const descriptors = props.entityType ? getEntityConfig(props.entityType)?.getDescriptors?.(context) : {};
        // Générer la cellule via entity.toCell()
        const cell = entity.toCell(cellId, {
            size: currentScreenSize.value,
            context: "table",
            format: colFormat,
            href: col?.cell?.href, // Pour les colonnes route, passer le href si défini
            config: descriptors, // Passer les descriptors pour que BaseModel puisse utiliser display.cell
        });
        
        if (cell) {
            // Mettre en cache la cellule générée pour éviter de la régénérer
            // IMPORTANT: Ne pas muter directement row.cells car cela peut causer des problèmes de réactivité
            // On retourne directement la cellule, le cache sera géré par le modèle lui-même
            return cell;
        } else {
            // Debug: log si la cellule n'est pas générée
            console.warn(`[TanStackTable] toCell returned null for fieldKey="${cellId}"`, {
                entity: entity.constructor.name,
                entityId: entity.id,
                hasField: cellId in (entity._data || {}),
                entityData: entity._data ? Object.keys(entity._data) : 'no _data',
            });
        }
    }

    // Note: buildCell a été supprimé, on utilise directement entity.toCell() ci-dessus

    // Convenience: colonne "id" sans cellule dédiée (le row.id existe toujours)
    if (cellId === "id" && (row?.id !== null && typeof row?.id !== "undefined")) {
        const v = row.id;
        return { type: "text", value: v, params: { sortValue: v, searchValue: String(v) } };
    }

    // Fallback final : cellule vide
    return { type: "text", value: "—", params: { sortValue: "", searchValue: "" } };
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
    // `entity` est une instance de modèle (BaseModel). Les valeurs brutes sont dans `_data`.
    // On évite hasOwnProperty(entity, ...) qui ne marche pas avec les getters.
    if (filterId && entity && typeof entity === "object") {
        const raw = entity?._data;
        if (raw && typeof raw === "object" && Object.prototype.hasOwnProperty.call(raw, filterId)) {
            const v = raw?.[filterId];
            if (typeof v !== "undefined") return v;
        }
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

    const toComparable = (v) => {
        if (v === null || typeof v === "undefined") return "";
        if (typeof v === "boolean") return v ? "1" : "0";
        return String(v);
    };

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

    if (f.type === "toggle") {
        // raw === true => actif (filtrer sur true), sinon pas de filtre (déjà géré en amont)
        if (raw !== true) return true;
        const rowBool = (() => {
            if (typeof rowValue === "boolean") return rowValue;
            const s = String(rowValue ?? "").toLowerCase();
            if (s === "1" || s === "true" || s === "yes" || s === "oui") return true;
            if (s === "0" || s === "false" || s === "no" || s === "non") return false;
            return Boolean(rowValue);
        })();
        return rowBool === true;
    }

    if (f.type === "text") {
        return normalize(rowValue).includes(normalize(raw));
    }

    // multi
    if (Array.isArray(raw)) {
        const selected = new Set(raw.map((v) => toComparable(v)).filter((s) => s !== ""));
        if (selected.size === 0) return true;
        return selected.has(toComparable(rowValue));
    }

    // select (default)
    return toComparable(rowValue) === toComparable(raw);
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

// État de tri : utiliser directement le format TanStack Table
const sortingState = ref([]);

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

// Utiliser shallowRef pour les colonnes TanStack pour garantir la réactivité
const tanstackColumnsRef = shallowRef([]);

// Fonction pour mettre à jour les colonnes TanStack
const updateTanStackColumns = () => {
    tanstackColumnsRef.value = columnsWithoutActions.value.map((col) => {
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
};

// Watch pour mettre à jour les colonnes TanStack quand columnsWithoutActions change
watch(
    () => columnsWithoutActions.value.map((c) => c.id).join(","),
    () => {
        updateTanStackColumns();
    },
    { immediate: true }
);

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

// Clé réactive pour forcer le re-render du tableau quand les colonnes changent
const columnsKey = ref(0);

// Watch pour incrémenter la clé quand:
// - la liste des colonnes change (nouvelle config / permissions)
// - la visibilité change (prefs visibleColumns)
// But: certains sous-composants utilisent du memo/caching; on force un repaint du <table>.
watch(
    () => [
        columnsWithoutActions.value.map((c) => c.id).join(","),
        JSON.stringify(visibleColumns.value || {}),
    ].join("|"),
    () => {
        // Incrémenter la clé pour forcer le re-render du tableau
        columnsKey.value++;
    },
);

// État de visibilité des colonnes pour TanStack Table
const columnVisibilityState = computed(() => {
    const visCols = visibleColumns.value || {};
    const state = {};
    for (const col of columnsConfig.value || []) {
        if (!col?.id) continue;
        // Colonnes non masquables / main = toujours visibles
        if (col?.hideable === false || col?.isMain) {
            state[col.id] = true;
        } else {
            // Utiliser la préférence utilisateur, ou true par défaut
            state[col.id] = visCols[col.id] !== false;
        }
    }
    return state;
});

// Utiliser directement les refs/computed pour TanStack Table
// TanStack Table détecte mieux les changements avec des refs/computed directs
const table = useVueTable({
    get data() {
        return filteredRows.value;
    },
    get columns() {
        return tanstackColumnsRef.value;
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    state: {
        get sorting() {
            return sortingState.value;
        },
        get pagination() {
            return toValue(paginationState);
        },
        get columnVisibility() {
            return columnVisibilityState.value;
        },
    },
    onSortingChange: (updater) => {
        const next = typeof updater === "function" ? updater(sortingState.value) : updater;
        sortingState.value = next;
        // Émettre l'événement pour compatibilité
        const firstSort = Array.isArray(next) && next.length > 0 ? next[0] : null;
        if (firstSort) {
            emit("sort-change", { sortBy: firstSort.id, sortOrder: firstSort.desc ? "desc" : "asc" });
        } else {
            emit("sort-change", { sortBy: "", sortOrder: "asc" });
        }
    },
    onPaginationChange: (updater) => {
        const next = typeof updater === "function" ? updater(paginationState.value) : updater;
        paginationState.value = next;
    },
    onColumnVisibilityChange: (updater) => {
        const next = typeof updater === "function" ? updater(columnVisibilityState.value) : updater;
        // Mettre à jour les préférences utilisateur
        for (const [colId, isVisible] of Object.entries(next)) {
            prefs.setColumnVisible(colId, isVisible);
        }
    },
});

const rowsToRender = computed(() => {
    return table.getRowModel().rows.map((r) => r.original);
});

const handleSort = (col) => {
    if (!col?.sort?.enabled) return;
    // Utiliser l'API TanStack Table pour le tri
    const currentSort = sortingState.value.find((s) => s.id === col.id);
    if (currentSort) {
        // Inverser l'ordre si déjà trié
        const newSort = currentSort.desc ? [] : [{ id: col.id, desc: true }];
        table.setSorting(newSort);
    } else {
        // Nouveau tri
        table.setSorting([{ id: col.id, desc: false }]);
    }
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

const toggleColumnVisibility = (col, forcedVisible = null) => {
    if (!col?.id) return;
    if (col?.hideable === false || col?.isMain) {
        prefs.setColumnVisible(col.id, true);
        // Forcer la mise à jour via TanStack Table
        table.setColumnVisibility((prev) => ({ ...prev, [col.id]: true }));
        return;
    }
    // Mode déterministe si la toolbar fournit la valeur
    let newVisibility = null;
    if (typeof forcedVisible === "boolean") {
        newVisibility = forcedVisible;
    } else {
        // Fallback: toggle
        const currentValue = visibleColumns.value?.[col.id];
        const isCurrentlyVisible = currentValue !== false; // undefined ou true = visible
        newVisibility = !isCurrentlyVisible;
    }
    prefs.setColumnVisible(col.id, newVisibility);
    // Mettre à jour via TanStack Table
    table.setColumnVisibility((prev) => ({ ...prev, [col.id]: newVisibility }));
};

const resetColumnsToDefaults = () => {
    prefs.resetColumns();
    try {
        table.setColumnVisibility({});
    } catch {
        // ignore
    }
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
                    :visible-columns="effectiveVisibleColumns"
                :export-enabled="exportEnabled"
                :selection-count="selectedCount"
                @update:search="updateSearch"
                @toggle-column="toggleColumnVisibility"
                @reset-columns="resetColumnsToDefaults"
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
                :columns="columnsWithoutActions"
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
            <div ref="tableContainerEl" class="overflow-x-auto">
                <table :key="columnsKey" class="table w-full" :class="[tableVariantClass, tableSizeClass]">
                <TanStackTableHeader
                    :columns="visibleColumnsFromTable"
                    :sort-by="sortingState.length > 0 ? sortingState[0].id : ''"
                    :sort-order="sortingState.length > 0 && sortingState[0].desc ? 'desc' : 'asc'"
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
                    :columns="visibleColumnsFromTable"
                    :rows-count="skeletonRows"
                    :show-selection="showSelectionCheckboxes"
                    :show-actions-column="showActionsColumn"
                />

                <tbody v-else-if="rowsToRender.length">
                    <TanStackTableRow
                        v-for="row in rowsToRender"
                        :key="row.id"
                        :row="row"
                        :columns="visibleColumnsFromTable"
                        :show-selection="showSelectionCheckboxes"
                        :is-selected="isSelected(row)"
                        :selected-bg-class="rowSelectedBgClass"
                        :ui-color="uiColor"
                        :entity-type="entityType"
                        :show-actions-column="showActionsColumn"
                        :get-cell-for="getCellFor"
                        @toggle-select="(r, checked) => toggleRow(r, checked)"
                        @row-click="handleRowClick"
                        @row-dblclick="(r) => emit('row-dblclick', r)"
                        @action="(actionKey, entity, row) => emit('action', actionKey, entity, row)"
                    />
                </tbody>

                <tbody v-else>
                    <tr>
                        <td :colspan="visibleColumnsFromTable.length + (showSelectionCheckboxes ? 1 : 0) + (showActionsColumn ? 1 : 0)" class="text-center py-8 text-base-content/60">
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


