<script setup>
/**
 * Monster Index Page
 * 
 * @description
 * Page de liste des monstres avec tableau et modal
 * 
 * @props {Object} monsters - Collection paginée des monstres
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Monster } from "@/Models/Entity/Monster";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';

const props = defineProps({
    monsters: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Monstres');

// Transformation des entités en instances de modèles
const monsters = computed(() => {
    return Monster.fromArray(props.monsters.data || []);
});

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Configuration des colonnes selon la documentation : ID (optionnel), Nom (via Creature, lien), Race, Taille, Boss, Créé par, Actions
const columns = computed(() => [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'creature', label: 'Nom', sortable: false, isMain: true, format: (value) => value?.name || '-' },
    { key: 'monsterRace', label: 'Race', sortable: false, format: (value) => value?.name || '-' },
    { key: 'size', label: 'Taille', sortable: true },
    { key: 'is_boss', label: 'Boss', sortable: true, type: 'badge', badgeColor: 'error' },
    { key: 'actions', label: 'Actions', sortable: false }
]);

// Handlers
const handleView = (entity) => {
    selectedEntity.value = entity;
    modalView.value = 'large';
    modalOpen.value = true;
};

const handleEdit = (entity) => {
    router.visit(route(`entities.monsters.edit`, { monster: entity.id }));
};

const handleDelete = (entity) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer ce monstre ?`)) {
        router.delete(route(`entities.monsters.delete`, { monster: entity.id }));
    }
};

const handleSort = ({ column, order }) => {
    router.get(route('entities.monsters.index'), {
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
        router.get(route('entities.monsters.index'), {
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
    router.get(route('entities.monsters.index'), {
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
    router.get(route('entities.monsters.index'), {}, {
        preserveState: true,
        preserveScroll: true
    });
};

const filterableColumns = computed(() => [
    {
        key: 'size',
        label: 'Taille',
        options: [
            { value: '', label: 'Tous' },
            { value: '1', label: '1' },
            { value: '2', label: '2' },
            { value: '3', label: '3' },
            { value: '4', label: '4' },
            { value: '5', label: '5+' }
        ]
    },
    {
        key: 'is_boss',
        label: 'Boss',
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
    router.visit(route('entities.monsters.create'));
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Monstres" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Monstres</h1>
                <p class="text-primary-200 mt-2">Gérez les monstres du jeu</p>
            </div>
            <Btn @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un monstre
            </Btn>
        </div>

        <!-- Tableau -->
        <EntityTable
            :entities="monsters"
            :columns="columns"
            entity-type="monsters"
            :pagination="props.monsters"
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
            entity-type="monster"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </Container>
</template>
