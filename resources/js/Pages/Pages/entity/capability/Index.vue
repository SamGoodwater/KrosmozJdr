<script setup>
/**
 * Capability Index Page
 *
 * @description
 * Liste des capacités ; édition complète en modal ({@link CapabilityEditModal}, charge utile `edit-payload`), comme les sorts.
 *
 * @props {Object} capabilities - Collection paginée des capacités
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Capability } from "@/Models/Entity/Capability";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { useScrapping } from "@/Composables/utils/useScrapping";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import CapabilityEditModal from '@/Pages/Organismes/entity/CapabilityEditModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import {
    buildCapabilityFormFieldsConfig,
    CAPABILITY_FORM_FIELD_SECTIONS_CREATE,
    getCapabilityCreateDefaultEntity,
} from "@/Entities/capability/capability-form-config";
import { useEntityIndexQuickEditTable } from "@/Composables/entity/useEntityIndexQuickEditTable.js";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";

defineProps({
    capabilities: {
        type: Object,
        required: true
    },
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Capacités');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('capabilities'));
const canModify = computed(() => canUpdateAny('capabilities'));

// Bulk request
const { bulkPatchJson } = useBulkRequest();
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf("capability");
const { refreshEntity } = useScrapping();

// Table v2
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);
const { tableQuickEditEnabled, onUpdateTableQuickEdit } = useEntityIndexQuickEditTable(Capability);

watch(
    () => canModify.value,
    (allowed) => {
        if (allowed) return;
        selectedIds.value = [];
    },
    { immediate: true },
);

// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
    };
    const descriptors = getCapabilityFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const serverUrl = computed(() => `${route('api.tables.capabilities')}?format=entities&limit=5000&_t=${refreshToken.value}`);

const capabilityCreateFieldsConfig = computed(() =>
    buildCapabilityFormFieldsConfig({ includeReadonlyMeta: false }),
);
const capabilityCreateDefaultEntity = getCapabilityCreateDefaultEntity();

// Calcul des entités sélectionnées depuis les IDs et les rows
const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Capability.fromArray(raw);
});

// Bulk edit
const handleBulkUpdate = async (payload) => {
  const ok = await bulkPatchJson('/api/entities/capabilities/bulk', payload);
  if (!ok) return;
  refreshToken.value++;
  selectedIds.value = [];
};

const clearSelection = () => {
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
    // Si c'est déjà une instance Capability, l'utiliser directement
    const model = raw instanceof Capability ? raw : Capability.fromArray([raw])[0] || null;
    if (!model) return;
    selectedEntity.value = model;
    modalView.value = "full";
    modalOpen.value = true;
};

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('full');
const createModalOpen = ref(false);

/** Éditeur complet en modal (même corps que la page Edit). */
const capabilityEditModalOpen = ref(false);
const capabilityEditId = ref(null);

const openCapabilityEditModal = (entityId) => {
    if (!entityId) return;
    capabilityEditId.value = entityId;
    capabilityEditModalOpen.value = true;
};

const closeCapabilityEditModal = () => {
    capabilityEditModalOpen.value = false;
    capabilityEditId.value = null;
};

const onCapabilityEditModalSaved = () => {
    closeCapabilityEditModal();
    refreshToken.value++;
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
    ModelClass: Capability,
    routeShowName: "entities.capabilities.show",
    routeShowParam: "capability",
    canModify: () => canModify.value,
    openFullModal: (model) => {
        selectedEntity.value = model;
        modalView.value = "full";
        modalOpen.value = true;
    },
    openEdit: (model) => {
        openCapabilityEditModal(model?.id);
    },
});

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance Capability, l'utiliser directement
    const model = targetEntity instanceof Capability ? targetEntity : Capability.fromArray([targetEntity])[0] || null;
    if (!model) return;
    
    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.capabilities.show', { capability: entityId }));
            break;

        case 'quick-view':
            selectedEntity.value = model;
            modalView.value = 'full';
            modalOpen.value = true;
            break;

        case 'edit':
            openCapabilityEditModal(entityId);
            break;

        case 'quick-edit':
            openCapabilityEditModal(entityId);
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('capability');
            const url = resolveEntityRouteUrl('capability', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            await downloadPdf(entityId);
            break;

        case 'refresh':
            await refreshEntity('capability', entityId, { forceUpdate: true });
            refreshToken.value++;
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
    openCapabilityEditModal(entityId);
};

const handleModalExpand = (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    router.visit(route('entities.capabilities.show', { capability: entityId }));
    closeModal();
};

const handleModalCopyLink = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    const cfg = getEntityRouteConfig('capability');
    const url = resolveEntityRouteUrl('capability', 'show', entityId, cfg);
    if (url) {
        await copyToClipboard(url, "Lien de l'entité copié !");
    }
};

const handleModalDownloadPdf = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    await downloadPdf(entityId);
};

const handleModalRefresh = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    await refreshEntity('capability', entityId, { forceUpdate: true });
    refreshToken.value++;
    closeModal();
};

const handleModalDelete = (_entity) => {
    // TODO: Implémenter la suppression avec confirmation
};

</script>

<template>
    <Head title="Liste des Capacités" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Capacités</h1>
                <p class="text-primary-200 mt-2">Gérez les capacités spéciales</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une capacité
            </Btn>
        </div>

        <!-- Grid layout pour permettre le scroll horizontal du tableau quand le quick edit est ouvert -->
        <div
            class="grid grid-cols-1 gap-4"
            :class="{
                'xl:grid-cols-[minmax(0,1fr)_380px]':
                    canModify && selectedEntities.length >= 1 && tableQuickEditEnabled,
            }"
        >
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="capabilities"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('capabilities')"
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
                    entity-type="capabilities"
                    :selected-entities="selectedEntities"
                    :is-admin="canModify"
                    mode="client"
                    :filtered-ids="selectedIds"
                    @applied="handleBulkUpdate"
                    @clear="clearSelection"
                />
            </div>
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="capabilities"
            :fields-config="capabilityCreateFieldsConfig"
            :default-entity="capabilityCreateDefaultEntity"
            :field-sections="CAPABILITY_FORM_FIELD_SECTIONS_CREATE"
            characteristics-group="capability"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('capabilities')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="capabilities"
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

        <CapabilityEditModal
            :open="capabilityEditModalOpen"
            :capability-id="capabilityEditId"
            @close="closeCapabilityEditModal"
            @saved="onCapabilityEditModalSaved"
        />
    </div>
</template>
