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
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { Resource } from "@/Models/Entity/Resource";
import { usePermissions } from "@/Composables/permissions/usePermissions";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import ResourceBulkEditPanel from './components/ResourceBulkEditPanel.vue';
import { createResourcesTanStackTableConfig } from './resources-tanstack-table-config';

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
const permissionsApi = usePermissions();
const { canUpdateAny, canCreate: canCreatePermission } = permissionsApi;
const canCreate = computed(() => Boolean(props.can?.create ?? canCreatePermission('resources')));
const canModify = computed(() => Boolean(props.can?.updateAny ?? canUpdateAny('resources')));
// canManageAny gardé pour plus tard (actions de maintenance)
// const canManage = computed(() => Boolean(props.can?.manageAny ?? canManageAny('resources')));

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const tableConfig = computed(() => createResourcesTanStackTableConfig());

const serverUrl = computed(() => {
    const base = route('api.tables.resources');
    return `${base}?limit=5000&_t=${refreshToken.value}`;
});

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    const idSet = new Set(selectedIds.value);
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(r?.id))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Resource.fromArray(raw);
});

const filteredIds = computed(() => selectedIds.value || []);

// Handlers
const openModal = (entity) => {
    selectedEntity.value = entity;
    modalView.value = 'large';
    modalOpen.value = true;
};

const clearSelection = () => {
    selectedIds.value = [];
};

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Resource.fromArray([raw])[0] || null;
    if (!model) return;
    openModal(model);
};

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
    refreshToken.value++;
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

        // Table v2: recharger le dataset serveur (client-first sur le dataset)
        refreshToken.value++;
        clearSelection();
    } catch (e) {
        notificationStore.addNotification({ type: 'error', message: 'Erreur bulk: ' + (e?.message || 'unknown') });
    }
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
                <Btn variant="ghost" @click="handleRefreshAll" title="Recharger le dataset">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger
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

        <div
            class="grid grid-cols-1 gap-4"
            :class="{ 'xl:grid-cols-[1fr_380px]': selectedEntities.length >= 1 }"
        >
            <div>
                <EntityTanStackTable
                    entity-type="resources"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <div v-if="canModify && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <ResourceBulkEditPanel
                    :selected-entities="selectedEntities"
                    :is-admin="canModify"
                    :resource-types="props.resourceTypes || []"
                    :filtered-ids="filteredIds"
                    mode="client"
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
