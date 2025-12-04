<script setup>
/**
 * Scenario Index Page
 * 
 * @description
 * Page de liste des scénarios avec tableau et modal
 * 
 * @props {Object} scenarios - Collection paginée des scénarios
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Scenario } from "@/Models/Entity/Scenario";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';

const props = defineProps({
    scenarios: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Scénarios');

// Transformation des entités en instances de modèles
const scenarios = computed(() => {
    return Scenario.fromArray(props.scenarios.data || []);
});

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Configuration des colonnes selon la documentation : ID (optionnel), Nom (lien), Slug, État (badge), Public, Créé par, Actions
const columns = computed(() => [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Nom', sortable: true, isMain: true },
    { key: 'slug', label: 'Slug', sortable: true },
    { key: 'state', label: 'État', sortable: true, type: 'badge', badgeColor: 'primary' },
    { key: 'is_public', label: 'Public', sortable: true, type: 'badge', badgeColor: 'success' },
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
    router.visit(route(`entities.scenarios.edit`, { scenario: entity.id }));
};

const handleDelete = (entity) => {
    // entity peut être une instance de modèle ou un objet brut
    const scenarioModel = entity instanceof Scenario ? entity : new Scenario(entity);
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${scenarioModel.name}" ?`)) {
        router.delete(route(`entities.scenarios.delete`, { scenario: scenarioModel.id }));
    }
};

const handleSort = ({ column, order }) => {
    router.get(route('entities.scenarios.index'), {
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
        router.get(route('entities.scenarios.index'), {
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
    router.get(route('entities.scenarios.index'), {
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
    router.get(route('entities.scenarios.index'), {}, {
        preserveState: true,
        preserveScroll: true
    });
};

const filterableColumns = computed(() => []);

const handlePageChange = (url) => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true
        });
    }
};

const handleCreate = () => {
    router.visit(route('entities.scenarios.create'));
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Scénarios" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Scénarios</h1>
                <p class="text-primary-200 mt-2">Gérez les scénarios de jeu</p>
            </div>
            <Btn @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un scénario
            </Btn>
        </div>

        <!-- Tableau -->
        <EntityTable
            :entities="scenarios"
            :columns="columns"
            entity-type="scenarios"
            :pagination="props.scenarios"
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
            entity-type="scenario"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </Container>
</template>
