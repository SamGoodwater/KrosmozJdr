<script setup>
/**
 * ResourceType Index Page
 *
 * @description
 * Table de gestion des types de ressources (incluant la registry DofusDB).
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useScrapping } from "@/Composables/utils/useScrapping";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from "@/Pages/Atoms/action/Btn.vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import EntityTanStackTable from "@/Pages/Organismes/table/EntityTanStackTable.vue";
import CreateEntityModal from "@/Pages/Organismes/entity/CreateEntityModal.vue";
import EntityModal from "@/Pages/Organismes/entity/EntityModal.vue";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import EntityQuickEditPanel from "@/Pages/Organismes/entity/EntityQuickEditPanel.vue";
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";
import { ResourceType } from "@/Models/Entity/ResourceType";
import { useEntityIndexQuickEditTable } from "@/Composables/entity/useEntityIndexQuickEditTable.js";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";

const props = defineProps({
    resourceTypes: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    can: { type: Object, default: () => ({}) },
});

const { setPageTitle } = usePageTitle();
setPageTitle("Types de ressources");

// (legacy) canModify inutilisé : garder uniquement les versions "Resolved"
const { canUpdateAny, canCreate } = usePermissions();
const canModifyResolved = computed(() => Boolean(props.can?.updateAny ?? canUpdateAny('resource-types')));
const canCreateResolved = computed(() => Boolean(props.can?.create ?? canCreate('resource-types')));
// (legacy) canManage inutilisé : garder uniquement les versions "Resolved"

const { bulkPatchJson } = useBulkRequest();
const { copyToClipboard } = useCopyToClipboard();
const { refreshEntity } = useScrapping();

const selectedEntity = ref(null);
const editOpen = ref(false);
const viewEntity = ref(null);
const viewModalOpen = ref(false);
const modalView = ref("full");
const createOpen = ref(false);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);
const { tableQuickEditEnabled, onUpdateTableQuickEdit } = useEntityIndexQuickEditTable(ResourceType);

const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModifyResolved.value,
            createAny: canCreateResolved.value,
        },
    };
    const descriptors = getResourceTypeFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const serverUrl = computed(() => `${route("api.tables.resource-types")}?limit=5000&format=entities&_t=${refreshToken.value}`);

const handleRefreshAll = () => {
    refreshToken.value++;
};

const handleEntityCreated = () => {
    createOpen.value = false;
    refreshToken.value++;
};

const handleEdit = (entity) => {
    selectedEntity.value = entity;
    editOpen.value = true;
};

const openViewModal = (model) => {
    viewEntity.value = model;
    modalView.value = "full";
    viewModalOpen.value = true;
};

const closeViewModal = () => {
    viewModalOpen.value = false;
    viewEntity.value = null;
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = raw instanceof ResourceType ? raw : ResourceType.fromArray([raw])[0] || null;
    if (!model) return;
    openViewModal(model);
};

const handleBulkApplied = async (payload) => {
    // payload: { ids, decision?, state?, read_level?, write_level? }
    const ok = await bulkPatchJson({ url: "/api/scrapping/resource-types/bulk", payload });
    if (!ok) return;
    refreshToken.value++;
    selectedIds.value = [];
};

const clearSelection = () => {
    selectedIds.value = [];
};

const closeEdit = () => {
    editOpen.value = false;
    selectedEntity.value = null;
};

const { handleKeyboardIntent } = useEntityIndexTableIntents({
    ModelClass: ResourceType,
    routeShowName: "entities.resource-types.show",
    routeShowParam: "resourceType",
    canModify: () => canModifyResolved.value,
    openFullModal: openViewModal,
    openEdit: (model) => {
        handleEdit(model);
    },
});

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    return (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
});

const tableMeta = ref({});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance ResourceType, l'utiliser directement
    const model = targetEntity instanceof ResourceType ? targetEntity : ResourceType.fromArray([targetEntity])[0] || null;
    if (!model) return;
    
    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
        case 'quick-view':
            openViewModal(model);
            break;

        case 'edit':
        case 'quick-edit':
            handleEdit(model);
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('resource-type');
            const url = resolveEntityRouteUrl('resource-type', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'refresh': {
            await refreshEntity('resource-type', entityId, { forceUpdate: true });
            refreshToken.value++;
            break;
        }

        default:
            // Action non gérée (peut être étendue dans le futur)
            break;
    }
};


const handleViewModalQuickEdit = (entity) => {
    closeViewModal();
    handleEdit(entity);
};

const handleViewModalExpand = (entity) => {
    const id = entity?.id;
    if (!id) return;
    router.visit(route("entities.resource-types.show", { resourceType: id }));
    closeViewModal();
};

const resourceTypeDescriptors = computed(() => getResourceTypeFieldDescriptors({ capabilities: props.can || {} }));
const fieldsConfig = computed(() => createFieldsConfigFromDescriptors(resourceTypeDescriptors.value, { meta: {}, capabilities: props.can || {} }));
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(resourceTypeDescriptors.value));
</script>

<template>
    <Head title="Types de ressources" />

    <div class="space-y-6 pb-8 w-full">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Types de ressources</h1>
                <p class="text-primary-200 mt-2">
                    Gérer les types métiers et la registry DofusDB (utilisé / non utilisé / en attente).
                </p>
            </div>
            <div class="flex gap-2">
                <Btn variant="ghost" @click="handleRefreshAll" title="Recharger le dataset">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger
                </Btn>

                <Btn v-if="canCreateResolved" @click="createOpen = true" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un type
                </Btn>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4"
            :class="{
                'xl:grid-cols-[minmax(0,1fr)_380px]':
                    canModifyResolved && selectedEntities.length >= 1 && tableQuickEditEnabled,
            }"
        >
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="resource-types"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('resource-types')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @keyboard-intent="handleKeyboardIntent"
                    @update:quick-edit-enabled="onUpdateTableQuickEdit"
                    @action="handleTableAction"
                />
            </div>

            <div v-if="canModifyResolved && selectedEntities.length >= 1 && tableQuickEditEnabled" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="resource-types"
                    :selected-entities="selectedEntities"
                    :is-admin="canModifyResolved"
                    mode="client"
                    :filtered-ids="selectedIds"
                    @applied="handleBulkApplied"
                    @clear="clearSelection"
                />
            </div>
        </div>

        <CreateEntityModal
            :open="createOpen"
            entity-type="resourceType"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
            route-name-base="entities.resource-types"
            route-param-key="resourceType"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('resource-types')"
            @close="createOpen = false"
            @created="handleEntityCreated"
        />

        <EntityModal
            v-if="viewEntity"
            :entity="viewEntity"
            entity-type="resource-types"
            :view="modalView"
            :open="viewModalOpen"
            :table-meta="tableMeta"
            @close="closeViewModal"
            @quick-edit="handleViewModalQuickEdit"
            @expand="handleViewModalExpand"
        />

        <Modal :open="editOpen" size="xl" placement="middle-center" close-on-esc @close="closeEdit">
            <template #header>
                <h3 class="text-2xl font-bold text-primary-100">Éditer type de ressource</h3>
            </template>
            <div class="max-h-[70vh] overflow-y-auto pr-2" v-if="selectedEntity">
                <EntityEditForm
                    :entity="selectedEntity"
                    entity-type="resourceType"
                    :fields-config="fieldsConfig"
                    :is-updating="true"
                    :shortcuts-active="editOpen"
                    route-name-base="entities.resource-types"
                    route-param-key="resourceType"
                    @submit="closeEdit"
                    @cancel="closeEdit"
                />
            </div>
        </Modal>
    </div>
</template>


