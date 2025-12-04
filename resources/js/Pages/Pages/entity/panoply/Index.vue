<script setup>
/**
 * Panoply Index Page
 * 
 * @description
 * Page de liste des panoplies avec tableau et modal
 * 
 * @props {Object} panoplies - Collection paginée des panoplies
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Panoply } from "@/Models/Entity/Panoply";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';

const props = defineProps({
    panoplies: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Panoplies');

// Transformation des entités en instances de modèles
const panoplies = computed(() => {
    return Panoply.fromArray(props.panoplies.data || []);
});

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Configuration des colonnes selon la documentation : ID (optionnel), Nom (lien), Bonus (tronqué), Items (count), dofusdb_id, Créé par, Actions
const columns = computed(() => [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Nom', sortable: true, isMain: true },
    { key: 'bonus', label: 'Bonus', sortable: false, type: 'truncate' },
    { key: 'items', label: 'Items', sortable: false, format: (value) => Array.isArray(value) ? value.length : 0 },
    { key: 'dofusdb_id', label: 'DofusDB ID', sortable: true },
    { key: 'createdBy', label: 'Créé par', sortable: false, format: (value) => value?.name || value?.email || '-' },
    { key: 'actions', label: 'Actions', sortable: false }
]);

// Handlers
const handleView = (entity) => {
    selectedEntity.value = entity;
    modalView.value = 'large';
    modalOpen.value = true;
};

const handleEdit = (entity) => {
    router.visit(route(`entities.panoplies.edit`, { panoply: entity.id }));
};

const handleDelete = (entity) => {
    // entity peut être une instance de modèle ou un objet brut
    const panoplyModel = entity instanceof Panoply ? entity : new Panoply(entity);
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${panoplyModel.name}" ?`)) {
        router.delete(route(`entities.panoplies.delete`, { panoply: panoplyModel.id }));
    }
};

const handleSort = ({ column, order }) => {
    router.get(route('entities.panoplies.index'), {
        sort: column,
        order: order,
        search: search.value,
        ...filters.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

let searchTimeout = null;

const handleSearchUpdate = (value) => {
    search.value = value;
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(route('entities.panoplies.index'), {
            search: value,
            ...filters.value
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }, 300);
};

onBeforeUnmount(() => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
});

const handleFiltersUpdate = (newFilters) => {
    filters.value = newFilters;
    router.get(route('entities.panoplies.index'), {
        search: search.value,
        ...newFilters
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const handleFiltersReset = () => {
    search.value = '';
    filters.value = {};
    router.get(route('entities.panoplies.index'), {}, {
        preserveState: true,
        preserveScroll: true
    });
};

const filterableColumns = computed(() => [
    {
        key: 'usable',
        label: 'Utilisable',
        options: [
            { value: '', label: 'Tous' },
            { value: '1', label: 'Oui' },
            { value: '0', label: 'Non' }
        ]
    }
]);

const handlePageChange = (url) => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true
        });
    }
};

const handleCreate = () => {
    router.visit(route('entities.panoplies.create'));
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Panoplies" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Panoplies</h1>
                <p class="text-primary-200 mt-2">Gérez les panoplies (ensembles d'équipements)</p>
            </div>
            <Btn @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une panoplie
            </Btn>
        </div>

        <!-- Tableau -->
        <EntityTable
            :entities="panoplies"
            :columns="columns"
            entity-type="panoplies"
            :pagination="props.panoplies"
            :show-filters="true"
            :search="search"
            :filters="filters"
            :filterable-columns="filterableColumns"
            @view="handleView"
            @edit="handleEdit"
            @delete="handleDelete"
            @sort="handleSort"
            @page-change="handlePageChange"
            @update:search="handleSearchUpdate"
            @update:filters="handleFiltersUpdate"
        />

        <!-- Modal -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="panoply"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </Container>
</template>
