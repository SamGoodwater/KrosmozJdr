<script setup>
/**
 * EntityTable Molecule
 * 
 * @description
 * Tableau complet pour afficher des entités avec pagination, tri et actions
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
import { ref, computed } from 'vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';
import EntityTableHeader from './EntityTableHeader.vue';
import EntityTableRow from './EntityTableRow.vue';
import EntityTableFilters from './EntityTableFilters.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import { useEntityTableSettings } from '@/Composables/store/useEntityTableSettings';
import { useEntityViewFormat } from '@/Composables/store/useEntityViewFormat';

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
    }
});

const emit = defineEmits(['view', 'edit', 'delete', 'sort', 'page-change', 'update:search', 'update:filters', 'select', 'deselect', 'quick-view', 'quick-edit', 'copy-link', 'download-pdf', 'refresh', 'refresh-all']);

// Gestion des colonnes visibles
const { visibleColumns, filteredColumns, toggleColumn } = useEntityTableSettings(props.entityType, props.columns);

// Gestion du format d'affichage
const { viewFormat, setViewFormat, availableFormats } = useEntityViewFormat(props.entityType);

const sortBy = ref('');
const sortOrder = ref('asc');

const handleSort = (columnKey) => {
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
    emit('select', entity);
};

const handleDeselect = (entity) => {
    emit('deselect', entity);
};

const isEntitySelected = (entity) => {
    return props.selectedEntities.some(selected => {
        const selectedId = selected?.id ?? selected?.id;
        const entityId = entity?.id ?? entity?.id;
        return selectedId === entityId;
    });
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

const disableQuickActions = computed(() => {
    return props.selectedEntities.length > 1;
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
                    :show-column-toggle="true"
                    :show-selection="showSelection"
                    @sort="handleSort"
                    @toggle-column="handleToggleColumn"
                />
                <tbody>
                    <tr v-if="loading">
                        <td :colspan="filteredColumns.length + (showSelection ? 1 : 0) + (showActionsMenu ? 1 : 0) + 1" class="text-center py-8">
                            <Loading />
                        </td>
                    </tr>
                    <template v-else-if="entities.length > 0">
                        <EntityTableRow
                            v-for="entity in entities"
                            :key="entity.id"
                            :entity="entity"
                            :columns="filteredColumns"
                            :entity-type="entityType"
                            :show-selection="showSelection"
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
                        />
                    </template>
                    <tr v-else>
                        <td :colspan="filteredColumns.length + (showSelection ? 1 : 0) + (showActionsMenu ? 1 : 0)" class="text-center py-8 text-primary-300">
                            Aucune entité trouvée
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.links && pagination.links.length > 3" 
             class="flex justify-center gap-2">
            <Btn
                v-for="link in pagination.links"
                :key="link.label"
                :disabled="!link.url || link.active"
                :variant="link.active ? 'outline' : 'ghost'"
                size="sm"
                @click="handlePageChange(link.url)">
                <span v-html="link.label"></span>
            </Btn>
        </div>
    </div>
</template>

