<script setup>
/**
 * Item Index Page
 * 
 * @description
 * Page de liste des items avec tableau et modal
 * 
 * @props {Object} items - Collection paginée des items
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Item } from "@/Models/Entity/Item";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useScrapping } from "@/Composables/utils/useScrapping";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import EntityModal from '@/Pages/Organismes/entity/EntityModal.vue';
import EntityQuickEditPanel from "@/Pages/Organismes/entity/EntityQuickEditPanel.vue";
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import EntityQuickEditModal from '@/Pages/Organismes/entity/EntityQuickEditModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";

// Props Inertia (gardées à titre documentaire, même si non utilisées directement ici)
defineProps({
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

const { bulkPatchJson } = useBulkRequest();
const { copyToClipboard } = useCopyToClipboard();
const { refreshEntity } = useScrapping();

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('items'));
const canModify = computed(() => canUpdateAny('items'));
// const canManage = computed(() => canManageAny('items'));

// Table v2 state (client-first)
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const serverUrl = computed(() => `${route('api.tables.items')}?limit=5000&format=entities&_t=${refreshToken.value}`);

const selectedEntities = computed(() => {
    if (!Array.isArray(selectedIds.value) || !selectedIds.value.length) return [];
    // Normaliser pour éviter les mismatch string vs number (Set.has est strict)
    const idSet = new Set(selectedIds.value.map((v) => Number(v)).filter((n) => Number.isFinite(n)));
    const raw = (tableRows.value || [])
        .filter((r) => idSet.has(Number(r?.id)))
        .map((r) => r?.rowParams?.entity)
        .filter(Boolean);
    return Item.fromArray(raw);
});

const handleTableLoaded = ({ rows }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
};

// État
const selectedEntity = ref(null);
const modalOpen = ref(false);
const modalView = ref('large');
const createModalOpen = ref(false);
const quickEditModalOpen = ref(false);
const quickEditEntity = ref(null);

// Sécurité UX: si l'utilisateur perd le droit de modifier, on coupe les modes d'édition.
watch(
    () => canModify.value,
    (allowed) => {
        if (allowed) return;
        selectedIds.value = [];
    },
    { immediate: true }
);

// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
        itemTypes: props.itemTypes || [],
    };
    const descriptors = getItemFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});

// Handlers
const openModal = (entity) => {
    selectedEntity.value = entity;
    modalView.value = 'large';
    modalOpen.value = true;
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Item.fromArray([raw])[0] || null;
    if (!model) return;
    openModal(model);
};

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    const model = Item.fromArray([targetEntity])[0] || null;
    if (!model) return;
    
    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.items.show', { item: entityId }));
            break;

        case 'quick-view':
            openModal(model);
            break;

        case 'edit':
            router.visit(route('entities.items.edit', { item: entityId }));
            break;

        case 'quick-edit':
            quickEditEntity.value = model;
            quickEditModalOpen.value = true;
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('item');
            const url = resolveEntityRouteUrl('item', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            // TODO: Implémenter le téléchargement PDF
            break;

        case 'refresh':
            await refreshEntity('item', entityId, { forceUpdate: true });
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
    router.visit(route('entities.items.show', { item: entityId }));
    closeModal();
};

const handleModalCopyLink = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    const cfg = getEntityRouteConfig('item');
    const url = resolveEntityRouteUrl('item', 'show', entityId, cfg);
    if (url) {
        await copyToClipboard(url, "Lien de l'entité copié !");
    }
};

const handleModalDownloadPdf = (entity) => {
    // TODO: Implémenter le téléchargement PDF
    console.log('Download PDF:', entity);
};

const handleModalRefresh = async (entity) => {
    const entityId = entity?.id;
    if (!entityId) return;
    await refreshEntity('item', entityId, { forceUpdate: true });
    refreshToken.value++;
    closeModal();
};

const handleModalDelete = (entity) => {
    // TODO: Implémenter la suppression avec confirmation
    console.log('Delete:', entity);
};

const handleQuickEditSubmit = () => {
    refreshToken.value++;
    quickEditEntity.value = null;
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

// Descriptors -> fieldsConfig (source de vérité unique)
const itemDescriptors = computed(() => getItemFieldDescriptors({ capabilities: { updateAny: canModify.value } }));
const fieldsConfig = computed(() => createFieldsConfigFromDescriptors(itemDescriptors.value, { meta: {}, capabilities: { updateAny: canModify.value } }));
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(itemDescriptors.value));

const handleBulkApplied = async (payload) => {
    const ok = await bulkPatchJson({ url: "/api/entities/items/bulk", payload });
    if (!ok) return;
    refreshToken.value++;
    selectedIds.value = [];
};

const clearSelection = () => {
    selectedIds.value = [];
};

// const handleRefreshAll = () => refreshToken.value++;
</script>

<template>
    <Head title="Liste des Objets" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Objets</h1>
                <p class="text-primary-200 mt-2">Gérez les objets et équipements</p>
            </div>
            <div class="flex gap-2 items-center">
                <Btn v-if="canCreate" @click="handleCreate" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un objet
                </Btn>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4"
            :class="{ 'xl:grid-cols-[minmax(0,1fr)_380px]': selectedEntities.length >= 1 }"
        >
            <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="items"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('items')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @action="handleTableAction"
                />
            </div>

            <div v-if="canModify && selectedEntities.length >= 1" class="sticky top-4 self-start">
                <EntityQuickEditPanel
                    entity-type="items"
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
            entity-type="item"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
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
            entity-type="item"
            :fields-config="fieldsConfig"
            :open="quickEditModalOpen"
            @close="quickEditModalOpen = false"
            @submit="handleQuickEditSubmit"
        />
    </div>
</template>

