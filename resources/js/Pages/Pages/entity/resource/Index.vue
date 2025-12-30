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
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import { createResourcesTanStackTableConfig } from './resources-tanstack-table-config';
import { adaptResourceEntitiesTableResponse } from "@/Entities/resource/resource-adapter";
import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

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
const { bulkPatchJson } = useBulkRequest();
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
    // Option B (migration): le backend renvoie des entités brutes, le front génère les `cells`.
    return `${base}?limit=5000&format=entities&_t=${refreshToken.value}`;
});

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
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

// Descriptors -> fieldsConfig (source de vérité unique)
const resourceDescriptors = computed(() =>
    getResourceFieldDescriptors({
        capabilities: { updateAny: canModify.value },
        resourceTypes: props.resourceTypes || [],
    })
);
const fieldsConfig = computed(() =>
    createFieldsConfigFromDescriptors(resourceDescriptors.value, {
        capabilities: { updateAny: canModify.value },
        resourceTypes: props.resourceTypes || [],
    })
);
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(resourceDescriptors.value));

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

const handleBulkApplied = async (payload) => {
    const ok = await bulkPatchJson({ url: "/api/entities/resources/bulk", payload });
    if (!ok) return;
    refreshToken.value++;
    clearSelection();
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
            :class="{ 'xl:grid-cols-[minmax(0,1fr)_380px]': selectedEntities.length >= 1 }"
        >
            <div class="min-w-0">
                <EntityTanStackTable
                    entity-type="resources"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="adaptResourceEntitiesTableResponse"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                />
            </div>

            <div v-if="canModify && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="resources"
                    :selected-entities="selectedEntities"
                    :is-admin="canModify"
                    :extra-ctx="{ resourceTypes: props.resourceTypes || [] }"
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
            :default-entity="defaultEntity"
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
