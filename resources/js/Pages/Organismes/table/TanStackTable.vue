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
import { useTableFilterPresets } from "@/Composables/table/useTableFilterPresets";
import { useTableSearch } from "@/Composables/table/useTableSearch";
import { useTableVirtualizer } from "@/Composables/table/useTableVirtualizer";
import { useUxFeedback } from "@/Composables/utils/useUxFeedback";
import { resolveEntityRouteHref } from "@/Composables/entity/entityRouteRegistry";
import { BREAKPOINTS } from "@/Utils/Entity/Constants.js";
import { getEntityConfig, normalizeEntityType } from "@/Entities/entity-registry.js";
import { resolveEntityViewComponentSync } from "@/Utils/entity/resolveEntityViewComponent.js";
import Btn from "@/Pages/Atoms/action/Btn.vue";

/** Mappe entityType → nom de prop attendu par les *ViewMinimal */
const ENTITY_PROP_MAP = {
    "resources": "resource",
    "items": "item",
    "consumables": "consumable",
    "spells": "spell",
    "monsters": "monster",
    "npcs": "npc",
    "breeds": "breed",
    "campaigns": "campaign",
    "scenarios": "scenario",
    "attributes": "attribute",
    "panoplies": "panoply",
    "capabilities": "capability",
    "specializations": "specialization",
    "resource-types": "resourceType",
    "shops": "shop",
};

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
    /**
     * Mode serveur : filtres, tri et pagination gérés côté API.
     * Les rows reçues sont déjà filtrées/triées/paginées.
     */
    serverSide: { type: Boolean, default: false },
    /**
     * Meta pagination du serveur (total, perPage, currentPage, lastPage).
     */
    serverPaginationMeta: { type: Object, default: null },
    /**
     * Params actuels pour le mode serveur (sync depuis le parent).
     */
    serverParams: { type: Object, default: null },
    /**
     * Composant pour la vue Ligne (liste dense verticale).
     * Si fourni et displayMode=line, affiche ResourceLineRow ou équivalent.
     * Sinon, fallback sur Minimal en colonne unique.
     */
    lineRowComponent: { type: Object, default: null },
});

const emit = defineEmits([
    "row-click",
    "row-dblclick",
    "sort-change",
    "refresh",
    // Compat: selon les listeners (template) on peut avoir besoin de la forme kebab-case
    "update:selectedIds",
    "update:selected-ids",
    "update:serverParams",
    "action", // Émis pour chaque action d'entité
]);
const { notifySuccess, notifyError, notifyInfo } = useUxFeedback();

const columnsConfig = computed(() => Array.isArray(props.config?.columns) ? props.config.columns : []);

const IMAGE_COLUMN_RE = /\b(image|images|img|icon|icone|avatar|portrait|thumbnail|thumb|illustration|visuel)\b/;
const NAME_COLUMN_RE = /\b(name|nom|title|titre|libelle|label)\b/;

const buildColumnHaystack = (col) => {
    const id = String(col?.id || "").toLowerCase();
    const cellId = String(col?.cellId || "").toLowerCase();
    const label = String(col?.label || "").toLowerCase();
    return `${id} ${cellId} ${label}`;
};

const isImageLikeColumn = (col) => IMAGE_COLUMN_RE.test(buildColumnHaystack(col));
const isNameLikeColumn = (col) => NAME_COLUMN_RE.test(buildColumnHaystack(col));

/**
 * UI (style global du tableau).
 * @see docs/30-UI/TANSTACK_TABLE.md
 */
const configUiSize = computed(() => String(props.config?.ui?.size || "md"));
const uiColor = computed(() => String(props.config?.ui?.color || "primary"));

const tablePrefsNamespace = computed(() => String(props.config?.id || props.entityType || "table"));
const densityStorageKey = computed(() => `tanstack_table_density_${tablePrefsNamespace.value}`);

