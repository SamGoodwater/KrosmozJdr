<script setup>
/**
 * EntityTable Molecule
 * 
 * @description
 * Tableau complet pour afficher des entités avec pagination, tri et actions
 * Supporte un mode hybride :
 * - server: tri/filtre/recherche/pagination via Inertia (backend)
 * - client: tri/filtre/recherche/pagination côté navigateur (TanStack Table) sur un dataset chargé
 * 
 * @props {Array} entities - Liste des entités à afficher
 * @props {Array} columns - Configuration des colonnes
 * @props {String} entityType - Type d'entité
 * @props {Object} pagination - Données de pagination Laravel
 * @props {Boolean} loading - État de chargement
 * @props {Boolean} showFilters - Afficher les filtres (défaut: false)
 * @props {String} search - Valeur de recherche
 * @props {Object} filters - Filtres actifs
 * @props {Array} filterableColumns - Colonnes filtrables
 * @emit view - Événement émis lors du clic sur une entité
 * @emit edit - Événement émis lors du clic sur éditer
 * @emit delete - Événement émis lors du clic sur supprimer
 * @emit sort - Événement émis lors du tri
 * @emit page-change - Événement émis lors du changement de page
 * @emit update:search - Événement émis lors du changement de recherche
 * @emit update:filters - Événement émis lors du changement de filtres
 */
