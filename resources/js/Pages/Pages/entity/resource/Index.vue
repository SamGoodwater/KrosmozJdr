<script setup>
/**
 * Resource Index Page
 * 
 * @description
 * Page de liste des ressources avec tableau et modal
 * 
 * @props {Object} resources - Collection paginée des ressources
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { Resource } from "@/Models/Entity/Resource";
import { useHybridEntityTable } from "@/Composables/entity/useHybridEntityTable";
import { applyPatchToDataset } from "@/Composables/entity/applyPatchToDataset";
import { usePermissions } from "@/Composables/permissions/usePermissions";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Alert from "@/Pages/Atoms/feedback/Alert.vue";
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import ResourceBulkEditPanel from './components/ResourceBulkEditPanel.vue';

const props = defineProps({
    resources: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    resourceTypes: {
        type: Array,
        default: () => []
    },
    can: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

// Notifications
const notificationStore = useNotificationStore();
setPageTitle('Liste des Ressources');

// Permissions
const { canUpdateAny, canCreate: canCreatePermission, canManageAny } = usePermissions();
const canCreate = computed(() => Boolean(props.can?.create ?? canCreatePermission('resources')));
const canModify = computed(() => Boolean(props.can?.updateAny ?? canUpdateAny('resources')));
const canManage = computed(() => Boolean(props.can?.manageAny ?? canManageAny('resources')));

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const selectedEntities = ref([]);
const filteredIds = ref([]);
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Garder trace du tri serveur courant (utile pour charger un dataset client cohérent)
const serverSort = ref('');
const serverOrder = ref('desc');

try {
    const qs = new URLSearchParams(window.location.search);
    serverSort.value = qs.get('sort') || '';
    serverOrder.value = qs.get('order') || 'desc';
} catch (e) {
    // SSR / tests -> ignore
}

const { tableMode, allRows: allResources, loadingAll, baseServerQuery, loadClientMode, reloadClientDataset, switchToServerMode } =
    useHybridEntityTable({
        entityKey: "resources",
        search,
        filters,
        serverSort,
        serverOrder,
        notifySuccess: (msg) => notificationStore.addNotification({ type: "success", message: msg }),
        notifyError: (msg) => notificationStore.addNotification({ type: "error", message: msg }),
        limit: 5000,
    });

// Normaliser en modèles (cohérence avec Item/Consumable)
const resources = computed(() => {
    const rows = tableMode.value === 'client' ? allResources.value : (props.resources.data || []);
    return Resource.fromArray(rows);
});

const rarityLabel = (value) => {
    const v = Number(value ?? 0);
    return [
        'Commun',
        'Peu commun',
        'Rare',
        'Très rare',
        'Légendaire',
        'Unique',
    ][v] ?? String(v);
};

const yesNoLabel = (v) => (v ? 'Oui' : 'Non');

// Configuration des colonnes selon la documentation : ID (optionnel), Nom (lien), Niveau, Type, Rareté (badge), dofusdb_id, Créé par, Actions
const columns = computed(() => [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'image', label: 'Image', sortable: false, type: 'image' },
    { key: 'name', label: 'Nom', sortable: true, isMain: true },
    { key: 'level', label: 'Niveau', sortable: true },
    { key: 'resourceType', label: 'Type', sortable: false, format: (value) => value?.name || '-' },
    { key: 'rarity', label: 'Rareté', sortable: true, type: 'badge', badgeColor: 'primary', format: rarityLabel },
    { key: 'price', label: 'Prix', sortable: true, format: (v) => v ?? '-' },
    { key: 'weight', label: 'Poids', sortable: true, format: (v) => v ?? '-' },
    { key: 'usable', label: 'Utilisable', sortable: true, type: 'badge', badgeColor: 'secondary', format: yesNoLabel },
    { key: 'auto_update', label: 'Auto-update', sortable: true, type: 'badge', badgeColor: 'accent', format: yesNoLabel },
    { key: 'dofusdb_id', label: 'DofusDB ID', sortable: true },
    { key: 'createdBy', label: 'Créé par', sortable: false, format: (value) => value?.name || value?.email || '-' },
    { key: 'actions', label: 'Actions', sortable: false }
]);

// Handlers
const openModal = (entity) => {
    selectedEntity.value = entity;
    modalView.value = 'large';
    modalOpen.value = true;
};

const handleViewPage = (entity) => {
    router.visit(route('entities.resources.show', entity.id));
};

const handleEdit = (entity) => {
    router.visit(route(`entities.resources.edit`, { resource: entity.id }));
};

const clearSelection = () => {
    selectedEntities.value = [];
};

const handleDelete = (entity) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${entity.name}" ?`)) {
        router.delete(route(`entities.resources.delete`, { resource: entity.id }));
    }
};

const handleSort = ({ column, order }) => {
    if (tableMode.value === 'client') return;
    serverSort.value = column;
    serverOrder.value = order;
    router.get(route('entities.resources.index'), {
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
    if (tableMode.value === 'client') return;
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(route('entities.resources.index'), {
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
    if (tableMode.value === 'client') return;
    router.get(route('entities.resources.index'), {
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
    if (tableMode.value === 'client') return;
    router.get(route('entities.resources.index'), {}, {
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
        key: 'resource_type_id',
        label: 'Type',
        options: [
            { value: '', label: 'Tous' },
            ...props.resourceTypes.map(t => ({ value: String(t.id), label: t.name }))
        ]
    },
    {
        key: 'rarity',
        label: 'Rareté',
        options: [
            { value: '', label: 'Toutes' },
            { value: '0', label: 'Commun' },
            { value: '1', label: 'Peu commun' },
            { value: '2', label: 'Rare' },
            { value: '3', label: 'Très rare' },
            { value: '4', label: 'Légendaire' },
            { value: '5', label: 'Unique' },
        ]
    },
    {
        key: 'usable',
        label: 'Utilisable',
        options: [
            { value: '', label: 'Tous' },
            { value: '1', label: 'Oui' },
            { value: '0', label: 'Non' },
        ]
    },
    {
        key: 'auto_update',
        label: 'Auto-update',
        options: [
            { value: '', label: 'Tous' },
            { value: '1', label: 'Oui' },
            { value: '0', label: 'Non' },
        ]
    }
]);

// Configuration des champs pour la création/édition via modal
const fieldsConfig = computed(() => ({
    name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
    description: { type: 'textarea', label: 'Description', required: false, showInCompact: false },
    level: { type: 'text', label: 'Niveau', required: false, showInCompact: true },
    rarity: {
        type: 'select',
        label: 'Rareté',
        required: false,
        showInCompact: true,
        options: [
            { value: 0, label: 'Commun' },
            { value: 1, label: 'Peu commun' },
            { value: 2, label: 'Rare' },
            { value: 3, label: 'Très rare' },
            { value: 4, label: 'Légendaire' },
            { value: 5, label: 'Unique' },
        ]
    },
    resource_type_id: {
        type: 'select',
        label: 'Type de ressource',
        required: false,
        showInCompact: true,
        options: [
            { value: '', label: '—' },
            ...props.resourceTypes.map(t => ({ value: t.id, label: t.name }))
        ]
    },
    usable: { type: 'checkbox', label: 'Utilisable', required: false, showInCompact: true },
    auto_update: { type: 'checkbox', label: 'Auto-update', required: false, showInCompact: true },
    price: { type: 'text', label: 'Prix', required: false, showInCompact: true },
    weight: { type: 'text', label: 'Poids', required: false, showInCompact: true },
    image: { type: 'text', label: 'Image (URL)', required: false, showInCompact: false },
}));

const handlePageChange = (url) => {
    if (url) {
        if (tableMode.value === 'client') return;
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

const handleRefreshAll = () => {
    router.reload({ preserveState: true, preserveScroll: true });
};

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

const handleBulkApplied = async (payload) => {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        notificationStore.addNotification({ type: 'error', message: 'Token CSRF introuvable. Recharge la page.' });
        return;
    }

    try {
        const response = await fetch('/api/entities/resources/bulk', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            notificationStore.addNotification({ type: 'error', message: data.message || 'Bulk update: erreur' });
            return;
        }

        notificationStore.addNotification({
            type: 'success',
            message: `Mis à jour: ${data.summary.updated}/${data.summary.requested}`,
        });

        // Mode client: mise à jour locale
        if (tableMode.value === 'client') {
            allResources.value = applyPatchToDataset(allResources.value, payload, {
                normalize: {
                    usable: (v) => (v ? 1 : 0),
                    auto_update: (v) => (v ? 1 : 0),
                },
                afterPatch: (next, { patch }) => {
                    // Met à jour la relation resourceType si le type change (utile pour l'affichage)
                    if (Object.prototype.hasOwnProperty.call(patch, "resource_type_id")) {
                        const typeId = patch.resource_type_id;
                        if (typeId === null) {
                            return { ...next, resourceType: null };
                        }
                        const rt = (props.resourceTypes || []).find((t) => Number(t.id) === Number(typeId));
                        if (rt) return { ...next, resourceType: rt };
                    }
                    return next;
                },
            });
        } else {
            router.reload({ preserveState: true, preserveScroll: true });
        }

        clearSelection();
    } catch (e) {
        notificationStore.addNotification({ type: 'error', message: 'Erreur bulk: ' + (e?.message || 'unknown') });
    }
};

const handleLoadAllForClientMode = async () => loadClientMode();
const handleReloadClientDataset = async () => reloadClientDataset();
const handleSwitchToServerMode = () => switchToServerMode();
</script>

<template>
    <Head title="Liste des Ressources" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Ressources</h1>
                <p class="text-primary-200 mt-2">Gérez les ressources (matériaux, composants, etc.)</p>
            </div>
            <div class="flex gap-2">
                <Btn
                    v-if="tableMode === 'server'"
                    variant="ghost"
                    :loading="loadingAll"
                    @click="handleLoadAllForClientMode"
                    :title="'Charge un lot (limité) et active tri/filtre/pagination côté client'"
                >
                    <i class="fa-solid fa-bolt mr-2"></i>
                    Mode client (charger tout)
                </Btn>
                <Btn
                    v-else
                    variant="ghost"
                    :loading="loadingAll"
                    @click="handleReloadClientDataset"
                    :disabled="!baseServerQuery"
                    :title="'Recharge le dataset (même sous-ensemble serveur) et conserve tes filtres client'"
                >
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger dataset
                </Btn>
                <Btn
                    v-if="tableMode === 'client'"
                    variant="ghost"
                    @click="handleSwitchToServerMode"
                    :title="'Revient au mode serveur (pagination/filtrage backend)'"
                >
                    <i class="fa-solid fa-server mr-2"></i>
                    Mode serveur
                </Btn>
                <Btn variant="ghost" @click="router.visit(route('entities.resource-types.index'))">
                    <i class="fa-solid fa-tags mr-2"></i>
                    Types de ressources
                </Btn>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une ressource
            </Btn>
        </div>
        </div>

        <!-- Baseline serveur (quand le mode client est activé) -->
        <Alert
            v-if="tableMode === 'client' && baseServerQuery"
            color="info"
            variant="soft"
        >
            <template #content>
                <div class="space-y-1">
                    <div class="font-semibold">Sous-ensemble chargé depuis le serveur</div>
                    <div class="text-sm opacity-80">
                        <span v-if="baseServerQuery.search">Recherche: "<b>{{ baseServerQuery.search }}</b>"</span>
                        <span v-else>Recherche: —</span>
                        <span class="mx-2">•</span>
                        <span>Filtres: {{ Object.keys(baseServerQuery.filters || {}).length || 0 }}</span>
                        <span class="mx-2">•</span>
                        <span>Tri: {{ baseServerQuery.sort ? `${baseServerQuery.sort} (${baseServerQuery.order || 'desc'})` : '—' }}</span>
                    </div>
                    <div class="text-sm opacity-80">
                        Tu peux maintenant appliquer des filtres/tri supplémentaires côté client (sans requête serveur).
                    </div>
                </div>
            </template>
        </Alert>

        <div
            class="grid grid-cols-1 gap-4"
            :class="{ 'xl:grid-cols-[1fr_380px]': selectedEntities.length >= 1 }"
        >
            <div>
                <EntityTable
                    v-model:selected-entities="selectedEntities"
                    :entities="resources || []"
                    :columns="columns"
                    entity-type="resources"
                    :pagination="props.resources"
                    :show-filters="true"
                    :show-selection="canModify"
                    :can-manage="canManage"
                    :search="search"
                    :filters="filters"
                    :filterable-columns="filterableColumns"
                    :mode="tableMode"
                    @view="handleViewPage"
                    @edit="handleEdit"
                    @quick-edit="openModal"
                    @delete="handleDelete"
                    @sort="handleSort"
                    @page-change="handlePageChange"
                    @update:search="handleSearchUpdate"
                    @update:filters="handleFiltersUpdate"
                    @refresh-all="handleRefreshAll"
                    @filtered-ids="(ids) => { filteredIds.value = ids }"
                />
            </div>

            <div v-if="canModify && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <ResourceBulkEditPanel
                    :selected-entities="selectedEntities"
                    :is-admin="canModify"
                    :resource-types="props.resourceTypes || []"
                    :filtered-ids="filteredIds"
                    :mode="tableMode"
                    @applied="handleBulkApplied"
                    @clear="clearSelection"
                />
            </div>
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="resource"
            :fields-config="fieldsConfig"
            :default-entity="{ rarity: 0, usable: false, auto_update: false }"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="resource"
            :view="modalView"
            :open="modalOpen"
            @close="closeModal"
        />
    </Container>
</template>