const densityOptions = [
    { value: "comfortable", label: "Confort", uiSize: "md" },
    { value: "compact", label: "Compact", uiSize: "sm" },
    { value: "dense", label: "Dense", uiSize: "xs" },
];
const densityMode = ref("comfortable");
const uiSize = computed(() => {
    const selected = densityOptions.find((opt) => opt.value === densityMode.value);
    if (selected?.uiSize) return selected.uiSize;
    return configUiSize.value;
});
const setDensityMode = (mode) => {
    if (!densityOptions.some((opt) => opt.value === mode)) return;
    densityMode.value = mode;
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
const tableAriaLabel = computed(() => {
    const entity = String(props.entityType || props.config?.id || "").trim();
    if (entity) return `Tableau ${entity}`;
    return "Tableau de données";
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

// Préférences (colonnes visibles + pageSize + displayMode)
const prefs = useTanStackTablePreferences(props.config?.id, {
    visibleColumns: {},
    pageSize: props.config?.features?.pagination?.perPage?.default ?? 25,
    displayMode: "line",
});

/** Composant Minimal pour la vue grille (flex-wrap) */
const minimalViewComponent = computed(() => {
    const et = String(props.entityType || "").trim();
    if (!et) return null;
    return resolveEntityViewComponentSync(et, "minimal");
});

/** Afficher la grille Minimal (mode minimal + entityType + composant dispo) */
const showMinimalGrid = computed(() => prefs.displayMode.value === "minimal" && minimalViewComponent.value && props.entityType);

/** Afficher la vue Ligne (liste dense verticale) */
const showLineView = computed(() => prefs.displayMode.value === "line" && (props.lineRowComponent || (minimalViewComponent.value && props.entityType)));

/** Récupère l'entité depuis une row (rowParams.entity ou fallback via Model) */
function getEntityFromRow(row, entityType) {
    const entity = row?.rowParams?.entity;
    if (entity) return entity;
    const et = normalizeEntityType(entityType);
    const cfg = getEntityConfig(et);
    const Model = cfg?.model;
    if (!Model) return null;
    const data = { id: row?.id ?? row?.original?.id };
    Object.assign(data, row?.original || {}, row?.rowParams || {});
    delete data.entity;
    return new Model(data);
}

/** Nom de prop pour le composant Minimal (resource, item, spell, etc.) */
function getEntityPropName(entityType) {
    const et = normalizeEntityType(entityType);
    return ENTITY_PROP_MAP[et] ?? "entity";
}

const filterPresets = ref([]);
const selectedPresetId = ref("");
const hasSavedPresets = computed(() => Array.isArray(filterPresets.value) && filterPresets.value.length > 0);
const presetFileInput = ref(null);
const showPresetPanel = ref(false);
const ariaLiveMessage = ref("");
const { listPresets, createPreset, updatePreset, deletePreset } = useTableFilterPresets();
const presetsLoading = ref(false);
const presetsEnabled = computed(() => {
    const fromProp = String(props.entityType || "");
    const fromConfig = String(props.config?._metadata?.entityType || props.config?.entityType || "");
    return Boolean(fromProp || fromConfig);
});
const presetEntityType = computed(() => {
    const fromProp = String(props.entityType || "").trim();
    if (fromProp) return fromProp;
    return String(props.config?._metadata?.entityType || props.config?.entityType || "").trim();
});
const activePreset = computed(() => {
    const id = String(selectedPresetId.value || "");
    if (!id) return null;
    return (filterPresets.value || []).find((preset) => preset.id === id) || null;
});
const defaultPreset = computed(() => {
    return (filterPresets.value || []).find((preset) => preset.isDefault) || null;
});
const canRestoreDefaultPreset = computed(() => {
    if (!defaultPreset.value) return false;
    if (!activePreset.value) return true;
    return defaultPreset.value.id !== activePreset.value.id;
});
const normalizeForPresetComparison = (value) => {
    if (Array.isArray(value)) {
        return value
            .map((item) => normalizeForPresetComparison(item))
            .sort((a, b) => String(a).localeCompare(String(b)));
    }
    if (value && typeof value === "object") {
        const sortedKeys = Object.keys(value).sort();
        const result = {};
        for (const key of sortedKeys) {
            result[key] = normalizeForPresetComparison(value[key]);
        }
        return result;
    }
    if (typeof value === "boolean") return value;
    if (value === null || typeof value === "undefined") return "";
    return String(value);
};
const isActivePresetDirty = computed(() => {
    if (!activePreset.value) return false;
    const presetSnapshot = {
        searchText: String(activePreset.value.searchText || ""),
        filters: normalizeForPresetComparison(activePreset.value.filters || {}),
    };
    const currentSearch = getCurrentSearch();
    const currentSnapshot = {
        searchText: currentSearch,
        filters: normalizeForPresetComparison(activeFilters.value || {}),
    };
    return JSON.stringify(presetSnapshot) !== JSON.stringify(currentSnapshot);
});

const normalizePresetPayload = (rawPresets) => {
    if (!Array.isArray(rawPresets)) return [];
    return rawPresets
        .map((preset) => {
            const id = String(preset?.id || `${Date.now()}_${Math.random()}`);
            const name = String(preset?.name || "").trim();
            if (!name) return null;
            return {
                id,
                name,
                searchText: String(preset?.searchText || ""),
                filters: typeof preset?.filters === "object" && preset?.filters !== null ? { ...preset.filters } : {},
                createdAt: Number(preset?.createdAt || Date.now()),
                isDefault: Boolean(preset?.isDefault),
            };
        })
        .filter(Boolean)
        .slice(0, 20);
};

const reloadPresetsFromApi = async () => {
    if (!presetsEnabled.value || !presetEntityType.value) {
        filterPresets.value = [];
        selectedPresetId.value = "";
        return;
    }

    presetsLoading.value = true;
    try {
        const presets = await listPresets({
            entityType: presetEntityType.value,
            tableId: tablePrefsNamespace.value,
            includeGlobal: true,
        });
        filterPresets.value = normalizePresetPayload(presets.map((preset) => ({
            id: preset.id,
            name: preset.name,
            searchText: preset.searchText,
            filters: preset.filters,
            createdAt: preset.createdAt,
            isDefault: preset.isDefault,
            tableId: preset.tableId,
            entityType: preset.entityType,
            limit: preset.limit,
        })));

        const hasSelected = selectedPresetId.value
            && filterPresets.value.some((preset) => preset.id === selectedPresetId.value);
        if (!hasSelected) {
            selectedPresetId.value = "";
        }

        // Ne pas appliquer le preset par défaut en mode serveur : l'utilisateur a pu
        // déjà taper dans la recherche ; l'auto-apply effacerait son input.
        if (!hasSelected && defaultPreset.value && !props.serverSide) {
            applyPresetById(defaultPreset.value.id);
            notifyInfo(`Preset par défaut appliqué : "${defaultPreset.value.name}".`);
        }
    } catch {
        notifyError("Impossible de charger les presets depuis la base.");
        filterPresets.value = [];
    } finally {
        presetsLoading.value = false;
    }
};

const applyPresetById = (presetId) => {
    const preset = (filterPresets.value || []).find((p) => p.id === presetId);
    if (!preset) return;
    setFilters(preset.filters || {});
    applySearchValue(preset.searchText || "");
    applyFilters();
    selectedPresetId.value = preset.id;
};

const handlePresetSelectionChange = (event) => {
    const presetId = String(event?.target?.value || "");
    selectedPresetId.value = presetId;
    if (!presetId) return;
    applyPresetById(presetId);
};

const saveCurrentPreset = () => {
    if (typeof window === "undefined" || !presetsEnabled.value || !presetEntityType.value) return;
    const name = window.prompt("Nom du preset de filtres", "")?.trim();
    if (!name) return;

    createPreset({
        entityType: presetEntityType.value,
        tableId: tablePrefsNamespace.value,
        name,
        searchText: String(searchText.value || ""),
        filters: { ...(activeFilters.value || {}) },
        limit: Number(paginationState.value?.pageSize || 25),
        isDefault: false,
    })
        .then(async (created) => {
            await reloadPresetsFromApi();
            selectedPresetId.value = created.id;
            notifySuccess(`Preset "${name}" sauvegardé.`);
        })
        .catch(() => {
            notifyError("Erreur lors de la sauvegarde du preset.");
        });
};

const deleteSelectedPreset = () => {
    const id = String(selectedPresetId.value || "");
    if (!id) return;
    const current = (filterPresets.value || []).find((p) => p.id === id);
    deletePreset(id)
        .then(async () => {
            selectedPresetId.value = "";
            await reloadPresetsFromApi();
            notifyInfo(`Preset "${current?.name || id}" supprimé.`);
        })
        .catch(() => {
            notifyError("Erreur lors de la suppression du preset.");
        });
};

const renameSelectedPreset = () => {
    const id = String(selectedPresetId.value || "");
    if (!id || typeof window === "undefined") return;
    const current = (filterPresets.value || []).find((p) => p.id === id);
    if (!current) return;
    const nextName = window.prompt("Nouveau nom du preset", current.name)?.trim();
    if (!nextName) return;
    updatePreset(id, { name: nextName })
        .then(async () => {
            await reloadPresetsFromApi();
            notifySuccess(`Preset renommé en "${nextName}".`);
        })
        .catch(() => {
            notifyError("Erreur lors du renommage du preset.");
        });
};

const setSelectedPresetAsDefault = () => {
    const id = String(selectedPresetId.value || "");
    if (!id) return;
    const current = (filterPresets.value || []).find((p) => p.id === id);
    updatePreset(id, { isDefault: true })
        .then(async () => {
            await reloadPresetsFromApi();
            notifySuccess(`Preset "${current?.name || id}" défini par défaut.`);
        })
        .catch(() => {
            notifyError("Erreur lors de la définition du preset par défaut.");
        });
};

const restoreDefaultPreset = () => {
    if (!defaultPreset.value) return;
    applyPresetById(defaultPreset.value.id);
};

const restoreActivePreset = () => {
    if (!activePreset.value) return;
    applyPresetById(activePreset.value.id);
    notifyInfo(`Retour au preset actif : "${activePreset.value.name}".`);
};

const updateActivePresetInPlace = () => {
    const active = activePreset.value;
    if (!active) return;
    updatePreset(active.id, {
        searchText: String(searchText.value || ""),
        filters: { ...(activeFilters.value || {}) },
        limit: Number(paginationState.value?.pageSize || 25),
    })
        .then(async () => {
            await reloadPresetsFromApi();
            notifySuccess(`Preset "${active.name}" mis à jour.`);
        })
        .catch(() => {
            notifyError("Erreur lors de la mise à jour du preset.");
        });
};

const exportPresetsJson = () => {
    if (!hasSavedPresets.value) return;
    const payload = {
        version: 1,
        namespace: tablePrefsNamespace.value,
        exportedAt: new Date().toISOString(),
        presets: filterPresets.value,
    };
    const blob = new Blob([JSON.stringify(payload, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `table-presets-${tablePrefsNamespace.value}.json`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
    notifyInfo("Presets exportés en JSON.");
};

const triggerImportPresets = () => {
    presetFileInput.value?.click();
};

const togglePresetPanel = () => {
    showPresetPanel.value = !showPresetPanel.value;
};

const importPresetsFromFile = async (event) => {
    const file = event?.target?.files?.[0];
    if (!file) return;
    try {
        const text = await file.text();
        const parsed = JSON.parse(text);
        const incoming = normalizePresetPayload(parsed?.presets || parsed);
        if (!incoming.length) {
            notifyError("Aucun preset valide trouvé dans le fichier importé.");
            return;
        }

        for (const preset of incoming) {
            // Import en "upsert by name" simplifié côté client.
            const exists = (filterPresets.value || []).find((p) => p.name === preset.name);
            if (exists) {
                await updatePreset(exists.id, {
                    searchText: preset.searchText || "",
                    filters: preset.filters || {},
                    isDefault: Boolean(preset.isDefault),
                });
            } else {
                await createPreset({
                    entityType: presetEntityType.value,
                    tableId: tablePrefsNamespace.value,
                    name: preset.name,
                    searchText: preset.searchText || "",
                    filters: preset.filters || {},
                    isDefault: Boolean(preset.isDefault),
                });
            }
        }

        await reloadPresetsFromApi();
        notifySuccess(`${incoming.length} preset(s) importé(s).`);
    } catch {
        notifyError("Import JSON invalide.");
    } finally {
        if (event?.target) event.target.value = "";
    }
};

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

        // Colonnes essentielles: toujours visibles pour garder "Actions > Image > Nom"
        if (isImageLikeColumn(col) || isNameLikeColumn(col)) {
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
    const visible = (columnsConfig.value || []).filter((col) => {
        if (!col?.id || col.id === "actions") return false;
        // Colonnes non masquables / main = toujours visibles
        if (col?.hideable === false || col?.isMain) return true;
        return visCols[col.id] !== false;
    });

    const getColumnPriority = (col) => {
        // Image en priorité (juste après "actions")
        if (isImageLikeColumn(col)) {
            return 1;
        }

        // Nom/titre immédiatement après l'image
        if (isNameLikeColumn(col)) {
            return 2;
        }

        return 10;
    };

    return visible
        .map((col, index) => ({ col, index, priority: getColumnPriority(col) }))
        .sort((a, b) => {
            if (a.priority !== b.priority) return a.priority - b.priority;
            return a.index - b.index;
        })
        .map((entry) => entry.col);
});

// Colonnes sans "actions" pour les filtres (utiliser toutes les colonnes configurées)
const columnsWithoutActions = computed(() => {
    return (columnsConfig.value || []).filter((col) => col.id !== 'actions');
});

/** Colonnes triables (pour dropdown « Trier par ») */
const sortableColumns = computed(() =>
    (columnsWithoutActions.value || []).filter((col) => Boolean(col?.sort?.enabled))
);

const sortEnabled = computed(() => sortableColumns.value.length > 0);

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

/**
 * Options de filtre résolues:
 * - priorité aux options serveur (`props.filterOptions` / `config.filterOptions`)
 * - sinon fallback aux `col.filter.options`
 * - si demandé (`col.filter.ui.optionsMode === 'rows'`) ou si aucune option fournie: génération depuis les rows
 */
const resolvedFilterOptions = computed(() => {
    const base = (filterOptions.value && typeof filterOptions.value === "object") ? filterOptions.value : {};
    const out = { ...base };

    const shouldDerive = (col) => {
        const f = col?.filter;
        if (!f?.id || !f?.type) return false;
        if (f.type !== "multi" && f.type !== "select") return false;
        const mode = String(f?.ui?.optionsMode || "");
        if (mode === "rows") return true;
        const hasServer = Array.isArray(base?.[f.id]) && base[f.id].length > 0;
        const hasColumn = Array.isArray(f?.options) && f.options.length > 0;
        return !hasServer && !hasColumn;
    };

    const rows = Array.isArray(props.rows) ? props.rows : [];
    for (const col of columnsWithoutActions.value || []) {
        if (!shouldDerive(col)) continue;
        const f = col.filter;
        const id = f.id;

        const values = new Set();
        for (const row of rows) {
            const v = getFilterValueFor(row, col);
            if (v === null || typeof v === "undefined") continue;
            const s = typeof v === "boolean" ? (v ? "1" : "0") : String(v);
            if (!s) continue;
            values.add(s);
        }

        const arr = Array.from(values);
        const numeric = arr.every((x) => x !== "" && Number.isFinite(Number(x)));
        arr.sort((a, b) => numeric ? (Number(a) - Number(b)) : a.localeCompare(b, "fr"));

        const max = Number(f?.ui?.maxOptions ?? 250);
        const sliced = Number.isFinite(max) && max > 0 ? arr.slice(0, max) : arr;

        out[id] = sliced.map((v) => ({ value: v, label: v }));
    }

    return out;
});
const activeFilters = ref({});

const tableSearch = useTableSearch({
    serverSide: computed(() => props.serverSide),
    activeFilters,
    debounceMs: searchDebounceMs,
    onServerParamsChange: (params) => emit("update:serverParams", params),
});

const {
    searchText,
    effectiveSearchDisplayValue,
    handleSearchInput,
    applySearchValue,
    clearSearch,
    getCurrentSearch,
} = tableSearch;

const hasActiveFilters = computed(() => {
    const filters = activeFilters.value || {};
    return Object.values(filters).some((value) => {
        if (Array.isArray(value)) return value.length > 0;
        if (value === null || typeof value === "undefined") return false;
        if (typeof value === "boolean") return value;
        return String(value).trim() !== "";
    });
});

/**
 * Appliquer des filtres par défaut (déclaratifs) si fournis sur les colonnes.
 * Exemple d'usage dans un descriptor:
 * table: { filterable: { id: 'state', type: 'multi' } }
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


const clearAllQueryState = () => {
    resetFilters();
    clearSearch();
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
    try {
        const storedDensity = localStorage.getItem(densityStorageKey.value);
        if (storedDensity && densityOptions.some((opt) => opt.value === storedDensity)) {
            densityMode.value = storedDensity;
        } else {
            const byUiSize = densityOptions.find((opt) => opt.uiSize === configUiSize.value);
            densityMode.value = byUiSize?.value || "comfortable";
        }
    } catch {
        densityMode.value = "comfortable";
    }

    reloadPresetsFromApi();

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

watch(
    () => `${presetEntityType.value}|${tablePrefsNamespace.value}`,
    () => {
        reloadPresetsFromApi();
    },
);

watch(
    () => densityMode.value,
    (mode) => {
        try {
            localStorage.setItem(densityStorageKey.value, String(mode));
        } catch {
            // ignore localStorage errors
        }
    },
    { immediate: false },
);

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
        const entityId = row?.id;
        let fallbackHref = "";
        if (
            props.entityType
            && entityId !== null
            && typeof entityId !== "undefined"
            && !col?.cell?.href
        ) {
            fallbackHref = resolveEntityRouteHref(props.entityType, "show", entityId);
            if (!fallbackHref) {
                const entityTypePath = String(props.entityType || "").trim();
                fallbackHref = entityTypePath ? `/entities/${entityTypePath}/${entityId}` : "";
            }
        }
        
        // Récupérer les descriptors avec le contexte complet stocké dans _metadata
        const context = props.config?._metadata?.context || {};
        const descriptors = props.entityType ? getEntityConfig(props.entityType)?.getDescriptors?.(context) : {};
        // Générer la cellule via entity.toCell()
        const cell = entity.toCell(cellId, {
            size: currentScreenSize.value,
            context: "table",
            format: colFormat,
            href: col?.cell?.href || fallbackHref, // fallback show route par entité si aucun href explicite
            ctx: context, // Contexte complet (meta serveur inclus: capabilities, filterOptions, characteristics, etc.)
            config: descriptors, // Passer les descriptors pour que BaseModel puisse utiliser display.cell
        });
        
        if (cell) {
            // Mettre en cache la cellule générée pour éviter de la régénérer
            // IMPORTANT: Ne pas muter directement row.cells car cela peut causer des problèmes de réactivité
            // On retourne directement la cellule, le cache sera géré par le modèle lui-même
            return cell;
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
        if (Array.isArray(rowValue)) {
            const rowSet = new Set(rowValue.map((v) => toComparable(v)));
            return [...selected].some((s) => rowSet.has(s));
        }
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
    if (props.serverSide) {
        return props.rows || [];
    }
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
const emptyState = computed(() => {
    const totalRows = Array.isArray(props.rows) ? props.rows.length : 0;
    const hasSearch = getCurrentSearch().length > 0;
    const hasFilters = hasActiveFilters.value;

    if (totalRows === 0) {
        return {
            title: "Aucune donnée disponible",
            description: "Aucun enregistrement n'est encore disponible pour cette vue.",
            canReset: false,
        };
    }
    if (hasSearch || hasFilters) {
        return {
            title: "Aucun résultat avec les filtres actuels",
            description: "Essaie d'élargir la recherche ou de réinitialiser les filtres.",
            canReset: true,
        };
    }
    return {
        title: "Aucun résultat",
        description: "La liste ne contient aucun élément visible pour le moment.",
        canReset: false,
    };
});

// État de tri : utiliser directement le format TanStack Table
const sortingState = ref([]);

const getSortValue = (row, col) => {
    const cell = getCellFor(row, col);
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
    activeFilters.value = v || {};
};
const resetFilters = () => {
    activeFilters.value = {};
};
const applyFilters = () => {
    paginationState.value = { ...paginationState.value, pageIndex: 0 };
    if (props.serverSide) {
        emit("update:serverParams", {
            filters: { ...(activeFilters.value || {}) },
            page: 1,
        });
    }
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
        if (!props.serverSide) {
            // Reset page quand le dataset change (client-side uniquement)
            paginationState.value = { ...paginationState.value, pageIndex: 0 };
        }
    },
);

// En mode serveur : garder paginationState en sync avec serverParams.
// La recherche est contrôlée via effectiveSearchDisplayValue (serverParams.search), plus de sync searchText.
watch(
    () => [props.serverSide, props.serverParams?.page, props.serverParams?.pageSize],
    () => {
        if (props.serverSide && props.serverParams) {
            const p = props.serverParams;
            const page = Math.max(1, p?.page ?? 1);
            const pageSize = Number(p?.pageSize ?? 25) || 25;
            paginationState.value = { pageIndex: page - 1, pageSize };
        }
    },
    { immediate: true },
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
    ...(props.serverSide ? {} : { getSortedRowModel: getSortedRowModel(), getPaginationRowModel: getPaginationRowModel() }),
    manualPagination: props.serverSide,
    manualSorting: props.serverSide,
    pageCount: props.serverSide ? (props.serverPaginationMeta?.lastPage ?? 1) : undefined,
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
        if (props.serverSide) {
            const firstSort = Array.isArray(next) && next.length > 0 ? next[0] : null;
            emit("update:serverParams", {
                sort: firstSort?.id || "id",
                order: firstSort?.desc ? "desc" : "asc",
                page: 1,
            });
        }
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
        if (props.serverSide) {
            emit("update:serverParams", {
                page: (next?.pageIndex ?? 0) + 1,
                pageSize: next?.pageSize ?? 25,
            });
        }
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

// Virtualisation (optionnel, pour tableaux client avec 500+ lignes)
const virtualizationConfig = computed(() => props.config?.features?.virtualization ?? {});
const virtualizationEnabled = computed(
    () => Boolean(virtualizationConfig.value?.enabled) && !props.serverSide,
);
const virtualizationMinRows = computed(
    () => Number(virtualizationConfig.value?.minRows ?? 500) || 500,
);
const shouldVirtualize = computed(
    () => virtualizationEnabled.value && rowsToRender.value.length >= virtualizationMinRows.value,
);

// tableContainerEl sert de scroll parent pour la virtualisation
const { virtualItems, totalSize, isEnabled: virtualizationActive } = useTableVirtualizer({
    parentRef: tableContainerEl,
    rowCount: computed(() => rowsToRender.value.length),
    rowHeight: Number(virtualizationConfig.value?.rowHeight ?? 48) || 48,
    enabled: shouldVirtualize,
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

/**
 * Applique le tri depuis le dropdown « Trier par » (utile en vue single-column / line).
 * @param {{ columnId: string, order: 'asc'|'desc' }} payload
 */
const handleSortFromDropdown = (payload) => {
    const { columnId, order } = payload || {};
    if (!columnId || !sortableColumns.value.some((c) => c.id === columnId)) {
        table.setSorting([]);
        return;
    }
    table.setSorting([{ id: columnId, desc: order === "desc" }]);
};

const skeletonRows = computed(() => Number(props.config?.ui?.skeletonRows ?? 8));

// Pagination config
const paginationEnabled = computed(() => Boolean(props.config?.features?.pagination?.enabled));
const perPageOptions = computed(() => props.config?.features?.pagination?.perPage?.options || [10, 25, 50, 100]);

// En mode serveur, utiliser la meta pagination pour l'UI
// (pageCount n'est pas réactif dans TanStack Table Vue, donc on ne peut pas se fier à getCanNextPage etc.)
const paginationPageCount = computed(() => {
    if (props.serverSide && props.serverPaginationMeta?.lastPage != null) {
        return Number(props.serverPaginationMeta.lastPage) || 1;
    }
    return table?.getPageCount?.() ?? 1;
});
const paginationTotalRows = computed(() => {
    if (props.serverSide && props.serverPaginationMeta?.total != null) {
        return Number(props.serverPaginationMeta.total) || 0;
    }
    return filteredRows.value.length;
});
const paginationCanPrev = computed(() => {
    if (props.serverSide) {
        const page = Math.max(1, props.serverParams?.page ?? 1);
        return page > 1;
    }
    return table?.getCanPreviousPage?.() ?? false;
});
const paginationCanNext = computed(() => {
    if (props.serverSide) {
        const page = Math.max(1, props.serverParams?.page ?? 1);
        const lastPage = paginationPageCount.value;
        return page < lastPage;
    }
    return table?.getCanNextPage?.() ?? false;
});

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

const handleRefresh = async () => {
    emit("refresh");
    applyFilters();
    await reloadPresetsFromApi();
    notifyInfo("Table actualisée.");
};

watch(
    () => selectedCount.value,
    (count, prev) => {
        if (count === prev) return;
        ariaLiveMessage.value = `${count} élément${count > 1 ? "s" : ""} sélectionné${count > 1 ? "s" : ""}.`;
    },
    { immediate: true },
);

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
        <div class="sr-only" aria-live="polite" aria-atomic="true">{{ ariaLiveMessage }}</div>
        <!-- Toolbar (Header) -->
        <div class="relative px-3 py-2" :class="[bgClass]">
            <TanStackTableToolbar
                :search-enabled="searchEnabled"
                :search-value="effectiveSearchDisplayValue"
                :search-placeholder="searchPlaceholder"
                :ui-size="uiSize"
                :ui-color="uiColor"
                :column-visibility-enabled="Boolean(props.config?.features?.columnVisibility?.enabled)"
                :columns="columnsConfig"
                :visible-columns="effectiveVisibleColumns"
                :sort-enabled="sortEnabled"
                :sortable-columns="sortableColumns"
                :sort-by="sortingState.length > 0 ? sortingState[0].id : ''"
                :sort-order="sortingState.length > 0 && sortingState[0].desc ? 'desc' : 'asc'"
                :export-enabled="exportEnabled"
                :refresh-enabled="true"
                :selection-count="selectedCount"
                @update:search="handleSearchInput"
                @toggle-column="toggleColumnVisibility"
                @reset-columns="resetColumnsToDefaults"
                @sort="handleSortFromDropdown"
                @export="handleExport"
                @refresh="handleRefresh"
                @clear-selection="clearSelection"
            />
            <div class="mt-2 flex flex-wrap items-center justify-end gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-base-content/70">Vue</span>
                    <Btn
                        size="xs"
                        :variant="prefs.displayMode.value === 'line' ? 'glass' : 'ghost'"
                        :color="uiColor"
                        title="Vue ligne (liste dense verticale)"
                        @click="prefs.setDisplayMode('line')"
                    >
                        <i class="fa-solid fa-list-ul mr-1" aria-hidden></i>
                        Ligne
                    </Btn>
                    <Btn
                        size="xs"
                        :variant="prefs.displayMode.value === 'minimal' ? 'glass' : 'ghost'"
                        :color="uiColor"
                        title="Vue minimal (grille de cartes)"
                        @click="prefs.setDisplayMode('minimal')"
                    >
                        <i class="fa-solid fa-grip mr-1" aria-hidden></i>
                        Minimal
                    </Btn>
                    <Btn
                        size="xs"
                        :variant="prefs.displayMode.value === 'table' ? 'glass' : 'ghost'"
                        :color="uiColor"
                        title="Vue tableau (colonnes)"
                        @click="prefs.setDisplayMode('table')"
                    >
                        <i class="fa-solid fa-table-columns mr-1" aria-hidden></i>
                        Colonne
                    </Btn>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-base-content/70">Densité</span>
                    <Btn
                        v-for="option in densityOptions"
                        :key="option.value"
                        size="xs"
                        :variant="densityMode === option.value ? 'glass' : 'ghost'"
                        :color="uiColor"
                        @click="setDensityMode(option.value)"
                    >
                        {{ option.label }}
                    </Btn>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            v-if="filtersEnabled && hasFilterableColumns"
            class="relative px-3 py-2"
            :class="[bgClass]"
        >
            <div v-if="presetsEnabled && showPresetPanel && activePreset" class="mb-2 flex items-center justify-between gap-2">
                <div class="inline-flex items-center gap-2 text-xs">
                    <span class="badge badge-soft badge-primary">
                        Preset actif: {{ activePreset.name }}
                    </span>
                    <span v-if="activePreset.isDefault" class="badge badge-soft badge-info">
                        Défaut
                    </span>
                    <span v-if="isActivePresetDirty" class="badge badge-soft badge-warning">
                        Non sauvegardé
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <Btn
                        v-if="defaultPreset"
                        size="xs"
                        variant="ghost"
                        :color="uiColor"
                        :disabled="!canRestoreDefaultPreset"
                        title="Revenir au preset par défaut"
                        @click="restoreDefaultPreset"
                    >
                        Revenir au défaut
                    </Btn>
                    <Btn
                        v-if="isActivePresetDirty"
                        size="xs"
                        variant="ghost"
                        :color="uiColor"
                        title="Annuler les changements et revenir au preset actif"
                        @click="restoreActivePreset"
                    >
                        Revenir au preset actif
                    </Btn>
                    <Btn
                        v-if="isActivePresetDirty"
                        size="xs"
                        variant="outline"
                        :color="uiColor"
                        title="Mettre à jour ce preset avec les filtres/recherche actuels"
                        @click="updateActivePresetInPlace"
                    >
                        Mettre à jour ce preset
                    </Btn>
                </div>
            </div>
            <div v-if="presetsEnabled && showPresetPanel" class="mb-2 flex flex-wrap items-center justify-end gap-2">
                <span v-if="presetsLoading" class="text-xs text-base-content/70">Chargement des presets...</span>
                <Btn
                    size="xs"
                    variant="outline"
                    :color="uiColor"
                    title="Sauvegarder un preset avec filtres et recherche"
                    @click="saveCurrentPreset"
                >
                    Sauver preset
                </Btn>
                <select
                    class="select select-xs select-bordered"
                    :value="selectedPresetId"
                    aria-label="Sélectionner un preset de filtres"
                    @change="handlePresetSelectionChange"
                >
                    <option value="">Presets de filtres</option>
                    <option
                        v-for="preset in filterPresets"
                        :key="preset.id"
                        :value="preset.id"
                    >
                        {{ preset.isDefault ? `★ ${preset.name}` : preset.name }}
                    </option>
                </select>
                <Btn
                    v-if="hasSavedPresets"
                    size="xs"
                    variant="ghost"
                    :color="uiColor"
                    :disabled="!selectedPresetId"
                    title="Renommer le preset sélectionné"
                    @click="renameSelectedPreset"
                >
                    Renommer
                </Btn>
                <Btn
                    v-if="hasSavedPresets"
                    size="xs"
                    variant="ghost"
                    :color="uiColor"
                    :disabled="!selectedPresetId"
                    title="Définir ce preset par défaut"
                    @click="setSelectedPresetAsDefault"
                >
                    Par défaut
                </Btn>
                <Btn
                    v-if="hasSavedPresets"
                    size="xs"
                    variant="ghost"
                    :color="uiColor"
                    title="Exporter les presets en JSON"
                    @click="exportPresetsJson"
                >
                    Export JSON
                </Btn>
                <Btn
                    size="xs"
                    variant="ghost"
                    :color="uiColor"
                    title="Importer des presets JSON"
                    @click="triggerImportPresets"
                >
                    Import JSON
                </Btn>
                <input
                    ref="presetFileInput"
                    type="file"
                    class="hidden"
                    accept="application/json,.json"
                    @change="importPresetsFromFile"
                >
                <Btn
                    v-if="hasSavedPresets"
                    size="xs"
                    variant="ghost"
                    :color="uiColor"
                    :disabled="!selectedPresetId"
                    @click="deleteSelectedPreset"
                >
                    Supprimer preset
                </Btn>
            </div>
            <TanStackTableFilters
                :columns="columnsWithoutActions"
                :filter-values="activeFilters"
                :filter-options="resolvedFilterOptions"
                :ui-color="uiColor"
                :presets-enabled="presetsEnabled"
                :show-preset-panel="showPresetPanel"
                :is-active-preset-dirty="isActivePresetDirty"
                @update:filters="setFilters"
                @apply="applyFilters"
                @reset="resetFilters"
                @toggle-presets="togglePresetPanel"
            />
        </div>

        <div
            v-if="selectedCount > 0"
            class="sticky bottom-2 z-20 rounded-lg border border-base-300 px-3 py-2 shadow-md flex items-center justify-between gap-2"
            :class="[bgClass]"
        >
            <div class="text-sm font-medium">
                {{ selectedCount }} élément(s) sélectionné(s)
            </div>
            <div class="flex items-center gap-2">
                <Btn size="xs" variant="outline" :color="uiColor" @click="handleExport">
                    Export sélection
                </Btn>
                <Btn size="xs" variant="ghost" :color="uiColor" @click="clearSelection">
                    Vider sélection
                </Btn>
            </div>
        </div>

        <!-- Vue Ligne (liste dense verticale : ResourceLineRow ou Minimal en colonne unique) -->
        <div
            v-if="showLineView"
            class="relative overflow-hidden p-1 w-full"
            :class="[bgClass]"
        >
            <div ref="tableContainerEl" class="w-full overflow-y-auto max-h-[70vh]">
                <TanStackTableSkeletonBody
                    v-if="loading"
                    :columns="columnsWithoutActions.slice(0, 1)"
                    :rows-count="skeletonRows"
                    :show-selection="false"
                    :show-actions-column="false"
                />
                <template v-else-if="rowsToRender.length">
                    <div class="space-y-2 p-2" :class="lineRowComponent ? '' : 'flex flex-col gap-3'">
                        <template v-if="lineRowComponent">
                            <component
                                v-for="row in rowsToRender"
                                :key="row.id"
                                :is="lineRowComponent"
                                :row="row"
                                :get-cell-for="getCellFor"
                                :columns="columnsWithoutActions"
                                :table-meta="config?._metadata?.context || {}"
                                :show-selection="showSelectionCheckboxes"
                                :is-selected="isSelected(row)"
                                :show-actions="showActionsColumn"
                                :ui-color="uiColor"
                                @row-click="handleRowClick"
                                @toggle-select="(r, checked) => toggleRow(r, checked)"
                                @action="(actionKey, entity) => emit('action', actionKey, entity, row)"
                            />
                        </template>
                        <template v-else>
                            <div
                                v-for="row in rowsToRender"
                                :key="row.id"
                                class="relative w-full"
                                :class="{ 'ring-2 ring-primary/50 rounded-lg': isSelected(row) }"
                            >
                                <div
                                    v-if="showSelectionCheckboxes"
                                    class="absolute top-2 left-2 z-10"
                                    @click.stop="toggleRow(row, !isSelected(row))"
                                >
                                    <input
                                        type="checkbox"
                                        class="checkbox checkbox-sm checkbox-primary"
                                        :checked="isSelected(row)"
                                        :aria-label="`Sélectionner la ligne ${row.id}`"
                                    />
                                </div>
                                <div
                                    class="cursor-pointer w-full"
                                    @click="handleRowClick(row)"
                                    @dblclick="emit('row-dblclick', row)"
                                >
                                    <component
                                        v-if="getEntityFromRow(row, entityType)"
                                        :is="minimalViewComponent"
                                        :[getEntityPropName(entityType)]="getEntityFromRow(row, entityType)"
                                        :show-actions="showActionsColumn"
                                        display-mode="extended"
                                        :table-meta="config?._metadata?.context || {}"
                                        @action="(actionKey, entity) => emit('action', actionKey, entity, row)"
                                    />
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <div
                    v-else
                    class="text-center py-8 text-base-content/60 rounded-lg border border-dashed border-base-300"
                >
                    <div class="flex flex-col items-center gap-2">
                        <div class="font-medium text-base-content/80">{{ emptyState.title }}</div>
                        <div class="text-sm text-base-content/60">{{ emptyState.description }}</div>
                        <Btn
                            v-if="emptyState.canReset"
                            size="xs"
                            variant="outline"
                            :color="uiColor"
                            @click="clearAllQueryState"
                        >
                            Réinitialiser filtres et recherche
                        </Btn>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue grille Minimal (flex-wrap : une ligne remplie puis passage à la suivante) -->
        <div
            v-else-if="showMinimalGrid"
            class="relative overflow-hidden p-1 w-full"
            :class="[bgClass]"
        >
            <div ref="tableContainerEl" class="w-full overflow-y-auto max-h-[70vh]">
                <TanStackTableSkeletonBody
                    v-if="loading"
                    :columns="columnsWithoutActions.slice(0, 1)"
                    :rows-count="skeletonRows"
                    :show-selection="false"
                    :show-actions-column="false"
                />
                <template v-else-if="rowsToRender.length">
                    <div class="flex flex-wrap gap-3 p-2">
                        <div
                            v-for="row in rowsToRender"
                            :key="row.id"
                            class="relative flex-[1_1_280px] min-w-[280px] max-w-full"
                            :class="{ 'ring-2 ring-primary/50 rounded-lg': isSelected(row) }"
                        >
                            <div
                                v-if="showSelectionCheckboxes"
                                class="absolute top-2 left-2 z-10"
                                @click.stop="toggleRow(row, !isSelected(row))"
                            >
                                <input
                                    type="checkbox"
                                    class="checkbox checkbox-sm checkbox-primary"
                                    :checked="isSelected(row)"
                                    :aria-label="`Sélectionner la ligne ${row.id}`"
                                />
                            </div>
                            <div
                                class="cursor-pointer h-full"
                                @click="handleRowClick(row)"
                                @dblclick="emit('row-dblclick', row)"
                            >
                                <component
                                    v-if="getEntityFromRow(row, entityType)"
                                    :is="minimalViewComponent"
                                    :[getEntityPropName(entityType)]="getEntityFromRow(row, entityType)"
                                    :show-actions="showActionsColumn"
                                    display-mode="extended"
                                    :table-meta="config?._metadata?.context || {}"
                                    @action="(actionKey, entity) => emit('action', actionKey, entity, row)"
                                />
                            </div>
                        </div>
                    </div>
                </template>
                <div
                    v-else
                    class="text-center py-8 text-base-content/60 rounded-lg border border-dashed border-base-300"
                >
                    <div class="flex flex-col items-center gap-2">
                        <div class="font-medium text-base-content/80">{{ emptyState.title }}</div>
                        <div class="text-sm text-base-content/60">{{ emptyState.description }}</div>
                        <Btn
                            v-if="emptyState.canReset"
                            size="xs"
                            variant="outline"
                            :color="uiColor"
                            @click="clearAllQueryState"
                        >
                            Réinitialiser filtres et recherche
                        </Btn>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table (vue colonnes) -->
        <div
            v-else
            class="relative overflow-hidden p-1 w-full"
            :class="[bgClass]"
        >
            <div
                ref="tableContainerEl"
                class="w-full overflow-x-auto"
                :class="{ 'overflow-y-auto max-h-[70vh]': shouldVirtualize }"
            >
                <table
                    :key="columnsKey"
                    class="table w-full tanstack-table-force-full"
                    :class="[tableVariantClass, tableSizeClass]"
                    :aria-label="tableAriaLabel"
                    :aria-busy="loading ? 'true' : 'false'"
                >
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
                    <!-- Virtualisation : spacers + lignes visibles uniquement -->
                    <template v-if="virtualizationActive && virtualItems.length">
                        <tr v-if="virtualItems[0]?.start > 0" role="presentation">
                            <td
                                :colspan="visibleColumnsFromTable.length + (showSelectionCheckboxes ? 1 : 0) + (showActionsColumn ? 1 : 0)"
                                :style="{ height: virtualItems[0].start + 'px' }"
                            />
                        </tr>
                        <TanStackTableRow
                            v-for="vi in virtualItems"
                            :key="rowsToRender[vi.index]?.id ?? vi.key"
                            :row="rowsToRender[vi.index]"
                            :columns="visibleColumnsFromTable"
                            :show-selection="showSelectionCheckboxes"
                            :is-selected="isSelected(rowsToRender[vi.index])"
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
                        <tr v-if="totalSize > (virtualItems[virtualItems.length - 1]?.end ?? 0)" role="presentation">
                            <td
                                :colspan="visibleColumnsFromTable.length + (showSelectionCheckboxes ? 1 : 0) + (showActionsColumn ? 1 : 0)"
                                :style="{ height: (totalSize - (virtualItems[virtualItems.length - 1]?.end ?? 0)) + 'px' }"
                            />
                        </tr>
                    </template>
                    <!-- Rendu normal (sans virtualisation) -->
                    <template v-else>
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
                    </template>
                </tbody>

                <tbody v-else>
                    <tr>
                        <td :colspan="visibleColumnsFromTable.length + (showSelectionCheckboxes ? 1 : 0) + (showActionsColumn ? 1 : 0)" class="text-center py-8 text-base-content/60">
                            <div class="flex flex-col items-center gap-2">
                                <div class="font-medium text-base-content/80">
                                    {{ emptyState.title }}
                                </div>
                                <div class="text-sm text-base-content/60">
                                    {{ emptyState.description }}
                                </div>
                                <Btn
                                    v-if="emptyState.canReset"
                                    size="xs"
                                    variant="outline"
                                    :color="uiColor"
                                    @click="clearAllQueryState"
                                >
                                    Réinitialiser filtres et recherche
                                </Btn>
                            </div>
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="paginationEnabled" class="relative px-2 py-1 " :class="[bgClass]">
            <TanStackTablePagination
                :page-index="serverSide ? Math.max(0, (serverParams?.page ?? 1) - 1) : paginationState.pageIndex"
                :page-count="paginationPageCount"
                :page-size="paginationState.pageSize"
                :total-rows="paginationTotalRows"
                :per-page-options="perPageOptions"
                :can-prev="paginationCanPrev"
                :can-next="paginationCanNext"
                :ui-size="uiSize"
                :ui-color="uiColor"
                @first="() => (serverSide ? emit('update:serverParams', { page: 1 }) : table.setPageIndex(0))"
                @prev="() => (serverSide ? emit('update:serverParams', { page: Math.max(1, (serverParams?.page ?? 1) - 1) }) : table.previousPage())"
                @go="(i) => (serverSide ? emit('update:serverParams', { page: Number(i) + 1 }) : table.setPageIndex(Number(i)))"
                @next="() => (serverSide ? emit('update:serverParams', { page: Math.min(paginationPageCount, (serverParams?.page ?? 1) + 1) }) : table.nextPage())"
                @last="() => (serverSide ? emit('update:serverParams', { page: paginationPageCount }) : table.setPageIndex(Math.max(0, table.getPageCount() - 1)))"
                @set-page-size="(n) => table.setPageSize(Number(n))"
            />
        </div>

    </div>
</template>


<style scoped lang="scss">
.tanstack-table-force-full {
    min-width: 100%;
    width: fit-content;
    table-layout: auto;
}
</style>
