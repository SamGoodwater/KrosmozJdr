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
    }
});

const emit = defineEmits(['view', 'edit', 'delete', 'sort', 'page-change', 'update:search', 'update:filters']);

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
</script>

<template>
    <div class="space-y-4">
        <!-- Filtres -->
        <EntityTableFilters
            v-if="showFilters"
            :search="search"
            :filters="filters"
            :filterable-columns="filterableColumns"
            @update:search="handleSearchUpdate"
            @update:filters="handleFiltersUpdate"
            @reset="handleFiltersReset"
        />

        <!-- Tableau -->
        <div class="overflow-x-auto rounded-lg border border-base-300">
            <table class="table w-full">
                <EntityTableHeader 
                    :columns="columns"
                    :sort-by="sortBy"
                    :sort-order="sortOrder"
                    @sort="handleSort"
                />
                <tbody>
                    <tr v-if="loading">
                        <td :colspan="columns.length" class="text-center py-8">
                            <Loading />
                        </td>
                    </tr>
                    <template v-else-if="entities.length > 0">
                        <EntityTableRow
                            v-for="entity in entities"
                            :key="entity.id"
                            :entity="entity"
                            :columns="columns"
                            :entity-type="entityType"
                            @view="handleView"
                            @edit="handleEdit"
                            @delete="handleDelete"
                        />
                    </template>
                    <tr v-else>
                        <td :colspan="columns.length" class="text-center py-8 text-primary-300">
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

