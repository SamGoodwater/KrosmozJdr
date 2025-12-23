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
import axios from "axios";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useEntityPermissions } from "@/Composables/permissions/useEntityPermissions";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { usePage } from "@inertiajs/vue3";
import { User } from "@/Models";
import { Resource } from "@/Models/Entity/Resource";

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
    }
});

const { setPageTitle } = usePageTitle();

// Notifications
const notificationStore = useNotificationStore();
setPageTitle('Liste des Ressources');

// Permissions
const { canCreateEntity } = useEntityPermissions();
const canCreate = computed(() => canCreateEntity('resource'));
const page = usePage();
const currentUser = computed(() => (page.props.auth?.user ? new User(page.props.auth.user) : null));
const isAdmin = computed(() => currentUser.value?.isAdmin ?? false);

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const selectedEntities = ref([]);
const filteredIds = ref([]);
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});
const tableMode = ref('server'); // server | client
const allResources = ref([]);
const loadingAll = ref(false);
const baseServerQuery = ref(null); // snapshot { search, filters, sort, order }

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
            const ids = new Set((payload.ids || []).map((v) => String(v)));
            allResources.value = (allResources.value || []).map((r) => {
                if (!ids.has(String(r.id))) return r;
                const next = { ...r };
                if (typeof payload.resource_type_id !== 'undefined') {
                    next.resource_type_id = payload.resource_type_id;
                    const rt = (props.resourceTypes || []).find((t) => Number(t.id) === Number(payload.resource_type_id));
                    if (rt) next.resourceType = rt;
                    if (payload.resource_type_id === null) next.resourceType = null;
                }
                if (typeof payload.usable !== 'undefined') next.usable = payload.usable ? 1 : 0;
                if (typeof payload.auto_update !== 'undefined') next.auto_update = payload.auto_update ? 1 : 0;
                if (typeof payload.is_visible !== 'undefined') next.is_visible = payload.is_visible;
                if (typeof payload.rarity !== 'undefined') next.rarity = payload.rarity;
                if (typeof payload.level !== 'undefined') next.level = payload.level;
                if (typeof payload.price !== 'undefined') next.price = payload.price;
                if (typeof payload.weight !== 'undefined') next.weight = payload.weight;
                if (typeof payload.description !== 'undefined') next.description = payload.description;
                if (typeof payload.image !== 'undefined') next.image = payload.image;
                if (typeof payload.dofus_version !== 'undefined') next.dofus_version = payload.dofus_version;
                return next;
            });
        } else {
            router.reload({ preserveState: true, preserveScroll: true });
        }

        clearSelection();
    } catch (e) {
        notificationStore.addNotification({ type: 'error', message: 'Erreur bulk: ' + (e?.message || 'unknown') });
    }
};

const handleLoadAllForClientMode = async () => {
    if (loadingAll.value) return;
    loadingAll.value = true;
    try {
        // Snapshot du sous-ensemble "serveur" (baseline)
        baseServerQuery.value = {
            search: search.value,
            filters: { ...(filters.value || {}) },
            sort: serverSort.value || null,
            order: serverOrder.value || null,
        };

        // On charge un lot conséquent pour permettre le filtrage/tri côté navigateur.
        // NB: limite backend (par défaut 5000, max 20000)
        const params = {
            limit: 5000,
            search: baseServerQuery.value.search || '',
            ...baseServerQuery.value.filters,
        };
        if (baseServerQuery.value.sort) params.sort = baseServerQuery.value.sort;
        if (baseServerQuery.value.order) params.order = baseServerQuery.value.order;

        const response = await axios.get('/api/entity-table/resources', { params });
        // Réponse: { data: ResourceResource::collection(...) }
        allResources.value = response.data?.data?.data ?? [];
        tableMode.value = 'client';

        // Réinitialiser les filtres UI : ils deviennent une couche "client" additionnelle
        search.value = '';
        filters.value = {};

        notificationStore.addNotification({
            type: 'success',
            message: `Mode client activé (${allResources.value.length} ressources chargées).`
        });
    } catch (e) {
        console.error(e);
        notificationStore.addNotification({
            type: 'error',
            message: 'Impossible de charger le dataset pour le mode client (API).'
        });
    } finally {
        loadingAll.value = false;
    }
};

const handleReloadClientDataset = async () => {
    if (loadingAll.value) return;
    if (tableMode.value !== 'client' || !baseServerQuery.value) return;

    const clientSearch = search.value;
    const clientFilters = { ...(filters.value || {}) };

    loadingAll.value = true;
    try {
        const params = {
            limit: 5000,
            search: baseServerQuery.value.search || '',
            ...(baseServerQuery.value.filters || {}),
        };
        if (baseServerQuery.value.sort) params.sort = baseServerQuery.value.sort;
        if (baseServerQuery.value.order) params.order = baseServerQuery.value.order;

        const response = await axios.get('/api/entity-table/resources', { params });
        allResources.value = response.data?.data?.data ?? [];

        // Conserver les filtres client en place
        search.value = clientSearch;
        filters.value = clientFilters;

        notificationStore.addNotification({
            type: 'success',
            message: `Dataset rechargé (${allResources.value.length} ressources).`
        });
    } catch (e) {
        console.error(e);
        notificationStore.addNotification({
            type: 'error',
            message: 'Impossible de recharger le dataset client.'
        });
    } finally {
        loadingAll.value = false;
    }
};

const handleSwitchToServerMode = () => {
    tableMode.value = 'server';
    baseServerQuery.value = null;
};
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
                    :show-selection="true"
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

            <div v-if="selectedEntities.length >= 1" class="sticky top-4 self-start">
                <ResourceBulkEditPanel
                    :selected-entities="selectedEntities"
                    :is-admin="isAdmin"
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