import { ref, computed, watch } from 'vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';
import EntityTableHeader from './EntityTableHeader.vue';
import EntityTableRow from './EntityTableRow.vue';
import EntityTableFilters from './EntityTableFilters.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import { useEntityTableSettings } from '@/Composables/store/useEntityTableSettings';
import { useEntityViewFormat } from '@/Composables/store/useEntityViewFormat';
import { getCoreRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from '@tanstack/vue-table';

const props = defineProps({
    entities: {
        type: Array,
        required: true,
        default: () => []
    },
    columns: {
        type: Array,
        required: true,
        default: () => []
    },
    entityType: {
        type: String,
        required: true
    },
    pagination: {
        type: Object,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    },
    showFilters: {
        type: Boolean,
        default: false
    },
    search: {
        type: String,
        default: ''
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    filterableColumns: {
        type: Array,
        default: () => []
    },
    showSelection: {
        type: Boolean,
        default: false
    },
    selectedEntities: {
        type: Array,
        default: () => []
    },
    showActionsMenu: {
        type: Boolean,
        default: false
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    /**
     * Mode de table:
     * - server: utilise `pagination`/Inertia pour tri/filtre/recherche/pagination
     * - client: utilise TanStack Table sur `entities`
     */
    mode: {
        type: String,
        default: 'server',
        validator: (v) => ['server', 'client'].includes(v)
    },
    /**
     * Activer l'export CSV (côté client, depuis le dataset courant).
     */
    enableExportCsv: {
        type: Boolean,
        default: true
    },
    exportFilename: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['view', 'edit', 'delete', 'sort', 'page-change', 'update:search', 'update:filters', 'select', 'deselect', 'quick-view', 'quick-edit', 'copy-link', 'download-pdf', 'refresh', 'refresh-all', 'update:selectedEntities', 'cell-update', 'filtered-ids']);

// Gestion des colonnes visibles
const { visibleColumns, filteredColumns, toggleColumn } = useEntityTableSettings(props.entityType, props.columns);

// Gestion du format d'affichage
const { viewFormat, setViewFormat, availableFormats } = useEntityViewFormat(props.entityType);

const sortBy = ref('');
const sortOrder = ref('asc');

const handleSort = (columnKey) => {
    // Mode client: tri local (ne pas déclencher le backend)
    if (props.mode === 'client') {
        if (sortBy.value === columnKey) {
            sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy.value = columnKey;
            sortOrder.value = 'asc';
        }
        return;
    }

    if (sortBy.value === columnKey) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = columnKey;
        sortOrder.value = 'asc';
    }
    emit('sort', { column: columnKey, order: sortOrder.value });
};

const handleView = (entity) => {
    emit('view', entity);
};

const handleEdit = (entity) => {
    emit('edit', entity);
};

const handleDelete = (entity) => {
    emit('delete', entity);
};

const handlePageChange = (url) => {
    if (url) {
        emit('page-change', url);
    }
};

const handleSearchUpdate = (value) => {
    emit('update:search', value);
};

const handleFiltersUpdate = (filters) => {
    emit('update:filters', filters);
};

const handleFiltersReset = () => {
    emit('update:search', '');
    emit('update:filters', {});
};

const handleToggleColumn = (columnKey) => {
    toggleColumn(columnKey);
};

const handleSelect = (entity) => {
    addToSelection(entity);
    emit('select', entity);
};

const handleDeselect = (entity) => {
    removeFromSelection(entity);
    emit('deselect', entity);
};

const internalSelected = ref([]);

// Si le parent fournit une sélection, on la reflète en interne (utile pour l'export CSV / UI)
watch(
    () => props.selectedEntities,
    (next) => {
        if (Array.isArray(next) && next.length) {
            internalSelected.value = [...next];
        }
        if (Array.isArray(next) && next.length === 0) {
            // Si le parent vide explicitement, on suit.
            internalSelected.value = [];
        }
    },
    { deep: true, immediate: true }
);

const selectedEntitiesEffective = computed(() => {
    return (props.selectedEntities && props.selectedEntities.length) ? props.selectedEntities : internalSelected.value;
});

const getEntityId = (e) => (e && typeof e.id !== 'undefined') ? e.id : null;

const isEntitySelected = (entity) => {
    const entityId = getEntityId(entity);
    if (!entityId) return false;
    return selectedEntitiesEffective.value.some((selected) => getEntityId(selected) === entityId);
};

const addToSelection = (entity) => {
    const entityId = getEntityId(entity);
    if (!entityId) return;
    if (isEntitySelected(entity)) return;
    internalSelected.value = [...selectedEntitiesEffective.value, entity];
    emit('update:selectedEntities', internalSelected.value);
};

const removeFromSelection = (entity) => {
    const entityId = getEntityId(entity);
    if (!entityId) return;
    internalSelected.value = selectedEntitiesEffective.value.filter((e) => getEntityId(e) !== entityId);
    emit('update:selectedEntities', internalSelected.value);
};

const handleQuickView = (entity) => {
    emit('quick-view', entity);
};

const handleQuickEdit = (entity) => {
    emit('quick-edit', entity);
};

const handleCopyLink = (entity) => {
    emit('copy-link', entity);
};

const handleDownloadPdf = (entity) => {
    emit('download-pdf', entity);
};

const handleRefresh = (entity) => {
    emit('refresh', entity);
};

const handleRefreshAll = () => {
    emit('refresh-all');
};

const handleCellUpdate = (payload) => {
    emit('cell-update', payload);
};

const disableQuickActions = computed(() => {
    return selectedEntitiesEffective.value.length > 1;
});

/**
 * Récupère une valeur "raw" pour un champ (support BaseModel ou objet brut).
 */
const getEntityFieldValue = (entity, key) => {
    if (!entity) return null;
    if (typeof entity._data !== 'undefined') {
        if (typeof entity[key] !== 'undefined') return entity[key];
        return entity._data?.[key] ?? null;
    }
    return entity[key] ?? null;
};

/**
 * Applique les filtres "select" (égalité) côté client sur le dataset.
 * NB: on garde les mêmes clés que celles envoyées au backend (ex: resource_type_id).
 */
const clientFilteredEntities = computed(() => {
    if (props.mode !== 'client') return props.entities || [];
    const activeFilters = props.filters || {};
    const searchValue = (props.search || '').trim().toLowerCase();

    const searchableColumns = (filteredColumns.value || []).filter((c) => c.key !== 'actions');

    return (props.entities || []).filter((entity) => {
        // Filtres "select"
        for (const [k, v] of Object.entries(activeFilters)) {
            if (v === '' || v === null || typeof v === 'undefined') continue;
            const raw = getEntityFieldValue(entity, k);

            // bool/int filter (0/1) ou string
            if (raw === null || typeof raw === 'undefined') return false;

            const rawStr = String(raw);
            if (rawStr !== String(v)) {
                return false;
            }
        }

        // Recherche globale (sur colonnes visibles)
        if (!searchValue) return true;
        for (const col of searchableColumns) {
            const raw = getEntityFieldValue(entity, col.key);
            const text = raw === null || typeof raw === 'undefined' ? '' : String(raw);
            if (text.toLowerCase().includes(searchValue)) return true;
        }
        return false;
    });
});

/**
 * Expose au parent les IDs "filtrés" (utile pour bulk actions).
 * - mode client: filtre local (search + filtres select)
 * - mode server: IDs de la page courante uniquement (pas un vrai "filtre global")
 */
const filteredIds = computed(() => {
    const rows = props.mode === 'client'
        ? (clientFilteredEntities.value || [])
        : (props.entities || []);

    return rows
        .map((e) => getEntityId(e))
        .filter((v) => v !== null && typeof v !== 'undefined');
});

watch(
    () => filteredIds.value.join(','),
    () => {
        emit('filtered-ids', filteredIds.value);
    },
    { immediate: true }
);

/**
 * TanStack Table: tri + pagination côté client.
 * (On conserve l'UI existante: header/rows, mais le dataset est transformé par TanStack.)
 */
const sorting = computed(() => {
    if (!sortBy.value) return [];
    return [{ id: sortBy.value, desc: sortOrder.value === 'desc' }];
});

const paginationState = ref({ pageIndex: 0, pageSize: 25 });

const tanstackColumns = computed(() => {
    // Pour le tri, on expose toutes les colonnes "data" sauf actions.
    return (props.columns || [])
        .filter((c) => c.key && c.key !== 'actions')
        .map((c) => ({
            id: c.key,
            accessorFn: (row) => getEntityFieldValue(row, c.key),
            enableSorting: c.sortable !== false,
        }));
});

const table = useVueTable({
    get data() {
        return clientFilteredEntities.value;
    },
    get columns() {
        return tanstackColumns.value;
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    state: {
        get sorting() {
            return sorting.value;
        },
        get pagination() {
            return paginationState.value;
        },
    },
    onPaginationChange: (updater) => {
        const next = typeof updater === 'function' ? updater(paginationState.value) : updater;
        paginationState.value = next;
    },
});

const entitiesToRender = computed(() => {
    if (props.mode !== 'client') return props.entities || [];
    return table.getRowModel().rows.map((r) => r.original);
});

const clientTotal = computed(() => clientFilteredEntities.value.length);

const clientPageCount = computed(() => table.getPageCount());

const handleClientPrev = () => {
    if (table.getCanPreviousPage()) table.previousPage();
};

const handleClientNext = () => {
    if (table.getCanNextPage()) table.nextPage();
};

const handleClientPageSizeChange = (event) => {
    const size = Number(event?.target?.value ?? 25);
    if (!Number.isFinite(size) || size <= 0) return;
    table.setPageSize(size);
};

const toCsvCell = (value) => {
    const v = value === null || typeof value === 'undefined' ? '' : String(value);
    // RFC4180-ish: quote if needed, escape quotes by doubling
    if (/[",\n]/.test(v)) {
        return `"${v.replaceAll('"', '""')}"`;
    }
    return v;
};

const downloadCsv = (filename, csvText) => {
    const blob = new Blob([csvText], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
};

const handleExportCsv = () => {
    if (!props.enableExportCsv) return;

    const cols = (filteredColumns.value || []).filter((c) => c.key !== 'actions');
    const headers = cols.map((c) => toCsvCell(c.label ?? c.key)).join(',');

    // Si sélection active -> on exporte la sélection, sinon toutes les lignes "filtrées" (client) ou affichées (server)
    const rows = selectedEntitiesEffective.value?.length
        ? selectedEntitiesEffective.value
        : props.mode === 'client'
            ? table.getPrePaginationRowModel().rows.map((r) => r.original)
            : (props.entities || []);

    const lines = rows.map((entity) => {
        return cols.map((col) => {
            const raw = getEntityFieldValue(entity, col.key);
            const formatted = typeof col.format === 'function' ? col.format(raw, entity) : raw;
            return toCsvCell(formatted);
        }).join(',');
    });

    const csv = [headers, ...lines].join('\n');
    const filename = props.exportFilename || `${props.entityType}.csv`;
    downloadCsv(filename, csv);
};

/**
 * Sélectionner / désélectionner toutes les lignes affichées (page courante).
 */
const canToggleAll = computed(() => props.showSelection);

// UX: masquer les checkboxes par défaut, les afficher seulement quand une sélection existe.
const showSelectionCheckboxes = computed(() => {
    return props.showSelection && selectedEntitiesEffective.value.length > 0;
});

const pageEntities = computed(() => entitiesToRender.value || []);

const allSelectedOnPage = computed(() => {
    if (!canToggleAll.value) return false;
    const rows = pageEntities.value;
    if (!rows.length) return false;
    return rows.every((e) => isEntitySelected(e));
});

const someSelectedOnPage = computed(() => {
    if (!canToggleAll.value) return false;
    const rows = pageEntities.value;
    if (!rows.length) return false;
    const selectedCount = rows.filter((e) => isEntitySelected(e)).length;
    return selectedCount > 0 && selectedCount < rows.length;
});

const handleToggleAll = (checked) => {
    if (!canToggleAll.value) return;
    const rows = pageEntities.value;
    if (!rows.length) return;

    if (checked) {
        const next = [...selectedEntitiesEffective.value];
        const existingIds = new Set(next.map((e) => getEntityId(e)));
        for (const e of rows) {
            const id = getEntityId(e);
            if (id && !existingIds.has(id)) next.push(e);
        }
        internalSelected.value = next;
        emit('update:selectedEntities', internalSelected.value);
    } else {
        const pageIds = new Set(rows.map((e) => getEntityId(e)));
        internalSelected.value = selectedEntitiesEffective.value.filter((e) => !pageIds.has(getEntityId(e)));
        emit('update:selectedEntities', internalSelected.value);
    }
};

const extraHeaderCellsCount = computed(() => {
    return (showSelectionCheckboxes.value ? 1 : 0) + (props.showActionsMenu ? 1 : 0);
});
</script>

<template>
    <div class="space-y-4">
        <!-- Barre d'outils : Filtres et sélecteur de format -->
        <div class="flex items-center justify-between gap-4">
            <!-- Filtres -->
            <div class="flex-1">
                <EntityTableFilters
                    v-if="showFilters"
                    :search="search"
                    :filters="filters"
                    :filterable-columns="filterableColumns"
                    @update:search="handleSearchUpdate"
                    @update:filters="handleFiltersUpdate"
                    @reset="handleFiltersReset"
                />
            </div>
            
            <!-- Boutons d'action -->
            <div class="flex-shrink-0 flex gap-2">
                <!-- Bouton de rafraîchissement -->
                <Btn 
                    size="sm" 
                    variant="ghost" 
                    class="gap-2"
                    @click="handleRefreshAll"
                    :title="'Rafraîchir les données depuis le backend'"
                >
                    <Icon source="fa-solid fa-arrow-rotate-right" alt="Rafraîchir" size="sm" />
                    <span class="hidden md:inline">Rafraîchir</span>
                </Btn>

                <!-- Export CSV -->
                <Btn
                    v-if="enableExportCsv"
                    size="sm"
                    variant="ghost"
                    class="gap-2"
                    @click="handleExportCsv"
                    :title="selectedEntitiesEffective.length ? 'Exporter la sélection en CSV' : (mode === 'client' ? 'Exporter le dataset filtré en CSV' : 'Exporter les lignes affichées en CSV')"
                >
                    <Icon source="fa-solid fa-file-csv" alt="Exporter CSV" size="sm" />
                    <span class="hidden md:inline">Exporter</span>
                </Btn>

                <!-- Colonnes visibles (déplacé hors du header pour éviter le décalage visuel) -->
                <Dropdown placement="bottom-end" :close-on-content-click="false">
                    <template #trigger>
                        <Btn size="sm" variant="ghost" class="gap-2" title="Colonnes visibles">
                            <Icon source="fa-solid fa-columns" alt="Colonnes visibles" size="sm" />
                            <span class="hidden md:inline">Colonnes</span>
                        </Btn>
                    </template>
                    <template #content>
                        <div class="p-3 w-60">
                            <div class="text-sm font-semibold mb-2">Colonnes visibles</div>
                            <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                <label
                                    v-for="column in (columns || [])"
                                    :key="column.key"
                                    class="flex items-center gap-2 cursor-pointer"
                                    :class="{ 'opacity-60 cursor-not-allowed': column?.isMain }"
                                >
                                    <input
                                        type="checkbox"
                                        class="checkbox checkbox-sm"
                                        :checked="visibleColumns[column.key] !== false"
                                        :disabled="column?.isMain"
                                        @change="handleToggleColumn(column.key)"
                                    />
                                    <span class="text-sm">{{ column.label }}</span>
                                </label>
                            </div>
                        </div>
                    </template>
                </Dropdown>
                
                <!-- Sélecteur de format d'affichage -->
                <Dropdown placement="bottom-end">
                    <template #trigger>
                        <Btn size="sm" variant="ghost" class="gap-2">
                            <Icon :source="availableFormats.find(f => f.value === viewFormat)?.icon || 'fa-solid fa-window-maximize'" alt="Format d'affichage" size="sm" />
                            <span class="hidden md:inline">{{ availableFormats.find(f => f.value === viewFormat)?.label || 'Format' }}</span>
                        </Btn>
                    </template>
                    <template #content>
                        <ul class="menu p-2 shadow bg-base-100 rounded-box w-48 z-[1]">
                            <li class="menu-title">
                                <span>Format d'affichage</span>
                            </li>
                            <li v-for="format in availableFormats" :key="format.value">
                                <button 
                                    @click="setViewFormat(format.value)" 
                                    class="flex items-center gap-2"
                                    :class="{ 'active': viewFormat === format.value }">
                                    <Icon :source="format.icon" :alt="format.label" size="sm" />
                                    <span>{{ format.label }}</span>
                                    <Icon v-if="viewFormat === format.value" source="fa-solid fa-check" alt="Actif" size="sm" class="ml-auto" />
                                </button>
                            </li>
                        </ul>
                    </template>
                </Dropdown>
            </div>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto rounded-lg border border-base-300">
            <table class="table w-full">
                <EntityTableHeader 
                    :columns="columns"
                    :sort-by="sortBy"
                    :sort-order="sortOrder"
                    :visible-columns="visibleColumns"
                    :show-selection="showSelectionCheckboxes"
                    :all-selected="allSelectedOnPage"
                    :some-selected="someSelectedOnPage"
                    @toggle-all="handleToggleAll"
                    @sort="handleSort"
                    @toggle-column="handleToggleColumn"
                />
                <tbody>
                    <tr v-if="loading">
                        <td :colspan="filteredColumns.length + extraHeaderCellsCount" class="text-center py-8">
                            <Loading />
                        </td>
                    </tr>
                    <template v-else-if="entitiesToRender.length > 0">
                        <EntityTableRow
                            v-for="entity in entitiesToRender"
                            :key="entity.id"
                            :entity="entity"
                            :columns="filteredColumns"
                            :entity-type="entityType"
                            :enable-selection="showSelection"
                            :show-selection="showSelectionCheckboxes"
                            :is-selected="isEntitySelected(entity)"
                            :show-actions-menu="showActionsMenu"
                            :disable-quick-actions="disableQuickActions"
                            :is-admin="isAdmin"
                            @view="handleView"
                            @edit="handleEdit"
                            @delete="handleDelete"
                            @select="handleSelect"
                            @deselect="handleDeselect"
                            @quick-view="handleQuickView"
                            @quick-edit="handleQuickEdit"
                            @copy-link="handleCopyLink"
                            @download-pdf="handleDownloadPdf"
                            @refresh="handleRefresh"
                            @cell-update="handleCellUpdate"
                        />
                    </template>
                    <tr v-else>
                        <td :colspan="filteredColumns.length + extraHeaderCellsCount" class="text-center py-8 text-primary-300">
                            Aucune entité trouvée
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination (server) -->
        <div v-if="mode !== 'client' && pagination && pagination.links && pagination.links.length > 3" 
             class="flex justify-center gap-2">
            <Btn
                v-for="link in pagination.links"
                :key="link.label"
                :disabled="!link.url || link.active"
                :variant="link.active ? 'outline' : 'ghost'"
                size="sm"
                @click="handlePageChange(link.url)">
                <!-- eslint-disable-next-line vue/no-v-html -- pagination Inertia/Laravel (label HTML contrôlé) -->
                <span v-html="link.label"></span>
            </Btn>
        </div>

        <!-- Pagination (client) -->
        <div v-else-if="mode === 'client'" class="flex items-center justify-between gap-3">
            <div class="text-sm text-base-content/70">
                {{ clientTotal }} lignes (filtrées)
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 text-sm text-base-content/70">
                    <span class="hidden sm:inline">Lignes</span>
                    <select
                        class="select select-bordered select-sm"
                        :value="paginationState.pageSize"
                        @change="handleClientPageSizeChange"
                        title="Lignes par page"
                    >
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                </label>
                <Btn size="sm" variant="ghost" :disabled="!table.getCanPreviousPage()" @click="handleClientPrev">
                    Précédent
                </Btn>
                <span class="text-sm">
                    Page {{ paginationState.pageIndex + 1 }} / {{ clientPageCount || 1 }}
                </span>
                <Btn size="sm" variant="ghost" :disabled="!table.getCanNextPage()" @click="handleClientNext">
                    Suivant
                </Btn>
            </div>
        </div>
    </div>
</template>

