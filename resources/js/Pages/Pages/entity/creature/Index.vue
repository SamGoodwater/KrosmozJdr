<script setup>
/**
 * Creature Index Page
 * 
 * @description
 * Page de liste des créatures avec tableau et modal
 * 
 * @props {Object} creatures - Collection paginée des créatures
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { Creature } from "@/Models/Entity/Creature";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';

const props = defineProps({
    creatures: {
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
setPageTitle('Liste des Créatures');

// Permissions
const { canCreate: canCreatePermission } = usePermissions();
const canCreate = computed(() => canCreatePermission('creatures'));

// Transformation des entités en instances de modèles
const creatures = computed(() => {
    return Creature.fromArray(props.creatures.data || []);
});

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Configuration des colonnes selon la documentation : ID (optionnel), Nom (lien), Niveau, Hostilité, Vie, Créé par, Actions
const columns = computed(() => [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Nom', sortable: true, isMain: true },
    { key: 'level', label: 'Niveau', sortable: true },
    { key: 'hostility', label: 'Hostilité', sortable: true },
    { key: 'life', label: 'Vie', sortable: true },
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
    router.visit(route(`entities.creatures.edit`, { creature: entity.id }));
};

const handleDelete = (entity) => {
    // entity peut être une instance de modèle ou un objet brut
    const creatureModel = entity instanceof Creature ? entity : new Creature(entity);
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${creatureModel.name}" ?`)) {
        router.delete(route(`entities.creatures.delete`, { creature: creatureModel.id }));
    }
};

const handleSort = ({ column, order }) => {
    router.get(route('entities.creatures.index'), {
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
        router.get(route('entities.creatures.index'), {
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
    router.get(route('entities.creatures.index'), {
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
    router.get(route('entities.creatures.index'), {}, {
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
    <Head title="Liste des Créatures" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Créatures</h1>
                <p class="text-primary-200 mt-2">Gérez les créatures (base pour NPCs et Monstres)</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une créature
            </Btn>
        </div>

        <!-- Tableau -->
        <EntityTable
            :entities="creatures"
            :columns="columns"
            entity-type="creatures"
            :pagination="props.creatures"
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
            entity-type="creature"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
                    @refresh-all="handleRefreshAll"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="creature"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </Container>
</template>
