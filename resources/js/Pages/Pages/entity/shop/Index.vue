<script setup>
/**
 * Shop Index Page
 * 
 * @description
 * Page de liste des boutiques avec tableau et modal
 * 
 * @props {Object} shops - Collection paginée des boutiques
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Shop } from "@/Models/Entity/Shop";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';

const props = defineProps({
    shops: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Boutiques');

// Transformation des entités en instances de modèles
const shops = computed(() => {
    return Shop.fromArray(props.shops.data || []);
});

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Configuration des colonnes selon la documentation : ID (optionnel), Nom (lien), Localisation, NPC, Items (count), Créé par, Actions
const columns = computed(() => [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Nom', sortable: true, isMain: true },
    { key: 'location', label: 'Localisation', sortable: false, type: 'truncate' },
    { key: 'npc', label: 'NPC', sortable: false, format: (value) => value?.creature?.name || '-' },
    { key: 'items', label: 'Items', sortable: false, format: (value) => Array.isArray(value) ? value.length : 0 },
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
    router.visit(route(`entities.shops.edit`, { shop: entity.id }));
};

const handleDelete = (entity) => {
    // entity peut être une instance de modèle ou un objet brut
    const shopModel = entity instanceof Shop ? entity : new Shop(entity);
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${shopModel.name}" ?`)) {
        router.delete(route(`entities.shops.delete`, { shop: shopModel.id }));
    }
};

const handleSort = ({ column, order }) => {
    router.get(route('entities.shops.index'), {
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
        router.get(route('entities.shops.index'), {
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
    router.get(route('entities.shops.index'), {
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
    router.get(route('entities.shops.index'), {}, {
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
    router.visit(route('entities.shops.create'));
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Boutiques" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Boutiques</h1>
                <p class="text-primary-200 mt-2">Gérez les boutiques et commerces</p>
            </div>
            <Btn @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une boutique
            </Btn>
        </div>

        <!-- Tableau -->
        <EntityTable
            :entities="shops"
            :columns="columns"
            entity-type="shops"
            :pagination="props.shops"
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
            entity-type="shop"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </Container>
</template>
