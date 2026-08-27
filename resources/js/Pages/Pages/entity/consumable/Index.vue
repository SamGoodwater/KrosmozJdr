<script setup>
/**
 * Consumable Index Page
 * 
 * @description
 * Page de liste des consommables avec tableau et modal
 * 
 * @props {Object} consumables - Collection paginée des consommables
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Consumable } from "@/Models/Entity/Consumable";
import { useEntityIndexQuickEditTable } from "@/Composables/entity/useEntityIndexQuickEditTable.js";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";
import { normalizeIndexTableFilters } from "@/Composables/entity/useEntityIndexTableFilters";
import {
    hasConsumableTypeFilter,
    resolveGameplayConsumableTypeIds,
} from "@/Utils/Entity/gameplayConsumableTypes";

const props = defineProps({
    consumables: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    consumableTypes: {
        type: Array,
        default: () => []
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Consommables');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('consumables'));
const canModify = computed(() => canUpdateAny('consumables'));

// Bulk request
const { bulkPatchJson } = useBulkRequest();
const { copyToClipboard } = useCopyToClipboard();

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('full');
const createModalOpen = ref(false);

// Table v2
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);
const { tableQuickEditEnabled, onUpdateTableQuickEdit } = useEntityIndexQuickEditTable(Consumable);

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Consumable.fromArray(raw);
});

// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
        consumableTypes: props.consumableTypes || [],
    };
    const descriptors = getConsumableFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const indexTableFilters = computed(() => {
    const fromQuery = normalizeIndexTableFilters(props.filters);
    if (hasConsumableTypeFilter(fromQuery)) {
        return fromQuery;
    }
    const typeIds = resolveGameplayConsumableTypeIds(props.consumableTypes || []);
    if (typeIds.length === 0) {
        return fromQuery;
    }
    return { ...fromQuery, consumable_type_id: typeIds };
});
const serverBaseUrl = computed(() => route('api.tables.consumables'));



const clearSelection = () => {
    selectedIds.value = [];
};

const handleBulkApplied = () => {
    refreshToken.value++;
    selectedIds.value = [];
};

const tableMeta = ref({});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Consumable.fromArray([raw])[0] || null;
    if (!model) return;
    selectedEntity.value = model;
    modalView.value = "full";
    modalOpen.value = true;
};

const handleCreate = () => {
    createModalOpen.value = true;
};

const handleCloseCreateModal = () => {
    createModalOpen.value = false;
};

const handleEntityCreated = () => {
    createModalOpen.value = false;
    refreshToken.value++;
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};


const { handleKeyboardIntent } = useEntityIndexTableIntents({
    ModelClass: Consumable,
    routeShowName: "entities.consumables.show",
    routeShowParam: "consumable",
    canModify: () => canModify.value,
    openFullModal: (model) => {
        selectedEntity.value = model;
        modalView.value = "full";
        modalOpen.value = true;
    },
    openEdit: (model) => {
        if (!model?.id) return;
        router.visit(route('entities.consumables.edit', { consumable: model.id }));
    },
});

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    const model = Consumable.fromArray([targetEntity])[0] || null;
    if (!model) return;
    
    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
        case 'quick-view':
            selectedEntity.value = model;
            modalView.value = 'full';
            modalOpen.value = true;
            break;

        case 'edit':
        case 'quick-edit':
            router.visit(route('entities.consumables.edit', { consumable: entityId }));
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('consumable');
            const url = resolveEntityRouteUrl('consumable', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            // TODO: Implémenter le téléchargement PDF
            break;
        case 'delete':
            // TODO: Implémenter la suppression avec confirmation
            break;
    }
};

// Handlers pour les actions du modal
const handleModalQuickEdit = (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    closeModal();
    router.visit(route('entities.consumables.edit', { consumable: entityId }));
};

const handleModalExpand = (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    router.visit(route('entities.consumables.show', { consumable: entityId }));
    closeModal();
};

const handleModalCopyLink = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    const cfg = getEntityRouteConfig('consumable');
    const url = resolveEntityRouteUrl('consumable', 'show', entityId, cfg);
    if (url) {
        await copyToClipboard(url, "Lien de l'entité copié !");
    }
};

const handleModalDownloadPdf = (entity) => {
    // TODO: Implémenter le téléchargement PDF
};

const handleModalRefresh = () => {
    refreshToken.value++;
    closeModal();
};

const handleModalDelete = (entity) => {
    // TODO: Implémenter la suppression avec confirmation
};

</script>

<template>
    <Head title="Liste des Consommables" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Consommables</h1>
                <p class="text-primary-200 mt-2">Gérez les consommables (potions, parchemins, etc.)</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un consommable
            </Btn>
        </div>

        <!-- Grid layout pour permettre le scroll horizontal du tableau quand le quick edit est ouvert -->
        <div
            class="grid grid-cols-1 gap-4 xl:gap-6"
            :class="{
                'xl:grid xl:grid-cols-[minmax(0,1fr)_380px]':
                    canModify && selectedEntities.length >= 1 && tableQuickEditEnabled,
            }"
        >
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="consumables"
                    :config="tableConfig"
                    server-side
                    :server-base-url="serverBaseUrl"
                    :refresh-token="refreshToken"
                    :initial-filter-values="indexTableFilters"
                    :response-adapter="getEntityResponseAdapter('consumables')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @keyboard-intent="handleKeyboardIntent"
                    @update:quick-edit-enabled="onUpdateTableQuickEdit"
                    @action="handleTableAction"
                />
            </div>

            <!-- Quick Edit Panel -->
            <div v-if="canModify && selectedEntities.length >= 1 && tableQuickEditEnabled" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="consumables"
                    :selected-entities="selectedEntities"
                    :is-admin="canModify"
                    mode="client"
                    :filtered-ids="selectedIds"
                    @applied="handleBulkApplied"
                    @clear="clearSelection"
                />
            </div>
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="consumable"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('consumables')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="consumable"
            :view="modalView"
            :open="modalOpen"
            :table-meta="tableMeta"
            @close="closeModal"
            @quick-edit="handleModalQuickEdit"
            @expand="handleModalExpand"
            @copy-link="handleModalCopyLink"
            @download-pdf="handleModalDownloadPdf"
            @refresh="handleModalRefresh"
            @delete="handleModalDelete"
        />
    </div>
</template>
