<script setup>
/**
 * Attribute Index Page
 * 
 * @description
 * Page de liste des attributs avec tableau et modal
 * 
 * @props {Object} attributes - Collection paginée des attributs
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';

const props = defineProps({
    attributes: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

// Notifications
const notificationStore = useNotificationStore();
setPageTitle('Liste des Attributs');

// Permissions
const { canCreate: canCreatePermission } = usePermissions();
const canCreate = computed(() => canCreatePermission('attributes'));

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Configuration des colonnes selon la documentation
const columns = computed(() => [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Nom', sortable: true, isMain: true },
    { key: 'description', label: 'Description', sortable: false, type: 'truncate' },
    { key: 'usable', label: 'Utilisable', sortable: true, type: 'badge', badgeColor: 'success' },
    { key: 'is_visible', label: 'Visible', sortable: true },
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
    router.visit(route(`entities.attributes.edit`, { attribute: entity.id }));
};

const handleDelete = (entity) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${entity.name}" ?`)) {
        router.delete(route(`entities.attributes.delete`, { attribute: entity.id }));
    }
};

const handleSort = ({ column, order }) => {
    // Reload avec tri
    router.get(route('entities.attributes.index'), {
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
    // Debounce pour éviter trop de requêtes
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(route('entities.attributes.index'), {
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
    router.get(route('entities.attributes.index'), {
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
    router.get(route('entities.attributes.index'), {}, {
        preserveState: true,
        preserveScroll: true
    });
};

// Colonnes filtrables
const filterableColumns = computed(() => [
    {
        key: 'usable',
        label: 'Utilisable',
        options: [
            { value: '', label: 'Tous' },
            { value: '1', label: 'Oui' },
            { value: '0', label: 'Non' }
        ]
    },
    {
        key: 'is_visible',
        label: 'Visible',
        options: [
            { value: '', label: 'Tous' },
            { value: 'guest', label: 'Invité' },
            { value: 'user', label: 'Utilisateur' },
            { value: 'player', label: 'Joueur' },
            { value: 'game_master', label: 'Maître de jeu' }
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
    createModalOpen.value = true;
};

const handleCloseCreateModal = () => {
    createModalOpen.value = false;
};

const handleEntityCreated = () => {
    createModalOpen.value = false;
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};
</script>

<template>
    <Head title="Liste des Attributs" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Attributs</h1>
                <p class="text-primary-200 mt-2">Gérez les attributs de votre système</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un attribut
            </Btn>
        </div>

        <!-- Tableau -->
        <EntityTable
            :entities="attributes.data || []"
            :columns="columns"
            entity-type="attributes"
            :pagination="attributes"
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

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="attribute"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
                    @refresh-all="handleRefreshAll"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="attribute"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </Container>
</template>

