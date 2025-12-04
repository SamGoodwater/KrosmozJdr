<script setup>
/**
 * Item Index Page
 * 
 * @description
 * Page de liste des items avec tableau et modal
 * 
 * @props {Object} items - Collection paginée des items
 */
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useEntityPermissions } from "@/Composables/permissions/useEntityPermissions";
import { useEntityComparison } from "@/Composables/utils/useEntityComparison";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { Item } from "@/Models/Entity/Item";
import { User } from "@/Models";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';

const props = defineProps({
    items: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Objets');

// Notifications
const notificationStore = useNotificationStore();

// Permissions
const { canCreateEntity } = useEntityPermissions();
const canCreate = computed(() => canCreateEntity('item'));

// Vérifier si l'utilisateur est admin
const page = usePage();
const currentUser = computed(() => {
    return page.props.auth?.user ? new User(page.props.auth.user) : null;
});
const isAdmin = computed(() => {
    return currentUser.value?.isAdmin ?? false;
});

// Transformation des entités en instances de modèles
const items = computed(() => {
    return Item.fromArray(props.items.data || []);
});

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Mode édition rapide
const quickEditMode = ref(false);
const quickEditEntity = ref(null);
const quickEditViewMode = ref('compact');

// Sélection multiple
const selectedEntities = ref([]);
const multiEditMode = ref(false);

// Configuration des colonnes selon la documentation : ID (optionnel), Nom (lien), Niveau, Rareté (badge), Type, dofusdb_id, Créé par, Actions
const columns = computed(() => [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Nom', sortable: true, isMain: true },
    { key: 'level', label: 'Niveau', sortable: true },
    { key: 'rarity', label: 'Rareté', sortable: true, type: 'badge', badgeColor: 'primary' },
    { key: 'itemType', label: 'Type', sortable: false, format: (value) => value?.name || '-' },
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
    router.visit(route(`entities.items.edit`, { item: entity.id }));
};

const handleDelete = (entity) => {
    // entity peut être une instance de modèle ou un objet brut
    const itemModel = entity instanceof Item ? entity : new Item(entity);
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${itemModel.name}" ?`)) {
        router.delete(route(`entities.items.delete`, { item: itemModel.id }));
    }
};

const handleSort = ({ column, order }) => {
    router.get(route('entities.items.index'), {
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
        router.get(route('entities.items.index'), {
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
    router.get(route('entities.items.index'), {
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
    router.get(route('entities.items.index'), {}, {
        preserveState: true,
        preserveScroll: true
    });
};

const filterableColumns = computed(() => [
    {
        key: 'level',
        label: 'Niveau',
        options: [
            { value: '', label: 'Tous' },
            { value: '1', label: '1' },
            { value: '50', label: '50' },
            { value: '100', label: '100' },
            { value: '150', label: '150' },
            { value: '200', label: '200' }
        ]
    },
    {
        key: 'rarity',
        label: 'Rareté',
        options: [
            { value: '', label: 'Tous' },
            { value: 'common', label: 'Commun' },
            { value: 'uncommon', label: 'Peu commun' },
            { value: 'rare', label: 'Rare' },
            { value: 'epic', label: 'Épique' },
            { value: 'legendary', label: 'Légendaire' }
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
    // Le rechargement est géré par CreateEntityModal
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};

// Configuration des champs pour les items (identique à Edit.vue)
const fieldsConfig = {
    name: { 
        type: 'text', 
        label: 'Nom', 
        required: true, 
        showInCompact: true 
    },
    description: { 
        type: 'textarea', 
        label: 'Description', 
        required: false, 
        showInCompact: false 
    },
    level: { 
        type: 'number', 
        label: 'Niveau', 
        required: false, 
        showInCompact: true 
    },
    rarity: { 
        type: 'select', 
        label: 'Rareté', 
        required: false, 
        showInCompact: true,
        options: [
            { value: 'common', label: 'Commun' },
            { value: 'uncommon', label: 'Peu commun' },
            { value: 'rare', label: 'Rare' },
            { value: 'epic', label: 'Épique' },
            { value: 'legendary', label: 'Légendaire' }
        ]
    },
    image: { 
        type: 'file', 
        label: 'Image', 
        required: false, 
        showInCompact: false 
    }
};

// Handlers pour l'édition rapide
const handleSelect = (entity) => {
    if (quickEditMode.value && !multiEditMode.value) {
        // Mode édition rapide simple : une seule entité
        quickEditEntity.value = entity;
        selectedEntities.value = [entity];
    } else if (multiEditMode.value) {
        // Mode sélection multiple
        if (!selectedEntities.value.some(e => {
            const eId = e?.id ?? e?.id;
            const entityId = entity?.id ?? entity?.id;
            return eId === entityId;
        })) {
            selectedEntities.value.push(entity);
        }
    }
};

const handleDeselect = (entity) => {
    if (quickEditMode.value && !multiEditMode.value) {
        // Mode édition rapide simple
        if (quickEditEntity.value?.id === entity?.id) {
            quickEditEntity.value = null;
        }
    }
    
    // Retirer de la sélection multiple
    selectedEntities.value = selectedEntities.value.filter(e => {
        const eId = e?.id ?? e?.id;
        const entityId = entity?.id ?? entity?.id;
        return eId !== entityId;
    });
};

// Comparaison des entités sélectionnées pour l'édition multiple
const comparison = computed(() => {
    if (multiEditMode.value && selectedEntities.value.length > 0) {
        return useEntityComparison(selectedEntities.value, fieldsConfig);
    }
    return {
        commonValues: {},
        differentFields: [],
        hasDifferences: false
    };
});

const handleQuickEditSubmit = () => {
    // Recharger les données après sauvegarde
    router.reload({
        only: ['items'],
        preserveState: true,
        preserveScroll: true
    });
    // Réinitialiser la sélection après sauvegarde
    quickEditEntity.value = null;
    selectedEntities.value = [];
};

const handleQuickEditCancel = () => {
    quickEditEntity.value = null;
    selectedEntities.value = [];
    if (multiEditMode.value) {
        multiEditMode.value = false;
    }
};

// Handlers pour les actions du menu
const handleQuickView = (entity) => {
    handleView(entity);
};

const handleQuickEditAction = (entity) => {
    if (!quickEditMode.value) {
        quickEditMode.value = true;
    }
    quickEditEntity.value = entity;
    selectedEntities.value = [entity];
    multiEditMode.value = false; // Désactiver le mode multiple si on passe en édition simple
};

const handleRefresh = (entity) => {
    // Recharger les données d'une entité spécifique (depuis le menu d'actions)
    router.reload({
        only: ['items'],
        preserveState: true,
        preserveScroll: true
    });
};

const handleRefreshAll = () => {
    // Recharger toutes les données depuis le backend
    // Utiliser router.reload() avec preserveState: false pour forcer un rechargement complet
    notificationStore.info('Rafraîchissement des données en cours...', { duration: 2000 });
    router.reload({
        only: ['items'],
        preserveState: false, // Ne pas préserver l'état pour forcer le rechargement complet depuis le serveur
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success('Données rafraîchies avec succès', { duration: 3000 });
        },
        onError: () => {
            notificationStore.error('Erreur lors du rafraîchissement des données', { duration: 5000 });
        }
    });
};

const handleDownloadPdf = async (entity) => {
    // Le téléchargement est géré directement par EntityActionsMenu via useDownloadPdf
    // Cette méthode peut être utilisée pour des actions supplémentaires si nécessaire
    console.log('Téléchargement PDF pour:', entity);
};
</script>

<template>
    <Head title="Liste des Objets" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Objets</h1>
                <p class="text-primary-200 mt-2">Gérez les objets et équipements</p>
            </div>
            <div class="flex gap-2 items-center">
                <!-- Toggle édition rapide -->
                <div class="flex items-center gap-2">
                    <ToggleField
                        v-model="quickEditMode"
                        label="Édition rapide"
                    />
                    <ToggleField
                        v-if="quickEditMode"
                        v-model="multiEditMode"
                        label="Sélection multiple"
                    />
                </div>
                <Btn v-if="canCreate" @click="handleCreate" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un objet
                </Btn>
            </div>
        </div>

        <!-- Vue en 2 colonnes si édition rapide activée -->
        <div v-if="quickEditMode" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Colonne gauche : Tableau -->
            <div>
                <EntityTable
                    :entities="items"
                    :columns="columns"
                    entity-type="items"
                    :pagination="props.items"
                    :show-filters="true"
                    :search="search"
                    :filters="filters"
                    :filterable-columns="filterableColumns"
                    :show-selection="true"
                    :selected-entities="multiEditMode ? selectedEntities : (quickEditEntity ? [quickEditEntity] : [])"
                    :show-actions-menu="true"
                    :is-admin="isAdmin"
                    @view="handleView"
                    @edit="handleEdit"
                    @delete="handleDelete"
                    @sort="handleSort"
                    @page-change="handlePageChange"
                    @update:search="handleSearchUpdate"
                    @update:filters="handleFiltersUpdate"
                    @select="handleSelect"
                    @deselect="handleDeselect"
                    @quick-view="handleQuickView"
                    @quick-edit="handleQuickEditAction"
                    @refresh="handleRefresh"
                    @refresh-all="handleRefreshAll"
                    @download-pdf="handleDownloadPdf"
                />
            </div>

            <!-- Colonne droite : Formulaire d'édition -->
            <div class="lg:sticky lg:top-4 lg:h-fit">
                <!-- Édition multiple -->
                <div v-if="multiEditMode && selectedEntities.length > 0" class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">
                            Édition multiple
                            <span class="badge badge-primary">{{ selectedEntities.length }}</span>
                        </h2>
                        <div v-if="comparison.hasDifferences" class="alert alert-warning mb-4">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <div>
                                <p class="text-sm">
                                    Certains champs ont des valeurs différentes entre les entités sélectionnées.
                                    Les champs vides seront ignorés lors de la sauvegarde.
                                </p>
                            </div>
                        </div>
                        <EntityEditForm
                            :entity="comparison.commonValues"
                            entity-type="item"
                            :view-mode="quickEditViewMode"
                            :fields-config="fieldsConfig"
                            :is-updating="true"
                            @submit="handleMultiEditSubmit"
                            @cancel="handleQuickEditCancel"
                            @update:view-mode="quickEditViewMode = $event"
                        />
                    </div>
                </div>
                <!-- Édition simple -->
                <div v-else-if="!multiEditMode && quickEditEntity" class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Édition rapide</h2>
                        <EntityEditForm
                            :entity="quickEditEntity"
                            entity-type="item"
                            :view-mode="quickEditViewMode"
                            :fields-config="fieldsConfig"
                            :is-updating="true"
                            @submit="handleQuickEditSubmit"
                            @cancel="handleQuickEditCancel"
                            @update:view-mode="quickEditViewMode = $event"
                        />
                    </div>
                </div>
                <div v-else class="card bg-base-200 shadow-xl">
                    <div class="card-body text-center">
                        <p class="text-base-content/70">
                            <span v-if="multiEditMode">
                                Sélectionnez un ou plusieurs objets dans le tableau pour les éditer
                            </span>
                            <span v-else>
                                Sélectionnez un objet dans le tableau pour l'éditer rapidement
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue normale (tableau seul) si édition rapide désactivée -->
        <EntityTable
            v-else
            :entities="items"
            :columns="columns"
            entity-type="items"
            :pagination="props.items"
            :show-filters="true"
            :search="search"
            :filters="filters"
            :filterable-columns="filterableColumns"
            :show-actions-menu="true"
            :is-admin="isAdmin"
            @view="handleView"
            @edit="handleEdit"
            @delete="handleDelete"
            @sort="handleSort"
            @page-change="handlePageChange"
            @update:search="handleSearchUpdate"
            @update:filters="handleFiltersUpdate"
            @quick-view="handleQuickView"
            @quick-edit="handleQuickEditAction"
            @refresh="handleRefresh"
            @refresh-all="handleRefreshAll"
            @download-pdf="handleDownloadPdf"
        />

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="item"
            :fields-config="fieldsConfig"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="item"
            :view="modalView"
            :open="modalOpen"
            :use-stored-format="true"
            @close="closeModal"
        />
    </Container>
</template>

