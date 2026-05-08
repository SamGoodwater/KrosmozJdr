<script setup>
/**
 * Page liste des états (entité Condition) — tableau et modals.
 *
 * @props {Object} conditions - Données Inertia / collection d’états
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { Condition } from "@/Models/Entity/Condition";
import { useEntityIndexQuickEditTable } from "@/Composables/entity/useEntityIndexQuickEditTable.js";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useScrapping } from "@/Composables/utils/useScrapping";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditPanel from '@/Pages/Organismes/entity/EntityQuickEditPanel.vue';
import EntityQuickEditModal from '@/Pages/Organismes/entity/EntityQuickEditModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getConditionFieldDescriptors } from "@/Entities/condition/condition-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

const props = defineProps({
    conditions: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des états');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('conditions'));
const canModify = computed(() => canUpdateAny('conditions'));

// Bulk request
const { bulkPatchJson } = useBulkRequest();
const { copyToClipboard } = useCopyToClipboard();
const { refreshEntity } = useScrapping();

// Table v2
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);
const { tableQuickEditEnabled, onUpdateTableQuickEdit } = useEntityIndexQuickEditTable(Condition);

// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
    };
    const descriptors = getConditionFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const serverUrl = computed(() => `${route('api.tables.conditions')}?format=entities&limit=5000&_t=${refreshToken.value}`);

// Fields config pour les formulaires (généré depuis les descriptors)
const fieldsConfig = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createFieldsConfigFromDescriptors(getConditionFieldDescriptors(ctx));
});

const defaultEntity = computed(() => {
  const ctx = { meta: { capabilities: { updateAny: canModify.value } } };
  return createDefaultEntityFromDescriptors(getConditionFieldDescriptors(ctx));
});

// Calcul des entités sélectionnées depuis les IDs et les rows
const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Condition.fromArray(raw);
});

// Bulk edit
const handleBulkUpdate = async (payload) => {
  const ok = await bulkPatchJson('/api/entities/conditions/bulk', payload);
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
    // Si c'est déjà une instance Condition, l'utiliser directement
    const model = raw instanceof Condition ? raw : Condition.fromArray([raw])[0] || null;
    if (!model) return;
    selectedEntity.value = model;
    modalView.value = "large";
    modalOpen.value = true;
};

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const quickEditModalOpen = ref(false);
const quickEditEntity = ref(null);

const handleCreate = () => {
    createModalOpen.value = true;
};

const handleCloseCreateModal = () => {
    createModalOpen.value = false;
};

const handleEntityCreated = () => {
    createModalOpen.value = false;
    // Données via API tables : forcer le refetch (reload Inertia ne met pas à jour TanStack).
    refreshToken.value++;
};

const closeModal = () => {
    modalOpen.value = false;
    selectedEntity.value = null;
};

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance Condition, l'utiliser directement
    const model = targetEntity instanceof Condition ? targetEntity : Condition.fromArray([targetEntity])[0] || null;
    if (!model) return;
    
    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.conditions.show', { condition: entityId }));
            break;

        case 'quick-view':
            selectedEntity.value = model;
            modalView.value = 'large';
            modalOpen.value = true;
            break;

        case 'edit':
            router.visit(route('entities.conditions.edit', { condition: entityId }));
            break;

        case 'quick-edit':
            quickEditEntity.value = model;
            quickEditModalOpen.value = true;
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('condition');
            const url = resolveEntityRouteUrl('condition', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'état copié !");
            }
            break;
        }

        case 'download-pdf':
            // TODO: Implémenter le téléchargement PDF
            break;

        case 'refresh':
            await refreshEntity('condition', entityId, { forceUpdate: true });
            refreshToken.value++;
            break;

        case 'delete':
            // TODO: Implémenter la suppression avec confirmation
            break;
    }
};

// Handlers pour les actions du modal
const handleModalQuickEdit = (entity) => {
    quickEditEntity.value = entity;
    quickEditModalOpen.value = true;
    closeModal();
};

const handleModalExpand = (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    router.visit(route('entities.conditions.show', { condition: entityId }));
    closeModal();
};

const handleModalCopyLink = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    const cfg = getEntityRouteConfig('condition');
    const url = resolveEntityRouteUrl('condition', 'show', entityId, cfg);
    if (url) {
        await copyToClipboard(url, "Lien de l'état copié !");
    }
};

const handleModalDownloadPdf = (entity) => {
    // TODO: Implémenter le téléchargement PDF
};

const handleModalRefresh = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    await refreshEntity('condition', entityId, { forceUpdate: true });
    refreshToken.value++;
    closeModal();
};

const handleModalDelete = (entity) => {
    // TODO: Implémenter la suppression avec confirmation
};

const handleQuickEditSubmit = async (payload) => {
    if (payload) {
        const ok = await bulkPatchJson('/api/entities/conditions/bulk', payload);
        if (!ok) return;
    }
    refreshToken.value++;
    quickEditEntity.value = null;
};
</script>

<template>
    <Head title="Liste des états" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des états</h1>
                <p class="text-primary-200 mt-2">Gère les états de ton système</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un état
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
                    entity-type="conditions"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('conditions')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @update:quick-edit-enabled="onUpdateTableQuickEdit"
                    @action="handleTableAction"
                />
            </div>

            <!-- Quick Edit Panel -->
            <div v-if="canModify && selectedEntities.length >= 1 && tableQuickEditEnabled" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="conditions"
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
            entity-type="condition"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

        <!-- Modal de visualisation -->
        <EntityModal
            v-if="selectedEntity"
            :entity="selectedEntity"
            entity-type="condition"
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

        <!-- Modal d'édition rapide -->
        <EntityQuickEditModal
            v-if="quickEditEntity"
            :entity="quickEditEntity"
            entity-type="conditions"
            :is-admin="canModify"
            :open="quickEditModalOpen"
            @close="quickEditModalOpen = false"
            @submit="handleQuickEditSubmit"
        />
    </div>
</template>

